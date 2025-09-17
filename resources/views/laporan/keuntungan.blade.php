@extends('layouts.main', ['title' => 'Laporan Keuntungan'])

@section('title-content')
    <i class="fas fa-chart-line mr-2"></i> Laporan Keuntungan
@endsection

@section('content')
<div class="card card-blue card-outline">
    <div class="card-header">
        <h3 class="card-title">Keuntungan Bulan {{ $bulanNama }} {{ $tahun }}</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="bg-gray-200">
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Jual</th>
                    <th>Harga Modal</th>
                    <th>Pendapatan</th>
                    <th>Modal</th>
                    <th>Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detail as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->produk }}</td>
                        <td>{{ $row->jumlah }}</td>
                        <td>Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->harga_modal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->pendapatan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->modal, 0, ',', '.') }}</td>
                        <td class="text-success"><b>Rp {{ number_format($row->laba, 0, ',', '.') }}</b></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500">Tidak ada transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            <h5>Total Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h5>
            <h5>Total Modal: Rp {{ number_format($totalModal, 0, ',', '.') }}</h5>
            <h4><b>Total Laba: Rp {{ number_format($totalLaba, 0, ',', '.') }}</b></h4>
        </div>
    </div>
</div>
@endsection