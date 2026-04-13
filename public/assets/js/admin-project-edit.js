/**
 * SkinLab – Admin: Crear/Editar Proyecto
 */

(function () {
    'use strict';

    var L = getLang();
    var btnSave = document.getElementById('btn-save-project');
    if (!btnSave) return;

    var isNew = btnSave.dataset.isNew === '1';
    var projectSlug = btnSave.dataset.slug;

    // ── Color sync ──
    document.querySelectorAll('.color-field').forEach(function (field) {
        var picker = field.querySelector('input[type="color"]');
        var hex = field.querySelector('.color-hex');
        if (!picker || !hex) return;

        picker.addEventListener('input', function () {
            hex.value = picker.value.toUpperCase();
        });

        function hexToPicker() {
            var val = hex.value.trim();
            if (!val.startsWith('#')) val = '#' + val;
            hex.value = val.toUpperCase();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) picker.value = val.toLowerCase();
        }

        hex.addEventListener('input', hexToPicker);
        hex.addEventListener('change', hexToPicker);
    });

    // ── Slug preview (solo crear) ──
    var nameInput = document.getElementById('project-name');
    var slugPreview = document.getElementById('slug-preview');

    if (nameInput && slugPreview) {
        nameInput.addEventListener('input', function () {
            slugPreview.textContent = toSlug(nameInput.value) || '---';
        });
    }

    function toSlug(text) {
        return text.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    // ── Org type toggle ──
    var orgType = document.getElementById('org-type');
    var orgCount = document.getElementById('org-count');

    if (orgType) {
        orgType.addEventListener('change', function () {
            orgCount.disabled = orgType.value === 'none';
            if (orgType.value === 'none') orgCount.value = '';
            else if (!orgCount.value) orgCount.value = 4;
        });
    }

    // ── Guardar proyecto ──
    btnSave.addEventListener('click', function () {
        var name = nameInput.value.trim();
        if (!name) {
            showToast(L.required || 'Campo obligatorio', 'error');
            return;
        }

        var cdns = [];
        document.querySelectorAll('.cdn-chip input:checked').forEach(function (cb) {
            cdns.push(cb.value);
        });

        // Actividades seleccionadas
        var activities = [];
        document.querySelectorAll('input[name="activities[]"]:checked').forEach(function (cb) {
            activities.push(cb.value);
        });

        // Páginas adicionales
        var customPages = [];
        document.querySelectorAll('.new-page-input').forEach(function (input) {
            var val = input.value.trim();
            if (val) customPages.push(val);
        });

        var body = {
            name: name,
            colors: {
                primary: document.getElementById('color-primary').value,
                secondary: document.getElementById('color-secondary').value
            },
            navColors: {
                bg: document.getElementById('nav-bg-color').value,
                text: document.getElementById('nav-text-color').value
            },
            cdns: cdns,
            activities: activities,
            customPages: customPages
        };

        if (isNew) {
            body.slug = toSlug(name);
            if (!body.slug) {
                showToast(L.required || 'Campo obligatorio', 'error');
                return;
            }
            body.orgType = orgType.value;
            body.orgCount = orgType.value !== 'none' ? parseInt(orgCount.value || 0) : 0;
        } else {
            body.project_id = parseInt(btnSave.dataset.projectId || 0);
        }

        btnSave.disabled = true;
        var url = isNew ? '/api/projects/create' : '/api/projects/edit';

        api(url, {
            method: 'POST',
            body: JSON.stringify(body)
        }).then(function (data) {
            showToast(data.message, 'success');
            btnSave.disabled = false;
            setTimeout(function () {
                window.location.href = '/admin/projects';
            }, 1000);
        }).catch(function (err) {
            showToast(err.message, 'error');
            btnSave.disabled = false;
        });
    });

    // ── Páginas adicionales: generar inputs según cantidad ──
    var pagesCountInput = document.getElementById('custom-pages-count');
    var pagesContainer = document.getElementById('new-pages-container');

    if (pagesCountInput && pagesContainer) {
        pagesCountInput.addEventListener('input', function () {
            var count = parseInt(pagesCountInput.value) || 0;
            var current = pagesContainer.querySelectorAll('.new-page-row').length;

            // Agregar filas
            while (current < count) {
                var row = document.createElement('div');
                row.className = 'new-page-row';
                row.innerHTML = '<span class="new-page-number">' + (current + 1) + '</span>' +
                    '<input type="text" class="form-input new-page-input" placeholder="' + escapeHtml(L.page_name || 'Nombre de la página') + '">';
                pagesContainer.appendChild(row);
                current++;
            }

            // Quitar filas
            while (current > count) {
                pagesContainer.lastElementChild.remove();
                current--;
            }
        });
    }

    // ── Guardar páginas adicionales ──
    var btnSavePages = document.getElementById('btn-save-pages');

    if (btnSavePages) {
        btnSavePages.addEventListener('click', function () {
            var inputs = pagesContainer.querySelectorAll('.new-page-input');
            var names = [];
            inputs.forEach(function (input) {
                var val = input.value.trim();
                if (val) names.push(val);
            });

            if (names.length === 0) {
                showToast(L.required || 'Campo obligatorio', 'error');
                return;
            }

            btnSavePages.disabled = true;
            api('/api/projects/pages/add', {
                method: 'POST',
                body: JSON.stringify({ slug: projectSlug, pages: names })
            }).then(function (data) {
                showToast(data.message, 'success');
                btnSavePages.disabled = false;
                setTimeout(function () { location.reload(); }, 1000);
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnSavePages.disabled = false;
            });
        });
    }

    // ── Eliminar páginas ──
    var confirmModal = document.getElementById('confirm-modal');
    var confirmBody = document.getElementById('confirm-body');
    var btnConfirm = document.getElementById('btn-confirm-action');
    var btnCancel = document.getElementById('btn-cancel-action');
    var pendingCallback = null;

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-page');
        if (!btn) return;

        var pageSlug = btn.dataset.slug;
        var pageName = btn.dataset.name;

        confirmBody.innerHTML = (L.confirm_delete_page || '').replace(':name', escapeHtml(pageName));
        btnConfirm.textContent = L.delete || 'Eliminar';
        btnConfirm.className = 'btn btn-danger';
        confirmModal.classList.remove('hidden');

        pendingCallback = function (done) {
            api('/api/projects/pages/delete', {
                method: 'POST',
                body: JSON.stringify({ slug: projectSlug, page: pageSlug })
            }).then(function (data) {
                showToast(data.message, 'success');
                done();
                var item = document.querySelector('.pages-list-item[data-slug="' + pageSlug + '"]');
                if (item) {
                    item.style.opacity = '0';
                    item.style.transition = 'opacity 0.3s';
                    setTimeout(function () { item.remove(); }, 300);
                }
            }).catch(function (err) {
                showToast(err.message, 'error');
                done();
            });
        };
    });

    if (btnConfirm) {
        btnConfirm.addEventListener('click', function () {
            if (pendingCallback) {
                btnConfirm.disabled = true;
                pendingCallback(function () {
                    btnConfirm.disabled = false;
                    confirmModal.classList.add('hidden');
                    pendingCallback = null;
                });
            }
        });
    }
    if (btnCancel) btnCancel.addEventListener('click', function () { confirmModal.classList.add('hidden'); pendingCallback = null; });
    if (confirmModal) confirmModal.addEventListener('click', function (e) { if (e.target === confirmModal) { confirmModal.classList.add('hidden'); pendingCallback = null; } });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && confirmModal && !confirmModal.classList.contains('hidden')) {
            confirmModal.classList.add('hidden');
            pendingCallback = null;
        }
    });

})();
