{{-- struck pembelian --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: #e5e7eb; margin: 0; padding: 1rem; }
        .receipt { width: 80mm; margin: 0 auto; background: #fff; padding: 6mm; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: bold; }
        .row { display: flex; justify-content: space-between; gap: .5rem; }
        .sep { border-top: 1px dashed #000; margin: 6px 0; }
        .item { margin-bottom: 4px; }
        .bold { font-weight: bold; }
        .actions { width: 80mm; margin: 1rem auto 0; display: flex; gap: .5rem; font-family: sans-serif; }
        .actions a, .actions button { flex: 1; padding: .7rem; text-align: center; border: 0; border-radius: .5rem;
                                      font-size: 14px; cursor: pointer; text-decoration: none; color: #fff; }
        .btn-print { background: #ea580c; }
        .btn-new { background: #374151; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .receipt { width: 100%; padding: 0; }
            @page { size: 80mm auto; margin: 3mm; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <div class="title">DAPUR BABEH</div>
            <div>Struk Pembayaran</div>
        </div>

        <div class="sep"></div>

        <div class="row"><span>No</span><span>{{ $transaction->invoice_number }}</span></div>
        <div class="row"><span>Tanggal</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span>Kasir</span><span>{{ $transaction->cashier->name }}</span></div>
        <div class="row">
            <span>Meja</span>
            <span>{{ $transaction->table ? $transaction->table->table_number : 'Takeaway' }}</span>
        </div>

        <div class="sep"></div>

        @foreach ($transaction->items as $item)
            <div class="item">
                <div>{{ $item->menu->name }}</div>
                <div class="row">
                    <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($item->notes)
                    <div>* {{ $item->notes }}</div>
                @endif
            </div>
        @endforeach

        <div class="sep"></div>

        <div class="row bold"><span>TOTAL</span><span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span></div>
        <div class="row"><span>Tunai</span><span>{{ number_format($transaction->cash_received, 0, ',', '.') }}</span></div>
        <div class="row"><span>Kembali</span><span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>

        <div class="sep"></div>

        <div class="center">Terima kasih atas kunjungan Anda</div>
    </div>

    <div class="actions no-print">
        <button type="button" class="btn-print" onclick="window.print()">Cetak Struk</button>
        <a href="{{ route('cashier.pos') }}" class="btn-new">Transaksi Baru</a>
    </div>
</body>
</html>