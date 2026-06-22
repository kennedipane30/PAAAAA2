<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Spekta Academy</title>

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;500&amp;display=swap" rel="stylesheet"/>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "outline": "#8e706c",
                      "on-primary-fixed-variant": "#910a0c",
                      "outline-variant": "#e2beba",
                      "inverse-primary": "#ffb4aa",
                      "tertiary": "#535252",
                      "surface-container-highest": "#e1e3e4",
                      "tertiary-fixed": "#e5e2e1",
                      "surface-dim": "#d9dadb",
                      "primary-fixed": "#ffdad5",
                      "secondary-fixed-dim": "#68d7da",
                      "inverse-surface": "#2e3132",
                      "error-container": "#ffdad6",
                      "on-secondary": "#ffffff",
                      "on-surface": "#191c1d",
                      "surface-container": "#edeeef",
                      "surface-container-lowest": "#ffffff",
                      "on-primary": "#ffffff",
                      "surface-variant": "#e1e3e4",
                      "surface-tint": "#b32821",
                      "surface-bright": "#f8f9fa",
                      "on-tertiary-container": "#eeebeb",
                      "surface-container-high": "#e7e8e9",
                      "on-surface-variant": "#5a413d",
                      "on-primary-container": "#ffe7e4",
                      "tertiary-fixed-dim": "#c8c6c5",
                      "error": "#ba1a1a",
                      "primary": "#a21b17",
                      "on-tertiary-fixed-variant": "#474746",
                      "on-secondary-fixed-variant": "#004f51",
                      "on-secondary-container": "#007072",
                      "secondary-fixed": "#86f4f7",
                      "primary-fixed-dim": "#ffb4aa",
                      "on-tertiary": "#ffffff",
                      "on-error-container": "#93000a",
                      "inverse-on-surface": "#f0f1f2",
                      "surface": "#f8f9fa",
                      "secondary": "#00696c",
                      "primary-container": "#c5352c",
                      "on-secondary-fixed": "#002021",
                      "secondary-container": "#86f4f7",
                      "on-primary-fixed": "#410001",
                      "tertiary-container": "#6b6a6a",
                      "background": "#f8f9fa",
                      "on-tertiary-fixed": "#1c1b1b",
                      "surface-container-low": "#f3f4f5",
                      "on-error": "#ffffff",
                      "on-background": "#191c1d"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "spacing": {
                      "md": "16px",
                      "gutter": "24px",
                      "xs": "4px",
                      "xl": "40px",
                      "lg": "24px",
                      "margin-mobile": "16px",
                      "margin-desktop": "64px",
                      "sm": "8px",
                      "base": "4px"
              },
              "fontFamily": {
                      "label-sm": ["Hanken Grotesk"],
                      "body-lg": ["Inter"],
                      "label-md": ["Hanken Grotesk"],
                      "body-md": ["Inter"],
                      "headline-md": ["Hanken Grotesk"],
                      "display-lg": ["Hanken Grotesk"],
                      "headline-lg-mobile": ["Hanken Grotesk"],
                      "headline-lg": ["Hanken Grotesk"]
              },
              "fontSize": {
                      "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.04em", "fontWeight": "700"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "700"}],
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
              }
            },
          },
        }
    </script>
    <style>
        .glass-island {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 20px 60px 0 rgba(0, 0, 0, 0.06), 0 8px 24px rgba(0, 0, 0, 0.02);
        }
        .mesh-gradient {
            background-color: #f8f9fa;
            background-image:
                radial-gradient(at 0% 0%, rgba(197, 53, 44, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(0, 105, 108, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(197, 53, 44, 0.08) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(0, 105, 108, 0.08) 0px, transparent 50%);
            animation: meshFlow 20s ease-in-out infinite alternate;
        }
        @keyframes meshFlow {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(46, 168, 171, 0.2);
            border-color: #2ea8ab;
            transition: all 0.3s ease;
        }
        .portal-pill {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Logo Styles */
        .logo-container {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #c5352c 0%, #a21b17 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(197, 53, 44, 0.3);
            transition: transform 0.3s ease;
        }
        .logo-container:hover {
            transform: scale(1.05) rotate(-2deg);
        }
        .logo-text {
            font-family: 'Hanken Grotesk', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.05em;
        }

        .brand-subtitle {
            color: #6b7a8a;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 20px 0;
        }
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 !important;
            display: inline-block;
            direction: ltr;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
        }

        /* Custom Button */
        .btn-login {
            background: linear-gradient(135deg, #c5352c 0%, #a21b17 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(197, 53, 44, 0.3);
        }
        .btn-login:active {
            transform: scale(0.98);
        }
        .btn-login::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .btn-login:hover::after {
            opacity: 1;
        }

        /* Animation */
        .animate-in {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="font-body-md text-on-surface mesh-gradient min-h-screen flex flex-col justify-between">

    <main class="flex-grow flex items-center justify-center px-margin-mobile md:px-margin-desktop py-12">
        <!-- Glass Island Login Card -->
        <div class="glass-island w-full max-w-[420px] p-lg md:p-xl rounded-2xl animate-in">

            <!-- Logo & Brand -->
            <div class="text-center mb-lg">
                <div class="logo-container mx-auto">
                    <span class="logo-text">S</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-sm" style="color: #a21b17;">
                    Spekta Academy
                </h1>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-xs" style="color: #6b7280;">
                    Teacher &amp; Student Portal
                </p>
            </div>

            <!-- ALERT ERROR -->
            @if(session('error'))
                <div class="flex items-center gap-2 p-md bg-red-50 text-red-700 rounded-lg text-xs font-semibold border border-red-200 mb-lg animate-in delay-100">
                    <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ url('/login') }}" id="loginForm" class="space-y-lg">
                @csrf

                <!-- Input Email -->
                <div class="space-y-sm animate-in delay-100">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase ml-xs" style="color: #6b7280;">
                        Institutional Email
                    </label>
                    <div class="input-glow relative flex items-center bg-surface-container-lowest border border-outline-variant rounded-xl transition-all">
                        <span class="material-symbols-outlined ml-md text-on-surface-variant" style="color: #9ca3af;">alternate_email</span>
                        <input class="w-full bg-transparent border-none focus:ring-0 py-3 px-md text-body-md font-body-md text-on-surface placeholder:text-outline rounded-xl"
                               placeholder="name@spekta.edu"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus/>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="space-y-sm animate-in delay-200">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase ml-xs" style="color: #6b7280;">
                        Password
                    </label>
                    <div class="input-glow relative flex items-center bg-surface-container-lowest border border-outline-variant rounded-xl transition-all">
                        <span class="material-symbols-outlined ml-md text-on-surface-variant" style="color: #9ca3af;">lock</span>
                        <input id="passwordInput"
                               class="w-full bg-transparent border-none focus:ring-0 py-3 px-md text-body-md font-body-md text-on-surface placeholder:text-outline rounded-xl"
                               placeholder="••••••••"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"/>
                        <button class="mr-md text-on-surface-variant hover:text-secondary transition-colors" type="button" onclick="togglePasswordVisibility()">
                            <span class="material-symbols-outlined" id="visibilityIcon" style="color: #9ca3af;">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Tombol Sign In -->
                <button type="submit" class="btn-login w-full py-3 text-on-primary font-headline-md text-headline-md rounded-xl shadow-lg transition-all active:scale-[0.98] mt-lg animate-in delay-300"
                        style="color: #ffffff; font-size: 16px; font-weight: 700; letter-spacing: 0.02em;">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="login-divider animate-in delay-300">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Secure Access</span>
            </div>

            <!-- Footer Card -->
            <div class="text-center animate-in delay-300">
                <p class="text-xs text-gray-400 font-medium">
                    <span class="material-symbols-outlined text-[14px] align-middle text-gray-300">verified</span>
                    Protected by Spekta Security
                </p>
            </div>
        </div>
    </main>

    <!-- Footer Minimalis -->
    <footer class="flex flex-col items-center py-6 px-margin-mobile md:px-margin-desktop w-full bg-transparent">
        <p class="font-label-sm text-label-sm text-on-tertiary-fixed-variant opacity-60" style="color: #9ca3af;">
            © {{ date('Y') }} Spekta Academy. All rights reserved.
        </p>
    </footer>

    <!-- Background Decorative Bubbles -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] rounded-full bg-secondary opacity-10 blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] rounded-full bg-primary opacity-10 blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- JS Logic -->
    <script>
        // Logika Intip Password
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const visibilityIcon = document.getElementById('visibilityIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                visibilityIcon.innerText = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                visibilityIcon.innerText = 'visibility';
            }
        }

        // Efek Atmosfer Mouse-Move Interaktif
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;
            document.body.style.backgroundImage = `
                radial-gradient(at ${x}% ${y}%, rgba(197, 53, 44, 0.12) 0px, transparent 50%),
                radial-gradient(at ${100-x}% ${100-y}%, rgba(0, 105, 108, 0.12) 0px, transparent 50%),
                radial-gradient(at 0% 0%, rgba(197, 53, 44, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(0, 105, 108, 0.08) 0px, transparent 50%)
            `;
        });
    </script>
</body>
</html>
