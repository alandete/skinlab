/**
 * SkinLab – Admin Projects JavaScript
 * CRUD de proyectos vía API.
 */

(function () {
    'use strict';

    var L = getLang();

    // Si no estamos en la página de proyectos, salir
    if (!document.getElementById('projects-grid')) return;

    // ── Slug preview ──
    var projectName = document.getElementById('project-name');
    var slugPreview = document.getElementById('slug-preview');

    if (projectName) {
        projectName.addEventListener('input', function () {
            var slug = toSlug(projectName.value);
            slugPreview.textContent = slug || '---';
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

    // ── Organization toggle ──
    var orgType = document.getElementById('org-type');
    var orgCount = document.getElementById('org-count');

    if (orgType) {
        orgType.addEventListener('change', function () {
            orgCount.disabled = orgType.value === 'none';
            if (orgType.value === 'none') orgCount.value = '';
            else if (!orgCount.value) orgCount.value = 4;
        });
    }

    // ── Color sync (picker ↔ hex) ──
    function syncColors(prefix) {
        ['primary', 'secondary'].forEach(function (id) {
            var picker = document.getElementById(prefix + 'color-' + id + '-picker');
            var hex = document.getElementById(prefix + 'color-' + id);
            if (!picker || !hex) return;

            picker.addEventListener('input', function () {
                hex.value = picker.value.toUpperCase();
            });
            hex.addEventListener('input', function () {
                var val = hex.value.trim();
                if (!val.startsWith('#')) val = '#' + val;
                hex.value = val.toUpperCase();
                if (/^#[0-9A-F]{6}$/i.test(val)) picker.value = val;
            });
        });
    }
    syncColors('');
    syncColors('edit-');

    // ── Show/hide create form ──
    var btnNew = document.getElementById('btn-new-project');
    var formCard = document.getElementById('project-form');
    var btnCancel = document.getElementById('btn-cancel-project');

    if (btnNew) {
        btnNew.addEventListener('click', function () {
            formCard.classList.remove('hidden');
            projectName.value = '';
            slugPreview.textContent = '---';
            projectName.focus();
        });
    }
    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            formCard.classList.add('hidden');
        });
    }

    // ── Create project ──
    var btnCreate = document.getElementById('btn-create-project');

    if (btnCreate) {
        btnCreate.addEventListener('click', function () {
            var name = projectName.value.trim();
            if (!name) {
                showToast(L.required || 'Campo obligatorio', 'error');
                return;
            }

            var slug = toSlug(name);
            if (!slug) {
                showToast(L.required || 'Campo obligatorio', 'error');
                return;
            }

            var cdns = [];
            document.querySelectorAll('.cdn-option input:checked').forEach(function (cb) {
                cdns.push(cb.value);
            });

            btnCreate.disabled = true;
            api('/api/projects/create', {
                method: 'POST',
                body: JSON.stringify({
                    name: name,
                    slug: slug,
                    cdns: cdns,
                    colors: {
                        primary: document.getElementById('color-primary').value,
                        secondary: document.getElementById('color-secondary').value
                    },
                    orgType: orgType.value,
                    orgCount: orgType.value !== 'none' ? parseInt(orgCount.value || 0) : 0
                })
            }).then(function (data) {
                showToast(data.message, 'success');
                btnCreate.disabled = false;
                setTimeout(function () { location.reload(); }, 1500);
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnCreate.disabled = false;
            });
        });
    }

    // ── Edit project (modal) ──
    var editModal = document.getElementById('edit-project-modal');

    var editOrgType = document.getElementById('edit-org-type');
    var editOrgCount = document.getElementById('edit-org-count');

    if (editOrgType) {
        editOrgType.addEventListener('change', function () {
            editOrgCount.disabled = editOrgType.value === 'none';
            if (editOrgType.value === 'none') editOrgCount.value = '';
            else if (!editOrgCount.value) editOrgCount.value = 4;
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit-project');
        if (!btn) return;

        document.getElementById('edit-project-id').value = btn.dataset.projectId;
        document.getElementById('edit-project-name').value = btn.dataset.name;
        document.getElementById('edit-color-primary').value = btn.dataset.primary;
        document.getElementById('edit-color-primary-picker').value = btn.dataset.primary;
        document.getElementById('edit-color-secondary').value = btn.dataset.secondary;
        document.getElementById('edit-color-secondary-picker').value = btn.dataset.secondary;

        // Pre-cargar organización
        var ot = btn.dataset.orgType || 'none';
        var oc = parseInt(btn.dataset.orgCount || 0);
        editOrgType.value = ot;
        editOrgCount.value = ot !== 'none' ? oc : '';
        editOrgCount.disabled = ot === 'none';

        // Pre-cargar CDNs
        var activeCdns = (btn.dataset.cdns || '').split(',').filter(Boolean);
        document.querySelectorAll('#edit-cdn-grid input[type="checkbox"]').forEach(function (cb) {
            cb.checked = activeCdns.indexOf(cb.value) !== -1;
        });

        editModal.classList.remove('hidden');
        document.getElementById('edit-project-name').focus();

        var menu = btn.closest('.dropdown-menu');
        if (menu) menu.classList.remove('open');
    });

    var btnSaveProject = document.getElementById('btn-save-project');
    var btnCancelEditProject = document.getElementById('btn-cancel-edit-project');

    if (btnSaveProject) {
        btnSaveProject.addEventListener('click', function () {
            var projectId = parseInt(document.getElementById('edit-project-id').value);
            var name = document.getElementById('edit-project-name').value.trim();

            var editCdns = [];
            document.querySelectorAll('#edit-cdn-grid input:checked').forEach(function (cb) {
                editCdns.push(cb.value);
            });

            btnSaveProject.disabled = true;
            api('/api/projects/edit', {
                method: 'POST',
                body: JSON.stringify({
                    project_id: projectId,
                    name: name,
                    colors: {
                        primary: document.getElementById('edit-color-primary').value,
                        secondary: document.getElementById('edit-color-secondary').value
                    },
                    orgType: editOrgType.value,
                    orgCount: editOrgType.value !== 'none' ? parseInt(editOrgCount.value || 0) : 0,
                    cdns: editCdns
                })
            }).then(function (data) {
                showToast(data.message, 'success');
                editModal.classList.add('hidden');
                btnSaveProject.disabled = false;
                setTimeout(function () { location.reload(); }, 1200);
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnSaveProject.disabled = false;
            });
        });
    }
    if (btnCancelEditProject) {
        btnCancelEditProject.addEventListener('click', function () {
            editModal.classList.add('hidden');
        });
    }
    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) editModal.classList.add('hidden');
        });
    }

    // ── Compile CSS ──
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-compile-project');
        if (!btn) return;

        var slug = btn.dataset.slug;
        var menu = btn.closest('.dropdown-menu');
        if (menu) menu.classList.remove('open');

        api('/api/projects/compile', {
            method: 'POST',
            body: JSON.stringify({ project: slug })
        }).then(function (data) {
            showToast(data.message, 'success');
        }).catch(function (err) {
            showToast(err.message, 'error');
        });
    });

    // ── Toggle project ──
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-toggle-project');
        if (!btn) return;

        var projectId = parseInt(btn.dataset.projectId);
        var menu = btn.closest('.dropdown-menu');
        if (menu) menu.classList.remove('open');

        api('/api/projects/toggle', {
            method: 'POST',
            body: JSON.stringify({ project_id: projectId })
        }).then(function (data) {
            showToast(data.message, 'success');
            var card = document.querySelector('.project-card[data-project-id="' + projectId + '"]');
            if (card) {
                card.classList.toggle('project-card-inactive', !data.is_active);
            }
        }).catch(function (err) {
            showToast(err.message, 'error');
        });
    });

    // ── Delete project ──
    var confirmModal = document.getElementById('confirm-modal');
    var confirmBody = document.getElementById('confirm-body');
    var btnConfirmAction = document.getElementById('btn-confirm-action');
    var btnCancelAction = document.getElementById('btn-cancel-action');
    var pendingCallback = null;

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-project');
        if (!btn) return;

        var projectId = parseInt(btn.dataset.projectId);
        var name = btn.dataset.name;

        var menu = btn.closest('.dropdown-menu');
        if (menu) menu.classList.remove('open');

        confirmBody.innerHTML = (L.confirm_delete_project || '').replace(':name', escapeHtml(name));
        btnConfirmAction.textContent = L.delete || 'Eliminar';
        btnConfirmAction.className = 'btn btn-danger';
        confirmModal.classList.remove('hidden');

        pendingCallback = function (done) {
            api('/api/projects/delete', {
                method: 'POST',
                body: JSON.stringify({ project_id: projectId })
            }).then(function (data) {
                showToast(data.message, 'success');
                done();
                var card = document.querySelector('.project-card[data-project-id="' + projectId + '"]');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transition = 'opacity 0.3s';
                    setTimeout(function () { card.remove(); }, 300);
                }
            }).catch(function (err) {
                showToast(err.message, 'error');
                done();
            });
        };
    });

    if (btnConfirmAction) {
        btnConfirmAction.addEventListener('click', function () {
            if (pendingCallback) {
                btnConfirmAction.disabled = true;
                pendingCallback(function () {
                    btnConfirmAction.disabled = false;
                    confirmModal.classList.add('hidden');
                    pendingCallback = null;
                });
            }
        });
    }
    if (btnCancelAction) {
        btnCancelAction.addEventListener('click', function () {
            confirmModal.classList.add('hidden');
            pendingCallback = null;
        });
    }
    if (confirmModal) {
        confirmModal.addEventListener('click', function (e) {
            if (e.target === confirmModal) {
                confirmModal.classList.add('hidden');
                pendingCallback = null;
            }
        });
    }

    // ── Escape ──
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (editModal && !editModal.classList.contains('hidden')) editModal.classList.add('hidden');
            if (confirmModal && !confirmModal.classList.contains('hidden')) {
                confirmModal.classList.add('hidden');
                pendingCallback = null;
            }
        }
    });

})();
