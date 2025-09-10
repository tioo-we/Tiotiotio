<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetilPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Cart;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $penjualans = Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->leftJoin('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
            ->select('penjualans.*', 'users.nama as nama_kasir', 'pelanggans.nama as nama_pelanggan')
            ->orderBy('id', 'desc')
            ->when($search, function ($q, $search) {
                return $q->where('nomor_transaksi', 'like', "%{$search}%");
            })
            ->paginate();

        if ($search)
            $penjualans->appends(['search' => $search]);

        return view('transaksi.index', [
            'penjualans' => $penjualans
        ]);
    }

    public function create(Request $request)
    {
        return view('transaksi.create', [
            'nama_kasir' => $request->user()->nama,
            'tanggal' => date('d F Y')
        ]);
    }

    public function store(Request $request)
    {
        // 🔥 Bersihkan titik pada input cash sebelum validasi
        $request->merge([
            'cash' => str_replace('.', '', $request->cash)
        ]);

        $request->validate([
            'pelanggan_id' => ['nullable', 'exists:pelanggans,id'],
            'cash' => ['required', 'numeric', 'gte:total_bayar']
        ]);

        $user = $request->user();
        $cart = Cart::name($user->id);
        $cartDetails = $cart->getDetails();

        $total = $cartDetails->get('total');
        $kembalian = $request->cash - $total;

        // Nomor transaksi unik
        $today = date('Ymd');
        $lastTransaction = Penjualan::where('nomor_transaksi', 'like', $today . '%')
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        $no = $lastTransaction
            ? str_pad(((int)substr($lastTransaction->nomor_transaksi, 8)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        $nomor_transaksi = $today . $no;

        $allItems = $cartDetails->get('items');

        // Cek stok
        foreach ($allItems as $item) {
            $produk = Produk::find($item->id);
            if ($produk && $produk->stok < $item->quantity) {
                $cart->destroy();
                return redirect()
                    ->route('transaksi.create')
                    ->with('store', 'gagal');
            }
        }

        // Simpan transaksi
        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $cart->getExtraInfo('pelanggan.id') ?? null,
            'nomor_transaksi' => $nomor_transaksi,
            'tanggal' => now(),
            'total' => $total,
            'tunai' => $request->cash,
            'kembalian' => $kembalian,
            'pajak' => $cartDetails->get('tax_amount'),
            'subtotal' => $cartDetails->get('subtotal')
        ]);

        // Detail penjualan
        foreach ($allItems as $item) {
            DetilPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'produk_id' => $item->id,
                'harga_produk' => $item->price,
                'jumlah' => $item->quantity,
                'subtotal' => $item->subtotal
            ]);

            $produk = Produk::find($item->id);
            $produk->stok -= $item->quantity;
            $produk->save();
        }

        $cart->destroy();

        return redirect()->route('transaksi.show', ['transaksi' => $penjualan->id]);
    }

    public function show(Request $request, Penjualan $transaksi)
    {
        $pelanggan = Pelanggan::find($transaksi->pelanggan_id);
        $user = User::find($transaksi->user_id);
        $detilPenjualan = DetilPenjualan::join('produks', 'produks.id', 'detil_penjualans.produk_id')
            ->select(
                'detil_penjualans.*',
                'nama_produk',
                'produks.harga_produk as harga_awal',
                'produks.diskon'
            )
            ->where('penjualan_id', $transaksi->id)
            ->get();

        return view('transaksi.invoice', [
            'penjualan' => $transaksi,
            'pelanggan' => $pelanggan,
            'user' => $user,
            'detilPenjualan' => $detilPenjualan
        ]);
    }

    public function destroy(Request $request, Penjualan $transaksi)
    {
        $transaksi->update([
            'status' => 'batal'
        ]);

        $detail = DetilPenjualan::where('penjualan_id', $transaksi->id)->get();

        foreach ($detail as $item) {
            $produk = Produk::find($item->produk_id);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->save();
            }
        }

        return back()->with('destroy', 'success');
    }

    public function produk(Request $request)
    {
        $search = $request->search;
        $produks = Produk::select('id', 'kode_produk', 'nama_produk')
            ->when($search, function ($q, $search) {
                return $q->where('nama_produk', 'like', "%{$search}%");
            })
            ->orderBy('nama_produk')
            ->take(15)
            ->get();

        return response()->json($produks);
    }

    public function pelanggan(Request $request)
    {
        $search = $request->search;
        $pelanggans = Pelanggan::select('id', 'nama')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->take(15)
            ->get();

        return response()->json($pelanggans);
    }

    public function addPelanggan(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:pelanggans']
        ]);

        $pelanggan = Pelanggan::find($request->id);
        $cart = Cart::name($request->user()->id);

        $cart->setExtraInfo([
            'pelanggan' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama
            ]
        ]);

        return response()->json(['message' => 'Berhasil.']);
    }

    public function cetak(Penjualan $transaksi)
    {
        $pelanggan = $transaksi->pelanggan_id ? Pelanggan::find($transaksi->pelanggan_id) : null;
        $user = User::find($transaksi->user_id);

        $detilPenjualan = DetilPenjualan::join('produks', 'produks.id', 'detil_penjualans.produk_id')
            ->select(
                'detil_penjualans.*',
                'nama_produk',
                'produks.harga_produk as harga_awal',
                'produks.diskon'
            )
            ->where('penjualan_id', $transaksi->id)
            ->get();

        return view('transaksi.cetak', [
            'penjualan' => $transaksi,
            'pelanggan' => $pelanggan,
            'user' => $user,
            'detilPenjualan' => $detilPenjualan
        ]);
    }
}