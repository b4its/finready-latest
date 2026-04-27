<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - Jurnal Penyesuaian</title>
    <style>
        /* === RESET & VARIABEL === */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; flex-direction: column; height: 100vh; background-color: #f1f3f4; overflow: hidden; }

        /* === TOP NAVBAR === */
        .top-bar { background-color: #323639; color: white; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); z-index: 10; }
        .top-bar .title { font-size: 14px; font-weight: normal; }
        .top-bar .actions .btn-print { background-color: #1a73e8; color: white; border: none; padding: 8px 24px; border-radius: 4px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .top-bar .actions .btn-print:hover { background-color: #1557b0; }

        /* === MAIN LAYOUT === */
        .main-content { display: flex; flex: 1; overflow: hidden; }
        .preview-area { flex: 1; background-color: #525659; display: flex; justify-content: center; overflow-y: auto; padding: 40px 20px; }

        /* Kertas A4 Landscape */
        .document-page { 
            background-color: white; 
            width: 297mm; /* Lebar A4 Landscape */
            min-height: 210mm; /* Tinggi A4 Landscape */
            padding: 15mm; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.5); 
            margin-bottom: 20px; 
        }

        /* === SIDEBAR PENGATURAN (RIGHT) === */
        .sidebar { width: 320px; background-color: white; border-left: 1px solid #dadce0; padding: 20px; overflow-y: auto; }
        .sidebar h3 { font-size: 14px; color: #3c4043; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; outline: none; }

        /* === STYLING DOKUMEN (TABEL AKUNTANSI) === */
        .doc-header { text-align: center; margin-bottom: 20px; }
        .doc-header h1 { font-size: 14px; margin-bottom: 2px; text-transform: uppercase; }
        .doc-header h2 { font-size: 12px; margin-bottom: 2px; text-transform: uppercase; font-weight: normal;}
        .doc-header p { font-size: 11px; font-weight: bold;}

        /* Flexbox Layout untuk Dua Tabel */
        .layout-container { display: flex; gap: 30px; align-items: flex-start; }
        .main-table-wrapper { flex: 2.5; } 
        .recap-table-wrapper { flex: 1; } 

        .currency-label { text-align: right; font-size: 10px; margin-bottom: 4px; font-weight: bold; }
        
        .journal-table { width: 100%; border-collapse: collapse; font-size: 11px; font-family: 'Times New Roman', Times, serif; }
        .journal-table th, .journal-table td { border: 1px solid #000; padding: 4px 6px; vertical-align: middle; }
        .journal-table th { text-align: center; font-weight: bold; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .desc-indent { padding-left: 20px !important; }
        .text-red { color: red; font-style: italic; font-size: 10px; }

        /* === PRINT MEDIA QUERIES === */
        @media print {
            @page { size: landscape; margin: 10mm; } 
            .top-bar, .sidebar { display: none !important; }
            body, .main-content, .preview-area { background-color: white; height: auto; overflow: visible; display: block; }
            .preview-area { padding: 0; }
            .document-page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .journal-table { font-size: 11pt; }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="title">Setelan cetak - Jurnal Penyesuaian</div>
        <div class="actions">
            <button class="btn-print" onclick="window.history.back()">BATAL</button>
            <button class="btn-print" onclick="window.print()" style="margin-left: 10px;">BERIKUTNYA (Cetak)</button>
        </div>
    </header>

    <main class="main-content">
        <div class="preview-area">
            <div class="document-page">
                
                <div class="doc-header">
                    <h1>Cafe Kopi Nusantara</h1>
                    <h2>JURNAL PENYESUAIAN</h2>
                    <p>{{ $periodeString }}</p>
                </div>
                
                <div class="layout-container">
                    
                    <div class="main-table-wrapper">
                        <div class="currency-label">(Dalam Rupiah)</div>
                        <table class="journal-table">
                            <thead>
                                <tr>
                                    <th colspan="2" width="12%">Date</th>
                                    <th width="10%">No Bukti</th>
                                    <th width="38%">Description</th>
                                    <th width="8%">Pos Ref</th>
                                    <th width="16%">Debit</th>
                                    <th width="16%">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentMonth = '';
                                @endphp

                                @foreach($formattedJurnals as $item)
                                    @php
                                        $tanggal = \Carbon\Carbon::parse($item->tanggal)->format('d');
                                        $bulanItem = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F');
                                    @endphp
                                    <tr>
                                        <td class="text-center" style="border-right: none; font-weight: bold; width: 6%;">
                                            @if($currentMonth !== $bulanItem)
                                                {{ $bulanItem }}
                                                @php $currentMonth = $bulanItem; @endphp
                                            @endif
                                        </td>
                                        <td class="text-center" style="border-left: none; width: 6%;">{{ $tanggal }}</td>
                                        
                                        <td class="text-center">{{ $item->no_faktur ?? '-' }}</td>
                                        <td class="{{ $item->is_debit ? 'fw-bold' : 'desc-indent' }}">
                                            {{ $item->akun_name }}
                                        </td>
                                        <td class="text-center">{{ $item->ref }}</td>
                                        <td class="text-right">{{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '' }}</td>
                                        <td class="text-right">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '' }}</td>
                                    </tr>
                                    
                                    @if($item->keterangan)
                                    <tr>
                                        <td style="border-right: none;"></td>
                                        <td style="border-left: none;"></td>
                                        <td></td>
                                        <td class="desc-indent">
                                            <span class="text-red">({{ $item->keterangan }})</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @endif
                                @endforeach

                                <tr>
                                    <td colspan="5" class="text-center fw-bold">TOTAL ..............................................................</td>
                                    <td class="text-right fw-bold">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                                    <td class="text-right fw-bold">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="recap-table-wrapper">
                        <div style="font-size: 11px; font-weight: bold; margin-bottom: 5px; font-family: 'Times New Roman', Times, serif;">
                            <u>Rekapitulasi:</u>
                        </div>
                        <table class="journal-table">
                            <thead>
                                <tr>
                                    <th width="30%">No Akun</th>
                                    <th width="35%">Debet</th>
                                    <th width="35%">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekap as $r)
                                <tr>
                                    <td class="text-center">{{ $r['no_referensi'] }}</td>
                                    <td class="text-right">
                                        @if($r['debit'] > 0)
                                            <span style="float: left;">Rp</span> {{ number_format($r['debit'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($r['kredit'] > 0)
                                            <span style="float: left;">Rp</span> {{ number_format($r['kredit'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <aside class="sidebar">
            <h3>Cetak</h3>
            
            <div class="form-group">
                <label>Orientasi halaman</label>
                <select>
                    <option>Lanskap (Landscape)</option>
                    <option>Potret (Portrait)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Ukuran kertas</label>
                <select>
                    <option>A4 (29.7 cm x 21 cm)</option>
                    <option>Letter (11" x 8.5")</option>
                    <option>Legal (14" x 8.5")</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Margin</label>
                <select>
                    <option>Normal</option>
                    <option>Sempit</option>
                    <option>Lebar</option>
                </select>
            </div>
        </aside>
    </main>

</body>
</html>