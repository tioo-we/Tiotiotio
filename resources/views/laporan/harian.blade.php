@extends('layouts.laporan', ['title' => 'Laporan Harian'])

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
        text-align: left;
        margin-bottom: 2rem;
        font-size: 1rem;
        font-weight: 500;
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

    .modern-table td:first-child {
        font-weight: 600;
        color: var(--gray-900);
    }

    .modern-table td:nth-child(2) {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .modern-table td:last-child {
        font-weight: 700;
        color: var(--primary);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
    }

    .status-selesai { background-color: var(--success); }
    .status-batal { background-color: var(--danger); }

    .modern-table tfoot th {
        background-color: var(--gray-100);
        padding: 1rem;
        text-align: center;
        font-weight: 700;
        border-top: 2px solid var(--gray-200);
    }

    .total-success {
        background-color: #ecfdf5;
        color: var(--success);
    }

    .total-all {
        background-color: #eff6ff;
        color: var(--primary);
    }

    .highlight-success {
        background-color: #f0fdf4 !important;
    }

    .highlight-danger {
        background-color: #fef2f2 !important;
        opacity: 0.7;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .stats-container {
            grid-template-columns: 1fr;
        }
        
        .modern-table th:nth-child(2),
        .modern-table td:nth-child(2) {
            display: none;
        }
    }


/* Print Styles - Menyembunyikan stats saat print */
@media print {
    .stats-container {
        display: none !important;
    }
    
    .notes {
        display: none !important;
    }
    
    /* Optional: Optimasi tampilan print lainnya */
    body {
        background-color: white !important;
        color: black !important;
    }
    
    .page-title {
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .table-container {
        box-shadow: none !important;
        border: 1px solid #000;
    }
    
    .modern-table thead th {
        background-color: #f0f0f0 !important;
        color: black !important;
        border: 1px solid #000;
    }
    
    .modern-table tbody tr {
        border-bottom: 1px solid #000;
    }
    
    .modern-table td {
        border: 1px solid #ccc;
    }
    
    .modern-table tfoot th {
        background-color: #f0f0f0 !important;
        color: black !important;
        border: 1px solid #000;
    }
    
    .status-badge {
        background-color: #e5e7eb !important;
        color: black !important;
        border: 1px solid #000;
    }
    
    .highlight-success {
        background-color: white !important;
    }
    
    .highlight-danger {
        background-color: #f9f9f9 !important;
    }
    
    /* Pastikan semua warna dapat terbaca saat print */
    .text-primary,
    .text-success,
    .text-danger {
        color: black !important;
    }
}
</style>


<div class="container mt-4">
    <h1 class="page-title">Laporan Harian</h1>
    <p class="page-subtitle">
        Tanggal: {{ date('d/m/Y', strtotime(request()->tanggal)) }}
    </p>

    {{-- Statistics --}}
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number text-primary">{{ $penjualan->count() }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-success">{{ $penjualan->where('status', '!=', 'batal')->count() }}</div>
            <div class="stat-label">Transaksi Selesai</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-danger">{{ $penjualan->where('status', 'batal')->count() }}</div>
            <div class="stat-label">Transaksi Batal</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-primary">{{ number_format($totalHarian, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>Nama Pelanggan</th>
                    <th>Kasir</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan as $key => $row)
                <tr class="{{ $row->status == 'batal' ? 'highlight-danger' : 'highlight-success' }}">
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row->nomor_transaksi }}</td>
                    <td>{{ $row->nama_pelanggan ?? 'Pelanggan' }}</td>
                    <td>{{ $row->nama_kasir }}</td>
                    <td>
                        @if($row->status == 'batal')
                            <span class="status-badge status-batal">{{ ucwords($row->status) }}</span>
                        @else
                            <span class="status-badge status-selesai">{{ ucwords($row->status) }}</span>
                        @endif
                    </td>
                    <td>{{ date('H:i:s', strtotime($row->tanggal)) }}</td>
                    <td>
                        @if($row->status == 'batal')
                            <span class="text-danger" style="text-decoration: line-through;">{{ number_format($row->total, 0, ',', '.') }}</span>
                        @else
                            <strong>{{ number_format($row->total, 0, ',', '.') }}</strong>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-success">
                    <th colspan="6">Jumlah Total (Transaksi Berhasil)</th>
                    <th>{{ number_format($totalHarian, 0, ',', '.') }}</th>
                </tr>
                <tr class="total-all">
                    <th colspan="6">Total Semua Transaksi (Termasuk Batal)</th>
                    <th>{{ number_format($penjualan->sum('total'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <div class="note">
            Transaksi dengan status <strong>BATAL</strong> ditampilkan dengan warna abu-abu dan dicoret.
        </div>
        <div class="note">
            Total hanya menghitung transaksi yang berhasil (tidak termasuk transaksi batal).
        </div>
    </div>
</div>
@endsection 