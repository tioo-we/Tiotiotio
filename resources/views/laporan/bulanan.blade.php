@extends('layouts.laporan', ['title' => 'Laporan Bulanan'])

@section('content')
<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-900: #111827;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--gray-50);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--gray-900);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .page-subtitle {
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: var(--shadow);
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .text-primary { color: var(--primary); }
    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }

    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .modern-table thead th {
        background-color: var(--gray-900);
        color: white;
        padding: 1rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .modern-table tbody tr {
        border-bottom: 1px solid var(--gray-200);
        transition: background-color 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .modern-table td {
        padding: 1rem;
        text-align: center;
        font-weight: 500;
    }

    .modern-table tfoot th {
        background-color: var(--gray-100);
        padding: 1rem;
        text-align: center;
        font-weight: 700;
        border-top: 2px solid var(--gray-200);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .badge-primary { background-color: var(--primary); }
    .badge-success { background-color: var(--success); }
    .badge-danger { background-color: var(--danger); }

    .summary-card {
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .summary-header {
        background-color: var(--gray-900);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 1.125rem;
    }

    .summary-body {
        padding: 1.5rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        font-weight: 500;
        color: var(--gray-600);
    }

    .summary-value {
        font-weight: 700;
        font-size: 1.125rem;
    }

    .notes {
        background: var(--gray-100);
        border-radius: 8px;
        padding: 1rem;
        border-left: 4px solid var(--primary);
    }

    .note {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    .note:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .stats-container {
            grid-template-columns: 1fr;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .summary-body {
            padding: 1rem;
        }
    }

    /* Tambahkan CSS ini di dalam tag <style> yang sudah ada di laporan bulanan */

/* Print Styles - Menyembunyikan elemen selain tabel saat print */
@media print {
    /* Sembunyikan stats container (4 kotak statistik) */
    .stats-container {
        display: none !important;
    }
    
    /* Sembunyikan summary card (ringkasan bulanan) */
    .summary-card {
        display: none !important;
    }
    
    /* Sembunyikan notes (catatan) */
    .notes {
        display: none !important;
    }
    
    /* Optimasi tampilan print */
    body {
        background-color: white !important;
        color: black !important;
    }
    
    .page-title {
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
        color: black !important;
    }
    
    .page-subtitle {
        margin-bottom: 1rem;
        font-size: 1rem;
        color: black !important;
    }
    
    .table-container {
        box-shadow: none !important;
        border: 1px solid #000;
        margin-bottom: 0;
    }
    
    .modern-table thead th {
        background-color: #f0f0f0 !important;
        color: black !important;
        border: 1px solid #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .modern-table tbody tr {
        border-bottom: 1px solid #000;
    }
    
    .modern-table td {
        border: 1px solid #ccc;
        color: black !important;
    }
    
    .modern-table tfoot th {
        background-color: #f0f0f0 !important;
        color: black !important;
        border: 1px solid #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Badge styling untuk print */
    .badge {
        background-color: #e5e7eb !important;
        color: black !important;
        border: 1px solid #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .badge-primary {
        background-color: #dbeafe !important;
        color: black !important;
    }
    
    .badge-success {
        background-color: #d1fae5 !important;
        color: black !important;
    }
    
    .badge-danger {
        background-color: #fee2e2 !important;
        color: black !important;
    }
    
    /* Pastikan semua warna dapat terbaca saat print */
    .text-primary,
    .text-success,
    .text-danger {
        color: black !important;
    }
    
    /* Container dan layout */
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Hapus hover effects */
    .modern-table tbody tr:hover {
        background-color: transparent !important;
    }
}
</style>

<div class="container mt-4">
    <h2 class="page-title">Laporan Bulanan</h2>
    <p class="page-subtitle">
        {{ $bulan }} {{ request()->tahun }}
    </p>

    {{-- Statistics --}}
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number text-primary">{{ $penjualan->sum('jumlah_transaksi') }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-success">{{ $penjualan->sum('jumlah_transaksi_berhasil') }}</div>
            <div class="stat-label">Transaksi Berhasil</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-danger">{{ $penjualan->sum('jumlah_transaksi') - $penjualan->sum('jumlah_transaksi_berhasil') }}</div>
            <div class="stat-label">Transaksi Batal</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-primary">{{ number_format($totalBulanan, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Total Transaksi</th>
                    <th>Transaksi Berhasil</th>
                    <th>Transaksi Batal</th>
                    <th>Total (Berhasil)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan as $key => $row)
                    @php
                        $transaksi_batal = $row->jumlah_transaksi - $row->jumlah_transaksi_berhasil;
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->tgl }}</td>
                        <td><span class="badge badge-primary">{{ $row->jumlah_transaksi }}</span></td>
                        <td><span class="badge badge-success">{{ $row->jumlah_transaksi_berhasil }}</span></td>
                        <td><span class="badge badge-danger">{{ $transaksi_batal }}</span></td>
                        <td class="text-primary"><strong>{{ number_format($row->jumlah_total, 0, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Jumlah Total</th>
                    <th><span class="badge badge-primary">{{ $penjualan->sum('jumlah_transaksi') }}</span></th>
                    <th><span class="badge badge-success">{{ $penjualan->sum('jumlah_transaksi_berhasil') }}</span></th>
                    <th><span class="badge badge-danger">{{ $penjualan->sum('jumlah_transaksi') - $penjualan->sum('jumlah_transaksi_berhasil') }}</span></th>
                    <th class="text-primary"><strong>{{ number_format($totalBulanan, 0, ',', '.') }}</strong></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Summary --}}
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="summary-card">
                <div class="summary-header">
                    Ringkasan Bulanan
                </div>
                <div class="summary-body">
                    <div class="summary-item">
                        <span class="summary-label">Total Transaksi</span>
                        <span class="summary-value text-primary">{{ $penjualan->sum('jumlah_transaksi') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Transaksi Berhasil</span>
                        <span class="summary-value text-success">{{ $penjualan->sum('jumlah_transaksi_berhasil') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Transaksi Batal</span>
                        <span class="summary-value text-danger">{{ $penjualan->sum('jumlah_transaksi') - $penjualan->sum('jumlah_transaksi_berhasil') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Pendapatan</span>
                        <span class="summary-value text-primary">Rp {{ number_format($totalBulanan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <div class="note">
            Laporan bulanan hanya menghitung total dari transaksi yang berhasil.
        </div>
        <div class="note">
            Transaksi dengan status <strong>BATAL</strong> tidak dihitung dalam total pendapatan.
        </div>
    </div>
</div>
@endsection