<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - NSSP</title>
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

        /* === SIDEBAR PENGATURAN === */
        .sidebar { width: 320px; background-color: white; border-left: 1px solid #dadce0; padding: 20px; overflow-y: auto; }
        .sidebar h3 { font-size: 14px; color: #3c4043; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; outline: none; }

        /* === STYLING DOKUMEN === */
        .doc-header { text-align: center; margin-bottom: 20px; }
        .doc-header h1 { font-size: 14px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header h2 { font-size: 13px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header p { font-size: 11px; font-weight: bold; margin-bottom: 5px; }

        .currency-label { text-align: center; font-size: 11px; margin-bottom: 10px; font-weight: normal; }
        
        .journal-table { width: 100%; border-collapse: collapse; font-size: 11px; font-family: 'Times New Roman', Times, serif; }
        .journal-table th, .journal-table td { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; }
        .journal-table th { text-align: center; font-weight: bold; text-transform: uppercase; background-color: #fff; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        
        .rp-box { display: flex; justify-content: space-between; }
        .rp-box span:first-child { float: left; margin-right: 5px; }

        /* === PRINT MEDIA QUERIES === */
        @media print {
            @page { size: landscape; margin: 10mm; } 
            .top-bar, .sidebar { display: none !important; }
            body, .main-content, .preview-area { background-color: white; height: auto; overflow: visible; display: block; }
            .preview-area { padding: 0; }
            .document-page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .journal-table { font-size: 10pt; }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="title">Setelan cetak - NSSP</div>
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
                    <h2>NERACA SALDO SETELAH PENYESUAIAN</h2>
                    <p>{{ $periodeString }}</p>
                    <div class="currency-label">(in rupiah)</div>
                </div>
                
                <table class="journal-table">
                    <thead>
                        <tr>
                            <th width="6%">KODE</th>
                            <th width="22%">NAMA AKUN</th>
                            <th width="12%">DEBET</th>
                            <th width="12%">KREDIT</th>
                            <th width="12%">DEBET JP</th>
                            <th width="12%">KREDIT JP</th>
                            <th width="12%">DEBET SETELAH DISESUAIKAN</th>
                            <th width="12%">KREDIT SETELAH DISESUAIKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($akunKeuangans as $akun)
                        <tr>
                            <td class="text-center">{{ $akun->kode }}</td>
                            <td class="text-left">{{ $akun->nama }}</td>
                            
                            <td class="text-right">
                                @if($akun->ns_debet > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->ns_debet, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($akun->ns_kredit > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->ns_kredit, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                            
                            <td class="text-right">
                                @if($akun->jp_debet > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->jp_debet, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($akun->jp_kredit > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->jp_kredit, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                            
                            <td class="text-right">
                                @if($akun->nssd_debet > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->nssd_debet, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($akun->nssd_kredit > 0)
                                    <div class="rp-box"><span>Rp</span><span>{{ number_format($akun->nssd_kredit, 0, ',', '.') }}</span></div>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="2" class="text-center fw-bold">TOTAL</td>
                            
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->ns_debet, 0, ',', '.') }}</span></div>
                            </td>
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->ns_kredit, 0, ',', '.') }}</span></div>
                            </td>
                            
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->jp_debet, 0, ',', '.') }}</span></div>
                            </td>
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->jp_kredit, 0, ',', '.') }}</span></div>
                            </td>
                            
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->nssd_debet, 0, ',', '.') }}</span></div>
                            </td>
                            <td class="text-right fw-bold">
                                <div class="rp-box"><span>Rp</span><span>{{ number_format($total->nssd_kredit, 0, ',', '.') }}</span></div>
                            </td>
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
                    <option>Lanskap (Landscape)</option>
                    <option>Potret (Portrait)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ukuran kertas</label>
                <select>
                    <option>A4 (29.7 cm x 21 cm)</option>
                    <option>Legal (14" x 8.5")</option>
                </select>
            </div>
            <div class="form-group">
                <label>Skala</label>
                <select>
                    <option>Sesuaikan halaman</option>
                    <option>Normal (100%)</option>
                </select>
            </div>
        </aside>
    </main>
        <script>
        window.print();
    </script>
</body>
</html>