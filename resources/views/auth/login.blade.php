<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HR-DWMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex">

    {{-- Sisi Kiri: Panel Branding --}}
    <section
        class="hidden lg:flex w-1/2 bg-[#0f172a] bg-cover bg-center flex-col items-center justify-center p-12 relative overflow-hidden min-h-screen"
        style="background-image: url('{{ asset('images/login-bg.jpg') }}');"
    >
        <div class="absolute inset-0 bg-[#0f172a]/70"></div>
        <div class="absolute right-0 top-0 h-full w-16 bg-white rounded-l-[60px] z-10"></div>

        <div class="relative z-20 flex flex-col items-center text-center">
            @php
                $useStorageLogo = !empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo);
            @endphp
            @if($useStorageLogo)
                <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}"
                     class="w-80 drop-shadow-xl object-contain">
            @else
                <img src="{{ asset('images/seveninc_logo.png') }}" alt="{{ $appName }}"
                     class="w-80 drop-shadow-xl">
            @endif
        </div>
    </section>

    {{-- Sisi Kanan: Form Login --}}
    <main class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8 min-h-screen">
        <div class="w-full max-w-lg">

            {{-- Header Form --}}
            <div class="mb-8 text-center">
                @php
                    $useStorageLogo = !empty($appLogo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo);
                @endphp
                @if($useStorageLogo)
                    <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}"
                         class="w-24 mx-auto lg:hidden mb-4 object-contain">
                @else
                    <img src="{{ asset('images/seveninc_logo.png') }}" alt="{{ $appName }}"
                         class="w-24 mx-auto lg:hidden mb-4">
                @endif
                <div class="flex justify-center mb-3">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-slate-800">Log In</h1>
                <p class="text-slate-500 text-sm mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-11.25a.75.75 0 011.5 0v4.5a.75.75 0 01-1.5 0v-4.5zm.75 7.5a.75.75 0 100-1.5.75.75 0 000 1.5z"
                              clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-600">{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input
                        type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="masukan email anda" autocomplete="email"
                        class="w-full px-4 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-lg outline-none transition focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-400 bg-red-50 @enderror"
                    >
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input
                            type="password" id="password" name="password"
                            placeholder="••••••••" autocomplete="current-password"
                            class="w-full px-4 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-lg outline-none transition focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 pr-11"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                            <svg id="icon-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="icon-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center mb-6">
                    <input
                        type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                    >
                    <label for="remember" class="ml-2 text-sm text-slate-600">Ingat Saya</label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-[#0f172a] hover:bg-[#1e293b] active:bg-[#0f172a] text-white text-sm font-semibold rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#0f172a] focus:ring-offset-2">
                    Masuk
                </button>
            </form>
        </div>
    </main>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOn = document.getElementById('icon-eye');
            const eyeOff = document.getElementById('icon-eye-off');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            eyeOn.classList.toggle('hidden', isHidden);
            eyeOff.classList.toggle('hidden', !isHidden);
        }
    </script>

</body>
</html>