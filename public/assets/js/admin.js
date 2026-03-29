/**
 * SkinLab – Admin JavaScript
 * Sidebar toggle + gestión de usuarios (CRUD via API).
 */

(function () {
    'use strict';

    // ── Sidebar mobile toggle ──
    var sidebar = document.getElementById('admin-sidebar');
    var overlay = document.getElementById('admin-overlay');
    var menuToggle = document.getElementById('admin-menu-toggle');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            var isOpen = sidebar.classList.contains('sidebar-open');
            if (isOpen) {
                closeSidebar();
            } else {
                sidebar.classList.add('sidebar-open');
                overlay.classList.add('visible');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('visible');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    }

    // ── Crear usuario ──
    var btnAdd = document.getElementById('btn-add-user');
    var userForm = document.getElementById('user-form');
    var btnCreate = document.getElementById('btn-create-user');
    var btnCancel = document.getElementById('btn-cancel-user');
    var formMsg = document.getElementById('user-form-msg');

    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            userForm.classList.remove('hidden');
            document.getElementById('new-username').focus();
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            userForm.classList.add('hidden');
            formMsg.classList.add('hidden');
        });
    }

    if (btnCreate) {
        btnCreate.addEventListener('click', function () {
            var username = document.getElementById('new-username').value.trim();
            var password = document.getElementById('new-password').value;
            var role = document.getElementById('new-role').value;

            if (!username || !password) {
                showMsg(formMsg, 'Campo obligatorio.', 'error');
                return;
            }
            if (password.length < 6) {
                showMsg(formMsg, 'Mínimo 6 caracteres.', 'error');
                return;
            }

            btnCreate.disabled = true;
            api('/api/users/create', {
                method: 'POST',
                body: JSON.stringify({ username: username, password: password, role: role })
            }).then(function (data) {
                showMsg(formMsg, data.message, 'success');
                setTimeout(function () { location.reload(); }, 1200);
            }).catch(function (err) {
                showMsg(formMsg, err.message, 'error');
                btnCreate.disabled = false;
            });
        });
    }

    // ── Cambiar contraseña ──
    document.querySelectorAll('.btn-change-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId = btn.dataset.userId;
            var input = btn.closest('.pw-row').querySelector('.pw-input');
            var password = input.value.trim();

            if (password.length < 6) {
                input.style.borderColor = 'var(--sl-danger)';
                setTimeout(function () { input.style.borderColor = ''; }, 2000);
                return;
            }

            btn.disabled = true;
            var icon = btn.querySelector('i');
            icon.className = 'bi bi-arrow-repeat';

            api('/api/users/password', {
                method: 'POST',
                body: JSON.stringify({ user_id: parseInt(userId), password: password })
            }).then(function () {
                icon.className = 'bi bi-check-lg';
                input.value = '';
                setTimeout(function () {
                    icon.className = 'bi bi-key';
                    btn.disabled = false;
                }, 2000);
            }).catch(function (err) {
                icon.className = 'bi bi-key';
                btn.disabled = false;
                alert(err.message);
            });
        });
    });

    // ── Toggle activo ──
    document.querySelectorAll('.btn-toggle-user').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId = btn.dataset.userId;
            btn.disabled = true;

            api('/api/users/toggle', {
                method: 'POST',
                body: JSON.stringify({ user_id: parseInt(userId) })
            }).then(function () {
                location.reload();
            }).catch(function (err) {
                alert(err.message);
                btn.disabled = false;
            });
        });
    });

    // ── Eliminar usuario ──
    var deleteOverlay = document.getElementById('delete-overlay');
    var deleteMessage = document.getElementById('delete-message');
    var btnConfirmDelete = document.getElementById('btn-confirm-delete');
    var btnCancelDelete = document.getElementById('btn-cancel-delete');
    var pendingDeleteId = null;

    document.querySelectorAll('.btn-delete-user').forEach(function (btn) {
        btn.addEventListener('click', function () {
            pendingDeleteId = btn.dataset.userId;
            var username = btn.dataset.username;
            deleteMessage.innerHTML = '¿Eliminar al usuario <strong>' + escapeHtml(username) + '</strong>?';
            deleteOverlay.classList.remove('hidden');
        });
    });

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener('click', function () {
            deleteOverlay.classList.add('hidden');
            pendingDeleteId = null;
        });
    }

    if (deleteOverlay) {
        deleteOverlay.addEventListener('click', function (e) {
            if (e.target === deleteOverlay) {
                deleteOverlay.classList.add('hidden');
                pendingDeleteId = null;
            }
        });
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function () {
            if (!pendingDeleteId) return;
            btnConfirmDelete.disabled = true;

            api('/api/users/delete', {
                method: 'POST',
                body: JSON.stringify({ user_id: parseInt(pendingDeleteId) })
            }).then(function () {
                location.reload();
            }).catch(function (err) {
                alert(err.message);
                btnConfirmDelete.disabled = false;
                deleteOverlay.classList.add('hidden');
            });
        });
    }

    // ── Correo de recuperación ──
    var btnSaveEmail = document.getElementById('btn-save-email');
    var emailInput = document.getElementById('recovery-email');
    var emailMsg = document.getElementById('email-msg');

    if (btnSaveEmail) {
        btnSaveEmail.addEventListener('click', function () {
            var email = emailInput.value.trim();
            btnSaveEmail.disabled = true;

            api('/api/settings/email', {
                method: 'POST',
                body: JSON.stringify({ email: email })
            }).then(function (data) {
                showMsg(emailMsg, data.message, 'success');
                btnSaveEmail.disabled = false;
            }).catch(function (err) {
                showMsg(emailMsg, err.message, 'error');
                btnSaveEmail.disabled = false;
            });
        });
    }

    // ── Escape key ──
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            if (deleteOverlay && !deleteOverlay.classList.contains('hidden')) {
                deleteOverlay.classList.add('hidden');
                pendingDeleteId = null;
            }
        }
    });

    // ── Helpers ──
    function showMsg(el, text, type) {
        el.textContent = text;
        el.className = 'alert alert-' + type;
        el.classList.remove('hidden');
    }

})();
