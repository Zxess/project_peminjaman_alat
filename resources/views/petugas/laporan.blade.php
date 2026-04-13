<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Alat - Laboratorium</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style> 
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', 'Segoe UI', 'Calibri', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8e8e8;
            padding: 30px;
            font-size: 14px;
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        /* Header Professional */
        .report-header {
            padding: 40px 40px 20px 40px;
            text-align: center;
            border-bottom: 2px solid #333;
        }

        .report-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a1a;
            letter-spacing: 1px;
        }

        .report-header .subtitle {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .report-header .period {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }

        /* Info Section */
        .info-section {
            padding: 20px 40px;
            background: #fafafa;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-card {
            flex: 1;
            min-width: 180px;
        }

        .info-card label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-card .value {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .info-card .value i {
            margin-right: 8px;
            color: #666;
            width: 16px;
        }

        /* Table Styles */
        .table-wrapper {
            padding: 20px 40px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .table thead th {
            background: #2c2c2c;
            color: white;
            font-weight: 600;
            padding: 12px 10px;
            border: 1px solid #444;
            text-align: center;
            font-size: 13px;
        }

        .table tbody td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f5f5f5;
        }

        /* Status Badge - Netral */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .status-dipinjam {
            background: #e8e8e8;
            color: #333;
            border-left: 3px solid #666;
        }

        .status-dikembalikan {
            background: #e8e8e8;
            color: #333;
            border-left: 3px solid #333;
        }

        .status-terlambat {
            background: #e8e8e8;
            color: #cc0000;
            border-left: 3px solid #cc0000;
        }

        .status-menunggu {
            background: #e8e8e8;
            color: #666;
            border-left: 3px solid #999;
        }

        /* Summary Section */
        .summary-section {
            margin: 0 40px 20px 40px;
            padding: 20px;
            background: #fafafa;
            border: 1px solid #ddd;
        }

        .summary-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a1a1a;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            display: block;
            font-family: 'Times New Roman', serif;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Signature Section */
        .signature-section {
            margin: 30px 40px 40px 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 40px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 5px;
            color: #555;
            font-size: 12px;
        }

        .signature-box strong {
            color: #1a1a1a;
        }

        .signature-line {
            margin-top: 50px;
            padding-top: 8px;
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
            font-size: 11px;
            color: #555;
        }

        /* Footer */
        .footer-note {
            background: #fafafa;
            padding: 15px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
        }

        /* Button Action Bar */
        .action-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 20px;
            background: #333;
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action:hover {
            background: #555;
            color: white;
        }

        .btn-back {
            background: #666;
        }

        .btn-back:hover {
            background: #888;
        }

        .btn-print {
            background: #333;
        }

        .btn-print:hover {
            background: #555;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .report-container {
                box-shadow: none;
                margin: 0;
            }
            
            .report-header {
                border-bottom: 2px solid #000;
            }
            
            .table thead th {
                background: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1px solid #ccc;
            }
            
            .summary-section, .info-section, .footer-note {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .action-bar {
                position: static;
                justify-content: center;
                margin-bottom: 20px;
            }
            
            .info-section, .table-wrapper, .signature-section {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .summary-section {
                margin-left: 20px;
                margin-right: 20px;
            }
            
            .table {
                font-size: 11px;
            }
            
            .signature-line {
                width: 150px;
            }
        }
    </style> 
</head> 
<body> 
    {{-- Action Buttons --}}
    <div class="action-bar no-print">
        <a href="{{ url()->previous() }}" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <div class="report-container">
        <div class="report-header">
            <h1>LAPORAN PEMINJAMAN ALAT</h1>
            <div class="subtitle">LABORATORIUM {{ $laboratoryName ?? 'TEKNIK INFORMATIKA' }}</div>
            <div class="subtitle">SEKOLAH TINGGI TEKNOLOGI {{ $collegeName ?? 'CIMAHI' }}</div>
            <div class="period">Periode: {{ $startDate ?? date('d/m/Y') }} - {{ $endDate ?? date('d/m/Y') }}</div>
        </div>

        <div class="info-section">
            <div class="info-card">
                <label><i class="fas fa-calendar-alt"></i> TANGGAL CETAK</label>
                <div class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            </div>
            <div class="info-card">
                <label><i class="fas fa-clock"></i> WAKTU CETAK</label>
                <div class="value">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
            </div>
            <div class="info-card">
                <label><i class="fas fa-chart-bar"></i> TOTAL TRANSAKSI</label>
                <div class="value">{{ $loans->count() }} Peminjaman</div>
            </div>
            <div class="info-card">
                <label><i class="fas fa-user"></i> PETUGAS</label>
                <div class="value">{{ Auth::user()->name ?? 'Admin' }}</div>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="18%">Peminjam</th>
                            <th width="22%">Alat</th>
                            <th width="11%">Tgl Pinjam</th>
                            <th width="11%">Tgl Kembali Rencana</th>
                            <th width="11%">Tgl Kembali Aktual</th>
                            <th width="10%">Status</th>
                            <th width="12%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $loan->user->name ?? '-' }}</strong><br>
                                <small style="color:#888;">{{ $loan->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <strong>{{ $loan->tool->nama_alat ?? '-' }}</strong><br>
                                <small style="color:#888;">{{ $loan->tool->category->nama_kategori ?? '-' }}</small>
                            </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($loan->tanggal_kembali_aktual)
                                    {{ \Carbon\Carbon::parse($loan->tanggal_kembali_aktual)->format('d/m/Y') }}
                                @else
                                    <span style="color:#999;">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $isOverdue = $loan->status == 'disetujui' && \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->isPast();
                                    $statusClass = '';
                                    $statusText = '';
                                    if($isOverdue) {
                                        $statusClass = 'status-terlambat';
                                        $statusText = 'TERLAMBAT';
                                    } else {
                                        switch($loan->status) {
                                            case 'pending':
                                                $statusClass = 'status-menunggu';
                                                $statusText = 'MENUNGGU';
                                                break;
                                            case 'disetujui':
                                                $statusClass = 'status-dipinjam';
                                                $statusText = 'DIPINJAM';
                                                break;
                                            case 'ditolak':
                                                $statusClass = 'status-menunggu';
                                                $statusText = 'DITOLAK';
                                                break;
                                            case 'kembali':
                                                $statusClass = 'status-dikembalikan';
                                                $statusText = 'DIKEMBALIKAN';
                                                break;
                                            default:
                                                $statusClass = 'status-menunggu';
                                                $statusText = 'MENUNGGU';
                                        }
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                @if($isOverdue || ($loan->tanggal_kembali_aktual && \Carbon\Carbon::parse($loan->tanggal_kembali_aktual)->gt(\Carbon\Carbon::parse($loan->tanggal_kembali_rencana))))
                                    @php
                                        $lateDuration = $loan->late_duration;
                                    @endphp
                                    <span style="color:#cc0000; font-size:11px;">
                                        Telat {{ $lateDuration ?? '0 hari' }}
                                    </span>
                                @elseif($loan->status == 'kembali')
                                    <span style="color:#333; font-size:11px;">Tepat waktu</span>
                                @else
                                    <span style="color:#999;">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5" style="color:#999;">
                                <i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#ccc;"></i>
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($loans->count() > 0)
        <div class="summary-section">
            <div class="summary-title">RINGKASAN STATISTIK</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $loans->count() }}</span>
                    <span class="stat-label">Total Peminjaman</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $loans->where('status', 'dikembalikan')->count() }}</span>
                    <span class="stat-label">Selesai</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $loans->where('status', 'dipinjam')->count() }}</span>
                    <span class="stat-label">Sedang Dipinjam</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $loans->where('status', 'terlambat')->count() }}</span>
                    <span class="stat-label">Terlambat</span>
                </div>
            </div>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala Laboratorium</strong></p>
                <br><br><br>
                <div class="signature-line">(_____________________)</div>
                <p style="margin-top:5px; font-size:10px;">NIP. ________________</p>
            </div>
            <div class="signature-box">
                <p>Cimahi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
                <p><strong>Petugas Laboratorium</strong></p>
                <br><br><br>
                <div class="signature-line">({{ Auth::user()->name ?? 'Petugas' }})</div>
                <p style="margin-top:5px; font-size:10px;">NIP. ________________</p>
            </div>
        </div>

        <div class="footer-note">
            <i class="fas fa-file-alt me-2"></i>
            Laporan ini digenerate secara otomatis oleh sistem informasi laboratorium
            <br>
            Dokumen ini sah dan dapat dipertanggungjawabkan
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body> 
</html>