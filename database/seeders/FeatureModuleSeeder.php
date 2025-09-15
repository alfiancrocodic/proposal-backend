<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MainModule;
use App\Models\SubModule;
use App\Models\Feature;
use App\Models\Condition;

class FeatureModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder ini berdasarkan data dari tabel detail feature Jamaah
     */
    public function run(): void
    {
        // 1. Main Module: Home
        $homeModule = MainModule::create([
            'name' => 'Home',
            'description' => 'Modul utama untuk halaman beranda aplikasi',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Banner
        $bannerSubModule = SubModule::create([
            'main_module_id' => $homeModule->id,
            'name' => 'Banner',
            'description' => 'Manajemen banner pada halaman home',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Features untuk Banner
        $listBannerFeature = Feature::create([
            'sub_module_id' => $bannerSubModule->id,
            'name' => 'List Banner',
            'description' => 'Menampilkan daftar banner',
            'mandays' => 2.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $detailBannerFeature = Feature::create([
            'sub_module_id' => $bannerSubModule->id,
            'name' => 'Detail Banner',
            'description' => 'Menampilkan detail banner',
            'mandays' => 1.5,
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        // 2. Main Module: Paket Umroh dan Haji
        $paketModule = MainModule::create([
            'name' => 'Paket Umroh dan Haji',
            'description' => 'Modul untuk manajemen paket umroh dan haji',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        // Sub Module: List Paket Umroh
        $listPaketSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'List Paket Umroh',
            'description' => 'Daftar paket umroh yang tersedia',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Features untuk List Paket Umroh
        $listPaketUmrohFeature = Feature::create([
            'sub_module_id' => $listPaketSubModule->id,
            'name' => 'List Paket Umroh',
            'description' => 'Menampilkan daftar paket umroh',
            'mandays' => 3.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Detail Paket Umroh
        $detailPaketSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'Detail Paket Umroh',
            'description' => 'Detail informasi paket umroh',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        // Features untuk Detail Paket Umroh
        $detailPaketUmrohFeature = Feature::create([
            'sub_module_id' => $detailPaketSubModule->id,
            'name' => 'Detail Paket Umroh',
            'description' => 'Menampilkan detail paket umroh',
            'mandays' => 2.5,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Detail Paket Haji
        $detailHajiSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'Detail Paket Haji',
            'description' => 'Detail informasi paket haji',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        // Features untuk Detail Paket Haji
        $detailPaketHajiFeature = Feature::create([
            'sub_module_id' => $detailHajiSubModule->id,
            'name' => 'Detail Paket Haji',
            'description' => 'Menampilkan detail paket haji',
            'mandays' => 2.5,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Form Order
        $formOrderSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'Form Order',
            'description' => 'Form pemesanan paket',
            'is_active' => true,
            'sort_order' => 4
        ]);
        
        // Features untuk Form Order
        $formOrderFeature = Feature::create([
            'sub_module_id' => $formOrderSubModule->id,
            'name' => 'Form Order',
            'description' => 'Form untuk memesan paket',
            'mandays' => 4.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Add to Cart
        $addToCartSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'Add to cart',
            'description' => 'Fitur menambahkan ke keranjang',
            'is_active' => true,
            'sort_order' => 5
        ]);
        
        $addToCartFeature = Feature::create([
            'sub_module_id' => $addToCartSubModule->id,
            'name' => 'Add to cart',
            'description' => 'Menambahkan paket ke keranjang',
            'mandays' => 2.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: List Cart
        $listCartSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'List Cart',
            'description' => 'Daftar item dalam keranjang',
            'is_active' => true,
            'sort_order' => 6
        ]);
        
        $listCartFeature = Feature::create([
            'sub_module_id' => $listCartSubModule->id,
            'name' => 'List Cart',
            'description' => 'Menampilkan daftar item dalam keranjang',
            'mandays' => 2.5,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Sub Module: Informasi Payment
        $infoPaymentSubModule = SubModule::create([
            'main_module_id' => $paketModule->id,
            'name' => 'Informasi Payment',
            'description' => 'Informasi pembayaran',
            'is_active' => true,
            'sort_order' => 7
        ]);
        
        $infoPaymentFeature = Feature::create([
            'sub_module_id' => $infoPaymentSubModule->id,
            'name' => 'Informasi Payment',
            'description' => 'Menampilkan informasi pembayaran',
            'mandays' => 1.5,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Informasi Payment
        Condition::create([
            'feature_id' => $infoPaymentFeature->id,
            'name' => 'Pembayaran Manual',
            'description' => 'Kondisi untuk pembayaran manual',
            'condition_text' => 'Pembayaran dilakukan manual di bank atau agen ops',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // 3. Main Module: Proses Umroh dan Haji
        $prosesModule = MainModule::create([
            'name' => 'Proses Umroh dan Haji',
            'description' => 'Modul untuk proses umroh dan haji',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        // Sub Module: Informasi Proses Umroh
        $infoProsesUmrohSubModule = SubModule::create([
            'main_module_id' => $prosesModule->id,
            'name' => 'Informasi Proses Umroh',
            'description' => 'Informasi proses umroh',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $infoProsesUmrohFeature = Feature::create([
            'sub_module_id' => $infoProsesUmrohSubModule->id,
            'name' => 'Informasi Proses Umroh',
            'description' => 'Menampilkan informasi proses umroh',
            'mandays' => 2.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Informasi Proses Umroh
        Condition::create([
            'feature_id' => $infoProsesUmrohFeature->id,
            'name' => 'Audit',
            'description' => 'Proses audit',
            'condition_text' => 'Audit',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Condition::create([
            'feature_id' => $infoProsesUmrohFeature->id,
            'name' => 'Deskripsi',
            'description' => 'Deskripsi proses',
            'condition_text' => 'Deskripsi',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Condition::create([
            'feature_id' => $infoProsesUmrohFeature->id,
            'name' => 'Image',
            'description' => 'Gambar proses',
            'condition_text' => 'Image',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        // Sub Module: Informasi Proses Haji
        $infoProsesHajiSubModule = SubModule::create([
            'main_module_id' => $prosesModule->id,
            'name' => 'Informasi Proses Haji',
            'description' => 'Informasi proses haji',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        $infoProsesHajiFeature = Feature::create([
            'sub_module_id' => $infoProsesHajiSubModule->id,
            'name' => 'Informasi Proses Haji',
            'description' => 'Menampilkan informasi proses haji',
            'mandays' => 2.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Informasi Proses Haji
        Condition::create([
            'feature_id' => $infoProsesHajiFeature->id,
            'name' => 'Audit',
            'description' => 'Proses audit haji',
            'condition_text' => 'Audit',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Condition::create([
            'feature_id' => $infoProsesHajiFeature->id,
            'name' => 'Deskripsi',
            'description' => 'Deskripsi proses haji',
            'condition_text' => 'Deskripsi',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Condition::create([
            'feature_id' => $infoProsesHajiFeature->id,
            'name' => 'Image',
            'description' => 'Gambar proses haji',
            'condition_text' => 'Image',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        // 4. Main Module: Panduan Umroh
        $panduanModule = MainModule::create([
            'name' => 'Panduan Umroh',
            'description' => 'Modul panduan umroh',
            'is_active' => true,
            'sort_order' => 4
        ]);
        
        // Sub Module: Mesjid & Ihram
        $mesjidIhramSubModule = SubModule::create([
            'main_module_id' => $panduanModule->id,
            'name' => 'Mesjid & Ihram',
            'description' => 'Panduan mesjid dan ihram',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $infoMesjidIhramFeature = Feature::create([
            'sub_module_id' => $mesjidIhramSubModule->id,
            'name' => 'Informasi Mesjid & Ihram',
            'description' => 'Informasi tentang mesjid dan ihram',
            'mandays' => 3.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Informasi Mesjid & Ihram
        Condition::create([
            'feature_id' => $infoMesjidIhramFeature->id,
            'name' => 'Nama Lokasi',
            'description' => 'Nama lokasi mesjid',
            'condition_text' => 'Nama Lokasi',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Condition::create([
            'feature_id' => $infoMesjidIhramFeature->id,
            'name' => 'Image',
            'description' => 'Gambar mesjid',
            'condition_text' => 'Image',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Condition::create([
            'feature_id' => $infoMesjidIhramFeature->id,
            'name' => 'Maps',
            'description' => 'Peta lokasi mesjid',
            'condition_text' => 'Maps',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        // Sub Module: Doa Mesjid & Ihram
        $doaMesjidIhramSubModule = SubModule::create([
            'main_module_id' => $panduanModule->id,
            'name' => 'Doa Mesjid & Ihram',
            'description' => 'Doa-doa mesjid dan ihram',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        $doaMesjidIhramFeature = Feature::create([
            'sub_module_id' => $doaMesjidIhramSubModule->id,
            'name' => 'Doa Mesjid & Ihram',
            'description' => 'Kumpulan doa mesjid dan ihram',
            'mandays' => 2.0,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Doa Mesjid & Ihram
        Condition::create([
            'feature_id' => $doaMesjidIhramFeature->id,
            'name' => 'Ayat Quran',
            'description' => 'Ayat Al-Quran terkait',
            'condition_text' => 'Ayat Quran',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Condition::create([
            'feature_id' => $doaMesjidIhramFeature->id,
            'name' => 'Latin',
            'description' => 'Tulisan latin doa',
            'condition_text' => 'Latin',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Condition::create([
            'feature_id' => $doaMesjidIhramFeature->id,
            'name' => 'Terjemahan',
            'description' => 'Terjemahan doa',
            'condition_text' => 'Terjemahan',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        Condition::create([
            'feature_id' => $doaMesjidIhramFeature->id,
            'name' => 'Audio Doa',
            'description' => 'Audio bacaan doa',
            'condition_text' => 'Audio Doa',
            'is_active' => true,
            'sort_order' => 4
        ]);
        
        // Sub Module: Doa Niat Umroh
        $doaNiatUmrohSubModule = SubModule::create([
            'main_module_id' => $panduanModule->id,
            'name' => 'Doa Niat Umroh',
            'description' => 'Doa niat umroh',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        $doaNiatUmrohFeature = Feature::create([
            'sub_module_id' => $doaNiatUmrohSubModule->id,
            'name' => 'Doa Niat Umroh',
            'description' => 'Doa niat untuk umroh',
            'mandays' => 1.5,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Conditions untuk Doa Niat Umroh
        Condition::create([
            'feature_id' => $doaNiatUmrohFeature->id,
            'name' => 'Ayat Quran',
            'description' => 'Ayat Al-Quran untuk niat umroh',
            'condition_text' => 'Ayat Quran',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        Condition::create([
            'feature_id' => $doaNiatUmrohFeature->id,
            'name' => 'Latin',
            'description' => 'Tulisan latin doa niat',
            'condition_text' => 'Latin',
            'is_active' => true,
            'sort_order' => 2
        ]);
        
        Condition::create([
            'feature_id' => $doaNiatUmrohFeature->id,
            'name' => 'Terjemahan',
            'description' => 'Terjemahan doa niat',
            'condition_text' => 'Terjemahan',
            'is_active' => true,
            'sort_order' => 3
        ]);
        
        Condition::create([
            'feature_id' => $doaNiatUmrohFeature->id,
            'name' => 'Audio Doa',
            'description' => 'Audio bacaan doa niat',
            'condition_text' => 'Audio Doa',
            'is_active' => true,
            'sort_order' => 4
        ]);
        
        $this->command->info('Feature Module Seeder completed successfully!');
    }
}