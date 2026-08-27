{{--
Komponen Toast Pop-up Interaktif
- Success: modal di tengah, motivatif, animasi confetti
- Error: toast pojok kanan bawah, minimalis
Dirender sekali di layouts/app.blade.php
--}}

@if(session('task_completed'))

    {{-- ── SUCCESS MODAL (Tengah Layar) ───────────────────── --}}
    <div id="toast-success-overlay" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm
                                    opacity-0 transition-opacity duration-300">

        <div id="toast-success-card" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden
                                        scale-90 opacity-0 transition-all duration-300 ease-out">

            {{-- Dekorasi gelombang hijau di atas --}}
            <div
                class="relative h-44 bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center overflow-hidden">

                {{-- Blur blobs dekoratif --}}
                <div class="absolute -top-6 -left-6 w-32 h-32 bg-green-300/50 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-4 -right-4 w-28 h-28 bg-emerald-300/50 rounded-full blur-2xl"></div>
                <div class="absolute top-4 right-8 w-16 h-8 bg-white/20 rounded-full blur-md rotate-12"></div>
                <div class="absolute bottom-8 left-8 w-20 h-6 bg-white/15 rounded-full blur-md -rotate-6"></div>
                <div class="absolute top-10 left-1/2 -translate-x-1/2 w-32 h-5 bg-white/10 rounded-full blur-md rotate-3">
                </div>

                {{-- Lingkaran ikon centang --}}
                <div class="relative z-10 w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center
                                                ring-4 ring-white/40">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-400 to-emerald-500
                                                    flex items-center justify-center" id="check-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" class="check-path" />
                        </svg>
                    </div>
                </div>

                {{-- Tombol tutup --}}
                <button onclick="dismissSuccessToast()" class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white/20 hover:bg-white/30
                                                   flex items-center justify-center text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Konten --}}
            <div class="px-7 pt-6 pb-7 text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    🎉 Luar Biasa!
                </h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-1">
                    {{ session('task_completed') }}
                </p>
                <p class="text-green-600 text-sm font-medium mt-3 mb-6">
                    Terus semangat dan selesaikan tugas berikutnya! 💪
                </p>

                {{-- Progress bar auto-dismiss --}}
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden mb-5">
                    <div id="success-progress" class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full"
                        style="width: 100%; transition: width 4s linear;"></div>
                </div>

                <button onclick="dismissSuccessToast()" class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-500
                                                   hover:from-green-600 hover:to-emerald-600
                                                   text-white font-semibold text-sm rounded-xl
                                                   shadow-lg shadow-green-200 transition-all duration-200
                                                   active:scale-95">
                    Lanjut Kerjakan Tugas →
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes drawCheck {
            from {
                stroke-dashoffset: 30;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        .check-path {
            stroke-dasharray: 30;
            stroke-dashoffset: 30;
            animation: drawCheck 0.4s ease-out 0.35s forwards;
        }

        @keyframes floatConfetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(-120px) rotate(720deg);
                opacity: 0;
            }
        }

        .confetti-dot {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: floatConfetti 1s ease-out forwards;
        }
    </style>

    <script>
        (function () {
            const overlay = document.getElementById('toast-success-overlay');
            const card = document.getElementById('toast-success-card');
            const progress = document.getElementById('success-progress');
            let timer;

            function show() {
                // Fade-in overlay
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');

                // Scale-in card
                setTimeout(function () {
                    card.classList.remove('scale-90', 'opacity-0');
                    card.classList.add('scale-100', 'opacity-100');

                    // Mulai progress bar mundur
                    setTimeout(function () {
                        progress.style.width = '0%';
                    }, 50);

                    // Spawn confetti dots
                    spawnConfetti();
                }, 50);

                // Auto dismiss
                timer = setTimeout(dismissSuccessToast, 4500);
            }

            window.dismissSuccessToast = function () {
                clearTimeout(timer);
                card.classList.remove('scale-100', 'opacity-100');
                card.classList.add('scale-90', 'opacity-0');
                setTimeout(function () {
                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0');
                    setTimeout(function () { overlay.remove(); }, 300);
                }, 200);
            };

            // Tutup saat klik overlay (bukan card)
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) dismissSuccessToast();
            });

            // Spawn confetti kecil dari tengah icon
            function spawnConfetti() {
                const icon = document.getElementById('check-icon');
                const rect = icon.getBoundingClientRect();
                const colors = ['#4ade80', '#34d399', '#86efac', '#fbbf24', '#f472b6', '#60a5fa'];
                for (let i = 0; i < 10; i++) {
                    const dot = document.createElement('div');
                    dot.className = 'confetti-dot';
                    dot.style.cssText = [
                        'left:' + (rect.left + rect.width / 2 + (Math.random() - 0.5) * 60) + 'px',
                        'top:' + (rect.top + rect.height / 2 + (Math.random() - 0.5) * 30) + 'px',
                        'background:' + colors[Math.floor(Math.random() * colors.length)],
                        'animation-delay:' + (Math.random() * 0.3) + 's',
                        'animation-duration:' + (0.7 + Math.random() * 0.5) + 's',
                        'position:fixed',
                        'z-index:99999',
                        'pointer-events:none',
                    ].join(';');
                    document.body.appendChild(dot);
                    dot.addEventListener('animationend', function () { dot.remove(); });
                }
            }
            requestAnimationFrame(show);
        })();
    </script>

