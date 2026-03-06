/**
 * @file guards.js
 * @description UI Protection Layer. Mencegah aksi fitur tertentu jika belum login.
 */
"use strict";

document.addEventListener("DOMContentLoaded", () => {

    const authModal = document.getElementById("auth-guard-modal");
    const authPanel = document.getElementById("auth-guard-panel");
    const closeBtn = document.getElementById("auth-guard-close");
    const loginForm = document.getElementById("guard-login-form");
    const forgotLink = document.getElementById("guard-forgot-link");
    const loadingState = document.getElementById("auth-loading-state");
    const errorState = document.getElementById("guard-error-message");

    // STATE CONTAINERS
    const stateContainers = {
        'login': document.getElementById("auth-state-login"),
        'register': document.getElementById("auth-state-register"),
        'forgot': document.getElementById("auth-state-forgot"),
        'reset_final': document.getElementById("auth-state-reset_final")
    };

    const socialGlobal = document.getElementById("auth-global-social");
    const alertMessage = document.getElementById("guard-alert-message");
    const loadingText = document.getElementById("auth-loading-text");

    let pendingAction = null;

    // ==========================================
    // UTILITIES
    // ==========================================

    window.switchAuthState = (state) => {
        // Reset state classes
        Object.values(stateContainers).forEach(el => {
            if (el) el.classList.add('hidden');
        });

        // Show active state
        if (stateContainers[state]) {
            stateContainers[state].classList.remove('hidden');
        }

        // Hide social on forgot password and reset final
        if ((state === 'forgot' || state === 'reset_final') && socialGlobal) {
            socialGlobal.classList.add('hidden');
        } else if (socialGlobal) {
            socialGlobal.classList.remove('hidden');
        }

        // Clear alerts
        showAlert('', 'hidden');
    }

    const showAlert = (message, type = 'error') => {
        if (type === 'hidden' || !message) {
            alertMessage.classList.add("hidden");
            return;
        }

        // Tailwind classes depending on type
        alertMessage.className = `text-xs rounded p-3 text-center mb-4 border transition-all ${type === 'success' ? 'text-emerald-400 bg-emerald-900/20 border-emerald-800/30' : 'text-rose-400 bg-rose-900/20 border-rose-800/30'
            }`;

        alertMessage.innerText = message;
        alertMessage.classList.remove("hidden");
    }

    window.openLoginModal = (action = null, initialState = 'login') => {
        pendingAction = typeof action === 'string' ? null : action; // prevent string passing as function

        // Reset forms
        loginForm?.reset();
        document.getElementById("guard-register-form")?.reset();
        document.getElementById("guard-forgot-form")?.reset();

        showAlert('', 'hidden');
        window.switchAuthState(initialState);

        // UI Animation
        authModal.classList.remove("pointer-events-none", "opacity-0");
        authModal.classList.add("opacity-100");

        // Panel slide up
        setTimeout(() => {
            authPanel.classList.remove("scale-95");
            authPanel.classList.add("scale-100");

            // Auto focus based on state
            const focusMap = {
                'login': 'guard-login-email',
                'register': 'guard-register-name',
                'forgot': 'guard-forgot-email'
            };
            document.getElementById(focusMap[initialState])?.focus();

        }, 50);
    };

    window.closeLoginModal = () => {
        pendingAction = null;

        authPanel.classList.remove("scale-100");
        authPanel.classList.add("scale-95");

        setTimeout(() => {
            authModal.classList.remove("opacity-100");
            authModal.classList.add("pointer-events-none", "opacity-0");
        }, 300);
    };

    const showLoading = (show, text = 'Processing...') => {
        if (show) {
            loadingText.innerText = text;
            loadingState.classList.remove("hidden");
        } else {
            loadingState.classList.add("hidden");
        }
    }

    // ==========================================
    // EVENT DELEGATION
    // ==========================================

    document.addEventListener("click", (e) => {
        // Cek apakah elemen atau parentnya butuh autentikasi
        const guardedEl = e.target.closest("[data-auth-required]");
        if (!guardedEl) return;

        // Check both server-side session (window.AUTH.loggedIn) AND localStorage SPA token
        const hasToken = !!(localStorage.getItem('auth_token') || (window.AUTH && window.AUTH.loggedIn));
        if (!hasToken) {
            e.preventDefault();
            e.stopPropagation(); // Mencegah function asli terpanggil

            // Simpan aksi tertunda via Event Delegation
            const clickEvent = new MouseEvent("click", {
                bubbles: true,
                cancelable: true,
                view: window
            });

            // Callback execution for memory feature
            const resumeAction = () => {
                guardedEl.removeAttribute("data-auth-required");
                guardedEl.dispatchEvent(clickEvent);
                guardedEl.setAttribute("data-auth-required", "true");
            };

            window.openLoginModal(resumeAction, 'login');
        }
    });

    // ==========================================
    // URL PARAM HANDLING (SPA ROUTING)
    // ==========================================
    const urlParams = new URLSearchParams(window.location.search);
    const authAction = urlParams.get('auth_action');
    const incomingToken = urlParams.get('auth_token');

    // Handle Google OAuth or external redirects that provide a token
    if (incomingToken) {
        localStorage.setItem('auth_token', incomingToken);
        // Clean the URL to hide the token
        window.history.replaceState({}, document.title, window.location.pathname);
        // Reload to apply the token
        window.location.reload();
    }

    if (authAction === 'login') {
        window.openLoginModal(null, 'login');
    } else if (authAction === 'reset_final') {
        const token = urlParams.get('token');
        const email = urlParams.get('email');
        if (token && email) {
            setTimeout(() => {
                document.getElementById('guard-reset-token').value = token;
                document.getElementById('guard-reset-email').value = email;
                window.openLoginModal(null, 'reset_final');
            }, 100);
        }
    }

    // ==========================================
    // MODAL EVENT LISTENERS
    // ==========================================

    if (closeBtn) closeBtn.addEventListener("click", window.closeLoginModal);

    // Close on backdrop click
    document.getElementById("auth-guard-backdrop")?.addEventListener("click", window.closeLoginModal);

    // 1. LOGIN HANDLER
    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const email = document.getElementById("guard-login-email").value;
            const password = document.getElementById("guard-login-password").value;

            showLoading(true, 'Signing In...');
            showAlert('', 'hidden');

            try {
                // Endpoint default SPA kita dari file api.php
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.AUTH?.csrf || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Login failed. Please check your credentials.');
                }

                // Sukses Login!
                localStorage.setItem('auth_token', data.token);

                window.closeLoginModal();
                showLoading(false);

                // Eksekusi aksi yang tertunda atau reload
                if (pendingAction && typeof pendingAction === 'function') {
                    setTimeout(() => { pendingAction(); }, 400);
                } else {
                    window.location.reload();
                }

            } catch (err) {
                showLoading(false);
                showAlert(err.message, 'error');
            }
        });
    }

    // 2. REGISTER HANDLER
    const registerForm = document.getElementById("guard-register-form");
    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const payload = {
                name: document.getElementById("guard-register-name").value,
                username: document.getElementById("guard-register-username").value,
                email: document.getElementById("guard-register-email").value,
                password: document.getElementById("guard-register-password").value,
                password_confirmation: document.getElementById("guard-register-password-confirm").value
            };

            showLoading(true, 'Creating Account...');
            showAlert('', 'hidden');

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.AUTH?.csrf || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    // Parse proper Laravel validation errors if present
                    let errorMsg = data.message || 'Registration failed.';
                    if (data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        errorMsg = data.errors[firstErrorKey][0];
                    }
                    throw new Error(errorMsg);
                }

                // Auto Login
                localStorage.setItem('auth_token', data.token);
                // Mark as new user so the guided tour auto-starts after reload
                localStorage.setItem('tour_trigger_new_user', 'true');

                window.closeLoginModal();
                showLoading(false);

                if (pendingAction && typeof pendingAction === 'function') {
                    setTimeout(() => { pendingAction(); }, 400);
                } else {
                    window.location.reload();
                }

            } catch (err) {
                showLoading(false);
                showAlert(err.message, 'error');
            }
        });
    }

    // 3. FORGOT PASSWORD HANDLER
    const forgotForm = document.getElementById("guard-forgot-form");
    if (forgotForm) {
        forgotForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const email = document.getElementById("guard-forgot-email").value;

            showLoading(true, 'Sending Reset Link...');
            showAlert('', 'hidden');

            try {
                const response = await fetch('/api/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.AUTH?.csrf || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to process request.');
                }

                showLoading(false);
                showAlert(data.message || 'Reset password link sent! Check your email.', 'success');
                forgotForm.reset();

            } catch (err) {
                showLoading(false);
                showAlert(err.message, 'error');
            }
        });
    }

    // 4. RESET PASSWORD FINAL HANDLER
    const resetFinalForm = document.getElementById("guard-reset-final-form");
    if (resetFinalForm) {
        resetFinalForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const payload = {
                token: document.getElementById("guard-reset-token").value,
                email: document.getElementById("guard-reset-email").value,
                password: document.getElementById("guard-reset-password").value,
                password_confirmation: document.getElementById("guard-reset-password-confirm").value
            };

            showLoading(true, 'Saving New Password...');
            showAlert('', 'hidden');

            try {
                const response = await fetch('/api/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.AUTH?.csrf || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    // Parse Laravel validation errors
                    let errorMsg = data.message || 'Failed to reset password.';
                    if (data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        errorMsg = data.errors[firstErrorKey][0];
                    }
                    throw new Error(errorMsg);
                }

                showLoading(false);
                showAlert('Password updated successfully! You can now log in.', 'success');
                resetFinalForm.reset();

                // Clear URL params
                window.history.replaceState({}, document.title, window.location.pathname);

                // Switch back to login
                setTimeout(() => { window.switchAuthState('login'); }, 2500);

            } catch (err) {
                showLoading(false);
                showAlert(err.message, 'error');
            }
        });
    }

    // 5. USERNAME AVAILABILITY CHECKER
    const usernameInput = document.getElementById("guard-register-username");
    const usernameIcon = document.getElementById("guard-username-icon");
    const usernameFeedback = document.getElementById("guard-username-feedback");
    let usernameTimeout = null;

    if (usernameInput) {
        usernameInput.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            usernameIcon.className = "fas fa-circle-notch fa-spin text-slate-400";
            usernameIcon.classList.remove('hidden');
            usernameFeedback.classList.add('hidden');

            clearTimeout(usernameTimeout);

            // 1. Length Check
            if (val.length < 3) {
                usernameIcon.classList.add('hidden');
                return;
            }

            // 2. Pre-flight Regex Check (match Laravel alpha_dash)
            const alphaDashRegex = /^[a-zA-Z0-9_-]+$/;
            if (!alphaDashRegex.test(val)) {
                usernameIcon.classList.add('fa-times-circle', 'text-rose-500');
                usernameIcon.classList.remove('fa-circle-notch', 'fa-spin', 'text-emerald-500', 'text-slate-400', 'fa-check-circle');
                usernameFeedback.textContent = "Hanya huruf, angka, min (-), dan underscore (_).";
                usernameFeedback.className = "text-[10px] mt-1 font-semibold text-rose-500 block animate-fade-in";
                return;
            }

            // 3. API Check
            usernameTimeout = setTimeout(async () => {
                try {
                    const res = await fetch('/api/check-username', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.AUTH?.csrf || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        body: JSON.stringify({ username: val })
                    });

                    const data = await res.json();

                    if (res.ok) {
                        usernameIcon.classList.remove('fa-circle-notch', 'fa-spin');
                        if (data.available) {
                            usernameIcon.classList.add('fa-check-circle', 'text-emerald-500');
                            usernameIcon.classList.remove('text-rose-500', 'text-slate-400', 'fa-times-circle');
                            usernameFeedback.textContent = "Username tersedia!";
                            usernameFeedback.className = "text-[10px] mt-1 font-semibold text-emerald-500 block animate-fade-in";
                        } else {
                            usernameIcon.classList.add('fa-times-circle', 'text-rose-500');
                            usernameIcon.classList.remove('text-emerald-500', 'text-slate-400', 'fa-check-circle');
                            usernameFeedback.textContent = "Username sudah dipakai.";
                            usernameFeedback.className = "text-[10px] mt-1 font-semibold text-rose-500 block animate-fade-in";
                        }
                    } else {
                        // Handle Laravel 422 Errors Gracefully
                        let errorMsg = data.message || "Masukkan username yang valid.";
                        if (data.errors && data.errors.username) {
                            errorMsg = data.errors.username[0];
                        }
                        throw new Error(errorMsg);
                    }
                } catch (err) {
                    usernameIcon.classList.add('fa-times-circle', 'text-rose-500');
                    usernameIcon.classList.remove('fa-circle-notch', 'fa-spin', 'text-emerald-500', 'text-slate-400', 'fa-check-circle');
                    usernameFeedback.textContent = err.message || "Gagal mengecek username.";
                    usernameFeedback.className = "text-[10px] mt-1 font-semibold text-rose-500 block animate-fade-in";
                }
            }, 600); // 600ms debounce
        });
    }

});

// ==========================================
// PASSWORD STRENGTH CHECKER (Global)
// ==========================================
window.checkPasswordStrength = (value) => {
    const checker = document.getElementById('password-strength-checker');
    if (!checker) return;

    // Show the checker on first keystroke
    if (value.length > 0) {
        checker.classList.remove('hidden');
    } else {
        checker.classList.add('hidden');
        return;
    }

    const rules = [
        { id: 'check-uppercase', test: /[A-Z]/.test(value) },
        { id: 'check-lowercase', test: /[a-z]/.test(value) },
        { id: 'check-number', test: /[0-9]/.test(value) },
        { id: 'check-symbol', test: /[^A-Za-z0-9]/.test(value) },
        { id: 'check-length', test: value.length >= 8 },
    ];

    rules.forEach(({ id, test }) => {
        const el = document.getElementById(id);
        if (!el) return;
        const icon = el.querySelector('i');
        const text = el.querySelector('span');
        if (test) {
            icon.className = 'fas fa-check-circle text-emerald-500 text-xs';
            text.className = 'text-emerald-600 dark:text-emerald-400';
        } else {
            icon.className = 'fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs';
            text.className = 'text-slate-400';
        }
    });
};
