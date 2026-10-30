@extends('layouts.cashier')

@section('title', 'POS')

@section('content')
<div class="pos">

    {{-- Kiri: daftar menu --}}
    <section class="pos-menu">
        <div class="pos-tools">
            <input id="search" type="text" placeholder="Cari menu..." autocomplete="off">
            <div id="cats" class="cats"></div>
        </div>
        <div id="menu-grid" class="menu-grid"></div>
    </section>

    {{-- Kanan: keranjang & pembayaran --}}
    <aside class="pos-cart">
        <h2>Pesanan</h2>

        @if ($useTables)
            <div class="field">
                <label for="table">Nomor Meja</label>
                <select id="table">
                    <option value="">Pilih meja</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}">Meja {{ $table->table_number }}</option>
                    @endforeach
                </select>
                @if ($tables->isEmpty())
                    <div class="warn">Semua meja sedang terisi.</div>
                @endif
            </div>
        @else
            <span class="takeaway">Takeaway / Tanpa Meja</span>
        @endif

        <div id="cart-list" class="cart-list"></div>

        <div class="totals">
            <div class="trow big"><span>Total</span><span id="total">Rp 0</span></div>

            <div class="field">
                <label for="cash">Uang Diterima (Rp)</label>
                <input id="cash" type="number" min="0" step="500" inputmode="numeric" placeholder="0">
            </div>

            <div class="quick">
                <button type="button" data-cash="exact">Uang Pas</button>
                <button type="button" data-cash="20000">20.000</button>
                <button type="button" data-cash="50000">50.000</button>
                <button type="button" data-cash="100000">100.000</button>
            </div>

            <div class="trow"><span>Kembalian</span><strong id="change">-</strong></div>

            <div id="error-box" class="error-box" hidden></div>

            <button id="pay-btn" type="button" class="pay-btn" disabled>Bayar</button>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const menus      = @json($menus);
    const categories = @json($categories);
    const useTables  = @json($useTables);
    const payUrl     = @json(route('cashier.store'));
    const csrf       = document.querySelector('meta[name="csrf-token"]').content;

    const cart = new Map();   // menu_id => { menu, qty, notes }
    let activeCat = 'all';
    let keyword   = '';
    let paying    = false;

    const $      = (id) => document.getElementById(id);
    const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
    const esc    = (s) => String(s).replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const total  = () => [...cart.values()].reduce((sum, l) => sum + l.menu.price * l.qty, 0);

    function renderCats() {
        const items = [{ id: 'all', name: 'Semua' }, ...categories];
        $('cats').innerHTML = items.map((c) =>
            `<button type="button" class="cat ${String(c.id) === String(activeCat) ? 'active' : ''}" data-cat="${c.id}">${esc(c.name)}</button>`
        ).join('');
    }

    function renderMenus() {
        const list = menus.filter((m) =>
            (activeCat === 'all' || String(m.category_id) === String(activeCat)) &&
            m.name.toLowerCase().includes(keyword)
        );

        $('menu-grid').innerHTML = list.length
            ? list.map((m) => `
                <button type="button" class="menu-card" data-id="${m.id}">
                    ${m.image
                        ? `<img src="${esc(m.image)}" alt="">`
                        : `<div class="noimg">${esc(m.name.charAt(0).toUpperCase())}</div>`}
                    <div class="mname">${esc(m.name)}</div>
                    <div class="mprice">${rupiah(m.price)}</div>
                </button>`).join('')
            : '<p class="empty">Menu tidak ditemukan.</p>';
    }

    function renderCart() {
        const el = $('cart-list');

        if (cart.size === 0) {
            el.innerHTML = '<p class="empty">Belum ada pesanan. Pilih menu di sebelah kiri.</p>';
        } else {
            el.innerHTML = [...cart.values()].map((l) => `
                <div class="line" data-id="${l.menu.id}">
                    <div class="line-top">
                        <span class="line-name">${esc(l.menu.name)}</span>
                        <span>${rupiah(l.menu.price * l.qty)}</span>
                    </div>
                    <div class="line-ctrl">
                        <button type="button" data-act="dec">-</button>
                        <span class="qty">${l.qty}</span>
                        <button type="button" data-act="inc">+</button>
                        <button type="button" data-act="del" class="del">Hapus</button>
                    </div>
                    <input type="text" class="note" maxlength="255" placeholder="Catatan (opsional)" value="${esc(l.notes)}">
                </div>`).join('');
        }

        updateTotals();
    }

    function updateTotals() {
        const t    = total();
        const cash = parseInt($('cash').value || '0', 10);

        $('total').textContent  = rupiah(t);
        $('change').textContent = (t > 0 && cash >= t) ? rupiah(cash - t) : '-';
        $('pay-btn').disabled   = paying || !(t > 0 && cash >= t);
    }

    function showError(message) {
        const box = $('error-box');
        box.textContent = message;
        box.hidden = false;
    }

    // Filter kategori & pencarian
    $('cats').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-cat]');
        if (!btn) return;
        activeCat = btn.dataset.cat;
        renderCats();
        renderMenus();
    });

    $('search').addEventListener('input', (e) => {
        keyword = e.target.value.trim().toLowerCase();
        renderMenus();
    });

    // Tambah menu ke keranjang
    $('menu-grid').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-id]');
        if (!btn) return;

        const menu = menus.find((m) => m.id === Number(btn.dataset.id));
        const line = cart.get(menu.id);

        if (line) { if (line.qty < 99) line.qty++; }
        else cart.set(menu.id, { menu, qty: 1, notes: '' });

        renderCart();
    });

    // Ubah jumlah / hapus
    $('cart-list').addEventListener('click', (e) => {
        const btn = e.target.closest('[data-act]');
        if (!btn) return;

        const id   = Number(btn.closest('.line').dataset.id);
        const line = cart.get(id);

        if (btn.dataset.act === 'inc' && line.qty < 99) line.qty++;
        if (btn.dataset.act === 'dec') { line.qty--; if (line.qty <= 0) cart.delete(id); }
        if (btn.dataset.act === 'del') cart.delete(id);

        renderCart();
    });

    // Catatan per item (tanpa render ulang agar fokus tidak hilang)
    $('cart-list').addEventListener('input', (e) => {
        if (!e.target.classList.contains('note')) return;
        cart.get(Number(e.target.closest('.line').dataset.id)).notes = e.target.value;
    });

    // Uang diterima
    $('cash').addEventListener('input', updateTotals);

    document.querySelectorAll('[data-cash]').forEach((btn) => {
        btn.addEventListener('click', () => {
            $('cash').value = btn.dataset.cash === 'exact' ? total() : btn.dataset.cash;
            updateTotals();
        });
    });

    // Bayar
    $('pay-btn').addEventListener('click', async () => {
        $('error-box').hidden = true;

        if (useTables && !$('table').value) {
            showError('Pilih nomor meja terlebih dahulu.');
            return;
        }

        paying = true;
        updateTotals();

        try {
            const res = await fetch(payUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    table_id: useTables ? $('table').value : null,
                    cash_received: parseInt($('cash').value || '0', 10),
                    items: [...cart.values()].map((l) => ({
                        menu_id: l.menu.id,
                        quantity: l.qty,
                        notes: l.notes.trim() || null,
                    })),
                }),
            });

            const data = await res.json();

            if (res.ok) {
                window.location.href = data.redirect;
                return;
            }

            const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'Terjadi kesalahan.'];
            showError(messages.join('\n'));
        } catch (err) {
            showError('Gagal terhubung ke server. Coba lagi.');
        }

        paying = false;
        updateTotals();
    });

    renderCats();
    renderMenus();
    renderCart();
})();
</script>
@endpush