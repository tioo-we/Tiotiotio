<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::create([
            'nama'=>'Administrator',
            'username'=>'admin',
            'role'=>'admin',
            'password'=> bcrypt('password'),
        ]);

        \App\Models\User::create([
            'nama'=>'Petugas',
            'username'=>'petugas',
            'role'=>'admin',
            'password'=> bcrypt('password'),
        ]);

        \App\Models\Pelanggan::create([
            'nama'=>'Dodo Sidodo',
            'alamat'=>'Padaherang',
            'nomor_tlp'=>'082288877766'
        ]);

        \App\Models\Pelanggan::create([
            'nama'=>'Hanifah',
            'alamat'=>'Kalipucang',
            'nomor_tlp'=>'082288866677'
        ]);

        \App\Models\Kategori::create([
            'nama_kategori'=>'Makanan',
        ]);

        \App\Models\Kategori::create([
            'nama_kategori'=>'Minuman',
        ]);

        \App\Models\Produk::create([
            'kategori_id'=>1,
            'kode_produk'=>'1001',
            'nama_produk'=>'Chiki Taro',
            'harga_produk' => 2500,    // Harga beli/modal
            'harga_jual' => 3500,      // Harga jual sebelum diskon
            'diskon' => 0,             // Tidak ada diskon
            'harga' => 3500,           // Harga final (3500 - 0% = 3500)
        ]);

        \App\Models\Produk::create([
            'kategori_id'=>2,
            'kode_produk'=>'1002',
            'nama_produk'=>'Le Minerale',
            'harga_produk' => 2500,    // Harga beli/modal
            'harga_jual' => 3500,      // Harga jual sebelum diskon
            'diskon' => 0,             // Tidak ada diskon
            'harga' => 3500,           // Harga final (3500 - 0% = 3500)
        ]);

        \App\Models\Stok::create([
            'produk_id'=>1,
            'nama_suplier'=>'Toko Haji Usman',
            'jumlah'=>'250',
            'tanggal'=> date('Y-m-d', strtotime('-1 week'))

        ]);
        \App\Models\Stok::create([
            'produk_id'=>2,
            'nama_suplier'=>'Agen Le Minerale',
            'jumlah'=>'100',
            'tanggal'=> date('Y-m-d', strtotime('-1 week'))
        ]);

        \App\Models\Produk::where('id',1)->update([
            'stok'=>250,
        ]);

        \App\Models\Produk::where('id',2)->update([
            'stok'=>100,
        ]);

    }
}