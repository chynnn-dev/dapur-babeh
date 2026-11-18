<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelayan - Dapur Babeh</title>
    <style>
        :root { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --cream: #f4efe4; --line: #e6dfd0; --muted: #6b7280; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--cream); color: #1f2937; font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.4; }
        button { font-family: inherit; cursor: pointer; }

        .bar { display: flex; align-items: center; justify-content: space-between; padding: .7rem 1.25rem; background: var(--gd); color: #fff; }
        .bar b { font-size: 1.1rem; }
        .bar small { color: #a8d5c3; margin-left: .5rem; }
        .bar .who { display: flex; align-items: center; gap: .8rem; font-size: .85rem; color: #cfe3da; }
        .bar .who button { border: 1px solid rgba(255,255,255,.35); background: transparent; color: #fff; padding: .35rem .9rem; border-radius: .5rem; }

        .wrap { display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem; padding: 1rem; align-items: start; }
        .panel { background: #ebe5d7; border-radius: 1rem; padding: .9rem; min-height: 70vh; }
        .panel h2 { margin: 0 0 .8rem; display: flex; align-items: center; gap: .6rem; font-size: 1rem; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; color: var(--gd); }
        .count { display: inline-grid; place-items: center; min-width: 1.9rem; height: 1.9rem; padding: 0 .5rem; border-radius: 99px; background: var(--o); color: #fff; font-weight: 900; }
        .empty { text-align: center; color: #9b9484; padding: 2.5rem 1rem; font-weight: 700; border: 2px dashed #d3cab6; border-radius: .8rem; }

        .cards { display: grid; gap: .8rem; grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr)); }
        .card { background: #fff; border-radius: .85rem; border-left: 6px solid var(--o); padding: .85rem .9rem; display: flex; flex-direction: column; gap: .55rem;
                box-shadow: 0 1px 3px rgba(15,95,70,.1), 0 6px 16px rgba(15,95,70,.06); }
        .card.late { outline: 2px solid #dc2626; }
        .hd { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; }
        .where { font-size: 1.35rem; font-weight: 900; color: var(--g); line-height: 1.1; }
        .inv { font-size: .72rem; color: var(--muted); margin-top: .15rem; }
        .time { flex-shrink: 0; padding: .25rem .65rem; border-radius: 99px; font-size: .78rem; font-weight: 900; background: #e3f4ea; color: var(--gd); }
        .late .time { background: #fde2e2; color: #b91c1c; }
        .items { list-style: none; margin: 0; padding: .35rem 0; border-top: 1px dashed var(--line); border-bottom: 1px dashed var(--line); }
        .items li { display: flex; flex-wrap: wrap; align-items: baseline; gap: .5rem; padding: .3rem 0; font-size: 1rem; font-weight: 700; }
        .q { display: inline-grid; place-items: center; min-width: 2.1rem; height: 1.7rem; padding: 0 .35rem; border-radius: .5rem; background: #f0ebe0; color: var(--g); font-size: .88rem; font-weight: 900; }
        .note { flex-basis: 100%; margin-left: 2.6rem; padding: .25rem .55rem; border-radius: .4rem; background: #fff8dc; color: #7a5b00; font-size: .8rem; font-weight: 600; }
        .act { padding: .8rem; border: 0; border-radius: .65rem; font-size: .95rem; font-weight: 900; letter-spacing: .06em; color: #fff; background: var(--g); }
        .act:hover { background: var(--gd); }
        .act:disabled { opacity: .6; cursor: wait; }

        .legend { display: flex; flex-wrap: wrap; gap: .8rem; font-size: .75rem; color: var(--muted); margin: 0 0 .7rem; }
        .legend i { display: inline-block; width: .8rem; height: .8rem; border-radius: .2rem; margin-right: .3rem; vertical-align: -1px; }
        .tiles { display: grid; gap: .6rem; grid-template-columns: repeat(auto-fill, minmax(8.5rem, 1fr)); }
        .tile { border: 2px solid transparent; border-radius: .8rem; padding: .7rem .6rem; text-align: center; background: #fff; }
        .tile .n { font-size: 1.5rem; font-weight: 900; line-height: 1; }
        .tile .s { margin-top: .3rem; font-size: .72rem; font-weight: 700; }
        .tile.available { background: #dcfce7; border-color: #86efac; color: #166534; cursor: default; }
        .tile.reserved { background: #fef3c7; border-color: #f59e0b; color: #92400e; cursor: default; }
        .tile.occupied { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
        .tile.occupied.clearable { background: #fff; border-color: var(--g); color: var(--gd); box-shadow: 0 0 0 3px rgba(15,95,70,.15); }

        .toast { position: fixed; left: 50%; bottom: 1.4rem; transform: translateX(-50%); z-index: 50; max-width: 90vw; padding: .75rem 1.2rem; border-radius: .7rem; background: #111827; color: #fff; font-weight: 700; box-shadow: 0 8px 24px rgba(0,0,0,.3); }

        @media (max-width: 900px) { .wrap { grid-template-columns: 1fr; } .panel { min-height: 0; } }
    </style>
</head>
<body>
    <header class="bar">
        <div><b>Dapur Babeh</b><small>Pelayan</small></div>
        <div class="who">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <main class="wrap">
        <section class="panel">
            <h2>Siap Diantar <span class="count" id="count-ready">0</span></h2>
            <div id="ready" class="cards"><p class="empty">Memuat pesanan...</p></div>
        </section>

        <section class="panel">
            <h2>Status Meja</h2>
            <div class="legend">
                <span><i style="background:#86efac"></i>Kosong</span>
                <span><i style="background:#f59e0b"></i>Reservasi</span>
                <span><i style="background:#fca5a5"></i>Terisi</span>
                <span><i style="background:#fff;border:2px solid #0f5f46"></i>Bisa dikosongkan</span>
            </div>
            <div id="tiles" class="tiles"><p class="empty">Memuat meja...</p></div>
        </section>
    </main>

    <div id="toast" class="toast" hidden></div>

<script>
(function () {
    const dataUrl   = @json(route('waiter.data'));
    const serveBase = @json(url('/waiter/orders'));
    const clearBase = @json(url('/waiter/tables'));
    const csrf      = document.querySelector('meta[name="csrf-token"]').content;

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    let tablesData = [];
    let busy = false;
    let toastTimer = null;

    function toast(message) {
        const el = $('toast');
        el.textContent = message;
        el.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { el.hidden = true; }, 3500);
    }

    function card(o) {
        return `
            <article class="card ${o.minutes >= 5 ? 'late' : ''}">
                <div class="hd">
                    <div>
                        <div class="where">${o.table ? 'MEJA ' + esc(o.table) : 'TAKEAWAY'}</div>
                        <div class="inv">${esc(o.invoice)}</div>
                    </div>
                    <span class="time">${o.minutes} mnt</span>
                </div>
                <ul class="items">
                    ${o.items.map((i) => `
                        <li>
                            <span class="q">${i.quantity}x</span>${esc(i.name)}
                            ${i.notes ? `<div class="note">Catatan: ${esc(i.notes)}</div>` : ''}
                        </li>`).join('')}
                </ul>
                <button type="button" class="act" data-serve="${o.id}">
                    ${o.table ? 'SUDAH DIANTAR' : 'SUDAH DISERAHKAN'}
                </button>
            </article>`;
    }

    function tile(t) {
        let sub = 'Kosong';
        let cls = t.status;

        if (t.status === 'reserved') sub = 'Reservasi';

        if (t.status === 'occupied') {
            if (t.open > 0) {
                sub = t.open + ' pesanan belum diantar';
            } else {
                sub = 'Tap untuk kosongkan';
                cls += ' clearable';
            }
        }

        return `<button type="button" class="tile ${cls}" data-table="${t.id}">
                    <div class="n">${esc(t.number)}</div>
                    <div class="s">${sub}</div>
                </button>`;
    }

    function render(data) {
        $('count-ready').textContent = data.ready.length;
        $('ready').innerHTML = data.ready.length
            ? data.ready.map(card).join('')
            : '<p class="empty">Belum ada pesanan yang siap diantar</p>';

        tablesData = data.tables;

        if (!data.tables_enabled) {
            $('tiles').innerHTML = '<p class="empty">Fitur manajemen meja sedang nonaktif.</p>';
        } else if (!data.tables.length) {
            $('tiles').innerHTML = '<p class="empty">Belum ada meja.</p>';
        } else {
            $('tiles').innerHTML = data.tables.map(tile).join('');
        }
    }

    async function load() {
        try {
            const res = await fetch(dataUrl, { headers: { 'Accept': 'application/json' } });

            if (res.status === 401 || res.status === 419) {
                window.location.reload();
                return;
            }

            render(await res.json());
        } catch (err) {
            // gagal sementara: tampilan terakhir dibiarkan, dicoba lagi otomatis
        }
    }

    async function patch(url) {
        try {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });

            if (!res.ok) {
                const d = await res.json().catch(() => ({}));
                const msg = d.errors ? Object.values(d.errors).flat()[0] : (d.message || 'Gagal memproses.');
                toast(msg);
            }
            return res.ok;
        } catch (err) {
            toast('Gagal terhubung ke server.');
            return false;
        }
    }

    $('ready').addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-serve]');
        if (!btn || busy) return;

        busy = true;
        btn.disabled = true;
        await patch(`${serveBase}/${btn.dataset.serve}/serve`);
        busy = false;
        load();
    });

    $('tiles').addEventListener('click', async (e) => {
        const el = e.target.closest('[data-table]');
        if (!el || busy) return;

        const t = tablesData.find((x) => x.id === Number(el.dataset.table));
        if (!t || t.status !== 'occupied') return;

        if (t.open > 0) {
            toast(`Meja ${t.number}: masih ada ${t.open} pesanan yang belum diantar.`);
            return;
        }

        if (!confirm(`Kosongkan meja ${t.number}? Pastikan pelanggan sudah pergi.`)) return;

        busy = true;
        await patch(`${clearBase}/${t.id}/clear`);
        busy = false;
        load();
    });

    load();
    setInterval(load, 5000);
})();
</script>
</body>
</html>