@elseif(session('success'))

    {{-- ── SUCCESS TOAST KECIL (Responsive: Bawah Tengah di Mobile, Bawah Kanan di Desktop) ────────── --}}
    <div id="toast-success-small" role="alert" class="fixed bottom-10 sm:bottom-6 inset-x-3 sm:inset-x-auto sm:right-6 z-[9999]
                                    flex items-start gap-3 p-3.5 sm:px-5 sm:py-4 rounded-xl sm:rounded-2xl shadow-2xl
                                    bg-white/95 backdrop-blur-sm border border-green-200 max-w-none sm:max-w-sm
                                    translate-y-4 opacity-0 transition-all duration-300 ease-out">

        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div class="flex-1 min-w-0 pr-1">
            <p class="text-xs sm:text-sm font-bold text-green-800">Berhasil</p>
            <p class="text-xs sm:text-sm text-gray-600 mt-0.5 leading-snug break-words">{{ session('success') }}</p>
        </div>

        <div class="absolute bottom-0 left-0 h-1 rounded-b-xl sm:rounded-b-2xl bg-green-500" id="success-small-progress"
            style="width: 100%; transition: width 4s linear;"></div>

        <button onclick="dismissSuccessSmall()"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition p-1 -mr-1 -mt-0.5 rounded-lg hover:bg-gray-100"
            aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        (function () {
            const toast = document.getElementById('toast-success-small');
            const progress = document.getElementById('success-small-progress');
            let timer;

            function show() {
                requestAnimationFrame(function () {
                    toast.classList.remove('translate-y-4', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                    requestAnimationFrame(function () { progress.style.width = '0%'; });
                });
                timer = setTimeout(dismissSuccessSmall, 4000);
            }

            window.dismissSuccessSmall = function () {
                clearTimeout(timer);
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(function () { toast.remove(); }, 300);
            };

            toast.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                progress.style.transition = 'none';
            });
            toast.addEventListener('mouseleave', function () {
                progress.style.transition = 'width 2s linear';
                progress.style.width = '0%';
                timer = setTimeout(dismissSuccessSmall, 2000);
            });

            // Jalankan langsung — elemen sudah ada di DOM karena script ada setelah elemen
            requestAnimationFrame(show);
        })();
    </script>

