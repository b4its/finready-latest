<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview - Jurnal Umum</title>
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

        /* Kertas A4 */
        .document-page { background-color: white; width: 210mm; min-height: 297mm; padding: 20mm; box-shadow: 0 4px 12px rgba(0,0,0,0.5); margin-bottom: 20px; }

        /* === SIDEBAR PENGATURAN (RIGHT) === */
        .sidebar { width: 320px; background-color: white; border-left: 1px solid #dadce0; padding: 20px; overflow-y: auto; }
        .sidebar h3 { font-size: 14px; color: #3c4043; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; }
        .form-group select { width: 100%; padding: 8px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; outline: none; }

        /* === STYLING DOKUMEN (TABEL AKUNTANSI) === */
        .doc-header { text-align: center; margin-bottom: 20px; }
        .doc-header h1 { font-size: 14px; margin-bottom: 2px; }
        .doc-header h2 { font-size: 12px; margin-bottom: 2px; text-transform: uppercase; }
        .doc-header p { font-size: 11px; }

        .currency-label { text-align: right; font-size: 10px; margin-bottom: 4px; }
        .journal-table { width: 100%; border-collapse: collapse; font-size: 11px; font-family: 'Times New Roman', Times, serif; }
        .journal-table th, .journal-table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        .journal-table th { text-align: center; font-weight: bold; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .desc-indent { padding-left: 15px !important; }
        .text-red { color: red; font-style: italic; font-size: 10px; }
        .fw-bold { font-weight: bold; }

        /* === PRINT MEDIA QUERIES === */
        @media print {
            .top-bar, .sidebar { display: none !important; }
            body, .main-content, .preview-area { background-color: white; height: auto; overflow: visible; display: block; }
            .preview-area { padding: 0; }
            .document-page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .journal-table { font-size: 12pt; }
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="title">Setelan cetak - Jurnal Umum</div>
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
                    <h2>JURNAL UMUM</h2>
                    <p>{{ $periodeString }}</p>
                </div>
                
                <div class="currency-label">(Dalam Rupiah)</div>
                
                <table class="journal-table">
                    <thead>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="8%">No. Bukti</th>
                            <th width="40%">Description</th>
                            <th width="8%">Pos Ref</th>
                            <th width="17%">Debit</th>
                            <th width="17%">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentDate = '';
                            $currentFaktur = '';
                        @endphp

                        @foreach($formattedJurnals as $item)
                            <tr>
                                <td class="text-center">
                                    @if($currentDate !== $item->tanggal)
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('F d') }}
                                        @php $currentDate = $item->tanggal; @endphp
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($currentFaktur !== $item->no_faktur)
                                        {{ $item->no_faktur }}
                                        @php $currentFaktur = $item->no_faktur; @endphp
                                    @endif
                                </td>
                                <td class="{{ $item->is_debit ? '' : 'desc-indent' }}">{{ $item->akun_name }}</td>
                                <td class="text-center">{{ $item->ref }}</td>
                                <td class="text-right">{{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '' }}</td>
                                <td class="text-right">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '' }}</td>
                            </tr>
                            
                            {{-- Tampilkan baris keterangan tambahan jika ada --}}
                            @if($item->keterangan)
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="text-red">({{ $item->keterangan }})</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endif
                        @endforeach

                        {{-- Baris Total --}}
                        <tr>
                            <td colspan="4" class="text-right fw-bold">TOTAL</td>
                            <td class="text-right fw-bold">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                            <td class="text-right fw-bold">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="sidebar">
            <h3>Cetak</h3>
            
            <div class="form-group">
                <label>Pilihan cetak</label>
                <select>
                    <option>Sheet sekarang</option>
                    <option>Seluruh workbook</option>
                </select>
            </div>

            <div class="form-group">
                <label>Ukuran kertas</label>
                <select>
                    <option>A4 (21 cm x 29.7 cm)</option>
                    <option>Letter (8.5" x 11")</option>
                    <option>Legal (8.5" x 14")</option>
                </select>
            </div>

            <div class="form-group">
                <label>Skala</label>
                <select>
                    <option>Normal (100%)</option>
                    <option>Sesuaikan dengan halaman</option>
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