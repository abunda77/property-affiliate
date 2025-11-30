<?php

namespace Database\Seeders;

use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;

class LegalDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure ALL settings exist to avoid MissingSettings exception
        $keys = [
            // Legal Documents
            'terms_and_conditions',
            'privacy_policy',
            'disclaimer',
            'about_us',
            // GoWA API Configuration
            'gowa_username',
            'gowa_password',
            'gowa_api_url',
            'test_phone',
            // Logo
            'logo_path',
            'logo_url',
            'favicon_path',
            // SEO Settings
            'seo_meta_title',
            'seo_meta_description',
            'seo_meta_keywords',
            // Contact Information
            'contact_email',
            'contact_whatsapp',
        ];
        
        foreach ($keys as $key) {
            if (!\Illuminate\Support\Facades\DB::table('settings')->where('group', 'general')->where('name', $key)->exists()) {
                \Illuminate\Support\Facades\DB::table('settings')->insert([
                    'group' => 'general',
                    'name' => $key,
                    'locked' => false,
                    'payload' => json_encode(null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $settings = app(GeneralSettings::class);
        // Syarat & Ketentuan
        $settings->terms_and_conditions = '
            <h2>1. Pendahuluan</h2>
            <p>Selamat datang di platform MND Properti. Dengan mengakses dan menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui Syarat dan Ketentuan ini. Jika Anda tidak menyetujui salah satu bagian dari ketentuan ini, mohon untuk tidak melanjutkan penggunaan layanan kami.</p>
            
            <h2>2. Definisi</h2>
            <ul>
                <li><strong>Platform:</strong> Merujuk pada website dan layanan MND Properti.</li>
                <li><strong>Pengguna:</strong> Siapapun yang mengakses platform, termasuk pembeli, penjual, dan afiliasi.</li>
                <li><strong>Afiliasi:</strong> Mitra yang terdaftar untuk mempromosikan properti dan mendapatkan komisi.</li>
            </ul>

            <h2>3. Program Afiliasi</h2>
            <p>Untuk bergabung sebagai Afiliasi, Anda wajib memberikan informasi yang akurat dan valid. Kami berhak menolak atau menghentikan akun afiliasi yang terindikasi melakukan kecurangan atau pelanggaran.</p>
            
            <h3>3.1. Komisi</h3>
            <p>Komisi akan dibayarkan sesuai dengan persentase yang telah ditentukan untuk setiap properti yang berhasil terjual melalui link referral Anda. Pembayaran dilakukan setelah transaksi properti selesai sepenuhnya.</p>

            <h2>4. Hak Kekayaan Intelektual</h2>
            <p>Seluruh konten dalam platform ini, termasuk logo, teks, gambar, dan desain adalah milik MND Properti dan dilindungi oleh undang-undang hak cipta.</p>
        ';

        // Kebijakan Privasi
        $settings->privacy_policy = '
            <h2>1. Pengumpulan Informasi</h2>
            <p>Kami mengumpulkan informasi yang Anda berikan secara langsung saat mendaftar, seperti nama, alamat email, nomor telepon, dan data rekening bank untuk keperluan pembayaran komisi.</p>

            <h2>2. Penggunaan Data</h2>
            <p>Data yang kami kumpulkan digunakan untuk:</p>
            <ul>
                <li>Memproses transaksi dan pembayaran komisi.</li>
                <li>Mengirimkan informasi pembaruan properti dan promosi.</li>
                <li>Meningkatkan kualitas layanan dan pengalaman pengguna.</li>
            </ul>

            <h2>3. Keamanan Data</h2>
            <p>Kami berkomitmen untuk melindungi data pribadi Anda. Kami menerapkan langkah-langkah keamanan teknis dan organisasional yang sesuai untuk mencegah akses tidak sah, perubahan, atau penyalahgunaan data.</p>

            <h2>4. Cookies</h2>
            <p>Platform kami menggunakan cookies untuk menyimpan preferensi pengguna dan melacak kinerja referral afiliasi. Anda dapat mengatur browser Anda untuk menolak cookies, namun hal ini dapat mempengaruhi fungsionalitas website.</p>
        ';

        // Disclaimer
        $settings->disclaimer = '
            <h2>1. Akurasi Informasi Properti</h2>
            <p>Meskipun kami berusaha menyajikan data seakurat mungkin, informasi properti (harga, luas, spesifikasi) yang ditampilkan di platform ini dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya. Kami menyarankan calon pembeli untuk memverifikasi langsung detail properti dengan tim kami.</p>

            <h2>2. Batasan Tanggung Jawab</h2>
            <p>MND Properti tidak bertanggung jawab atas kerugian langsung maupun tidak langsung yang timbul akibat penggunaan informasi dari platform ini. Keputusan investasi properti sepenuhnya menjadi tanggung jawab pengguna.</p>

            <h2>3. Tautan Pihak Ketiga</h2>
            <p>Website ini mungkin berisi tautan ke situs web pihak ketiga. Kami tidak memiliki kendali atas konten atau kebijakan privasi situs-situs tersebut dan tidak bertanggung jawab atas segala risiko yang mungkin timbul dari penggunaannya.</p>
        ';

        // About Us
        $settings->about_us = '
            <h2>Tentang MND Properti</h2>
            <p>Selamat datang di platform MND Properti. MND properties adalah perusahaan yang bergerak di bidang pemasaran properti digital yang inovatif dan terpercaya. Kami hadir untuk menjembatani kebutuhan hunian masyarakat Indonesia dengan menyediakan akses mudah ke berbagai pilihan properti berkualitas.</p>

            <p>Dengan dukungan teknologi terkini dan jaringan afiliasi yang luas, kami berkomitmen untuk memberikan pengalaman jual beli properti yang transparan, efisien, dan menguntungkan bagi semua pihak. Kami percaya bahwa setiap orang berhak memiliki hunian impian mereka, dan kami di sini untuk mewujudkannya.</p>

            <h2>Visi Kami</h2>
            <p>Menjadi platform properti nomor satu di Indonesia yang memberdayakan masyarakat melalui peluang bisnis afiliasi dan solusi hunian terbaik.</p>

            <h2>Misi Kami</h2>
            <ul>
                <li>Menyediakan listing properti yang lengkap, akurat, dan terverifikasi.</li>
                <li>Menciptakan peluang penghasilan tambahan bagi masyarakat melalui program afiliasi yang adil dan transparan.</li>
                <li>Memberikan pelayanan prima dan edukasi properti kepada seluruh pengguna platform.</li>
            </ul>
        ';

        $settings->save();

        $this->command->info('General settings and legal documents have been populated successfully!');
    }
}
