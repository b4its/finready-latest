<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Neraca Saldo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f5f7; color: #000; font-size: 11px; }

        .page-container { width: 100%; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        
        .paper {
            background-color: #fff; width: 21.6cm; min-height: 27.9cm; /* Ukuran Letter Portrait */
            padding: 2cm; box-shadow: 0 4px 10px rgba(0,0,0,0.1); position: relative;
        }

        .btn-print {
            margin-bottom: 20px; padding: 10px 24px; background-color: #4285f4; color: white;
            border: none; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background-color: #3367d6; }

        .header-title { text-align: center; font-weight: bold; font-size: 13px; line-height: 1.4; margin-bottom: 5px; }
        .currency-label { text-align: right; font-weight: bold; font-size: 11px; margin-bottom: 5px; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { text-align: center; font-weight: bold; vertical-align: middle; }
        
        .text-center { text-align: center; }
        
        /* Flexbox untuk memisahkan Rp dan Angka */
        .uang { display: flex; justify-content: space-between; align-items: center; }
        .uang span:first-child { margin-right: 10px; }

        @media print {
            @page { size: letter portrait; margin: 1.5cm; }
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
                NERACA SALDO<br>
                {{ $periodeString }}
            </div>
            <div class="currency-label">(In rupiah)</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">KODE</th>
                        <th style="width: 48%;">NAMA AKUN</th>
                        <th style="width: 20%;">DEBET</th>
                        <th style="width: 20%;">KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($akunKeuangans as $akun)
                    <tr>
                        <td class="text-center">{{ $akun->no_referensi }}</td>
                        <td>{{ $akun->nama_akun }}</td>
                        
                        <td>
                            @if($akun->debet > 0)
                                <div class="uang">
                                    <span>Rp</span>
                                    <span>{{ number_format($akun->debet, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </td>
                        
                        <td>
                            @if($akun->kredit > 0)
                                <div class="uang">
                                    <span>Rp</span>
                                    <span>{{ number_format($akun->kredit, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-center">TOTAL</th>
                        <th>
                            <div class="uang">
                                <span>Rp</span>
                                <span>{{ number_format($totalDebet, 0, ',', '.') }}</span>
                            </div>
                        </th>
                        <th>
                            <div class="uang">
                                <span>Rp</span>
                                <span>{{ number_format($totalKredit, 0, ',', '.') }}</span>
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>