@elseif(session('error'))

    {{-- ── ERROR TOAST (Responsive: Bawah Tengah di Mobile, Bawah Kanan di Desktop) ────────────────── --}}
    <div id="toast-error" role="alert" class="fixed bottom-10 sm:bottom-6 inset-x-3 sm:inset-x-auto sm:right-6 z-[9999]
                                    flex items-start gap-3 p-3.5 sm:px-5 sm:py-4 rounded-xl sm:rounded-2xl shadow-2xl
                                    bg-white/95 backdrop-blur-sm border border-red-200 max-w-none sm:max-w-sm
                                    translate-y-4 opacity-0 transition-all duration-300 ease-out">

        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <div class="flex-1 min-w-0 pr-1">
            <p class="text-xs sm:text-sm font-bold text-red-800">Terjadi Kesalahan</p>
            <p class="text-xs sm:text-sm text-gray-600 mt-0.5 leading-snug break-words">{{ session('error') }}</p>
        </div>

        <div class="absolute bottom-0 left-0 h-1 rounded-b-xl sm:rounded-b-2xl bg-red-500" id="error-progress"
            style="width: 100%; transition: width 4s linear;"></div>

        <button onclick="dismissErrorToast()"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition p-1 -mr-1 -mt-0.5 rounded-lg hover:bg-gray-100"
            aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        (function () {
            const toast = document.getElementById('toast-error');
            const progress = document.getElementById('error-progress');
            let timer;

            function show() {
                requestAnimationFrame(function () {
                    toast.classList.remove('translate-y-4', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                    requestAnimationFrame(function () {
                        progress.style.width = '0%';
                    });
                });
                timer = setTimeout(dismissErrorToast, 4000);
            }

            window.dismissErrorToast = function () {
                clearTimeout(timer);
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(function () { toast.remove(); }, 300);
            };

            toast.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                progress.style.transition = 'none';
            });
            toast.addEventListener('mouseleave', function () {
                progress.style.transition = 'width 2s linear';
                progress.style.width = '0%';
                timer = setTimeout(dismissErrorToast, 2000);
            });

            // Jalankan langsung — elemen sudah ada di DOM karena script ada setelah elemen
            requestAnimationFrame(show);
        })();
    </script>

@elseif($errors->any())

    {{-- ── VALIDATION ERROR TOAST (Responsive: Bawah Tengah di Mobile, Bawah Kanan di Desktop) ──────── --}}
    <div id="toast-val-error" role="alert" class="fixed bottom-10 sm:bottom-6 inset-x-3 sm:inset-x-auto sm:right-6 z-[9999]
                                    flex items-start gap-3 p-3.5 sm:px-5 sm:py-4 rounded-xl sm:rounded-2xl shadow-2xl
                                    bg-white/95 backdrop-blur-sm border border-red-200 max-w-none sm:max-w-sm
                                    translate-y-4 opacity-0 transition-all duration-300 ease-out">

        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="flex-1 min-w-0 pr-1">
            <p class="text-xs sm:text-sm font-bold text-red-800">Periksa Inputan Anda</p>
            <p class="text-xs sm:text-sm text-gray-600 mt-0.5 leading-snug break-words">{{ $errors->first() }}</p>
        </div>

        <div class="absolute bottom-0 left-0 h-1 rounded-b-xl sm:rounded-b-2xl bg-red-500" id="val-error-progress"
            style="width: 100%; transition: width 4.5s linear;"></div>

        <button onclick="dismissValErrorToast()"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition p-1 -mr-1 -mt-0.5 rounded-lg hover:bg-gray-100"
            aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        (function () {
            const toast = document.getElementById('toast-val-error');
            const progress = document.getElementById('val-error-progress');
            let timer;

            function show() {
                requestAnimationFrame(function () {
                    toast.classList.remove('translate-y-4', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                    requestAnimationFrame(function () {
                        progress.style.width = '0%';
                    });
                });
                timer = setTimeout(dismissValErrorToast, 4500);
            }

            window.dismissValErrorToast = function () {
                clearTimeout(timer);
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(function () { toast.remove(); }, 300);
            };

            toast.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                progress.style.transition = 'none';
            });
            toast.addEventListener('mouseleave', function () {
                progress.style.transition = 'width 2s linear';
                progress.style.width = '0%';
                timer = setTimeout(dismissValErrorToast, 2000);
            });

            requestAnimationFrame(show);
        })();
    </script>

@endif