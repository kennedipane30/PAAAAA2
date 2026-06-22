<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Spekta Academy - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <!-- KODE UNTUK MENGGANTI LOGO TAB BROWSER (FAVICON) -->
    <link rel="icon" href="<?php echo e(asset('logo.png')); ?>?v=1" type="image/png">
    <link rel="shortcut icon" href="<?php echo e(asset('logo.png')); ?>?v=1" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            /* Dark Sidebar Colors */
            --sidebar-bg: #0f1219;
            --sidebar-secondary: #161b24;
            --sidebar-hover: #222b36;
            --sidebar-active: #1a2a33;
            --sidebar-border: #2a3340;
            --sidebar-text: #e8edf5;
            --sidebar-text-secondary: #9aa8b9;
            --sidebar-text-muted: #6b7a8a;

            /* Teal Active Color */
            --teal-primary: #14b8a6;
            --teal-dark: #0d9488;
            --teal-glow: rgba(20, 184, 166, 0.25);

            /* Light Content Colors */
            --bg-content: #f3f4f6;
            --bg-white: #ffffff;
            --bg-card: #ffffff;
            --border-light: #e5e7eb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;

            --red: #e53935;
            --red-dark: #c5352c;

            --sidebar-width: 260px;
            --sidebar-collapsed: 80px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-content);
            color: var(--text-primary);
            overflow: hidden;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg-content); }
        ::-webkit-scrollbar-thumb { background: var(--teal-primary); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--teal-dark); }

        .app-shell {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background: var(--bg-content);
        }

        /* ── SIDEBAR DARK ── */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 40;
            overflow: hidden;
            position: relative;
        }

        .sidebar-header {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-dark) 100%);
            color: var(--sidebar-text);
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 900;
            flex-shrink: 0;
            box-shadow: 0 4px 15px var(--teal-glow);
        }

        .sidebar-brand {
            flex: 1;
            min-width: 0;
        }

        .sidebar-brand h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: var(--sidebar-text);
        }

        .sidebar-brand span {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: var(--sidebar-text-muted);
            letter-spacing: 0.1em;
            margin-top: 2px;
        }

        /* Sidebar Navigation - FONT LEBIH BESAR DAN BOLD */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 14px;
        }

        .nav-section {
            margin-bottom: 28px;
        }

        .nav-heading {
            padding: 0 12px;
            margin-bottom: 10px;
            font-size: 12px; /* Diperbesar dari 10px */
            font-weight: 800; /* Dipertebal */
            letter-spacing: 0.15em;
            color: var(--sidebar-text-muted);
            text-transform: uppercase;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px; /* Padding lebih besar */
            border-radius: 10px;
            color: var(--sidebar-text-secondary);
            font-size: 15px; /* Diperbesar dari 13px */
            font-weight: 700; /* Dipertebal */
            transition: all 0.2s ease;
            margin-bottom: 3px;
            cursor: pointer;
            position: relative;
        }

        .sidebar-item i {
            width: 22px; /* Lebih lebar */
            text-align: center;
            font-size: 17px; /* Diperbesar dari 15px */
            color: var(--sidebar-text-muted);
            transition: color 0.2s ease;
        }

        .sidebar-item:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text);
        }

        .sidebar-item:hover i {
            color: var(--teal-primary);
        }

        .sidebar-item.is-active {
            background: var(--sidebar-active);
            color: var(--teal-primary);
            border: 1px solid rgba(20, 184, 166, 0.2);
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.05);
        }

        .sidebar-item.is-active i {
            color: var(--teal-primary);
        }

        .sidebar-item.is-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 28px; /* Lebih tinggi */
            background: var(--teal-primary);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 15px var(--teal-glow);
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--sidebar-border);
            text-align: center;
            font-size: 11px; /* Diperbesar */
            font-weight: 700; /* Dipertebal */
            color: var(--sidebar-text-muted);
        }

        /* ── SIDEBAR COLLAPSED ── */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed);
            min-width: var(--sidebar-collapsed);
        }

        body.sidebar-collapsed .sidebar-brand span,
        body.sidebar-collapsed .sidebar-item span,
        body.sidebar-collapsed .nav-heading,
        body.sidebar-collapsed .sidebar-footer {
            display: none;
        }

        body.sidebar-collapsed .sidebar-item {
            justify-content: center;
            padding: 14px;
            width: 52px;
            margin: 0 auto 4px;
        }

        body.sidebar-collapsed .sidebar-item i {
            margin: 0;
            font-size: 20px;
        }

        body.sidebar-collapsed .sidebar-item.is-active::before {
            display: none;
        }

        body.sidebar-collapsed .sidebar-item.is-active {
            border: 1px solid rgba(20, 184, 166, 0.3);
        }

        body.sidebar-collapsed .sidebar-header {
            justify-content: center;
            padding: 16px 12px;
        }

        body.sidebar-collapsed .sidebar-logo {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        body.sidebar-collapsed .sidebar-brand h2 {
            display: none;
        }

        /* ── MAIN WRAPPER ── */
        .main-wrapper {
            flex: 1;
            min-width: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--bg-content);
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 72px;
            flex-shrink: 0;
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            gap: 20px;
            z-index: 25;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .sidebar-toggle {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: var(--bg-white);
            color: var(--text-secondary);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 16px;
        }

        .sidebar-toggle:hover {
            background: var(--bg-content);
            border-color: var(--teal-primary);
            color: var(--teal-primary);
        }

        .topbar-title {
            display: flex;
            flex-direction: column;
        }

        .topbar-title h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .topbar-title p {
            margin: 0;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }

        .search-wrapper {
            position: relative;
            width: 200px;
        }

        .search-wrapper input {
            width: 100%;
            padding: 8px 16px 8px 40px;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            background: var(--bg-content);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .search-wrapper input:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
            background: var(--bg-white);
        }

        .search-wrapper input::placeholder {
            color: var(--text-muted);
        }

        .search-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .profile-button {
            border: 1px solid var(--border-light);
            border-radius: 12px;
            background: var(--bg-white);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .profile-button:hover {
            border-color: var(--teal-primary);
            background: var(--bg-content);
        }

        .profile-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--teal-primary) 0%, var(--teal-dark) 100%);
            color: var(--bg-white);
            font-size: 13px;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 2px 10px var(--teal-glow);
        }

        .profile-info {
            text-align: left;
            min-width: 0;
        }

        .profile-info strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .profile-info span {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .profile-button .fa-chevron-down {
            font-size: 11px;
            color: var(--text-muted);
            margin-left: 4px;
        }

        .profile-wrap {
            position: relative;
        }

        .dropdown-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 260px;
            background: var(--bg-white);
            border: 1px solid var(--border-light);
            border-radius: 14px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            padding: 10px;
            display: none;
            z-index: 60;
        }

        .dropdown-panel.show {
            display: block;
            animation: dropdownFade 0.2s ease;
        }

        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-title {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 8px;
        }

        .dropdown-title strong {
            display: block;
            font-size: 13px;
            color: var(--text-primary);
        }

        .dropdown-title span {
            display: block;
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .logout-button {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: var(--red);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-button:hover {
            background: #fef2f2;
            color: var(--red-dark);
        }

        .logout-button i {
            font-size: 15px;
        }

        .content-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 24px 28px;
            background: var(--bg-content);
        }

        .content-container {
            width: 100%;
            margin: 0 auto;
        }

        .mobile-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 35;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { overflow: auto; }

            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                transform: translateX(-100%);
                width: 280px;
                min-width: 280px;
                height: 100vh;
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            }

            body.mobile-sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.mobile-sidebar-open .mobile-backdrop {
                display: block;
            }

            .topbar {
                height: 64px;
                padding: 0 16px;
            }

            .search-wrapper {
                display: none;
            }

            .profile-info,
            .profile-button .fa-chevron-down {
                display: none;
            }

            .profile-button {
                padding: 6px;
            }

            .topbar-title h1 {
                font-size: 15px;
            }

            .topbar-title p {
                font-size: 10px;
            }

            .content-scroll {
                padding: 16px;
            }

            /* Sidebar font lebih besar di mobile */
            .sidebar-item {
                font-size: 16px;
                padding: 14px 18px;
            }

            .sidebar-item i {
                font-size: 18px;
            }

            .nav-heading {
                font-size: 13px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .search-wrapper {
                width: 150px;
            }
        }
    </style>
