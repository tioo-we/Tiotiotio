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
        // Ambil semua transaksi, termasuk yang tidak ada pelanggan
        $penjualan = Penjualan::join('users', 'users.id', '=', 'penjualans.user_id')
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'penjualans.pelanggan_id') // ganti join jadi leftJoin
            ->whereDate('tanggal', $request->tanggal)
            ->select(
                'penjualans.*',
                DB::raw('COALESCE(pelanggans.nama, "Pelanggan") as nama_pelanggan'), // default "Pelanggan" kalau null
                'users.nama as nama_kasir'
            )
            ->orderBy('id')
            ->get();

        // Hitung total hanya transaksi tidak batal
        $totalHarian = Penjualan::whereDate('tanggal', $request->tanggal)
            ->where('status', '!=', 'batal')
            ->sum('total');

        return view('laporan.harian', [
            'penjualan' => $penjualan,
            'totalHarian' => $totalHarian
        ]);
    }


    public function bulanan(Request $request)
    {
        // Data bulanan pakai agregasi
        $penjualan = Penjualan::select(
        DB::raw('DATE(tanggal) as tgl_asli'),
        DB::raw("DATE_FORMAT(tanggal, '%d/%m/%Y') as tgl"),
        DB::raw('COUNT(id) as jumlah_transaksi'),
        DB::raw('SUM(CASE WHEN status != "batal" THEN total ELSE 0 END) as jumlah_total'),
        DB::raw('COUNT(CASE WHEN status != "batal" THEN 1 END) as jumlah_transaksi_berhasil'),
        DB::raw('COUNT(CASE WHEN status = "batal" THEN 1 END) as jumlah_transaksi_batal'),
        DB::raw('SUM(CASE WHEN status = "batal" THEN total ELSE 0 END) as jumlah_total_batal')
        )
        ->whereMonth('tanggal', $request->bulan)
        ->whereYear('tanggal', $request->tahun)
        ->groupBy('tgl_asli', 'tgl') // pakai DATE(tanggal), bukan string 
        ->orderBy('tgl_asli')
        ->get();


        // Total keseluruhan untuk bulan tsb (hanya berhasil)
        $totalBulanan = Penjualan::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('status', '!=', 'batal')
            ->sum('total');

        // Total keseluruhan untuk bulan tsb (hanya batal)
        $totalBatal = Penjualan::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('status', 'batal')
            ->sum('total');

        $nama_bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei',
            'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $bulan = isset($nama_bulan[$request->bulan - 1]) ? $nama_bulan[$request->bulan - 1] : null;

        return view('laporan.bulanan', [
            'penjualan' => $penjualan,
            'bulan' => $bulan,
            'totalBulanan' => $totalBulanan,
            'totalBatal' => $totalBatal // kirim ke view
        ]);
    }

    public function keuntungan(Request $request)
{
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    // Ambil data penjualan + detail + produk
    $penjualan = Penjualan::with(['detilPenjualan.produk'])
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('status', '!=', 'batal')
        ->get();

    $totalPendapatan = 0;
    $totalModal = 0;
    $detail = [];

    foreach ($penjualan as $pj) {
        foreach ($pj->detilPenjualan as $detil) {
            $pendapatan = $detil->jumlah * $detil->harga_produk;   // harga jual
            $modal = $detil->jumlah * $detil->produk->harga_produk; // harga beli supplier
            $laba = $pendapatan - $modal;

            $totalPendapatan += $pendapatan;
            $totalModal += $modal;

            $detail[] = (object) [
                'tanggal' => $pj->tanggal,
                'produk' => $detil->produk->nama_produk ?? '-',
                'jumlah' => $detil->jumlah,
                'harga_jual' => $detil->harga_produk,
                'harga_modal' => $detil->produk->harga_produk,
                'pendapatan' => $pendapatan,
                'modal' => $modal,
                'laba' => $laba,
            ];
        }
    }

    $totalLaba = $totalPendapatan - $totalModal;

    $nama_bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei',
        'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    return view('laporan.keuntungan', [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'bulanNama' => $nama_bulan[$bulan - 1] ?? '',
        'detail' => $detail,
        'totalPendapatan' => $totalPendapatan,
        'totalModal' => $totalModal,
        'totalLaba' => $totalLaba,
    ]);
}



}