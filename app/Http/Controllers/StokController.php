<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $search = $request->search;

        $stoks = Stok::join('produks', 'produks.id', 'stoks.produk_id')
            ->select('stoks.*', 'nama_produk')
            ->orderBy('stoks.id','desc')
            ->when($search, function ($q, $search) {
                return $q->where('tanggal', 'like', "%{$search}%");
            })
            ->paginate();

        if ($search) $stoks->appends(['search' => $search]);

        return view('stok.index', [
            'stoks' => $stoks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stok.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function produk(Request $request)
    {
        $produks = Produk::select('id', 'nama_produk')
            ->where('nama_produk', 'like', "%{$request->search}%")
            ->take(15)
            ->orderBy('nama_produk')
            ->get();

        return response()->json($produks);
    }

    /**
     * Get suppliers list for autocomplete
     */
    public function suppliers(Request $request)
    {
        $suppliers = Stok::select('nama_suplier')
            ->where('nama_suplier', 'like', "%{$request->search}%")
            ->distinct()
            ->orderBy('nama_suplier')
            ->take(10)
            ->get();

        return response()->json($suppliers);
    }

    /**
     * Get products by supplier
     */
    public function produkBySuplier(Request $request)
    {
        $nama_suplier = $request->nama_suplier;
        
        // Get products that have been supplied by this supplier before
        $produkIds = Stok::where('nama_suplier', $nama_suplier)
            ->distinct()
            ->pluck('produk_id');

        $produks = Produk::select('id', 'nama_produk', 'stok')
            ->whereIn('id', $produkIds)
            ->orderBy('nama_produk')
            ->get();

        return response()->json($produks);
    }

    /**
     * Store multiple products from same supplier
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'nama_suplier' => ['required', 'max:150'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.produk_id' => ['required', 'exists:produks,id'],
            'products.*.jumlah' => ['required', 'numeric', 'min:1']
        ], [
            'products.required' => 'Pilih minimal satu produk',
            'products.*.produk_id.required' => 'Produk harus dipilih',
            'products.*.jumlah.required' => 'Jumlah harus diisi',
            'products.*.jumlah.numeric' => 'Jumlah harus berupa angka',
            'products.*.jumlah.min' => 'Jumlah minimal 1'
        ]);

        DB::beginTransaction();
        
        try {
            $tanggal = date('Y-m-d');
            
            foreach ($request->products as $product) {
                // Create stock record
                Stok::create([
                    'produk_id' => $product['produk_id'],
                    'nama_suplier' => $request->nama_suplier,
                    'jumlah' => $product['jumlah'],
                    'tanggal' => $tanggal
                ]);

                // Update product stock
                $produk = Produk::find($product['produk_id']);
                $produk->update([
                    'stok' => $produk->stok + $product['jumlah']
                ]);
            }

            DB::commit();
            return redirect()->route('stok.index')->with('store', 'success');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data')->withInput();
        }
    }

    /**
     * Store single product (keep for backward compatibility)
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' =>['required', 'exists:produks,id'],
            'jumlah' => ['required', 'numeric'],
            'nama_suplier' => ['required', 'max:150']
        ], [], [
            'produk_id' => 'Nama produk'
        ]);

        $request->merge([
            'tanggal' => date('Y-m-d')
        ]);

        Stok::create($request->all());

        $produk = Produk::find($request->produk_id);

        $produk->update([
            'stok' => $produk->stok + $request->jumlah
        ]);

        return redirect()->route('stok.index')->with('store', 'success');
    }

    public function destroy(Stok $stok)
    {
        $produk = Produk::find($stok->produk_id);
        $produk->update([
            'stok' => $produk->stok - $stok->jumlah
        ]);

        $stok->delete();

        return back()->with('destroy', 'success');
    }
}