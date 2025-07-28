<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            padding: 20px;
            color: #333;
        }
        .title {
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 2px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .table th, .table td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="title">
        <h2>Resi Pembayaran Resep</h2>
    </div>
    <div class="subtitle">
        RS Delta Surya Sidoarjo – {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="info">
        <p><strong>Nama Pasien:</strong> {{ $pemeriksaan->nama_pasien }}</p>
        <p><strong>Tanggal Pemeriksaan:</strong> {{ \Carbon\Carbon::parse($pemeriksaan->waktu_pemeriksaan)->format('d M Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Obat</th>
                <th>Dosis</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($pemeriksaan->reseps as $item)
                @php
                    $subtotal = $item->quantity * $item->prices;
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $item->medicine_name }}</td>
                    <td>{{ $item->dosage }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->prices) }}</td>
                    <td>Rp {{ number_format($subtotal) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total Bayar</strong></td>
                <td><strong>Rp {{ number_format($grandTotal) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
        <p>Tanggal Cetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

</body>
</html>
