@extends('layouts.kitchen')

@section('title', 'Pesanan')

@section('content')
    <div class="summary">
        <div class="pill">Baru <b id="count-new">0</b></div>
        <div class="pill">Dimasak <b id="count-cooking">0</b></div>
        <div class="spacer"></div>
        <span id="conn" class="conn">Menghubungkan...</span>
        <button type="button" id="sound-btn" class="sound">Suara: MATI</button>
    </div>

    <div id="orders" class="orders-grid">
        <p class="empty">Memuat pesanan...</p>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const ordersUrl = @json(route('kitchen.orders'));
    const updateBase = @json(url('/kitchen/orders'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const grid = document.getElementById('orders');
    let knownIds = null;   // null = pemuatan pertama (tanpa bunyi)
    let soundOn = false;
    let audioCtx = null;
    let busy = false;

    const esc = (s) => String(s).replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function beep() {
        if (!soundOn || !audioCtx) return;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.value = 880;
        gain.gain.value = 0.2;
        osc.start();
        osc.stop(audioCtx.currentTime + 0.3);
    }

    function render(orders) {
        const fresh = orders.filter((o) => o.status === 'paid').length;
        document.getElementById('count-new').textContent = fresh;
        document.getElementById('count-cooking').textContent = orders.length - fresh;

        if (orders.length === 0) {
            grid.innerHTML = '<p class="empty">Tidak ada pesanan aktif.</p>';
            return;
        }

        grid.innerHTML = orders.map((o) => {
            const isNew = o.status === 'paid';
            const level = o.minutes >= 10 ? 'late' : (o.minutes >= 5 ? 'warn' : '');

            return `
                <article class="order ${isNew ? 'new' : 'cooking'} ${level}">
                    <div class="where">${o.table ? 'MEJA ' + esc(o.table) : 'TAKEAWAY'}</div>
                    <div class="meta">${esc(o.invoice)} &middot; ${o.minutes} menit</div>
                    <ul>
                        ${o.items.map((i) => `
                            <li>
                                <span class="q">${i.quantity}x</span>${esc(i.name)}
                                ${i.notes ? `<div class="note">Catatan: ${esc(i.notes)}</div>` : ''}
                            </li>`).join('')}
                    </ul>
                    <button type="button" class="act ${isNew ? 'start' : 'finish'}"
                            data-id="${o.id}" data-status="${isNew ? 'cooking' : 'done'}">
                        ${isNew ? 'MULAI MASAK' : 'SELESAI'}
                    </button>
                </article>`;
        }).join('');
    }

    async function load() {
        const conn = document.getElementById('conn');

        try {
            const res = await fetch(ordersUrl, { headers: { 'Accept': 'application/json' } });

            if (res.status === 401 || res.status === 419) {
                window.location.reload();
                return;
            }

            const data = await res.json();
            const ids = data.orders.map((o) => o.id);

            if (knownIds !== null && ids.some((id) => !knownIds.includes(id))) beep();
            knownIds = ids;

            render(data.orders);
            conn.textContent = 'Terhubung';
            conn.classList.remove('off');
        } catch (err) {
            conn.textContent = 'Terputus, mencoba lagi...';
            conn.classList.add('off');
        }
    }

    grid.addEventListener('click', async (e) => {
        const btn = e.target.closest('.act');
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

    document.getElementById('sound-btn').addEventListener('click', (e) => {
        soundOn = !soundOn;

        if (soundOn) {
            audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
            audioCtx.resume();
            beep();
        }

        e.currentTarget.textContent = soundOn ? 'Suara: HIDUP' : 'Suara: MATI';
    });

    load();
    setInterval(load, 5000);
})();
</script>
@endpush