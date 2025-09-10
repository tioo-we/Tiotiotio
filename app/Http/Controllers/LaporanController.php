<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.form');
    }

    public function harian(Request $request)
    {
        $penjualan = Penjualan::join('users', 'users.id', 'penjualans.user_id')
           ->join('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
           ->whereDate('tanggal', $request->tanggal)
           ->select('penjualans.*', 'pelanggans.nama as nama_pelanggan', 'users.nama as nama_kasir')
           ->orderBy('id')
           ->get();

           $totalHarian = $penjualan->where('status', '!=', 'batal')->sum('total');

           return view('laporan.harian', [
                'penjualan' => $penjualan,
                'totalHarian' => $totalHarian
           ]);
    }

  public function bulanan(Request $request)
{
    $penjualan = Penjualan::select(
        DB::raw('COUNT(id) as jumlah_transaksi'),
        DB::raw("SUM(CASE WHEN status != 'batal' THEN 1 ELSE 0 END) as jumlah_transaksi_berhasil"),
        DB::raw('SUM(CASE WHEN status != "batal" THEN total ELSE 0 END) as jumlah_total'),
        DB::raw("DATE_FORMAT(tanggal, '%d/%m/%Y') tgl")
    )
    ->whereMonth('tanggal', $request->bulan)
    ->whereYear('tanggal', $request->tahun)
    ->groupBy('tgl')
    ->get();

    $totalBulanan = $penjualan->sum('jumlah_total');

    $nama_bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei',
        'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $bulan = isset($nama_bulan[$request->bulan - 1]) ? $nama_bulan[$request->bulan - 1] : null;

    return view('laporan.bulanan', [
        'penjualan' => $penjualan,
        'bulan' => $bulan,
        'totalBulanan' => $totalBulanan
    ]);
}

}