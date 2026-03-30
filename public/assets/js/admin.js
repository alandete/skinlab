/**
 * SkinLab – Admin JavaScript
 * Sidebar toggle + gestión de usuarios.
 */

(function () {
    'use strict';

    var L = getLang();

    // ── Sidebar mobile toggle ──
    var sidebar = document.getElementById('admin-sidebar');
    var overlay = document.getElementById('admin-overlay');
    var menuToggle = document.getElementById('admin-menu-toggle');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            if (sidebar.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                sidebar.classList.add('sidebar-open');
                overlay.classList.add('visible');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });
    }
    if (overlay) overlay.addEventListener('click', closeSidebar);

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('sidebar-open');
        if (overlay) overlay.classList.remove('visible');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    }

    // ── Si no estamos en la página de usuarios, salir ──
    if (!document.getElementById('users-grid')) return;

    // ── Search & Filter ──
    var searchInput = document.getElementById('user-search');
    var filterBtns = document.querySelectorAll('.filter-btn');
    var usersGrid = document.getElementById('users-grid');
    var noUsersMsg = document.getElementById('no-users-msg');
    var activeFilter = 'all';

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            activeFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    function applyFilters() {
        var query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        var cards = usersGrid ? usersGrid.querySelectorAll('.user-card') : [];
        var visibleCount = 0;

        cards.forEach(function (card) {
            var username = (card.dataset.username || '').toLowerCase();
            var role = card.dataset.role;
            var isActive = card.dataset.active === '1';

            var matchSearch = !query || username.indexOf(query) !== -1;
            var matchFilter = activeFilter === 'all'
                || activeFilter === role
                || (activeFilter === 'active' && isActive)
                || (activeFilter === 'inactive' && !isActive);

            if (matchSearch && matchFilter) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noUsersMsg) {
            noUsersMsg.classList.toggle('hidden', visibleCount > 0);
        }
    }

    // ── Confirm modal ──
    var confirmModal = document.getElementById('confirm-modal');
    var confirmBody = document.getElementById('confirm-body');
    var btnConfirmAction = document.getElementById('btn-confirm-action');
    var btnCancelAction = document.getElementById('btn-cancel-action');
    var pendingConfirmCallback = null;

    function showConfirm(html, btnText, btnClass, callback) {
        if (!confirmModal || !confirmBody) return;
        confirmBody.innerHTML = html;
        btnConfirmAction.textContent = btnText;
        btnConfirmAction.className = 'btn ' + (btnClass || 'btn-danger');
        pendingConfirmCallback = callback;
        confirmModal.classList.remove('hidden');
    }

    function hideConfirm() {
        if (confirmModal) confirmModal.classList.add('hidden');
        pendingConfirmCallback = null;
    }

    if (btnConfirmAction) {
        btnConfirmAction.addEventListener('click', function () {
            if (pendingConfirmCallback) {
                btnConfirmAction.disabled = true;
                pendingConfirmCallback(function () {
                    btnConfirmAction.disabled = false;
                    hideConfirm();
                });
            }
        });
    }
    if (btnCancelAction) btnCancelAction.addEventListener('click', hideConfirm);
    if (confirmModal) confirmModal.addEventListener('click', function (e) {
        if (e.target === confirmModal) hideConfirm();
    });

    // ── Create user ──
    var btnAdd = document.getElementById('btn-add-user');
    var userForm = document.getElementById('user-form');
    var btnCreate = document.getElementById('btn-create-user');
    var btnCancel = document.getElementById('btn-cancel-user');
    var credentialsPanel = document.getElementById('credentials-panel');

    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            userForm.classList.remove('hidden');
            credentialsPanel.classList.add('hidden');
            document.getElementById('new-username').value = '';
            document.getElementById('new-email').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('new-username').focus();
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            userForm.classList.add('hidden');
        });
    }

    if (btnCreate) {
        btnCreate.addEventListener('click', function () {
            var username = document.getElementById('new-username').value.trim();
            var email = document.getElementById('new-email').value.trim();
            var password = document.getElementById('new-password').value;
            var role = document.getElementById('new-role').value;

            if (!username || !password || !email) {
                showToast(L.required || 'Campo obligatorio', 'error');
                return;
            }
            if (password.length < 6) {
                showToast(L.password_min || 'Mínimo 6 caracteres', 'error');
                return;
            }

            btnCreate.disabled = true;
            api('/api/users/create', {
                method: 'POST',
                body: JSON.stringify({ username: username, password: password, role: role, email: email })
            }).then(function (data) {
                showToast(data.message, 'success');
                btnCreate.disabled = false;
                document.getElementById('cred-username').textContent = username;
                document.getElementById('cred-password').textContent = password;
                credentialsPanel.classList.remove('hidden');
                document.getElementById('new-username').value = '';
                document.getElementById('new-email').value = '';
                document.getElementById('new-password').value = '';
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnCreate.disabled = false;
            });
        });
    }

    // Copy password
    var btnCopyPw = document.getElementById('btn-copy-password');
    if (btnCopyPw) {
        btnCopyPw.addEventListener('click', function () {
            var pw = document.getElementById('cred-password').textContent;
            copyToClipboard(pw).then(function () {
                var icon = btnCopyPw.querySelector('i');
                icon.className = 'bi bi-check-lg';
                setTimeout(function () { icon.className = 'bi bi-clipboard'; }, 2000);
            });
        });
    }

    // Close credentials
    var btnCloseCred = document.getElementById('btn-close-credentials');
    if (btnCloseCred) {
        btnCloseCred.addEventListener('click', function () {
            credentialsPanel.classList.add('hidden');
            userForm.classList.add('hidden');
            location.reload();
        });
    }

    // ── Edit user (modal) ──
    var editModal = document.getElementById('edit-modal');

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit-user');
        if (!btn) return;

        document.getElementById('edit-user-id').value = btn.dataset.userId;
        document.getElementById('edit-username').value = btn.dataset.username || '';
        document.getElementById('edit-email').value = btn.dataset.email || '';
        document.getElementById('edit-role').value = btn.dataset.role;
        editModal.classList.remove('hidden');
        document.getElementById('edit-username').focus();
    });

    var btnSaveEdit = document.getElementById('btn-save-edit');
    var btnCancelEdit = document.getElementById('btn-cancel-edit');

    if (btnSaveEdit) {
        btnSaveEdit.addEventListener('click', function () {
            var userId = parseInt(document.getElementById('edit-user-id').value);
            var username = document.getElementById('edit-username').value.trim();
            var email = document.getElementById('edit-email').value.trim();
            var role = document.getElementById('edit-role').value;

            btnSaveEdit.disabled = true;
            api('/api/users/update', {
                method: 'POST',
                body: JSON.stringify({ user_id: userId, username: username, email: email, role: role })
            }).then(function (data) {
                showToast(data.message, 'success');
                editModal.classList.add('hidden');
                btnSaveEdit.disabled = false;
                location.reload();
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnSaveEdit.disabled = false;
            });
        });
    }
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', function () {
        editModal.classList.add('hidden');
    });
    if (editModal) editModal.addEventListener('click', function (e) {
        if (e.target === editModal) editModal.classList.add('hidden');
    });

    // ── Change password (modal with re-auth) ──
    var pwModal = document.getElementById('password-modal');

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-change-pw');
        if (!btn) return;

        document.getElementById('pw-user-id').value = btn.dataset.userId;
        document.getElementById('pw-username').textContent = btn.dataset.username;
        document.getElementById('pw-current').value = '';
        document.getElementById('pw-new').value = '';
        pwModal.classList.remove('hidden');
        document.getElementById('pw-current').focus();
    });

    var btnSavePw = document.getElementById('btn-save-password');
    var btnCancelPw = document.getElementById('btn-cancel-password');

    if (btnSavePw) {
        btnSavePw.addEventListener('click', function () {
            var userId = parseInt(document.getElementById('pw-user-id').value);
            var currentPw = document.getElementById('pw-current').value;
            var newPw = document.getElementById('pw-new').value;

            if (!currentPw) {
                showToast(L.current_password_hint || 'Ingresa tu contraseña actual', 'error');
                return;
            }
            if (newPw.length < 6) {
                showToast(L.password_min || 'Mínimo 6 caracteres', 'error');
                return;
            }

            btnSavePw.disabled = true;
            api('/api/users/password', {
                method: 'POST',
                body: JSON.stringify({ user_id: userId, password: newPw, current_password: currentPw })
            }).then(function (data) {
                showToast(data.message, 'success');
                pwModal.classList.add('hidden');
                btnSavePw.disabled = false;
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnSavePw.disabled = false;
            });
        });
    }
    if (btnCancelPw) btnCancelPw.addEventListener('click', function () {
        pwModal.classList.add('hidden');
    });
    if (pwModal) pwModal.addEventListener('click', function (e) {
        if (e.target === pwModal) pwModal.classList.add('hidden');
    });

    // ── Toggle user (with confirmation) ──
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-toggle-user');
        if (!btn) return;

        var userId = parseInt(btn.dataset.userId);
        var username = btn.dataset.username;
        var isActive = btn.dataset.active === '1';
        var action = isActive ? (L.deactivate || 'Desactivar') : (L.activate || 'Activar');
        var warning = isActive ? '<br><small>' + (L.confirm_toggle_deactivate || '') + '</small>' : '';
        var html = (L.confirm_toggle || '').replace(':action', action).replace(':name', escapeHtml(username)) + warning;

        showConfirm(html, action, isActive ? 'btn-danger' : 'btn-primary', function (done) {
            api('/api/users/toggle', {
                method: 'POST',
                body: JSON.stringify({ user_id: userId })
            }).then(function (data) {
                showToast(data.message, 'success');
                done();
                var card = document.querySelector('.user-card[data-user-id="' + userId + '"]');
                if (card) {
                    card.dataset.active = data.is_active ? '1' : '0';
                    card.classList.toggle('user-card-inactive', !data.is_active);
                    var toggleBtn = card.querySelector('.btn-toggle-user');
                    if (toggleBtn) {
                        toggleBtn.dataset.active = data.is_active ? '1' : '0';
                        var icon = toggleBtn.querySelector('i');
                        icon.className = data.is_active ? 'bi bi-toggle-off' : 'bi bi-toggle-on';
                        toggleBtn.childNodes[toggleBtn.childNodes.length - 1].textContent =
                            ' ' + (data.is_active ? (L.deactivate || 'Desactivar') : (L.activate || 'Activar'));
                    }
                    var badges = card.querySelector('.user-card-badges');
                    var inactiveBadge = badges.querySelector('.status-inactive');
                    if (!data.is_active && !inactiveBadge) {
                        var span = document.createElement('span');
                        span.className = 'status-badge status-inactive';
                        span.textContent = L.badge_inactive || 'Inactivo';
                        badges.appendChild(span);
                    } else if (data.is_active && inactiveBadge) {
                        inactiveBadge.remove();
                    }
                }
                applyFilters();
            }).catch(function (err) {
                showToast(err.message, 'error');
                done();
            });
        });
    });

    // ── Delete user ──
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-user');
        if (!btn) return;

        var userId = parseInt(btn.dataset.userId);
        var username = btn.dataset.username;

        showConfirm(
            (L.confirm_delete || '').replace(':name', escapeHtml(username)),
            L.delete || 'Eliminar',
            'btn-danger',
            function (done) {
                api('/api/users/delete', {
                    method: 'POST',
                    body: JSON.stringify({ user_id: userId })
                }).then(function (data) {
                    showToast(data.message, 'success');
                    done();
                    var card = document.querySelector('.user-card[data-user-id="' + userId + '"]');
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transition = 'opacity 0.3s';
                        setTimeout(function () { card.remove(); applyFilters(); }, 300);
                    }
                }).catch(function (err) {
                    showToast(err.message, 'error');
                    done();
                });
            }
        );
    });

    // ── Recovery email ──
    var btnSaveEmail = document.getElementById('btn-save-email');
    var emailInput = document.getElementById('recovery-email');

    if (btnSaveEmail) {
        btnSaveEmail.addEventListener('click', function () {
            var email = emailInput.value.trim();
            btnSaveEmail.disabled = true;

            api('/api/settings/email', {
                method: 'POST',
                body: JSON.stringify({ email: email })
            }).then(function (data) {
                showToast(data.message, 'success');
                btnSaveEmail.disabled = false;
            }).catch(function (err) {
                showToast(err.message, 'error');
                btnSaveEmail.disabled = false;
            });
        });
    }

    // ── Escape key ──
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            hideConfirm();
            if (editModal && !editModal.classList.contains('hidden')) editModal.classList.add('hidden');
            if (pwModal && !pwModal.classList.contains('hidden')) pwModal.classList.add('hidden');
        }
    });

})();
