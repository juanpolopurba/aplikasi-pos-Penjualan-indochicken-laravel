<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cabang
        DB::table('cabang')->insert([
            ['nama_cabang' => 'Indo Chicken Pusat', 'alamat' => 'Jl. Jend. Sudirman No. 1'],
            ['nama_cabang' => 'Indo Chicken Cabang Bekasi', 'alamat' => 'Jl. Raya Perjuangan No. 10'],
            ['nama_cabang' => 'Indo Chicken Cabang Bandung', 'alamat' => 'Jl. Asia Afrika No. 5'],
            ['nama_cabang' => 'Indo Chicken Cabang Surabaya', 'alamat' => 'Jl. Tunjungan No. 100'],
        ]);

        // 2. User Admin (Asumsi ID Cabang Pusat = 1)
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Password yang di-hash
            'role' => 'admin',
            'cabang_id' => 1, 
        ]);

        // 3. Menu
        DB::table('menu')->insert([
            ['nama_menu' => 'Dada', 'harga' => 10000],
            ['nama_menu' => 'Paha atas', 'harga' => 10000],
            ['nama_menu' => 'Paha bawah', 'harga' => 9000],
            ['nama_menu' => 'Sayap', 'harga' => 8000],
            ['nama_menu' => 'Kulit', 'harga' => 5000],
            ['nama_menu' => 'Kentang', 'harga' => 8000],
            ['nama_menu' => 'Nasi', 'harga' => 3000],
            ['nama_menu' => 'Geprek', 'harga' => 3000],
            ['nama_menu' => 'Saos BBQ', 'harga' => 2000],
            ['nama_menu' => 'Saos Lada Hitam', 'harga' => 2000],
            ['nama_menu' => 'Keju Mozarella', 'harga' => 6000],
            ['nama_menu' => 'Keju Biasa', 'harga' => 4000],
            ['nama_menu' => 'Geprek Mozarella', 'harga' => 8000],
        ]);
        
        // 4. Kategori Pengeluaran (Contoh)
        DB::table('kategori_pengeluaran')->insert([
            ['nama_kategori' => 'Gaji Karyawan'],
            ['nama_kategori' => 'Sewa Gedung'],
            ['nama_kategori' => 'Biaya Listrik & Air'],
            ['nama_kategori' => 'Pembelian Bahan Baku'], // Ini yang akan kita pakai di controller pembelian
            ['nama_kategori' => 'Biaya Pemasaran'],
        ]);

        // 5. Bahan Baku (Contoh)
        DB::table('bahan_baku')->insert([
            ['nama' => 'Ayam Fillet Dada', 'satuan' => 'Kg',],
            ['nama' => 'Tepung Terigu Kiloan', 'satuan' => 'Kg'],
            ['nama' => 'Minyak Goreng Pouch 2L', 'satuan' => 'Pcs'],
        ]);
    }
}