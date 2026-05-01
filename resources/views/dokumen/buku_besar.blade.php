<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Buku Besar Umum</title>
    <style>
        /* Reset & Base Styles */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f5f7; color: #000; font-size: 11px; }

        /* Layout untuk Layar Browser */
        .page-container {
            width: 100%; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 20px;
        }
        .paper {
            background-color: #fff; width: 27.9cm; min-height: 21.6cm; padding: 2cm;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); position: relative;
        }

        /* Tombol Cetak */
        .btn-print {
            margin-bottom: 20px; padding: 10px 24px; background-color: #4285f4; color: white;
            border: none; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background-color: #3367d6; }

        /* Typography & Header */
        .header-title { text-align: center; font-weight: bold; font-size: 13px; line-height: 1.4; margin-bottom: 30px; }

        /* Table Wrapper & Info Akun */
        .table-wrapper { width: 100%; margin-bottom: 40px; page-break-inside: avoid; }
        .account-info { display: flex; justify-content: space-between; font-weight: bold; font-size: 11px; margin-bottom: 4px; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { text-align: center; font-weight: bold; vertical-align: middle; }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .border-none { border: none !important; }
        .border-right-none { border-right: none !important; }
        .border-left-none { border-left: none !important; }

        /* Pengaturan Cetak (Print Preview) */
        @media print {
            @page { size: letter landscape; margin: 1.5cm; }
            body { background-color: #fff; }
            .page-container { padding: 0; display: block; }
            .paper { width: 100%; min-height: auto; padding: 0; box-shadow: none; margin: 0 auto; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="page-container">
        <button class="btn-print" onclick="window.print()">Cetak Halaman</button>

        <div class="paper">
            
            <div class="header-title">
                                {{ $detailProfilUMKM->name ?? "Cafe Kopi Nusantara" }}<br>
                BUKU BESAR UMUM<br>
                {{ $periodeString }}<br>
                (In Rupiah)
            </div>

            @foreach($akunKeuangans as $akun)
            <div class="table-wrapper">
                <div class="account-info">
                    <span>NAMA : {{ $akun->nama_akun }}</span>
                    <span>No.: {{ $akun->no_referensi }}</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th colspan="2" rowspan="2" style="width: 8%;">TANGGAL</th>
                            <th rowspan="2" style="width: 25%;">KETERANGAN</th>
                            <th rowspan="2" style="width: 7%;">REF</th>
                            <th rowspan="2" style="width: 15%;">DEBIT</th>
                            <th rowspan="2" style="width: 15%;">KREDIT</th>
                            <th colspan="2" style="width: 30%;">SALDO</th>
                        </tr>
                        <tr>
                            <th>DEBIT</th>
                            <th>KREDIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center border-right-none">{{ $tahun }}<br>{{ substr($namaBulan, 0, 3) }}</td>
                            <td class="text-center border-left-none">1</td>
                            <td>Saldo Awal</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            @if($akun->saldo_awal >= 0)
                                <td class="text-right">{{ number_format($akun->saldo_awal, 0, ',', '.') }}</td>
                                <td></td>
                            @else
                                <td></td>
                                <td class="text-right">{{ number_format(abs($akun->saldo_awal), 0, ',', '.') }}</td>
                            @endif
                        </tr>

                        @foreach($akun->transaksi as $trx)
                        <tr>
                            <td class="border-right-none"></td>
                            <td class="text-center border-left-none">{{ $trx['tanggal'] }}</td>
                            <td>{{ $trx['keterangan'] }}</td>
                            <td class="text-center">{{ $trx['ref'] }}</td>
                            
                            <td class="text-right">{{ $trx['debit'] > 0 ? number_format($trx['debit'], 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ $trx['kredit'] > 0 ? number_format($trx['kredit'], 0, ',', '.') : '' }}</td>
                            
                            @if($trx['saldo'] >= 0)
                                <td class="text-right">{{ number_format($trx['saldo'], 0, ',', '.') }}</td>
                                <td></td>
                            @else
                                <td></td>
                                <td class="text-right">{{ number_format(abs($trx['saldo']), 0, ',', '.') }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-center">Saldo akhir</th>
                            <th class="text-right">{{ number_format($akun->total_debit, 0, ',', '.') }}</th>
                            <th class="text-right">{{ number_format($akun->total_kredit, 0, ',', '.') }}</th>
                            
                            @if($akun->saldo_akhir >= 0)
                                <th class="text-right">{{ number_format($akun->saldo_akhir, 0, ',', '.') }}</th>
                                <th></th>
                            @else
                                <th></th>
                                <th class="text-right">{{ number_format(abs($akun->saldo_akhir), 0, ',', '.') }}</th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endforeach

        </div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>