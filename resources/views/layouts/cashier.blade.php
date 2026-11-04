<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - Dapur Babeh</title>
    <style>
      :root {
    --bg: #f4f5f7;
    --card: #ffffff;
    --line: #e8eaee;
    --text: #1f2937;
    --muted: #6b7280;
    --accent: #ea580c;
    --accent-soft: #ffedd5;
    --shadow: 0 1px 2px rgba(15, 23, 42, .05), 0 4px 14px rgba(15, 23, 42, .05);
}

* { box-sizing: border-box; }
html { font-size: 15px; }
body { margin: 0; background: var(--bg); color: var(--text); font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.4; }
button, input, select { font-family: inherit; font-size: .9rem; }
button { cursor: pointer; transition: background .15s, border-color .15s, color .15s; }

/* ---------- Bar atas ---------- */
.topbar { height: 3.5rem; display: flex; align-items: center; justify-content: space-between; padding: 0 1.25rem; background: #111827; color: #fff; }
.topbar .brand { font-weight: 700; font-size: 1.05rem; }
.topbar .brand small { font-weight: 400; color: #9ca3af; margin-left: .5rem; }
.topbar .user { display: flex; align-items: center; gap: .9rem; font-size: .85rem; color: #d1d5db; }
.topbar .user button { background: #1f2937; color: #fff; border: 1px solid #374151; padding: .35rem .9rem; border-radius: .5rem; }
.topbar .user button:hover { background: #374151; }

/* ---------- Kerangka halaman POS ---------- */
.pos { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1rem; padding: 1rem; }

/* ---------- Kolom menu ---------- */
.pos-menu { display: flex; flex-direction: column; min-height: 0; gap: .75rem; }
.pos-tools { background: var(--card); border: 1px solid var(--line); border-radius: .9rem; padding: .75rem; box-shadow: var(--shadow); }
.pos-tools input { width: 100%; padding: .6rem .9rem; border: 1px solid var(--line); border-radius: .6rem; background: #f9fafb; margin-bottom: .6rem; }
.pos-tools input:focus { outline: none; border-color: var(--accent); background: #fff; }
.cats { display: flex; flex-wrap: wrap; gap: .4rem; }
.cat { border: 1px solid var(--line); background: #fff; color: var(--muted); border-radius: 9999px; padding: .3rem .9rem; font-weight: 600; font-size: .8rem; }
.cat:hover { border-color: var(--accent); color: var(--accent); }
.cat.active { background: var(--accent); border-color: var(--accent); color: #fff; }

.menu-grid { flex: 1; min-height: 0; max-height: 55vh; overflow: auto; display: grid; align-content: start; gap: .75rem;
             grid-template-columns: repeat(auto-fill, minmax(9rem, 1fr)); padding: .15rem; }
.menu-card { background: var(--card); border: 1px solid var(--line); border-radius: .9rem; padding: .55rem; text-align: left; box-shadow: var(--shadow); }
.menu-card:hover { border-color: var(--accent); }
.menu-card img, .menu-card .noimg { width: 100%; height: 5.5rem; border-radius: .6rem; object-fit: cover; display: block; margin-bottom: .5rem; }
.menu-card .noimg { display: flex; align-items: center; justify-content: center; background: var(--accent-soft); color: var(--accent); font-size: 1.8rem; font-weight: 700; }
.mname { font-weight: 600; font-size: .85rem; }
.mprice { color: var(--accent); font-size: .8rem; font-weight: 600; margin-top: .15rem; }
.empty { color: #9ca3af; font-size: .85rem; text-align: center; padding: 1.5rem 0; grid-column: 1 / -1; }

/* ---------- Panel pesanan (mode tumpuk: satu kartu) ---------- */
.pos-cart { background: var(--card); border: 1px solid var(--line); border-radius: .9rem; padding: 1rem; box-shadow: var(--shadow);
            display: flex; flex-direction: column; gap: .75rem; }
.pos-cart h2 { margin: 0; font-size: 1rem; }

.field label { display: flex; align-items: center; justify-content: space-between; font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: .35rem; }
.field input, .field select { width: 100%; padding: .55rem .75rem; border: 1px solid var(--line); border-radius: .6rem; background: #f9fafb; }
.field input:focus { outline: none; border-color: var(--accent); background: #fff; }
.takeaway { display: inline-block; background: #e0f2fe; color: #0369a1; border-radius: 9999px; padding: .3rem .9rem; font-size: .8rem; font-weight: 600; }
.warn { color: #b45309; font-size: .8rem; margin-top: .3rem; }
.selected-label { color: #0369a1; background: #e0f2fe; border-radius: 9999px; padding: .05rem .6rem; font-size: .75rem; }

/* Peta meja */
.legend { display: flex; flex-wrap: wrap; gap: .7rem; font-size: .72rem; color: var(--muted); margin: .1rem 0 .5rem; }
.legend span { display: inline-flex; align-items: center; gap: .3rem; }
.dot { width: .8rem; height: .8rem; border-radius: .2rem; display: inline-block; }
.mode-btn { width: 100%; margin: 0 0 .5rem; padding: .4rem; border: 1px dashed #f59e0b; background: #fffbeb; color: #92400e; border-radius: .55rem; font-size: .75rem; font-weight: 600; }
.mode-btn.on { background: #f59e0b; color: #fff; border-style: solid; }
.table-map { display: grid; grid-template-columns: repeat(auto-fill, minmax(2.7rem, 1fr)); gap: .4rem; max-height: 11rem; overflow: auto; padding: .15rem; }
.table-map.reserve-mode .tbl:not(.occupied) { outline: 2px dashed #f59e0b; outline-offset: 1px; }
.tbl { height: 2.4rem; border-radius: .55rem; border: 1px solid transparent; font-weight: 700; font-size: .85rem; }
.tbl.available { background: #dcfce7; border-color: #86efac; color: #166534; }
.tbl.reserved { background: #fef3c7; border-color: #f59e0b; color: #92400e; }
.tbl.occupied { background: #ef4444; color: #fff; cursor: not-allowed; }
.tbl.selected { background: #0ea5e9; color: #fff; border-color: #0284c7; }

/* Daftar pesanan */
.cart-list { max-height: 20rem; overflow: auto; }
.line { background: #f9fafb; border: 1px solid #f1f2f4; border-radius: .7rem; padding: .6rem .75rem; margin-bottom: .5rem; }
.line-top { display: flex; justify-content: space-between; gap: .5rem; font-size: .88rem; }
.line-name { font-weight: 600; }
.line-ctrl { display: flex; align-items: center; gap: .4rem; margin: .45rem 0; }
.line-ctrl button { width: 1.8rem; height: 1.8rem; border: 1px solid var(--line); background: #fff; border-radius: .45rem; font-weight: 700; }
.line-ctrl button:hover { border-color: var(--accent); }
.line-ctrl .del { width: auto; margin-left: auto; padding: 0 .6rem; color: #dc2626; border-color: #fecaca; font-weight: 500; font-size: .78rem; }
.qty { min-width: 1.5rem; text-align: center; font-weight: 700; }
.note { width: 100%; padding: .4rem .6rem; border: 1px solid var(--line); border-radius: .45rem; font-size: .8rem; background: #fff; }

/* Pembayaran */
.totals { display: flex; flex-direction: column; gap: .8rem; border-top: 1px solid var(--line); padding-top: .9rem; }
.trow { display: flex; justify-content: space-between; align-items: center; }
.trow.big { font-size: 1.2rem; font-weight: 700; }
.quick { display: grid; grid-template-columns: repeat(2, 1fr); gap: .4rem; }
.quick button { border: 1px solid var(--line); background: #fff; border-radius: .55rem; padding: .5rem; font-size: .82rem; font-weight: 600; }
.quick button:hover { border-color: var(--accent); color: var(--accent); }
.error-box { white-space: pre-line; word-break: break-word; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
             padding: .6rem .8rem; border-radius: .6rem; font-size: .82rem; }
.pay-btn { width: 100%; margin-top: auto; padding: .9rem; border: 0; border-radius: .7rem; background: #16a34a; color: #fff; font-weight: 700; font-size: 1rem; }
.pay-btn:hover:not(:disabled) { background: #15803d; }
.pay-btn:disabled { background: #d1d5db; cursor: not-allowed; }

/* ---------- Layar lebar: tiga kolom (Menu | Meja & Pesanan | Pembayaran) ---------- */
@media (min-width: 1100px) {
    .pos { grid-template-columns: minmax(0, 1fr) 21rem 19rem; grid-template-rows: auto auto minmax(0, 1fr);
           gap: .75rem 1rem; height: calc(100vh - 3.5rem); }

    .pos-menu { grid-column: 1; grid-row: 1 / -1; }
    .menu-grid { max-height: none; }

    /* panel pesanan dilepas dari satu kartu, tiap bagian jadi kartu sendiri */
    .pos-cart { display: contents; }

    .pos-cart > h2,
    .pos-cart > .field,
    .pos-cart > .takeaway,
    .pos-cart > .cart-list,
    .pos-cart > .totals { background: var(--card); border: 1px solid var(--line); border-radius: .9rem; box-shadow: var(--shadow); }

    .pos-cart > h2 { grid-column: 2; grid-row: 1; padding: .75rem 1rem; }
    .pos-cart > .field,
    .pos-cart > .takeaway { grid-column: 2; grid-row: 2; padding: 1rem; }
    .pos-cart > .takeaway { display: block; text-align: center; background: #e0f2fe; border-color: #bae6fd; }
    .pos-cart > .cart-list { grid-column: 2; grid-row: 3; max-height: none; min-height: 0; overflow: auto; padding: .75rem; }
    .pos-cart > .totals { grid-column: 3; grid-row: 1 / -1; padding: 1rem; overflow: auto; }
}

[hidden] { display: none !important; }

.methods { display: grid; grid-auto-flow: column; grid-auto-columns: 1fr; gap: .35rem; }
.method { border: 1px solid var(--line); background: #fff; border-radius: .55rem; padding: .5rem; font-weight: 600; font-size: .82rem; }
.method:hover { border-color: var(--accent); }
.method.active { background: var(--accent); border-color: var(--accent); color: #fff; }

.pay-section { display: flex; flex-direction: column; gap: .8rem; }
.qris-box img { display: block; margin: 0 auto; max-width: 100%; max-height: 14rem; object-fit: contain;
                border: 1px solid var(--line); border-radius: .6rem; background: #fff; padding: .4rem; }
.qris-box p, .hint { margin: .5rem 0 0; font-size: .78rem; color: var(--muted); text-align: center; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">Dapur Babeh <small>Kasir</small></div>
        <div class="user">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    @yield('content')

    @stack('scripts')
</body>
</html>