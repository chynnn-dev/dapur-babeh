<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Dapur Babeh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green: #0f5f46;
            --green-d: #0b4a37;
            --green-l: #14785a;
            --orange: #f58220;
            --cream: #f4f1ea;
            --cream-l: #faf8f3;
        }

        body { background: var(--cream); color: #1f2937; }

        .app { display: flex; min-height: 100vh; }

        .sidebar {
            width: 16rem; flex-shrink: 0;
            position: sticky; top: 0; height: 100vh;
            display: flex; flex-direction: column;
            background: var(--green); color: #f3f4f6;
        }
        .sidebar-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--green-d); }
        .sidebar-title { font-size: 1.25rem; font-weight: 700; color: #fff; }
        .sidebar-sub { font-size: .75rem; color: #b9d8cb; }
        .sidebar-nav { flex: 1; padding: 1rem; display: flex; flex-direction: column; gap: .25rem; overflow-y: auto; }
        .sidebar-link {
            display: flex; align-items: center; justify-content: space-between;
            padding: .5rem 1rem; border-radius: .5rem;
            font-size: .875rem; font-weight: 500; color: #d6ebe2; text-decoration: none;
        }
        .sidebar-link:hover { background: var(--green-d); color: #fff; }
        .sidebar-link.active { background: var(--orange); color: #fff; }
        .sidebar-badge {
            background: var(--orange); color: #fff; border-radius: 99px;
            padding: 0 .5rem; font-size: .7rem; font-weight: 700; line-height: 1.4rem;
        }
        .sidebar-link.active .sidebar-badge { background: #fff; color: var(--orange); }
        .sidebar-foot { padding: 1rem; border-top: 1px solid var(--green-d); font-size: .875rem; }
        .sidebar-foot .role { font-size: .75rem; color: #b9d8cb; margin-bottom: .75rem; }
        .sidebar-foot button {
            width: 100%; text-align: left; padding: .5rem .75rem;
            border-radius: .5rem; background: var(--green-d); color: #f3f4f6;
        }
        .sidebar-foot button:hover { background: var(--green-l); }

        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar {
            display: flex; align-items: center; gap: .75rem;
            padding: .75rem 2rem; background: var(--cream-l); box-shadow: 0 1px 2px rgba(0,0,0,.06);
        }
        .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--green); }
        .menu-btn { display: none; border: 1px solid var(--green); color: var(--green); border-radius: .5rem; padding: .25rem .75rem; }
        .content { flex: 1; padding: 2rem; }

        .stat-grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); }
        .toolbar {
            display: flex; flex-wrap: wrap; gap: .75rem;
            align-items: center; justify-content: space-between; margin-bottom: 1rem;
        }

        @media (max-width: 767px) {
            .sidebar {
                position: fixed; left: 0; top: 0; z-index: 30;
                transform: translateX(-100%); transition: transform .2s;
            }
            .sidebar.open { transform: none; }
            .menu-btn { display: inline-block; }
            .topbar, .content { padding-left: 1rem; padding-right: 1rem; }
        }
    </style>
</head>
<body>
<div class="app">

    <aside id="sidebar" class="sidebar">
        <div class="sidebar-head">
            <div class="sidebar-title">Dapur Babeh</div>
            <div class="sidebar-sub">Panel Admin</div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Kategori</a>
            <a href="{{ route('admin.menus.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">Menu</a>
            <a href="{{ route('admin.tables.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">Meja</a>
            <a href="{{ route('admin.front.edit') }}"
               class="sidebar-link {{ request()->routeIs('admin.front.*') ? 'active' : '' }}">Halaman Depan</a>
            <a href="{{ route('admin.posts.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">Postingan</a>
            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Kelola User</a>
            {{-- <a href="{{ route('admin.reports.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Laporan</a> --}}
            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Pengaturan</a>

            @php $newReviews = \App\Models\Review::where('is_approved', false)->count(); @endphp
            <a href="{{ route('admin.reviews.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <span>Ulasan</span>
                @if ($newReviews)
                    <span class="sidebar-badge">{{ $newReviews }}</span>
                @endif
            </a>
        </nav>

        <div class="sidebar-foot">
            <div style="font-weight:500">{{ auth()->user()->name }}</div>
            <div class="role">{{ \App\Models\User::ROLES[auth()->user()->role] }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" id="sidebar-toggle" class="menu-btn">Menu</button>
            <h1>@yield('title', 'Admin')</h1>
        </header>

        <main class="content">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>
</body>
</html>