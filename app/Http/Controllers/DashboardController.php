<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    function index()
    {
        $user = User::selectRaw('count(*) as jumlah')->first();
        $pelanggan = Pelanggan::selectRaw('count(*) as jumlah')->first();
        $kategori = Kategori::selectRaw('count(*) as jumlah')->first();
        $produk = Produk::selectRaw('count(*) as jumlah')->first();

        // Ambil data penjualan untuk bulan ini
        $penjualanData = Penjualan::select(
            DB::raw('SUM(total) as jumlah_total'),
            DB::raw("DAY(tanggal) as hari")
        )
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->groupBy(DB::raw('DAY(tanggal)'))
            ->pluck('jumlah_total', 'hari')
            ->toArray();

        $nama_bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 
            'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $currentDate = now();
        $label = 'Transaksi ' . $nama_bulan[$currentDate->month - 1] . ' ' . $currentDate->year;
        
        // Dapatkan jumlah hari dalam bulan ini menggunakan Carbon
        $daysInMonth = $currentDate->daysInMonth;
        
        $labels = [];
        $data = [];

        // Loop untuk semua hari dalam bulan
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;
            // Jika ada data untuk hari tersebut, gunakan nilai tersebut, jika tidak gunakan 0
            $data[] = $penjualanData[$day] ?? 0;
        }

        return view('welcome', [
            'user' => $user,
            'pelanggan' => $pelanggan,
            'kategori' => $kategori,
            'produk' => $produk,
            'cart' => [
                'label' => $label,
                'labels' => json_encode($labels),
                'data' => json_encode($data)
            ]
        ]);
    }
}