<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur') - Dapur Babeh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5f46;
            --green-d: #0b4a37;
            --green-l: #14785a;
            --orange: #f58220;
            --cream: #f4f1ea;
            --line: #e6dfd0;
            --ink: #1f2937;
            --muted: #6b7280;
            --red: #dc2626;
        }

        * { box-sizing: border-box; }
        html { font-size: 14px; }
        body { margin: 0; background: var(--cream); color: var(--ink); font-family: 'Lato', ui-sans-serif, system-ui, sans-serif; line-height: 1.4; }
        button { font-family: inherit; cursor: pointer; transition: filter .15s, background .15s; }
        button:hover:not(:disabled) { filter: brightness(1.06); }

        /* Bar atas (terang) */
        .topbar { position: sticky; top: 0; z-index: 10; background: #fff; border-bottom: 3px solid var(--orange); box-shadow: 0 2px 10px rgba(15,95,70,.1); }
        .topbar-in { max-width: 96rem; margin: 0 auto; padding: .6rem 1.25rem; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: .75rem; }

        .brand { display: flex; align-items: center; gap: .65rem; text-decoration: none; }
        .logo { width: 2.3rem; height: 2.3rem; border-radius: .7rem; display: grid; place-items: center; background: var(--green); color: var(--orange); font-weight: 900; font-size: .95rem; letter-spacing: .02em; }
        .brand-name { font-weight: 900; font-size: 1.15rem; color: var(--green); line-height: 1.1; }
        .brand-tag { display: inline-block; margin-top: .1rem; padding: .02rem .55rem; border-radius: 99px; background: #fff1de; color: #9a5a00; font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }

        .nav { display: flex; gap: .35rem; background: #f0ebe0; padding: .25rem; border-radius: .8rem; }
        .nav a { padding: .45rem 1.3rem; border-radius: .6rem; color: var(--green); text-decoration: none; font-weight: 800; font-size: .88rem; }
        .nav a:hover { background: rgba(15,95,70,.08); }
        .nav a.active { background: var(--green); color: #fff; box-shadow: 0 2px 6px rgba(15,95,70,.3); }

        .right { display: flex; align-items: center; justify-content: flex-end; gap: .6rem; }
        .clock { font-weight: 900; font-size: 1.05rem; color: var(--green); padding: .3rem .75rem; border-radius: .6rem; background: #e9f3ee; font-variant-numeric: tabular-nums; }
        .user { display: flex; align-items: center; gap: .5rem; font-size: .85rem; font-weight: 700; color: var(--ink); }
        .avatar { width: 2rem; height: 2rem; border-radius: 50%; display: grid; place-items: center; background: var(--orange); color: #fff; font-weight: 800; }
        .logout { background: #fff; color: #b91c1c; border: 1px solid #f3c1c1; padding: .4rem .9rem; border-radius: .5rem; font-size: .8rem; font-weight: 800; }
        .logout:hover:not(:disabled) { background: #fde2e2; filter: none; }

        .container { max-width: 96rem; margin: 0 auto; padding: 1rem 1.25rem 2rem; }

        .flash { margin: 0 0 1rem; padding: .65rem 1rem; border-radius: .6rem; background: #e3f4ea; color: var(--green-d);
                 border-left: 4px solid var(--green); font-size: .88rem; font-weight: 700; }

        /* Halaman Stok Menu */
        .page { padding: 0; }
        .hint { color: var(--muted); margin: 0 0 .75rem; font-size: .9rem; }
        .cat-title { display: flex; align-items: center; gap: .6rem; font-size: .85rem; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; margin: 1.4rem 0 .7rem; color: var(--green); }
        .cat-title::after { content: ""; flex: 1; height: 2px; background: var(--line); border-radius: 2px; }
        .stock-grid { display: grid; gap: .8rem; grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr)); }
        .stock-card { background: #fff; border-radius: .85rem; border-left: 5px solid #22a06b; padding: .85rem 1rem;
                      box-shadow: 0 1px 3px rgba(15,95,70,.08), 0 6px 18px rgba(15,95,70,.05); }
        .stock-card.out { border-left-color: var(--red); }
        .sname { font-size: 1.05rem; font-weight: 700; color: #111827; }
        .sstate { display: inline-block; font-size: .72rem; font-weight: 800; margin: .3rem 0 .7rem; padding: .15rem .65rem; border-radius: 99px; background: #e3f4ea; color: var(--green-d); }
        .stock-card.out .sstate { background: #fde2e2; color: #991b1b; }
        .stock-btn { width: 100%; padding: .6rem; border: 0; border-radius: .55rem; font-size: .82rem; font-weight: 900; letter-spacing: .04em; color: #fff; }
        .stock-btn.mark-out { background: var(--red); }
        .stock-btn.mark-in { background: var(--green); }

        @media (max-width: 820px) {
            .topbar-in { grid-template-columns: 1fr auto; }
            .nav { grid-column: 1 / -1; grid-row: 2; justify-content: center; }
            .nav a { flex: 1; text-align: center; }
            .user span { display: none; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-in">
            <a href="{{ route('kitchen.index') }}" class="brand">
                <div class="logo">DB</div>
                <div>
                    <div class="brand-name">Dapur Babeh</div>
                    <span class="brand-tag">Dapur</span>
                </div>
            </a>

            <nav class="nav">
                <a href="{{ route('kitchen.index') }}" class="{{ request()->routeIs('kitchen.index') ? 'active' : '' }}">Pesanan</a>
                <a href="{{ route('kitchen.stock') }}" class="{{ request()->routeIs('kitchen.stock') ? 'active' : '' }}">Stok Menu</a>
            </nav>

            <div class="right">
                <div class="clock" id="clock">--:--</div>
                <div class="user">
                    <div class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="logout">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        (function () {
            const el = document.getElementById('clock');
            const tick = () => { el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':'); };
            tick();
            setInterval(tick, 10000);
        })();
    </script>

    @stack('scripts')
</body>
</html>