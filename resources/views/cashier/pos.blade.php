@extends('layouts.cashier')

@section('title', 'POS')

@section('content')
<style>
    .mid-wait { margin-top: .6rem; padding: .8rem .9rem; border-radius: .6rem; background: #fff4e5; border: 1px solid #f7c98b; color: #7a4300; font-size: .85rem; }
    .mid-wait strong { display: block; font-size: .95rem; }
    .mid-wait span { display: block; opacity: .75; word-break: break-all; margin-bottom: .5rem; }
    .mid-actions { display: flex; flex-wrap: wrap; gap: .4rem; }
    .mid-actions button { flex: 1; padding: .45rem .6rem; border-radius: .4rem; border: 1px solid #d9b27a; background: #fff; color: #7a4300; font-weight: 700; cursor: pointer; }
    .mid-actions button.danger { border-color: #f3c1c1; color: #b91c1c; }

    /* Pilihan metode: dua tombol besar sama lebar */
    .methods { display: grid; grid-template-columns: repeat(auto-fit, minmax(0, 1fr)); gap: .5rem; }
    .methods .method { padding: .75rem .5rem; font-weight: 800; border-radius: .6rem; }
    .mid-info { display: grid; gap: .35rem; margin-top: .2rem; padding: .8rem .9rem; border-radius: .6rem; background: #e9f3ee; color: #0b4a37; font-size: .85rem; }
    .mid-info b { font-size: .9rem; }

    .pos-head-row { display: flex; justify-content: space-between; align-items: center; gap: .5rem; }
    .rh-link { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .8rem; border-radius: 99px; background: #e9f3ee; color: #0b4a37; font-size: .8rem; font-weight: 800; text-decoration: none; white-space: nowrap; }
    .rh-link:hover { background: #d3e8de; }
    .rh-badge { padding: .05rem .5rem; border-radius: 99px; background: #f58220; color: #fff; font-size: .7rem; }
</style>

<div class="pos">

    {{-- Kiri: daftar menu --}}
    <section class="pos-menu">
        <div class="pos-tools">
            <input id="search" type="text" placeholder="Cari menu..." autocomplete="off">
            <div id="cats" class="cats"></div>
        </div>
        <div id="menu-grid" class="menu-grid"></div>
    </section>

    {{-- Kanan: meja, keranjang & pembayaran --}}
    <aside class="pos-cart">
        <div class="pos-head-row">
            <h2>Pesanan</h2>
            <a href="{{ route('cashier.history') }}" class="rh-link">
                Riwayat
                @if ($pendingCount)
                    <span class="rh-badge" title="Pembayaran Non-Tunai yang menunggu">{{ $pendingCount }} menunggu</span>
                @endif
            </a>
        </div>

        @if ($useTables)
            <div class="field">
                <label>Pilih Meja <span id="table-selected" class="selected-label">Belum dipilih</span></label>
                <div class="legend">
                    <span><i class="dot" style="background:#86efac"></i>Kosong</span>
                    <span><i class="dot" style="background:#ef4444"></i>Terisi</span>
                    <span><i class="dot" style="background:#f59e0b"></i>Reservasi</span>
                    <span><i class="dot" style="background:#0ea5e9"></i>Dipilih</span>
                </div>
                <button type="button" id="reserve-mode" class="mode-btn">Atur Reservasi: MATI</button>
                <div id="table-map" class="table-map"></div>
            </div>
        @else
            <span class="takeaway">Takeaway / Tanpa Meja</span>
        @endif

        <div id="cart-list" class="cart-list"></div>

        <div class="totals">
            <div class="trow big"><span>Total</span><span id="total">Rp 0</span></div>

            {{-- Pilihan metode (hanya muncul jika ada metode selain tunai) --}}
            @if (count($methods) > 1)
                <div class="methods" id="methods">
                    @foreach ($methods as $key => $label)
                        <button type="button" class="method {{ $loop->first ? 'active' : '' }}" data-method="{{ $key }}">{{ $label }}</button>
                    @endforeach
                </div>
            @endif

            {{-- Tunai --}}
            <div id="cash-box" class="pay-section">
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
            </div>

            {{-- QRIS / Debit --}}
            <div id="noncash-box" class="pay-section" hidden>
                @if ($qrisImage)
                    <div id="qris-box" class="qris-box" hidden>
                        <img src="{{ $qrisImage }}" alt="QRIS toko">
                        <p>Minta pelanggan scan QRIS. Pastikan dana sudah masuk sebelum konfirmasi.</p>
                    </div>
                @endif

                <div id="debit-hint" class="hint" hidden>
                    Proses pembayaran di mesin EDC. Konfirmasi setelah transaksi berhasil.
                </div>

                <div class="field">
                    <label for="reference">No. Referensi (opsional)</label>
                    <input id="reference" type="text" maxlength="100" placeholder="Mis. 4 digit kartu / no. approval">
                </div>
            </div>

            {{-- Midtrans (QRIS, e-wallet, kartu, dll) --}}
            @if (isset($methods['midtrans']))
                <div id="midtrans-box" class="pay-section" hidden>
                    <div class="mid-info">
                        <b>Pembayaran Non-Tunai</b>
                        <span>Tekan tombol di bawah, lalu pelanggan memilih cara bayar: QRIS, e-wallet, transfer bank, atau kartu.</span>
                        <span>Pesanan masuk ke dapur otomatis setelah pembayaran berhasil.</span>
                    </div>

                    <div id="mid-wait" class="mid-wait" hidden>
                        <strong>Menunggu pembayaran...</strong>
                        <span id="mid-order-id"></span>
                        <div class="mid-actions">
                            <button type="button" id="mid-reopen">Buka Pembayaran</button>
                            <button type="button" id="mid-check">Cek Status</button>
                            <button type="button" id="mid-cancel" class="danger">Batalkan</button>
                        </div>
                    </div>
                </div>
            @endif

            <div id="error-box" class="error-box" hidden></div>

            <button id="pay-btn" type="button" class="pay-btn" disabled>Bayar</button>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const menus       = @json($menus);
    const categories  = @json($categories);
    const useTables   = @json($useTables);
    const payUrl      = @json(route('cashier.store'));
    const tablesUrl   = @json(route('cashier.tables'));
    const reserveBase = @json(url('/cashier/tables'));
    const midCreate   = @json(route('cashier.midtrans.create'));
    const midBase     = @json(url('/cashier/midtrans/payments'));
    const snapUrl     = @json($snapUrl);
    const midKey      = @json($midtransKey);
    const csrf        = document.querySelector('meta[name="csrf-token"]').content;

    const STATUS_LABEL = { available: 'Kosong', occupied: 'Terisi', reserved: 'Reservasi' };

    const cart = new Map();   // menu_id => { menu, qty, notes }
    let tables        = @json($tables);
    let selectedTable = null; // id meja yang dipilih
    let reserveMode   = false;
    let method        = 'cash';
    let activeCat     = 'all';
    let keyword       = '';
    let paying        = false;
    let midOrder      = null; // { id, token } pembayaran Midtrans yang sedang berjalan
    let midTimer      = null;

    const $         = (id) => document.getElementById(id);
    const rupiah    = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
    const esc       = (s) => String(s).replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const total     = () => [...cart.values()].reduce((sum, l) => sum + l.menu.price * l.qty, 0);
    const tableById = (id) => tables.find((t) => t.id === id);

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

    function renderTables() {
        const el = $('table-map');
        if (!el) return;

        el.innerHTML = tables.map((t) => {
            const cls = (!reserveMode && selectedTable === t.id) ? 'selected' : t.status;
            return `<button type="button" class="tbl ${cls}" data-table="${t.id}"
                       
                        title="Meja ${esc(t.number)} - ${STATUS_LABEL[t.status]}">${esc(t.number)}</button>`;
        }).join('');

        const chosen = selectedTable ? tableById(selectedTable) : null;
        $('table-selected').textContent = reserveMode
            ? 'Mode reservasi'
            : (chosen ? 'Meja ' + chosen.number : 'Belum dipilih');
    }

    function updateTotals() {
        const t    = total();
        const cash = parseInt($('cash').value || '0', 10);
        const ok   = method === 'cash' ? (t > 0 && cash >= t) : t > 0;

        $('total').textContent  = rupiah(t);
        $('change').textContent = (t > 0 && cash >= t) ? rupiah(cash - t) : '-';
        $('pay-btn').disabled   = paying || !ok;
    }

    // Tampilkan bagian pembayaran sesuai metode yang dipilih
    function applyMethod() {
        $('cash-box').hidden    = method !== 'cash';
        $('noncash-box').hidden = method === 'cash' || method === 'midtrans';
        if ($('qris-box')) $('qris-box').hidden = method !== 'qris';
        $('debit-hint').hidden  = method !== 'debit';
        if ($('midtrans-box')) $('midtrans-box').hidden = method !== 'midtrans';

        document.querySelectorAll('.method').forEach((b) =>
            b.classList.toggle('active', b.dataset.method === method));

        $('pay-btn').textContent = method === 'cash' ? 'Bayar'
            : (method === 'midtrans' ? 'Bayar Non-Tunai' : 'Konfirmasi Pembayaran');
        updateTotals();
    }

    function showError(message) {
        const box = $('error-box');
        box.textContent = message;
        box.hidden = false;
    }

    // Ambil status meja terbaru dari server
    async function refreshTables() {
        try {
            const res = await fetch(tablesUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            // Admin mengubah pengaturan fitur meja
            if (data.enabled !== useTables) {
                if (cart.size === 0) {
                    window.location.reload();
                } else {
                    showError('Pengaturan meja diubah admin. Selesaikan pesanan ini, lalu muat ulang halaman.');
                }
                return;
            }

            tables = data.tables;

            const chosen = selectedTable ? tableById(selectedTable) : null;
                       if (selectedTable && !chosen) {
                selectedTable = null;
                showError('Meja yang dipilih sudah tidak tersedia. Pilih meja lain.');
            }

            renderTables();
        } catch (err) {
            // abaikan, dicoba lagi pada putaran berikutnya
        }
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
        if (midOrder) return;   // pembayaran Midtrans sedang berjalan

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
        if (midOrder) return;

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

    // Peta meja: pilih meja, atau atur reservasi bila mode reservasi aktif
    $('table-map')?.addEventListener('click', async (e) => {
        if (midOrder) return;

        const btn = e.target.closest('[data-table]');
        if (!btn) return;

        const t = tableById(Number(btn.dataset.table));
        if (!t) return;
        // Mode reservasi: tandai atau batalkan reservasi
        if (reserveMode) {
                        if (t.status === 'occupied') {
                showError('Meja sedang terisi, status reservasi tidak bisa diubah.');
                return;
            }
            try {
                const res = await fetch(`${reserveBase}/${t.id}/reservation`, {
                    method: 'PATCH',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });

                if (!res.ok) {
                    const data = await res.json();
                    showError(data.message || 'Gagal mengubah reservasi.');
                }
            } catch (err) {
                showError('Gagal terhubung ke server.');
            }

            refreshTables();
            return;
        }

        // Mode pilih meja
        if (t.status === 'reserved' && selectedTable !== t.id &&
            !confirm(`Meja ${t.number} sudah direservasi. Pilih untuk tamu yang sudah reservasi?`)) {
            return;
        }

        if (t.status === 'occupied' && selectedTable !== t.id &&
            !confirm(`Meja ${t.number} sedang terisi. Tambahkan pesanan baru ke meja ini?`)) {
            return;
        }

        selectedTable = (selectedTable === t.id) ? null : t.id;
        renderTables();
    });

    $('reserve-mode')?.addEventListener('click', (e) => {
        reserveMode = !reserveMode;
        selectedTable = null;

        e.currentTarget.textContent = reserveMode ? 'Atur Reservasi: HIDUP' : 'Atur Reservasi: MATI';
        e.currentTarget.classList.toggle('on', reserveMode);
        $('table-map').classList.toggle('reserve-mode', reserveMode);

        renderTables();
    });

    // Pilih metode pembayaran
    $('methods')?.addEventListener('click', (e) => {
        if (midOrder) return;   // jangan ganti metode saat menunggu pembayaran Midtrans

        const btn = e.target.closest('[data-method]');
        if (!btn) return;
        method = btn.dataset.method;
        applyMethod();
    });

    // Uang diterima (tunai)
    $('cash').addEventListener('input', updateTotals);

    document.querySelectorAll('[data-cash]').forEach((btn) => {
        btn.addEventListener('click', () => {
            $('cash').value = btn.dataset.cash === 'exact' ? total() : btn.dataset.cash;
            updateTotals();
        });
    });

    /* ---------------- Midtrans ---------------- */

    const cartItems = () => [...cart.values()].map((l) => ({
        menu_id: l.menu.id,
        quantity: l.qty,
        notes: l.notes.trim() || null,
    }));

    function loadSnap() {
        return new Promise((resolve, reject) => {
            if (window.snap) return resolve();

            const s = document.createElement('script');
            s.src = snapUrl;
            s.dataset.clientKey = midKey;
            s.onload = resolve;
            s.onerror = () => reject(new Error('snap'));
            document.head.appendChild(s);
        });
    }

    function stopWatch() {
        clearInterval(midTimer);
        midTimer = null;
    }

    function startWatch() {
        if (!midTimer) midTimer = setInterval(checkMidStatus, 3000);
    }

    function resetMid() {
        stopWatch();
        midOrder = null;
        paying = false;
        if ($('mid-wait')) $('mid-wait').hidden = true;
        try { window.snap && window.snap.hide(); } catch (err) { /* abaikan */ }
        updateTotals();
    }

    function openSnap() {
        if (!midOrder || !window.snap) return;

        window.snap.pay(midOrder.token, {
            onSuccess: () => checkMidStatus(),
            onPending: () => checkMidStatus(),
            onError:   () => showError('Pembayaran gagal. Coba lagi atau pilih metode lain.'),
            onClose:   () => { /* panel "Menunggu pembayaran" tetap tampil */ },
        });
    }

    // Tanyakan ke server (yang menanyakan ke Midtrans) apakah sudah lunas
    async function checkMidStatus() {
        if (!midOrder) return;

        try {
            const res = await fetch(`${midBase}/${midOrder.id}/status`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;

            const data = await res.json();

            if (data.status === 'paid' && data.redirect) {
                stopWatch();
                window.location.href = data.redirect;
                return;
            }

            if (['failed', 'expired', 'cancelled'].includes(data.status)) {
                const label = { failed: 'ditolak', expired: 'kedaluwarsa', cancelled: 'dibatalkan' }[data.status];
                resetMid();
                showError('Pembayaran ' + label + '. Silakan coba lagi.');
            }
        } catch (err) {
            // dicoba lagi pada putaran berikutnya
        }
    }

    async function payWithMidtrans() {
        paying = true;
        updateTotals();

        try {
            const res = await fetch(midCreate, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    table_id: useTables ? selectedTable : null,
                    items: cartItems(),
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'Terjadi kesalahan.'];
                showError(messages.join('\n'));
                refreshTables();
                paying = false;
                updateTotals();
                return;
            }

            midOrder = { id: data.order_id, token: data.token };
            $('mid-order-id').textContent = data.order_id;
            $('mid-wait').hidden = false;

            await loadSnap();
            startWatch();
            openSnap();
        } catch (err) {
            showError('Gagal membuka pembayaran Midtrans. Periksa koneksi lalu coba lagi.');

            if (!midOrder) {
                paying = false;
                updateTotals();
            }
        }
    }

    $('mid-reopen')?.addEventListener('click', openSnap);
    $('mid-check')?.addEventListener('click', checkMidStatus);

    $('mid-cancel')?.addEventListener('click', async () => {
        if (!midOrder || !confirm('Batalkan pembayaran ini?')) return;

        try {
            const res = await fetch(`${midBase}/${midOrder.id}/cancel`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            const data = await res.json();

            // Ternyata sudah dibayar: lanjut ke struk
            if (data.status === 'paid' && data.redirect) {
                stopWatch();
                window.location.href = data.redirect;
                return;
            }
        } catch (err) {
            showError('Gagal membatalkan. Coba lagi.');
            return;
        }

        resetMid();
    });

    // Bayar / konfirmasi pembayaran
    $('pay-btn').addEventListener('click', async () => {
        $('error-box').hidden = true;

        if (useTables && !selectedTable) {
            showError('Pilih nomor meja terlebih dahulu.');
            return;
        }

        if (method === 'midtrans') {
            payWithMidtrans();
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
                    table_id: useTables ? selectedTable : null,
                    payment_method: method,
                    cash_received: method === 'cash' ? parseInt($('cash').value || '0', 10) : total(),
                    payment_reference: method === 'cash' ? null : ($('reference').value.trim() || null),
                    items: cartItems(),
                }),
            });

            const data = await res.json();

            if (res.ok) {
                window.location.href = data.redirect;
                return;
            }

            const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'Terjadi kesalahan.'];
            showError(messages.join('\n'));
            refreshTables();
        } catch (err) {
            showError('Gagal terhubung ke server. Coba lagi.');
        }

        paying = false;
        updateTotals();
    });

    renderCats();
    renderMenus();
    renderCart();
    renderTables();
    applyMethod();
    setInterval(refreshTables, 5000);
})();
</script>
@endpush