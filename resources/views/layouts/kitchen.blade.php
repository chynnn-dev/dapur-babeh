<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur') - Dapur Babeh</title>
    <style>
        :root {
    --bg: #0f172a;
    --bar: #0b1222;
    --panel: #1e293b;
    --line: #334155;
    --text: #e2e8f0;
    --muted: #94a3b8;
    --accent: #f97316;
    --blue: #3b82f6;
    --green: #22c55e;
    --yellow: #eab308;
    --red: #ef4444;
}

* { box-sizing: border-box; }
html { font-size: 14px; }
body { margin: 0; background: var(--bg); color: var(--text); font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.4; }
button { font-family: inherit; cursor: pointer; transition: filter .15s, background .15s; }
button:hover:not(:disabled) { filter: brightness(1.1); }

/* Bar atas */
.topbar { position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between;
          flex-wrap: wrap; gap: .75rem; padding: .6rem 1.25rem; background: var(--bar); border-bottom: 1px solid var(--line); }
.brand { font-weight: 700; font-size: 1.1rem; }
.nav { display: flex; gap: .4rem; }
.nav a { padding: .35rem .9rem; border-radius: 9999px; color: var(--muted); text-decoration: none; font-weight: 600; font-size: .85rem; background: var(--panel); }
.nav a:hover { color: var(--text); }
.nav a.active { background: var(--accent); color: #fff; }
.right { display: flex; align-items: center; gap: .6rem; font-size: .85rem; color: var(--muted); }
.right button { background: var(--panel); color: var(--text); border: 1px solid var(--line); padding: .35rem .8rem; border-radius: .5rem; font-size: .8rem; }

.flash { margin: .9rem 1.25rem 0; padding: .6rem .9rem; border-radius: .6rem; background: rgba(34,197,94,.15); color: #86efac; border: 1px solid rgba(34,197,94,.35); font-size: .85rem; }

/* Ringkasan */
.summary { display: flex; flex-wrap: wrap; gap: .6rem; align-items: center; padding: .9rem 1.25rem 0; }
.pill { background: var(--panel); border: 1px solid var(--line); border-radius: 9999px; padding: .25rem .8rem; font-size: .8rem; font-weight: 600; color: var(--muted); }
.pill b { font-size: 1rem; margin-left: .35rem; color: var(--text); }
.spacer { flex: 1; }
.conn { font-size: .75rem; color: var(--green); }
.conn.off { color: var(--red); }
.sound { background: var(--panel); color: var(--text); border: 1px solid var(--line); padding: .35rem .8rem; border-radius: .5rem; font-size: .8rem; }

/* Kartu pesanan */
.orders-grid { display: grid; gap: .9rem; padding: .9rem 1.25rem 2rem; align-items: start;
               grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr)); }
.empty { grid-column: 1 / -1; text-align: center; color: #64748b; font-size: 1rem; padding: 3rem 0; }

.order { background: var(--panel); border: 1px solid var(--line); border-left: 4px solid var(--blue);
         border-radius: .85rem; padding: .9rem 1rem; box-shadow: 0 4px 14px rgba(0,0,0,.25); }
.order.cooking { border-left-color: var(--accent); }
.order.warn { border-left-color: var(--yellow); }
.order.late { border-left-color: var(--red); border-color: rgba(239,68,68,.5); }

.where { font-size: 1.2rem; font-weight: 700; letter-spacing: .02em; }
.meta { color: var(--muted); font-size: .78rem; margin-top: .1rem; }
.order ul { list-style: none; padding: 0; margin: .75rem 0; font-size: 1rem; }
.order li { padding: .4rem 0; border-bottom: 1px dashed var(--line); }
.order li:last-child { border-bottom: 0; }
.q { display: inline-block; min-width: 2.1rem; font-weight: 700; color: #fbbf24; }
.note { font-size: .8rem; color: #fda4af; margin: .1rem 0 0 2.1rem; font-style: italic; }

.act { width: 100%; padding: .6rem; border: 0; border-radius: .6rem; font-size: .9rem; font-weight: 700; letter-spacing: .04em; color: #fff; }
.act.start { background: var(--blue); }
.act.finish { background: #16a34a; }
.act:disabled { opacity: .5; cursor: not-allowed; }

/* Stok Menu */
.page { padding: .9rem 1.25rem 2rem; }
.hint { color: var(--muted); margin: 0 0 .75rem; font-size: .9rem; }
.cat-title { font-size: .85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; margin: 1.25rem 0 .6rem; color: var(--accent); }
.stock-grid { display: grid; gap: .7rem; grid-template-columns: repeat(auto-fill, minmax(13rem, 1fr)); }
.stock-card { background: var(--panel); border: 1px solid var(--line); border-left: 4px solid var(--green);
              border-radius: .8rem; padding: .75rem .9rem; }
.stock-card.out { border-left-color: var(--red); }
.sname { font-size: 1rem; font-weight: 600; }
.sstate { font-size: .75rem; margin: .1rem 0 .6rem; color: #86efac; }
.stock-card.out .sstate { color: #fca5a5; }
.stock-btn { width: 100%; padding: .5rem; border: 0; border-radius: .5rem; font-size: .8rem; font-weight: 700; color: #fff; }
.stock-btn.mark-out { background: #dc2626; }
.stock-btn.mark-in { background: #16a34a; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">Dapur Babeh <span style="font-weight:400;color:#94a3b8">Dapur</span></div>

        <nav class="nav">
            <a href="{{ route('kitchen.index') }}" class="{{ request()->routeIs('kitchen.index') ? 'active' : '' }}">Pesanan</a>
            <a href="{{ route('kitchen.stock') }}" class="{{ request()->routeIs('kitchen.stock') ? 'active' : '' }}">Stok Menu</a>
        </nav>

        <div class="right">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>