</head>

<body>
    <div class="app-shell">

        <!-- ── SIDEBAR ── -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">S</div>
                <div class="sidebar-brand">
                    <h2>Spekta</h2>
                    <span>Academy</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php if(Auth::check() && Auth::user()->role_id == 1): ?>
                    <div class="nav-section">
                        <div class="nav-heading">Overview</div>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.dashboard') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-heading">Manajemen Akademik</div>
                        <a href="<?php echo e(route('admin.siswa.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.siswa.index') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-user-group"></i> <span>Siswa</span>
                        </a>
                        <a href="<?php echo e(route('admin.manajemen-pengajar.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.manajemen-pengajar.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-chalkboard-user"></i> <span>Pengajar</span>
                        </a>
                        <a href="<?php echo e(route('admin.jadwal.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.jadwal.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-calendar-days"></i> <span>Jadwal Kelas</span>
                        </a>
                        <a href="<?php echo e(route('admin.scores.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.scores.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-clipboard-list"></i> <span>Rekap Nilai</span>
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-heading">Pembelajaran</div>
                        <a href="<?php echo e(route('admin.assignments.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.assignments.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-book-open"></i> <span>Materi</span>
                        </a>
                        <a href="<?php echo e(route('admin.tryout.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.tryout.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-stopwatch-20"></i> <span>Master Tryout</span>
                        </a>
                        <a href="<?php echo e(route('admin.classes.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.classes.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-layer-group"></i> <span>Program Kelas</span>
                        </a>
                        <a href="<?php echo e(route('admin.tutor.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.tutor.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-headset"></i> <span>Dedicated Tutor</span>
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-heading">Promosi & Informasi</div>
                        <a href="<?php echo e(route('admin.promo.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.promo.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-tags"></i> <span>Promo</span>
                        </a>
                        <a href="<?php echo e(route('admin.banners.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.banners.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-image"></i> <span>Banner</span>
                        </a>
                        <a href="<?php echo e(route('admin.announcement.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.announcement.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-bullhorn"></i> <span>Pengumuman</span>
                        </a>
                    </div>
                <?php elseif(Auth::check() && Auth::user()->role_id == 2): ?>
                    <div class="nav-section">
                        <div class="nav-heading">Overview</div>
                        <a href="<?php echo e(route('pengajar.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.dashboard') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-heading">Pembelajaran</div>
                        <a href="<?php echo e(route('pengajar.absensi.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.absensi.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-clipboard-check"></i> <span>Absensi Siswa</span>
                        </a>
                        <a href="<?php echo e(route('pengajar.materi.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.materi.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-book-open"></i> <span>Upload Materi</span>
                        </a>
                        <a href="<?php echo e(route('pengajar.latihan.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.latihan.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-file-pen"></i> <span>Latihan Soal</span>
                        </a>
                        <a href="<?php echo e(route('pengajar.tryout.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.tryout.*') ? 'is-active' : ''); ?>">
                            <i class="fa-solid fa-pen-to-square"></i> <span>Setor Soal TO</span>
                        </a>
                    </div>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">Spekta Academy © <?php echo e(date('Y')); ?></div>
        </aside>

        <div class="mobile-backdrop" onclick="closeMobileSidebar()"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="topbar-title">
                        <h1><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                        <p><?php echo $__env->yieldContent('subtitle', 'Sistem Manajemen Terpadu Spekta Academy'); ?></p>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" placeholder="Search Here..." readonly>
                    </div>

                    <div class="profile-wrap">
                        <button type="button" class="profile-button" onclick="toggleDropdown('profileDropdown')">
                            <div class="profile-avatar">
                                <?php echo e(Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'S'); ?>

                            </div>
                            <div class="profile-info">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span><?php echo e(Auth::check() && Auth::user()->role ? ucfirst(Auth::user()->role->name) : 'User'); ?></span>
                            </div>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>

                        <div class="dropdown-panel" id="profileDropdown">
                            <div class="dropdown-title">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span><?php echo e(Auth::check() ? Auth::user()->email : '-'); ?></span>
                            </div>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="logout-button">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content-scroll">
                <div class="content-container"><?php echo $__env->yieldContent('content'); ?></div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                document.body.classList.toggle('mobile-sidebar-open');
                return;
            }
            document.body.classList.toggle('sidebar-collapsed');
        }

        function closeMobileSidebar() {
            document.body.classList.remove('mobile-sidebar-open');
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            document.querySelectorAll('.dropdown-panel').forEach(item => {
                if (item.id !== id) item.classList.remove('show');
            });
            if (dropdown) dropdown.classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-panel') && !e.target.closest('.profile-button')) {
                document.querySelectorAll('.dropdown-panel').forEach(item => item.classList.remove('show'));
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.body.classList.remove('mobile-sidebar-open');
            }
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/layouts/spekta.blade.php ENDPATH**/ ?>