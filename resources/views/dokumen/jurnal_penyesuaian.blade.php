<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal Penyesuaian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f5f7; color: #000; font-size: 11px; }

        .page-container { width: 100%; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        
        .paper {
            background-color: #fff; width: 27.9cm; min-height: 21.6cm; /* Letter Landscape */
            padding: 1.5cm; box-shadow: 0 4px 10px rgba(0,0,0,0.1); position: relative;
        }

        .btn-print { margin-bottom: 20px; padding: 10px 24px; background-color: #4285f4; color: white; border: none; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer; }
        .btn-print:hover { background-color: #3367d6; }

        .header-title { text-align: center; font-weight: bold; font-size: 13px; line-height: 1.4; margin-bottom: 5px; text-transform: uppercase; }
        .currency-label { text-align: right; font-weight: bold; font-size: 10px; margin-bottom: 5px; }

        /* Flexbox Layout untuk Dua Tabel */
        .layout-container { display: flex; gap: 20px; align-items: flex-start; }
        .main-table-wrapper { flex: 3; } /* Tabel Jurnal (Lebih Lebar) */
        .recap-table-wrapper { flex: 1; } /* Tabel Rekap (Lebih Kecil) */

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        th { text-align: center; font-weight: bold; vertical-align: middle; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .note-red { color: red; font-size: 9px; font-style: italic; display: block; margin-top: 2px; }

        @media print {
            @page { size: letter landscape; margin: 1cm; }
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
                CAFE KOPI NUSANTARA<br>
                JURNAL PENYESUAIAN<br>
                {{ $periodeString }}
            </div>

            <div class="layout-container">
                
                <div class="main-table-wrapper">
                    <div class="currency-label">(Dalam Rupiah)</div>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2" style="width: 10%;">Date</th>
                                <th style="width: 8%;">No Bukti</th>
                                <th style="width: 40%;">Description</th>
                                <th style="width: 10%;">Pos Ref</th>
                                <th style="width: 16%;">Debit</th>
                                <th style="width: 16%;">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jurnals as $index => $jurnal)
                                @foreach($jurnal->details as $detail)
                                    @php 
                                        $isDebet = strtoupper(trim($detail->is_debet)) === 'D'; 
                                        $tanggal = \Carbon\Carbon::parse($jurnal->periode)->format('d');
                                    @endphp
                                    <tr>
                                        @if($loop->parent->first && $loop->first)
                                            <td class="text-center" style="border-right: none;">{{ substr($namaBulan, 0, 3) }}</td>
                                        @else
                                            <td style="border-right: none;"></td>
                                        @endif
                                        
                                        <td class="text-center" style="border-left: none;">{{ $tanggal }}</td>
                                        <td class="text-center">{{ $detail->no_faktur ?? '-' }}</td>
                                        
                                        <td style="{{ !$isDebet ? 'padding-left: 20px;' : 'font-weight: bold;' }}">
                                            {{ $jurnal->akunKeuangan->name ?? '-' }}
                                            @if($detail->keterangan)
                                                <span class="note-red">({{ $detail->keterangan }})</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">{{ $jurnal->akunKeuangan->no_referensi ?? '-' }}</td>
                                        
                                        <td class="text-right">{{ $isDebet ? number_format($detail->amount, 0, ',', '.') : '' }}</td>
                                        <td class="text-right">{{ !$isDebet ? number_format($detail->amount, 0, ',', '.') : '' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-center">TOTAL ..............................................................</th>
                                <th class="text-right">{{ number_format($totalDebit, 0, ',', '.') }}</th>
                                <th class="text-right">{{ number_format($totalKredit, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="recap-table-wrapper">
                    <div style="font-size: 10px; font-weight: bold; margin-top: 15px;">Rekapitulasi :</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30%;">No Akun</th>
                                <th style="width: 35%;">Debet</th>
                                <th style="width: 35%;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $r)
                            <tr>
                                <td class="text-center">{{ $r['no_referensi'] }}</td>
                                <td class="text-right">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '' }}</td>
                                <td class="text-right">{{ $r['kredit'] > 0 ? number_format($r['kredit'], 0, ',', '.') : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div> </div>
    </div>

</body>
</html>