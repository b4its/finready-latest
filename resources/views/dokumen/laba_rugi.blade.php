<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - Laporan Laba Rugi</title>
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

        /* Kertas A4 Portrait */
        .document-page { 
            background-color: white; 
            width: 210mm; 
            min-height: 297mm; 
            padding: 20mm; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.5); 
            margin-bottom: 20px; 
        }

        /* === SIDEBAR PENGATURAN === */
        .sidebar { width: 320px; background-color: white; border-left: 1px solid #dadce0; padding: 20px; overflow-y: auto; }
        .sidebar h3 { font-size: 14px; color: #3c4043; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; outline: none; }

        /* === STYLING DOKUMEN === */
        .doc-header { text-align: center; margin-bottom: 30px; }
        .doc-header h1 { font-size: 15px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header h2 { font-size: 14px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header p { font-size: 12px; font-weight: bold; margin-bottom: 5px; }

        .currency-label { text-align: right; font-size: 11px; margin-bottom: 5px; font-weight: bold; }
        
        .laporan-table { width: 100%; border-collapse: collapse; font-size: 12px; font-family: 'Times New Roman', Times, serif; }
        .laporan-table th, .laporan-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .indent-1 { padding-left: 20px !important; }

        /* Titik-titik otomatis */
        .dotted-cell {
            position: relative;
        }
        .dotted-cell::after {
            content: "..................................................";
            position: absolute;
            left: 5px;
            bottom: 6px;
            color: #000;
            overflow: hidden;
            white-space: nowrap;
            width: 90%;
        }

        /* === PRINT MEDIA QUERIES === */
        @media print {
            @page { size: portrait; margin: 15mm; } 
            .top-bar, .sidebar { display: none !important; }
            body, .main-content, .preview-area { background-color: white; height: auto; overflow: visible; display: block; }
            .preview-area { padding: 0; }
            .document-page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .laporan-table { font-size: 11pt; }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="title">Setelan cetak - Laporan Laba Rugi</div>
        <div class="actions">
            <button class="btn-print" onclick="window.history.back()">BATAL</button>
            <button class="btn-print" onclick="window.print()" style="margin-left: 10px;">BERIKUTNYA (Cetak)</button>
        </div>
    </header>

    <main class="main-content">
        <div class="preview-area">
            <div class="document-page">
                
                <div class="doc-header">
                    <h1>                {{ $detailProfilUMKM->name ?? "Cafe Kopi Nusantara" }}</h1>
                    <h2>LAPORAN LABA RUGI</h2>
                    <p>Untuk periode yang berakhir {{ $periodeString }}</p>
                </div>
                
                <div class="currency-label">(In Rupiah)</div>
                
                <table class="laporan-table">
                    <tbody>
                        <tr>
                            <td colspan="4" class="fw-bold">Pendapatan :</td>
                        </tr>
                        @foreach($pendapatans as $item)
                        <tr>
                            <td width="35%" class="indent-1">{{ $item->nama }}</td>
                            <td width="20%" class="dotted-cell"></td>
                            <td width="20%"></td>
                            <td width="25%" class="text-right">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="fw-bold">Total Pendapatan</td>
                            <td class="text-right fw-bold">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td colspan="4" class="fw-bold">Beban Pokok - Bahan Baku Terpakai</td>
                        </tr>
                        @foreach($bebanPokoks as $item)
                        <tr>
                            <td class="indent-1">{{ $item->nama }}</td>
                            <td class="dotted-cell"></td>
                            <td class="text-right">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="fw-bold">Total Bahan Baku Terpakai</td>
                            <td class="text-right fw-bold">{{ number_format($totalBebanPokok, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td colspan="3" class="fw-bold text-center">Laba Kotor</td>
                            <td class="text-right fw-bold">{{ number_format($labaKotor, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td colspan="4" class="fw-bold">Beban Operasional</td>
                        </tr>
                        @foreach($bebanOperasionals as $item)
                        <tr>
                            <td class="indent-1">{{ $item->nama }}</td>
                            <td class="dotted-cell"></td>
                            <td class="text-right">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="fw-bold">Total Beban Operasional</td>
                            <td class="text-right fw-bold">{{ number_format($totalBebanOperasional, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td colspan="3" class="fw-bold text-center">Laba Bersih</td>
                            <td class="text-right fw-bold">{{ number_format($labaBersih, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="sidebar">
            <h3>Cetak</h3>
            <div class="form-group">
                <label>Orientasi halaman</label>
                <select>
                    <option>Potret (Portrait)</option>
                    <option>Lanskap (Landscape)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ukuran kertas</label>
                <select>
                    <option>A4 (21 cm x 29.7 cm)</option>
                    <option>Legal (8.5" x 14")</option>
                </select>
            </div>
            <div class="form-group">
                <label>Skala</label>
                <select>
                    <option>Normal (100%)</option>
                    <option>Sesuaikan halaman</option>
                </select>
            </div>
        </aside>
    </main>
        <script>
        window.print();
    </script>
</body>
</html>