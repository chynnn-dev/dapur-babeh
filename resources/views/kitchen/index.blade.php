@extends('layouts.kitchen')

@section('title', 'Pesanan')

@section('content')
    <style>
        .kb { --g: #0f5f46; --gd: #0b4a37; --o: #f58220; --line: #e6dfd0; --muted: #6b7280; }
        .kb * { box-sizing: border-box; }

        /* Toolbar */
        .kb-tools { display: flex; flex-wrap: wrap; align-items: center; gap: .75rem; margin-bottom: 1rem; }
        .kb-tools h2 { margin: 0; font-size: 1.35rem; font-weight: 900; color: var(--g); }

        /* Papan dua kolom */
        .kb-board { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start; }
        .kb-col { background: #ebe5d7; border-radius: 1rem; padding: .8rem; min-height: 60vh; }
        .kb-col-head { display: flex; align-items: center; gap: .6rem; margin: 0 .2rem .8rem; }
        .kb-col-head h3 { margin: 0; font-size: 1rem; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
        .kb-count { display: inline-grid; place-items: center; min-width: 1.9rem; height: 1.9rem; padding: 0 .5rem; border-radius: 99px; color: #fff; font-weight: 900; }
        .kb-col.new h3 { color: #9a5a00; }
        .kb-col.new .kb-count { background: var(--o); }
        .kb-col.cooking h3 { color: var(--gd); }
        .kb-col.cooking .kb-count { background: var(--g); }
        .kb-dot { width: .7rem; height: .7rem; border-radius: 50%; }
        .kb-col.new .kb-dot { background: var(--o); }
        .kb-col.cooking .kb-dot { background: var(--g); }

        .kb-list { display: grid; gap: .8rem; grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr)); }
        .kb-empty { grid-column: 1 / -1; text-align: center; color: #9b9484; padding: 2.5rem 1rem; font-weight: 700; border: 2px dashed #d3cab6; border-radius: .8rem; }

        /* Kartu */
        .kb-card { background: #fff; border-radius: .85rem; border-left: 6px solid var(--c); padding: .85rem .9rem .9rem; display: flex; flex-direction: column; gap: .55rem;
                   box-shadow: 0 1px 3px rgba(15,95,70,.1), 0 6px 16px rgba(15,95,70,.06); }
        .kb-col.new .kb-card { --c: var(--o); }
        .kb-col.cooking .kb-card { --c: var(--g); }
        .kb-card.warn { outline: 2px solid #f5b041; }
        .kb-card.late { outline: 2px solid #dc2626; animation: kb-pulse 1.4s ease-in-out infinite; }
        @keyframes kb-pulse { 50% { box-shadow: 0 0 0 5px rgba(220,38,38,.18); } }

        .kb-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; }
        .kb-where { font-size: 1.35rem; font-weight: 900; color: var(--g); line-height: 1.1; }
        .kb-inv { font-size: .72rem; color: var(--muted); margin-top: .15rem; }
        .kb-time { flex-shrink: 0; padding: .25rem .65rem; border-radius: 99px; font-size: .78rem; font-weight: 900; background: #e3f4ea; color: var(--gd); }
        .warn .kb-time { background: #fff1de; color: #9a5a00; }
        .late .kb-time { background: #fde2e2; color: #b91c1c; }

        .kb-items { list-style: none; margin: 0; padding: .35rem 0; border-top: 1px dashed var(--line); border-bottom: 1px dashed var(--line); }
        .kb-items li { display: flex; flex-wrap: wrap; align-items: baseline; gap: .5rem; padding: .3rem 0; font-size: 1.02rem; font-weight: 700; color: #111827; }
        .kb-q { display: inline-grid; place-items: center; min-width: 2.1rem; height: 1.7rem; padding: 0 .35rem; border-radius: .5rem; background: #f0ebe0; color: var(--g); font-size: .88rem; font-weight: 900; }
        .kb-note { flex-basis: 100%; margin-left: 2.6rem; padding: .25rem .55rem; border-radius: .4rem; background: #fff8dc; color: #7a5b00; font-size: .8rem; font-weight: 600; }

        .kb-act { padding: .8rem; border: 0; border-radius: .65rem; cursor: pointer; font: inherit; font-size: .95rem; font-weight: 900; letter-spacing: .06em; color: #fff; transition: background .2s, transform .1s; }
        .kb-act:active { transform: scale(.98); }
        .kb-act:disabled { opacity: .6; cursor: wait; }
        .kb-act.start { background: var(--o); }
        .kb-act.start:hover { background: #d96d10; }
        .kb-act.finish { background: var(--g); }
        .kb-act.finish:hover { background: var(--gd); }

        @media (max-width: 900px) {
            .kb-board { grid-template-columns: 1fr; }
            .kb-col { min-height: 0; }
        }
    </style>

    <div class="kb">
        <div class="kb-tools">
            <h2>Papan Pesanan</h2>
        </div>

        <div id="board" class="kb-board">
            <section class="kb-col new">
                <div class="kb-col-head">
                    <span class="kb-dot"></span>
                    <h3>Baru</h3>
                    <span class="kb-count" id="count-new">0</span>
                </div>
                <div id="list-new" class="kb-list">
                    <p class="kb-empty">Memuat pesanan...</p>
                </div>
            </section>

            <section class="kb-col cooking">
                <div class="kb-col-head">
                    <span class="kb-dot"></span>
                    <h3>Dimasak</h3>
                    <span class="kb-count" id="count-cooking">0</span>
                </div>
                <div id="list-cooking" class="kb-list">
                    <p class="kb-empty">Memuat pesanan...</p>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const ordersUrl = @json(route('kitchen.orders'));
    const updateBase = @json(url('/kitchen/orders'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const board = document.getElementById('board');
    const listNew = document.getElementById('list-new');
    const listCooking = document.getElementById('list-cooking');
    let busy = false;

    const esc = (s) => String(s).replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function card(o, isNew) {
        const level = o.minutes >= 10 ? 'late' : (o.minutes >= 5 ? 'warn' : '');

        return `
            <article class="kb-card ${level}">
                <div class="kb-top">
                    <div>
                        <div class="kb-where">${o.table ? 'MEJA ' + esc(o.table) : 'TAKEAWAY'}</div>
                        <div class="kb-inv">${esc(o.invoice)}</div>
                    </div>
                    <span class="kb-time">${o.minutes} menit</span>
                </div>
                <ul class="kb-items">
                    ${o.items.map((i) => `
                        <li>
                            <span class="kb-q">${i.quantity}x</span>${esc(i.name)}
                            ${i.notes ? `<div class="kb-note">Catatan: ${esc(i.notes)}</div>` : ''}
                        </li>`).join('')}
                </ul>
                <button type="button" class="kb-act ${isNew ? 'start' : 'finish'}"
                        data-id="${o.id}" data-status="${isNew ? 'cooking' : 'done'}">
                    ${isNew ? 'MULAI MASAK' : 'SELESAI'}
                </button>
            </article>`;
    }

    function render(orders) {
        const fresh = orders.filter((o) => o.status === 'paid');
        const cooking = orders.filter((o) => o.status !== 'paid');

        document.getElementById('count-new').textContent = fresh.length;
        document.getElementById('count-cooking').textContent = cooking.length;

        listNew.innerHTML = fresh.length
            ? fresh.map((o) => card(o, true)).join('')
            : '<p class="kb-empty">Tidak ada pesanan baru</p>';

        listCooking.innerHTML = cooking.length
            ? cooking.map((o) => card(o, false)).join('')
            : '<p class="kb-empty">Belum ada yang dimasak</p>';
    }

    async function load() {
        try {
            const res = await fetch(ordersUrl, { headers: { 'Accept': 'application/json' } });

            if (res.status === 401 || res.status === 419) {
                window.location.reload();
                return;
            }

            const data = await res.json();
            render(data.orders);
        } catch (err) {
            // gagal sementara: tampilan terakhir dibiarkan, dicoba lagi otomatis
        }
    }

    board.addEventListener('click', async (e) => {
        const btn = e.target.closest('.kb-act');
        if (!btn || busy) return;

        busy = true;
        btn.disabled = true;

        try {
            const res = await fetch(`${updateBase}/${btn.dataset.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ status: btn.dataset.status }),
            });

            if (!res.ok) {
                const data = await res.json();
                alert((data.errors && data.errors.status && data.errors.status[0]) || data.message || 'Gagal mengubah status.');
            }
        } catch (err) {
            alert('Gagal terhubung ke server.');
        }

        busy = false;
        load();
    });

    load();
    setInterval(load, 5000);
})();
</script>
@endpush