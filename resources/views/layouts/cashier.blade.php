<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - Dapur Babeh</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; background: #f3f4f6; color: #1f2937; }
        button, input, select { font-family: inherit; font-size: .9rem; }
        button { cursor: pointer; }

        .topbar { height: 3.5rem; display: flex; align-items: center; justify-content: space-between;
                  padding: 0 1.5rem; background: #111827; color: #fff; }
        .topbar .brand { font-weight: 700; font-size: 1.1rem; }
        .topbar .brand small { font-weight: 400; color: #9ca3af; margin-left: .5rem; }
        .topbar .user { display: flex; align-items: center; gap: 1rem; font-size: .875rem; }
        .topbar .user button { background: #1f2937; color: #fff; border: 0; padding: .4rem .9rem; border-radius: .5rem; }
        .topbar .user button:hover { background: #374151; }

        .pos { display: grid; grid-template-columns: 1fr 24rem; gap: 1rem; padding: 1rem; height: calc(100vh - 3.5rem); }
        .pos-menu { display: flex; flex-direction: column; min-height: 0; }
        .pos-tools { margin-bottom: .75rem; }
        .pos-tools input { width: 100%; padding: .6rem .9rem; border: 1px solid #d1d5db; border-radius: .5rem; margin-bottom: .6rem; }
        .cats { display: flex; flex-wrap: wrap; gap: .4rem; }
        .cat { border: 1px solid #d1d5db; background: #fff; border-radius: 9999px; padding: .35rem .9rem; }
        .cat.active { background: #ea580c; border-color: #ea580c; color: #fff; }

        .menu-grid { flex: 1; overflow: auto; display: grid; align-content: start; gap: .75rem;
                     grid-template-columns: repeat(auto-fill, minmax(9rem, 1fr)); }
        .menu-card { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: .6rem; text-align: left; }
        .menu-card:hover { border-color: #ea580c; }
        .menu-card img, .menu-card .noimg { width: 100%; height: 6rem; border-radius: .5rem; object-fit: cover; display: block; margin-bottom: .5rem; }
        .menu-card .noimg { display: flex; align-items: center; justify-content: center; background: #ffedd5; color: #ea580c; font-size: 2rem; font-weight: 700; }
        .mname { font-weight: 600; font-size: .875rem; }
        .mprice { color: #ea580c; font-size: .85rem; margin-top: .15rem; }

        .pos-cart { background: #fff; border-radius: .75rem; padding: 1rem; display: flex; flex-direction: column;
                    min-height: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .pos-cart h2 { margin: 0 0 .75rem; font-size: 1.1rem; }
        .field label { display: block; font-size: .8rem; color: #6b7280; margin-bottom: .2rem; }
        .field select, .field input { width: 100%; padding: .5rem .7rem; border: 1px solid #d1d5db; border-radius: .5rem; }
        .takeaway { display: inline-block; background: #e0f2fe; color: #0369a1; border-radius: 9999px; padding: .25rem .8rem; font-size: .8rem; font-weight: 600; }
        .warn { color: #b45309; font-size: .8rem; margin-top: .3rem; }

        .cart-list { flex: 1; overflow: auto; margin: .75rem 0; }
        .empty { color: #9ca3af; font-size: .875rem; text-align: center; padding: 1.5rem 0; grid-column: 1 / -1; }
        .line { border-bottom: 1px solid #f3f4f6; padding: .6rem 0; }
        .line-top { display: flex; justify-content: space-between; gap: .5rem; font-size: .9rem; }
        .line-name { font-weight: 600; }
        .line-ctrl { display: flex; align-items: center; gap: .4rem; margin: .4rem 0; }
        .line-ctrl button { width: 1.9rem; height: 1.9rem; border: 1px solid #d1d5db; background: #fff; border-radius: .4rem; }
        .line-ctrl .del { width: auto; margin-left: auto; padding: 0 .6rem; color: #dc2626; border-color: #fecaca; }
        .qty { min-width: 1.5rem; text-align: center; font-weight: 600; }
        .note { width: 100%; padding: .35rem .6rem; border: 1px solid #e5e7eb; border-radius: .4rem; font-size: .8rem; }

        .totals { border-top: 1px solid #e5e7eb; padding-top: .75rem; }
        .trow { display: flex; justify-content: space-between; align-items: center; margin-bottom: .5rem; }
        .trow.big { font-size: 1.2rem; font-weight: 700; }
        .quick { display: flex; flex-wrap: wrap; gap: .35rem; margin: .5rem 0; }
        .quick button { border: 1px solid #d1d5db; background: #fff; border-radius: .4rem; padding: .3rem .6rem; font-size: .8rem; }
        .error-box { white-space: pre-line; background: #fee2e2; color: #991b1b; padding: .6rem .8rem; border-radius: .5rem; font-size: .85rem; margin-bottom: .5rem; }
        .pay-btn { width: 100%; padding: .8rem; border: 0; border-radius: .6rem; background: #16a34a; color: #fff; font-weight: 700; font-size: 1rem; }
        .pay-btn:disabled { background: #d1d5db; cursor: not-allowed; }

        @media (max-width: 900px) {
            .pos { grid-template-columns: 1fr; height: auto; }
            .menu-grid { max-height: 60vh; }
        }
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