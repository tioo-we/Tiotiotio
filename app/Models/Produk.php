<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'kategori_id',
        'kode_produk',
        'nama_produk',
        'harga',
        'stok',
        // diskon
        'harga_produk',
         'harga_jual',
        'diskon'
    ];

    // Relasi ke tabel stok
    public function stoks()
    {
        return $this->hasMany(Stok::class, 'produk_id');
    }
    
    // Accessor untuk mendapatkan harga final
    public function getHargaFinalAttribute()
    {
        return $this->harga_jual - ($this->harga_jual * $this->diskon / 100);
    }
    
    // Accessor untuk mendapatkan keuntungan per unit
    public function getKeuntunganAttribute()
    {
        return $this->getHargaFinalAttribute() - $this->harga_produk;
    }
}