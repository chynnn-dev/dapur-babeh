<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 15px; line-height: 1.4; color: #000; background: #e5e7eb; margin: 0; padding: 1rem; }
        .receipt { width: 80mm; margin: 0 auto; background: #fff; padding: 5mm; overflow-wrap: anywhere; }
        .center { text-align: center; }
        .title { font-size: 24px; font-weight: bold; letter-spacing: .04em; line-height: 1.2; }
        .subtitle { font-size: 15px; margin-top: 2px; }
        .row { display: flex; justify-content: space-between; gap: .6rem; margin: 2px 0; }
        .row span:last-child { text-align: right; }
        .sep { border-top: 2px dashed #000; margin: 10px 0; }
        .item { margin-bottom: 8px; }
        .item-name { font-weight: bold; font-size: 16px; }
        .note { font-size: 14px; }
        .bold { font-weight: bold; }
        .total { font-size: 19px; font-weight: bold; margin: 4px 0; }
        .thanks { font-size: 15px; margin-top: 4px; }

        .actions { width: 80mm; margin: 1rem auto 0; display: flex; gap: .5rem; font-family: sans-serif; }
        .actions a, .actions button { flex: 1; padding: .85rem; text-align: center; border: 0; border-radius: .5rem;
                                      font-size: 16px; font-weight: bold; cursor: pointer; text-decoration: none; color: #fff; }
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
            <div class="subtitle">Struk Pembayaran</div>
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
                <div class="item-name">{{ $item->menu->name }}</div>
                <div class="row">
                    <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($item->notes)
                    <div class="note">* {{ $item->notes }}</div>
                @endif
            </div>
        @endforeach

        <div class="sep"></div>

        <div class="row total"><span>TOTAL</span><span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span></div>

        @php $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit']; @endphp

        <div class="row"><span>Metode</span><span>{{ $transaction->payment_detail ?: ($methodLabels[$transaction->payment_method] ?? $transaction->payment_method) }}</span></div>

        @if ($transaction->payment_method === 'cash')
            <div class="row"><span>Tunai</span><span>{{ number_format($transaction->cash_received, 0, ',', '.') }}</span></div>
            <div class="row"><span>Kembali</span><span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>
        @elseif ($transaction->payment_reference)
            <div class="row"><span>Ref</span><span>{{ $transaction->payment_reference }}</span></div>
        @endif

        <div class="sep"></div>

        <div class="center thanks">Terima kasih atas kunjungan Anda</div>
    </div>

    <div class="actions no-print">
        <button type="button" class="btn-print" onclick="window.print()">Cetak Struk</button>
        <a href="{{ route('cashier.pos') }}" class="btn-new">Transaksi Baru</a>
    </div>
</body>
</html>