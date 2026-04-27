<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - Neraca</title>
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
            width: 297mm; 
            min-height: 210mm; 
            padding: 20mm; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.5); 
            margin-bottom: 20px; 
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* === SIDEBAR PENGATURAN === */
        .sidebar { width: 320px; background-color: white; border-left: 1px solid #dadce0; padding: 20px; overflow-y: auto; }
        .sidebar h3 { font-size: 14px; color: #3c4043; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; outline: none; }

        /* === STYLING DOKUMEN === */
        .doc-header { text-align: center; margin-bottom: 10px; width: 100%; max-width: 900px; }
        .doc-header h1 { font-size: 16px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header h2 { font-size: 14px; margin-bottom: 2px; text-transform: uppercase; font-weight: bold; }
        .doc-header p { font-size: 12px; font-weight: bold; margin-bottom: 5px; }

        .currency-label { text-align: center; font-size: 11px; margin-bottom: 15px; font-weight: bold; width: 100%; max-width: 900px; }
        
        .neraca-container {
            width: 100%;
            max-width: 900px;
            display: flex;
            border: 2px solid #000;
        }

        .neraca-col {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .neraca-col:first-child {
            border-right: 2px solid #000;
        }

        .laporan-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 11px; 
            font-family: 'Times New Roman', Times, serif; 
        }
        .laporan-table th, .laporan-table td { border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        .laporan-table tr:last-child td { border-bottom: none; }
        .laporan-table td:last-child { border-right: none; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-red { color: red; }
        .indent-1 { padding-left: 20px !important; }

        /* Mengakali titik-titik spasi */
        .dot-fill {
            display: flex;
            justify-content: space-between;
        }
        .dot-fill::after {
            content: " ......................................................";
            overflow: hidden;
            white-space: nowrap;
            flex: 1;
            margin-left: 5px;
            color: #000;
        }

        /* === PRINT MEDIA QUERIES === */
        @media print {
            @page { size: landscape; margin: 15mm; } 
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
        <div class="title">Setelan cetak - Neraca</div>
        <div class="actions">
            <button class="btn-print" onclick="window.history.back()">BATAL</button>
            <button class="btn-print" onclick="window.print()" style="margin-left: 10px;">BERIKUTNYA (Cetak)</button>
        </div>
    </header>

    <main class="main-content">
        <div class="preview-area">
            <div class="document-page">
                
                <div class="doc-header">
                    <h1>CAFE KOPI NUSANTARA</h1>
                    <h2>NERACA</h2>
                    <p>{{ $periodeString }}</p>
                </div>
                <div class="currency-label">(In Rupiah)</div>
                
                <div class="neraca-container">
                    <div class="neraca-col">
                        <table class="laporan-table" style="height: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="fw-bold">Aktiva Lancar</td>
                                </tr>
                                @foreach($aktivaLancar as $item)
                                <tr>
                                    <td width="70%" class="indent-1 dot-fill"><span>{{ $item->nama }}</span></td>
                                    <td width="30%" class="text-right">{{ $item->saldo < 0 ? '(' . number_format(abs($item->saldo), 0, ',', '.') . ')' : number_format($item->saldo, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-bold dot-fill"><span>Total Aktiva Lancar</span></td>
                                    <td class="text-right fw-bold">{{ number_format($totalAktivaLancar, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                                <tr>
                                    <td colspan="2" class="fw-bold" style="border-top: 1px solid #000;">Aktiva Tetap</td>
                                </tr>
                                @foreach($aktivaTetap as $item)
                                <tr>
                                    <td class="indent-1 dot-fill">
                                        <span class="{{ $item->saldo < 0 ? 'text-red' : '' }}">{{ $item->nama }}</span>
                                    </td>
                                    <td class="text-right {{ $item->saldo < 0 ? 'text-red' : '' }}">
                                        {{ $item->saldo < 0 ? '(' . number_format(abs($item->saldo), 0, ',', '.') . ')' : number_format($item->saldo, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-bold dot-fill"><span>Total Aktiva Tetap</span></td>
                                    <td class="text-right fw-bold">{{ number_format($totalAktivaTetap, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2" style="border: none; height: 100%;"></td></tr>

                                <tr>
                                    <td class="fw-bold dot-fill" style="border-top: 2px solid #000;"><span>Total Aktiva</span></td>
                                    <td class="text-right fw-bold" style="border-top: 2px solid #000;">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="neraca-col">
                        <table class="laporan-table" style="height: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="fw-bold">Hutang Jangka Pendek</td>
                                </tr>
                                @foreach($hutangPendek as $item)
                                <tr>
                                    <td width="70%" class="indent-1 dot-fill"><span>{{ $item->nama }}</span></td>
                                    <td width="30%" class="text-right">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-bold dot-fill"><span>Total Hutang Jangka Pendek</span></td>
                                    <td class="text-right fw-bold">{{ number_format($totalHutangPendek, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                                <tr>
                                    <td colspan="2" class="fw-bold" style="border-top: 1px solid #000;">Hutang Jangka Panjang</td>
                                </tr>
                                @foreach($hutangPanjang as $item)
                                <tr>
                                    <td class="indent-1 dot-fill"><span>{{ $item->nama }}</span></td>
                                    <td class="text-right">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                @if(count($hutangPanjang) == 0)
                                <tr>
                                    <td class="indent-1 dot-fill"><span>-</span></td>
                                    <td class="text-right">-</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold dot-fill"><span>Total Hutang Jangka Panjang</span></td>
                                    <td class="text-right fw-bold">{{ count($hutangPanjang) > 0 ? number_format($totalHutangPanjang, 0, ',', '.') : '-' }}</td>
                                </tr>

                                <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                                <tr>
                                    <td class="fw-bold dot-fill" style="border-top: 1px solid #000;"><span>Total Hutang</span></td>
                                    <td class="text-right fw-bold" style="border-top: 1px solid #000;">{{ number_format($totalHutang, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2" style="border: none; height: 15px;"></td></tr>

                                <tr>
                                    <td colspan="2" class="fw-bold" style="border-top: 1px solid #000;">Ekuitas</td>
                                </tr>
                                <tr>
                                    <td class="indent-1 dot-fill"><span>Modal Akhir</span></td>
                                    <td class="text-right">{{ number_format($modalAkhir, 0, ',', '.') }}</td>
                                </tr>
                                
                                <tr><td colspan="2" style="border: none; height: 100%;"></td></tr>

                                <tr>
                                    <td class="fw-bold dot-fill" style="border-top: 2px solid #000;"><span>Total Hutang & Ekuitas</span></td>
                                    <td class="text-right fw-bold" style="border-top: 2px solid #000;">{{ number_format($totalPasiva, 0, ',', '.') }}</td>
                                </tr>
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
                    <option>Tabloid (43.2 cm x 27.9 cm)</option>
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
</body>
</html>