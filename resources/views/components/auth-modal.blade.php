<!-- resources/views/components/auth-modal.blade.php -->
<div id="auth-guard-modal" class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 p-4 sm:p-6">
    <!-- Animated Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-md transition-opacity duration-500" id="auth-guard-backdrop"></div>
    
    <!-- Modal Container: Compact, focused login form -->
    <div class="relative w-full max-w-md bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white/40 dark:border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden transform scale-95 transition-all duration-300 max-h-[90vh] flex flex-col" id="auth-guard-panel">
        
        <!-- Left panel hidden for compact mode -->

        <!-- Right Panel: The Forms -->
        <div class="w-full p-6 sm:p-8 relative flex flex-col overflow-y-auto flex-1">
            
            <button id="auth-guard-close" class="absolute top-6 right-6 text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 z-20">
                <i class="fas fa-times text-lg"></i>
            </button>
            
            <!-- Loading Spinner State (Overlay) -->
            <div id="auth-loading-state" class="absolute inset-0 bg-white/80 dark:bg-slate-900/90 backdrop-blur-sm z-50 flex flex-col items-center justify-center hidden md:rounded-r-3xl rounded-3xl">
                <div class="w-12 h-12 border-4 border-slate-200 dark:border-slate-700 border-t-emerald-500 rounded-full animate-spin mb-4 shadow-lg shadow-emerald-500/20"></div>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 animate-pulse" id="auth-loading-text">Memproses...</p>
            </div>

            <!-- Error/Success Message State -->
            <div id="guard-alert-message" class="hidden text-xs rounded-xl p-3.5 text-center mb-6 border transition-all font-medium shadow-sm"></div>

            <div class="w-full max-w-sm mx-auto">
                <!-- LOGIN STATE CONTAINER -->
                <div id="auth-state-login" class="auth-state-container relative transition-all duration-300">
                    <div class="mb-8 text-center md:text-left">
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 flex items-center gap-2" id="auth-modal-title">Selamat Datang! <i class="fas fa-hand-sparkles text-emerald-500 text-xl animate-pulse"></i></h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium" id="auth-login-desc">Buka analisis lengkap & simpan strategimu—<span class="font-bold text-emerald-600 dark:text-emerald-400">100% Gratis.</span></p>
                    </div>

                    <form id="guard-login-form" autocomplete="off">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <input type="email" id="guard-login-email" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm" placeholder="bintanggenteng@email.com">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 flex justify-between uppercase tracking-wide">
                                    <span>Password</span>
                                    <button type="button" onclick="window.switchAuthState('forgot')" class="text-emerald-600 dark:text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-400 font-bold capitalize tracking-normal">Lupa Password?</button>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <input type="password" id="guard-login-password" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-10 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm" placeholder="••••••••">
                                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" onclick="const input = this.previousElementSibling; const icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); } else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 relative overflow-hidden group/btn mt-2 active:scale-[0.98]">
                                <span class="relative z-10 flex items-center justify-center gap-2">Masuk Sekarang <i class="fas fa-arrow-right text-sm group-hover/btn:translate-x-1 transition-transform"></i></span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400">
                        Belum punya akun? <button type="button" onclick="window.switchAuthState('register')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold ml-1 hover:underline underline-offset-4">Daftar Gratis</button>
                    </div>
                </div>

                <!-- REGISTER STATE CONTAINER -->
                <div id="auth-state-register" class="auth-state-container hidden relative transition-all duration-300">
                    <div class="mb-6 text-center md:text-left">
                        <button type="button" onclick="window.switchAuthState('login')" class="md:hidden w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-emerald-500 flex items-center justify-center mb-4 transition-colors mx-auto"><i class="fas fa-arrow-left"></i></button>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 flex items-center gap-2 mt-4 md:mt-0">Buat Akun Baru <i class="fas fa-rocket text-blue-500 text-xl animate-bounce"></i></h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-medium">Satu langkah kecil untuk menyelamatkan margin bisnis Anda dari keboncosan.</p>
                    </div>

                    <form id="guard-register-form" autocomplete="off">
                        <div class="space-y-3.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" id="guard-register-name" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm shadow-sm" placeholder="Bintang">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">Username</label>
                                <div class="relative">
                                    <input type="text" id="guard-register-username" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm shadow-sm" placeholder="bintanggenteng220">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i id="guard-username-icon" class="fas fa-circle-notch fa-spin text-slate-400 hidden"></i>
                                    </div>
                                </div>
                                <p id="guard-username-feedback" class="text-[9px] mt-1 font-semibold hidden"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">Email</label>
                                <input type="email" id="guard-register-email" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm shadow-sm" placeholder="anda@email.com">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">Password</label>
                                <div class="relative group">
                                    <input type="password" id="guard-register-password" required minlength="8" class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-3.5 pr-10 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm shadow-sm" placeholder="Min. 8 karakter" oninput="checkPasswordStrength(this.value)">
                                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" onclick="const input = this.previousElementSibling; const icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); } else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                {{-- Real-time strength checker --}}
                                <div id="password-strength-checker" class="hidden mt-2 grid grid-cols-2 gap-x-3 gap-y-1">
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold" id="check-uppercase">
                                        <i class="fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs"></i>
                                        <span class="text-slate-400">Huruf Besar (A-Z)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold" id="check-lowercase">
                                        <i class="fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs"></i>
                                        <span class="text-slate-400">Huruf Kecil (a-z)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold" id="check-number">
                                        <i class="fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs"></i>
                                        <span class="text-slate-400">Angka (0-9)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold" id="check-symbol">
                                        <i class="fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs"></i>
                                        <span class="text-slate-400">Simbol (!@#$...)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold col-span-2" id="check-length">
                                        <i class="fas fa-times-circle text-slate-300 dark:text-slate-600 text-xs"></i>
                                        <span class="text-slate-400">Minimal 8 karakter</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider">Konfirmasi Password</label>
                                <div class="relative group">
                                    <input type="password" id="guard-register-password-confirm" required minlength="6" class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-3.5 pr-10 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-sm shadow-sm" placeholder="Ulangi password">
                                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" onclick="const input = this.previousElementSibling; const icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); } else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full mt-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 relative overflow-hidden group/btn text-sm active:scale-[0.98]">
                                <span class="relative z-10 flex items-center justify-center gap-2">Daftar Sekarang <i class="fas fa-paper-plane text-xs group-hover/btn:-translate-y-0.5 group-hover/btn:translate-x-0.5 transition-transform"></i></span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-6 text-center text-xs text-slate-600 dark:text-slate-400">
                        Sudah punya akun? <button type="button" onclick="window.switchAuthState('login')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold ml-1 hover:underline underline-offset-4">Masuk</button>
                    </div>
                </div>

                <!-- FORGOT PASSWORD STATE CONTAINER -->
                <div id="auth-state-forgot" class="auth-state-container hidden relative transition-all duration-300">
                    <div class="mb-8 text-center md:text-left">
                        <button type="button" onclick="window.switchAuthState('login')" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-emerald-500 flex items-center justify-center mx-auto md:mx-0 mb-4 transition-colors"><i class="fas fa-arrow-left"></i></button>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Lupa Password? </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Masukkan email Anda untuk menerima link reset password.</p>
                    </div>

                    <form id="guard-forgot-form" autocomplete="off">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Alamat Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <input type="email" id="guard-forgot-email" required class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm" placeholder="anda@email.com">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 relative active:scale-[0.98]">
                                <span class="relative z-10 text-sm">Kirim Link Reset</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RESET PASSWORD FINAL STATE CONTAINER -->
                <div id="auth-state-reset_final" class="auth-state-container hidden relative transition-all duration-300">
                    <div class="mb-8 text-center md:text-left">
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Buat Password Baru 🔑</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Silakan buat password baru yang aman untuk akun Anda.</p>
                    </div>

                    <form id="guard-reset-final-form" autocomplete="off">
                        <input type="hidden" id="guard-reset-token">
                        <input type="hidden" id="guard-reset-email">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Password Baru</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <input type="password" id="guard-reset-password" required minlength="6" class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-10 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm" placeholder="Minimal 6 karakter">
                                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" onclick="const input = this.previousElementSibling; const icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); } else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Konfirmasi Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-check-circle text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <input type="password" id="guard-reset-password-confirm" required minlength="6" class="w-full bg-white/50 dark:bg-slate-950/50 backdrop-blur-sm border border-slate-300 dark:border-slate-700 rounded-xl pl-10 pr-10 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm" placeholder="Ulangi password baru">
                                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" onclick="const input = this.previousElementSibling; const icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); } else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 relative active:scale-[0.98]">
                                <span class="relative z-10 text-sm">Simpan Password Baru</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- GLOBAL SOCIAL LOGIN (Visible on Login & Register) -->
                <div id="auth-global-social" class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700/50">
                    <div class="relative flex items-center justify-center mb-6">
                        <div class="absolute inset-x-0 h-px bg-slate-200 dark:bg-slate-700/50"></div>
                        <span class="relative px-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">Atau masuk dengan</span>
                    </div>
                    
                    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 font-bold py-3 px-4 rounded-xl transition-all border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow active:scale-[0.98]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span id="social-btn-text">Google</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
