/**
 * SkinLab – Admin: Crear/Editar Proyecto
 * Layout de pestañas con save unificado (crear y editar usan el mismo flujo).
 */

(function () {
    'use strict';

    var L = getLang();
    var btnSave = document.getElementById('btn-save-project');
    if (!btnSave) return;

    var isNew = btnSave.dataset.isNew === '1';
    var projectSlug = btnSave.dataset.slug;

    // ══════════════════════════════════════════════
    // TABS
    // ══════════════════════════════════════════════
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.project-tab'));

    function activateTab(tab) {
        tabs.forEach(function (t) {
            var panel = document.getElementById(t.getAttribute('aria-controls'));
            var isActive = t === tab;
            t.classList.toggle('active', isActive);
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
            t.setAttribute('tabindex', isActive ? '0' : '-1');
            if (panel) {
                panel.classList.toggle('active', isActive);
                panel.hidden = !isActive;
            }
        });
    }

    tabs.forEach(function (tab, idx) {
        tab.addEventListener('click', function () { activateTab(tab); tab.focus(); });
        tab.addEventListener('keydown', function (e) {
            var next = null;
            if (e.key === 'ArrowRight') next = tabs[(idx + 1) % tabs.length];
            else if (e.key === 'ArrowLeft') next = tabs[(idx - 1 + tabs.length) % tabs.length];
            else if (e.key === 'Home') next = tabs[0];
            else if (e.key === 'End') next = tabs[tabs.length - 1];
            if (next) { e.preventDefault(); activateTab(next); next.focus(); }
        });
    });

    // ══════════════════════════════════════════════
    // COLOR PICKERS ↔ HEX SYNC
    // ══════════════════════════════════════════════
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

    // ══════════════════════════════════════════════
    // SLUG PREVIEW (solo crear)
    // ══════════════════════════════════════════════
    var nameInput = document.getElementById('project-name');
    var slugPreview = document.getElementById('slug-preview');

    function toSlug(text) {
        return text.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    if (nameInput && slugPreview) {
        nameInput.addEventListener('input', function () {
            slugPreview.textContent = toSlug(nameInput.value) || '---';
        });
    }

    // ══════════════════════════════════════════════
    // ORG TYPE TOGGLE
    // ══════════════════════════════════════════════
    var orgType = document.getElementById('org-type');
    var orgCount = document.getElementById('org-count');

    if (orgType && orgCount) {
        orgType.addEventListener('change', function () {
            var isNone = orgType.value === 'none';
            orgCount.disabled = isNone;
            if (isNone) orgCount.value = '';
            else if (!orgCount.value) orgCount.value = 4;
        });
    }

    // ══════════════════════════════════════════════
    // PÁGINAS: AGREGAR / RENOMBRAR / ELIMINAR
    // ══════════════════════════════════════════════
    var customList = document.getElementById('custom-list');
    var btnAddCustom = document.getElementById('btn-add-custom-page');

    function createPageItem(name, oldSlug) {
        var li = document.createElement('li');
        li.className = 'page-list-item';
        li.dataset.oldSlug = oldSlug || '';

        var span = document.createElement('span');
        span.className = 'page-list-name';
        span.textContent = name;

        var actions = document.createElement('div');
        actions.className = 'page-list-actions';

        var btnRename = document.createElement('button');
        btnRename.type = 'button';
        btnRename.className = 'btn-icon btn-icon-sm btn-rename-page';
        btnRename.title = L.rename_page || 'Renombrar';
        btnRename.setAttribute('aria-label', (L.rename_page || 'Renombrar') + ' — ' + name);
        btnRename.innerHTML = '<i class="bi bi-pencil" aria-hidden="true"></i>';

        var btnDelete = document.createElement('button');
        btnDelete.type = 'button';
        btnDelete.className = 'btn-icon btn-icon-sm btn-delete-page';
        btnDelete.title = L.delete || 'Eliminar';
        btnDelete.setAttribute('aria-label', (L.delete || 'Eliminar') + ' — ' + name);
        btnDelete.dataset.name = name;
        btnDelete.innerHTML = '<i class="bi bi-trash" aria-hidden="true"></i>';

        actions.appendChild(btnRename);
        actions.appendChild(btnDelete);
        li.appendChild(span);
        li.appendChild(actions);
        return li;
    }

    function startInlineRename(item) {
        if (item.querySelector('.page-rename-input')) return;

        var span = item.querySelector('.page-list-name');
        if (!span) return;

        var currentName = span.textContent;
        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-input form-input-sm page-rename-input';
        input.value = currentName;
        input.maxLength = 80;

        span.replaceWith(input);
        item.classList.add('is-editing');
        input.focus();
        input.select();

        function commit() {
            var newName = input.value.trim();
            var span2 = document.createElement('span');
            span2.className = 'page-list-name';
            span2.textContent = newName || currentName;
            input.replaceWith(span2);
            item.classList.remove('is-editing');

            if (!newName) return;

            // Actualizar aria-label del botón delete
            var btnDel = item.querySelector('.btn-delete-page');
            if (btnDel) {
                btnDel.dataset.name = newName;
                btnDel.setAttribute('aria-label', (L.delete || 'Eliminar') + ' — ' + newName);
            }
            var btnRen = item.querySelector('.btn-rename-page');
            if (btnRen) {
                btnRen.setAttribute('aria-label', (L.rename_page || 'Renombrar') + ' — ' + newName);
            }
        }

        function cancel() {
            var span2 = document.createElement('span');
            span2.className = 'page-list-name';
            span2.textContent = currentName;
            input.replaceWith(span2);
            item.classList.remove('is-editing');
        }

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); commit(); }
            else if (e.key === 'Escape') { e.preventDefault(); cancel(); }
        });
        input.addEventListener('blur', commit);
    }

    // Agregar página adicional
    if (btnAddCustom && customList) {
        btnAddCustom.addEventListener('click', function () {
            var li = document.createElement('li');
            li.className = 'page-list-item is-editing';
            li.dataset.oldSlug = '';

            var input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-input form-input-sm page-rename-input';
            input.placeholder = L.page_name || 'Nombre de la página';
            input.maxLength = 80;

            var actions = document.createElement('div');
            actions.className = 'page-list-actions';

            var btnDelete = document.createElement('button');
            btnDelete.type = 'button';
            btnDelete.className = 'btn-icon btn-icon-sm btn-delete-page';
            btnDelete.title = L.delete || 'Eliminar';
            btnDelete.setAttribute('aria-label', L.delete || 'Eliminar');
            btnDelete.innerHTML = '<i class="bi bi-trash" aria-hidden="true"></i>';
            actions.appendChild(btnDelete);

            li.appendChild(input);
            li.appendChild(actions);
            customList.appendChild(li);

            input.focus();

            function commit() {
                var name = input.value.trim();
                if (!name) {
                    li.remove();
                    return;
                }
                var span = document.createElement('span');
                span.className = 'page-list-name';
                span.textContent = name;

                // Agregar botón rename junto al delete
                var btnRename = document.createElement('button');
                btnRename.type = 'button';
                btnRename.className = 'btn-icon btn-icon-sm btn-rename-page';
                btnRename.title = L.rename_page || 'Renombrar';
                btnRename.setAttribute('aria-label', (L.rename_page || 'Renombrar') + ' — ' + name);
                btnRename.innerHTML = '<i class="bi bi-pencil" aria-hidden="true"></i>';
                actions.insertBefore(btnRename, btnDelete);

                btnDelete.dataset.name = name;
                btnDelete.setAttribute('aria-label', (L.delete || 'Eliminar') + ' — ' + name);

                input.replaceWith(span);
                li.classList.remove('is-editing');
            }

            function cancel() {
                li.remove();
            }

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); commit(); }
                else if (e.key === 'Escape') { e.preventDefault(); cancel(); }
            });
            input.addEventListener('blur', commit);
        });
    }

    var orgList = document.getElementById('org-list');
    var btnAddOrg = document.getElementById('btn-add-org-page');
    var btnAddOrgLabel = document.getElementById('btn-add-org-label');

    var orgMeta = {
        semanas:  { prefix: 'semana',  label: 'Semana' },
        modulos:  { prefix: 'modulo',  label: 'Módulo' },
        unidades: { prefix: 'unidad',  label: 'Unidad' }
    };

    function syncOrgCount() {
        if (!orgList || !orgCount) return;
        var count = orgList.querySelectorAll('.page-list-item').length;
        orgCount.value = count > 0 ? count : '';
    }

    function refreshAddOrgButton() {
        if (!btnAddOrg || !orgType) return;
        var t = orgType.value;
        if (t === 'none' || !orgMeta[t]) {
            btnAddOrg.hidden = true;
            return;
        }
        btnAddOrg.hidden = false;
        if (btnAddOrgLabel) {
            btnAddOrgLabel.textContent = L['add_org_' + t] || ('Agregar ' + orgMeta[t].label);
        }
    }

    function currentOrgMax(prefix) {
        if (!orgList) return 0;
        var max = 0;
        orgList.querySelectorAll('.page-list-item').forEach(function (li) {
            var slug = li.dataset.oldSlug || '';
            var re = new RegExp('^' + prefix + '-(\\d+)$');
            var m = slug.match(re);
            if (m) max = Math.max(max, parseInt(m[1], 10));
        });
        return max;
    }

    function addOrgPage() {
        if (!orgType || !orgList) return;
        var t = orgType.value;
        if (t === 'none' || !orgMeta[t]) return;

        var prefix = orgMeta[t].prefix;
        var label = orgMeta[t].label;
        var nextNum = currentOrgMax(prefix) + 1;
        var padded = nextNum < 10 ? '0' + nextNum : '' + nextNum;
        var newSlug = prefix + '-' + padded;
        var newName = label + ' ' + padded;

        var li = document.createElement('li');
        li.className = 'page-list-item';
        li.dataset.oldSlug = newSlug;

        var span = document.createElement('span');
        span.className = 'page-list-name';
        span.textContent = newName;

        var actions = document.createElement('div');
        actions.className = 'page-list-actions';

        var btnDelete = document.createElement('button');
        btnDelete.type = 'button';
        btnDelete.className = 'btn-icon btn-icon-sm btn-delete-page';
        btnDelete.title = L.delete || 'Eliminar';
        btnDelete.dataset.name = newName;
        btnDelete.setAttribute('aria-label', (L.delete || 'Eliminar') + ' — ' + newName);
        btnDelete.innerHTML = '<i class="bi bi-trash" aria-hidden="true"></i>';
        actions.appendChild(btnDelete);

        li.appendChild(span);
        li.appendChild(actions);
        orgList.appendChild(li);

        syncOrgCount();
    }

    if (btnAddOrg) btnAddOrg.addEventListener('click', addOrgPage);
    if (orgType) orgType.addEventListener('change', refreshAddOrgButton);
    refreshAddOrgButton();

    // Delegación para rename y delete (custom + org)
    document.addEventListener('click', function (e) {
        var renameBtn = e.target.closest('.btn-rename-page');
        if (renameBtn) {
            var item = renameBtn.closest('.page-list-item');
            if (item) startInlineRename(item);
            return;
        }

        var deleteBtn = e.target.closest('.btn-delete-page');
        if (deleteBtn) {
            var item2 = deleteBtn.closest('.page-list-item');
            if (!item2) return;
            var name = deleteBtn.dataset.name || item2.querySelector('.page-list-name')?.textContent || '';
            var isOrgItem = orgList && orgList.contains(item2);
            confirmDelete(name, function () {
                item2.style.opacity = '0';
                item2.style.transition = 'opacity 0.3s';
                setTimeout(function () {
                    item2.remove();
                    if (isOrgItem) syncOrgCount();
                }, 300);
            });
        }
    });

    // ══════════════════════════════════════════════
    // MODAL DE CONFIRMACIÓN (solo para eliminar)
    // ══════════════════════════════════════════════
    var confirmModal = document.getElementById('confirm-modal');
    var confirmBody = document.getElementById('confirm-body');
    var btnConfirm = document.getElementById('btn-confirm-action');
    var btnCancel = document.getElementById('btn-cancel-action');
    var pendingCallback = null;

    function confirmAction(message, callback) {
        if (!confirmModal) { callback(); return; }
        confirmBody.innerHTML = message;
        btnConfirm.textContent = L.delete || 'Eliminar';
        btnConfirm.className = 'btn btn-danger';
        confirmModal.classList.remove('hidden');
        pendingCallback = callback;
    }

    function confirmDelete(name, callback) {
        var msg = (L.confirm_delete_page || '').replace(':name', escapeHtml(name));
        confirmAction(msg, callback);
    }

    if (btnConfirm) {
        btnConfirm.addEventListener('click', function () {
            if (pendingCallback) {
                pendingCallback();
                confirmModal.classList.add('hidden');
                pendingCallback = null;
            }
        });
    }
    if (btnCancel) btnCancel.addEventListener('click', function () { confirmModal.classList.add('hidden'); pendingCallback = null; });
    if (confirmModal) confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) { confirmModal.classList.add('hidden'); pendingCallback = null; }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && confirmModal && !confirmModal.classList.contains('hidden')) {
            confirmModal.classList.add('hidden');
            pendingCallback = null;
        }
    });

    // ══════════════════════════════════════════════
    // RECOLECCIÓN DE ESTADO + GUARDAR
    // ══════════════════════════════════════════════
    function collectPages(listEl) {
        var pages = [];
        if (!listEl) return pages;
        listEl.querySelectorAll('.page-list-item').forEach(function (li) {
            var span = li.querySelector('.page-list-name');
            var name = span ? span.textContent.trim() : '';
            if (!name) return;
            pages.push({
                oldSlug: li.dataset.oldSlug || null,
                name: name
            });
        });
        return pages;
    }

    // ══════════════════════════════════════════════
    // PROTECCIÓN (sincroniza botón eliminar con checkbox)
    // ══════════════════════════════════════════════
    var isProtectedCb = document.getElementById('is-protected');
    var btnDeleteProject = document.getElementById('btn-delete-project');

    if (isProtectedCb && btnDeleteProject) {
        var syncDeleteVisibility = function () {
            btnDeleteProject.hidden = isProtectedCb.checked;
        };
        isProtectedCb.addEventListener('change', syncDeleteVisibility);
        syncDeleteVisibility();
    }

    // ══════════════════════════════════════════════
    // ELIMINAR PROYECTO
    // ══════════════════════════════════════════════
    if (btnDeleteProject) {
        btnDeleteProject.addEventListener('click', function () {
            if (isProtectedCb && isProtectedCb.checked) return;
            var projectId = parseInt(btnDeleteProject.dataset.projectId || 0, 10);
            var name = btnDeleteProject.dataset.name || '';
            if (!projectId) return;

            var msg = (L.confirm_delete || '').replace(':name', escapeHtml(name));
            confirmAction(msg, function () {
                btnDeleteProject.disabled = true;
                api('/api/projects/delete', {
                    method: 'POST',
                    body: JSON.stringify({ project_id: projectId })
                }).then(function (data) {
                    showToast(data.message || L.project_deleted, 'success');
                    setTimeout(function () {
                        window.location.href = '/admin/projects';
                    }, 800);
                }).catch(function (err) {
                    showToast(err.message, 'error');
                    btnDeleteProject.disabled = false;
                });
            });
        });
    }

    btnSave.addEventListener('click', function () {
        var name = nameInput.value.trim();
        if (!name) {
            showToast(L.required || 'Campo obligatorio', 'error');
            activateTab(document.getElementById('tab-config'));
            nameInput.focus();
            return;
        }

        var cdns = [];
        document.querySelectorAll('input[name="cdns[]"]:checked').forEach(function (cb) {
            cdns.push(cb.value);
        });

        var activities = [];
        document.querySelectorAll('input[name="activities[]"]:checked').forEach(function (cb) {
            activities.push(cb.value);
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
            customPages: collectPages(customList),
            organization: collectPages(document.getElementById('org-list')),
            orgType: orgType ? orgType.value : 'none',
            orgCount: orgType && orgType.value !== 'none' ? (parseInt(orgCount.value, 10) || 0) : 0
        };

        if (isNew) {
            body.slug = toSlug(name);
            if (!body.slug) {
                showToast(L.required || 'Campo obligatorio', 'error');
                activateTab(document.getElementById('tab-config'));
                return;
            }
        } else {
            body.project_id = parseInt(btnSave.dataset.projectId || 0);
            if (isProtectedCb) body.is_protected = isProtectedCb.checked ? 1 : 0;
        }

        btnSave.disabled = true;
        var url = isNew ? '/api/projects/create' : '/api/projects/edit';

        api(url, {
            method: 'POST',
            body: JSON.stringify(body)
        }).then(function (data) {
            showToast(data.message, 'success');
            setTimeout(function () {
                window.location.href = '/admin/projects';
            }, 800);
        }).catch(function (err) {
            showToast(err.message, 'error');
            btnSave.disabled = false;
        });
    });

})();
