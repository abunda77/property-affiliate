<?php

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            // Published properties (12)
            [
                'title' => 'Rumah Mewah di Menteng Jakarta Pusat',
                'price' => 8500000000,
                'location' => 'Menteng, Jakarta Pusat',
                'description' => 'Rumah mewah dengan desain modern minimalis di kawasan elite Menteng. Lokasi strategis dekat dengan pusat bisnis dan fasilitas umum. Lingkungan asri dan aman dengan keamanan 24 jam.',
                'features' => ['Swimming Pool', 'Garden', 'Carport 3 Mobil', 'Security 24/7', 'Smart Home System', 'Rooftop Terrace'],
                'specs' => [
                    'Luas Tanah' => '450 m²',
                    'Luas Bangunan' => '380 m²',
                    'Kamar Tidur' => '5',
                    'Kamar Mandi' => '4',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Villa Modern di Ubud Bali',
                'price' => 4200000000,
                'location' => 'Ubud, Bali',
                'description' => 'Villa modern dengan pemandangan sawah yang menakjubkan. Desain arsitektur Bali kontemporer dengan fasilitas lengkap. Cocok untuk investasi villa rental atau hunian pribadi.',
                'features' => ['Infinity Pool', 'Rice Field View', 'Tropical Garden', 'Open Kitchen', 'Gazebo', 'Parking Area'],
                'specs' => [
                    'Luas Tanah' => '600 m²',
                    'Luas Bangunan' => '280 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '3',
                    'Lantai' => '1',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Apartemen Luxury di Sudirman Jakarta',
                'price' => 3500000000,
                'location' => 'Sudirman, Jakarta Selatan',
                'description' => 'Apartemen mewah dengan view kota Jakarta yang spektakuler. Fasilitas lengkap termasuk gym, swimming pool, dan sky lounge. Lokasi premium di jantung CBD Jakarta.',
                'features' => ['City View', 'Fully Furnished', 'Gym Access', 'Swimming Pool', 'Sky Lounge', 'Concierge Service'],
                'specs' => [
                    'Luas Bangunan' => '180 m²',
                    'Kamar Tidur' => '3',
                    'Kamar Mandi' => '2',
                    'Lantai' => '35',
                    'Sertifikat' => 'Strata Title',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Rumah Cluster di BSD Tangerang',
                'price' => 2800000000,
                'location' => 'BSD City, Tangerang Selatan',
                'description' => 'Rumah cluster modern dengan konsep smart living. Lingkungan nyaman dan aman dengan fasilitas lengkap. Akses mudah ke tol dan stasiun kereta.',
                'features' => ['Clubhouse', 'Jogging Track', 'Playground', 'Security 24/7', 'CCTV', 'One Gate System'],
                'specs' => [
                    'Luas Tanah' => '200 m²',
                    'Luas Bangunan' => '180 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '3',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Townhouse Minimalis di Kemang Jakarta',
                'price' => 5500000000,
                'location' => 'Kemang, Jakarta Selatan',
                'description' => 'Townhouse eksklusif dengan desain minimalis modern. Lokasi strategis di kawasan Kemang yang terkenal dengan lifestyle dan kuliner. Cocok untuk keluarga muda.',
                'features' => ['Private Pool', 'Rooftop Garden', 'Smart Home', 'Carport 2 Mobil', 'Maid Room', 'Storage Room'],
                'specs' => [
                    'Luas Tanah' => '150 m²',
                    'Luas Bangunan' => '250 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '3',
                    'Lantai' => '3',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Rumah Tropis di Pondok Indah',
                'price' => 12000000000,
                'location' => 'Pondok Indah, Jakarta Selatan',
                'description' => 'Rumah mewah dengan konsep tropical modern. Taman luas dengan kolam renang dan gazebo. Lokasi premium di kawasan Pondok Indah yang prestisius.',
                'features' => ['Large Swimming Pool', 'Tropical Garden', 'Gazebo', 'Home Theater', 'Wine Cellar', 'Gym Room'],
                'specs' => [
                    'Luas Tanah' => '800 m²',
                    'Luas Bangunan' => '600 m²',
                    'Kamar Tidur' => '6',
                    'Kamar Mandi' => '5',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Kost Eksklusif di Senopati Jakarta',
                'price' => 6500000000,
                'location' => 'Senopati, Jakarta Selatan',
                'description' => 'Kost eksklusif dengan 12 kamar fully furnished. ROI tinggi dengan okupansi 95%. Lokasi strategis dekat dengan kantor dan pusat hiburan.',
                'features' => ['Fully Furnished', 'AC', 'WiFi', 'Laundry', 'Pantry', 'CCTV'],
                'specs' => [
                    'Luas Tanah' => '250 m²',
                    'Luas Bangunan' => '400 m²',
                    'Jumlah Kamar' => '12',
                    'Kamar Mandi' => '12',
                    'Lantai' => '4',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Rumah Klasik di Menteng Dalam',
                'price' => 15000000000,
                'location' => 'Menteng Dalam, Jakarta Pusat',
                'description' => 'Rumah klasik dengan arsitektur kolonial yang terawat. Lokasi sangat strategis di pusat kota. Cocok untuk kantor atau hunian mewah.',
                'features' => ['Classic Architecture', 'Large Garden', 'High Ceiling', 'Carport 4 Mobil', 'Servant Quarter', 'Security Post'],
                'specs' => [
                    'Luas Tanah' => '1000 m²',
                    'Luas Bangunan' => '700 m²',
                    'Kamar Tidur' => '7',
                    'Kamar Mandi' => '6',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Rumah Modern di Bintaro Sektor 9',
                'price' => 3200000000,
                'location' => 'Bintaro Sektor 9, Tangerang Selatan',
                'description' => 'Rumah modern dengan desain industrial. Lokasi strategis dekat dengan sekolah internasional dan mall. Lingkungan nyaman dan aman.',
                'features' => ['Modern Design', 'Open Space', 'Carport 2 Mobil', 'Garden', 'CCTV', 'Smart Lock'],
                'specs' => [
                    'Luas Tanah' => '180 m²',
                    'Luas Bangunan' => '200 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '3',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Villa Pantai di Canggu Bali',
                'price' => 7800000000,
                'location' => 'Canggu, Bali',
                'description' => 'Villa pantai dengan akses langsung ke beach club. Desain modern tropical dengan infinity pool. Investasi menguntungkan untuk villa rental.',
                'features' => ['Beach Access', 'Infinity Pool', 'Ocean View', 'Outdoor Shower', 'BBQ Area', 'Parking'],
                'specs' => [
                    'Luas Tanah' => '500 m²',
                    'Luas Bangunan' => '320 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '4',
                    'Lantai' => '2',
                    'Sertifikat' => 'Leasehold 25 tahun',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Rumah Minimalis di Cilandak Jakarta',
                'price' => 4500000000,
                'location' => 'Cilandak, Jakarta Selatan',
                'description' => 'Rumah minimalis dengan taman yang asri. Lokasi tenang namun dekat dengan akses tol dan fasilitas umum. Cocok untuk keluarga.',
                'features' => ['Garden', 'Carport 2 Mobil', 'Maid Room', 'Storage', 'CCTV', 'Water Heater'],
                'specs' => [
                    'Luas Tanah' => '280 m²',
                    'Luas Bangunan' => '220 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '3',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],
            [
                'title' => 'Penthouse Mewah di SCBD Jakarta',
                'price' => 18000000000,
                'location' => 'SCBD, Jakarta Selatan',
                'description' => 'Penthouse eksklusif dengan private lift dan rooftop pool. View 360 derajat kota Jakarta. Fasilitas super premium dan lokasi paling bergengsi.',
                'features' => ['Private Lift', 'Rooftop Pool', '360 City View', 'Fully Furnished', 'Smart Home', 'Concierge 24/7'],
                'specs' => [
                    'Luas Bangunan' => '450 m²',
                    'Kamar Tidur' => '4',
                    'Kamar Mandi' => '4',
                    'Lantai' => '45-46',
                    'Sertifikat' => 'Strata Title',
                ],
                'status' => PropertyStatus::PUBLISHED,
            ],

            // Draft properties (5)
            [
                'title' => 'Rumah Baru di Serpong',
                'price' => 2500000000,
                'location' => 'Serpong, Tangerang Selatan',
                'description' => 'Rumah baru siap huni dengan desain modern. Masih dalam tahap finishing.',
                'features' => ['Carport', 'Garden', 'CCTV'],
                'specs' => [
                    'Luas Tanah' => '150 m²',
                    'Luas Bangunan' => '120 m²',
                    'Kamar Tidur' => '3',
                    'Kamar Mandi' => '2',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::DRAFT,
            ],
            [
                'title' => 'Apartemen Studio di Kuningan',
                'price' => 1200000000,
                'location' => 'Kuningan, Jakarta Selatan',
                'description' => 'Apartemen studio dengan view kota. Sedang dalam proses dokumentasi foto.',
                'features' => ['Furnished', 'City View', 'Swimming Pool'],
                'specs' => [
                    'Luas Bangunan' => '35 m²',
                    'Kamar Tidur' => '1',
                    'Kamar Mandi' => '1',
                    'Lantai' => '20',
                    'Sertifikat' => 'Strata Title',
                ],
                'status' => PropertyStatus::DRAFT,
            ],
            [
                'title' => 'Ruko Strategis di Gading Serpong',
                'price' => 5000000000,
                'location' => 'Gading Serpong, Tangerang',
                'description' => 'Ruko 3 lantai di lokasi strategis. Masih dalam proses verifikasi dokumen.',
                'features' => ['3 Floors', 'Wide Frontage', 'Parking Area'],
                'specs' => [
                    'Luas Tanah' => '100 m²',
                    'Luas Bangunan' => '280 m²',
                    'Lantai' => '3',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::DRAFT,
            ],
            [
                'title' => 'Tanah Kavling di Sentul',
                'price' => 800000000,
                'location' => 'Sentul, Bogor',
                'description' => 'Tanah kavling siap bangun. Sedang dalam proses survey.',
                'features' => ['Flat Land', 'Ready to Build', 'Access Road'],
                'specs' => [
                    'Luas Tanah' => '400 m²',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::DRAFT,
            ],
            [
                'title' => 'Rumah Subsidi di Cibubur',
                'price' => 450000000,
                'location' => 'Cibubur, Jakarta Timur',
                'description' => 'Rumah subsidi type 36. Masih dalam proses listing.',
                'features' => ['Carport', 'Small Garden'],
                'specs' => [
                    'Luas Tanah' => '72 m²',
                    'Luas Bangunan' => '36 m²',
                    'Kamar Tidur' => '2',
                    'Kamar Mandi' => '1',
                    'Lantai' => '1',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::DRAFT,
            ],

            // Sold properties (3)
            [
                'title' => 'Rumah Dijual Cepat di Kelapa Gading',
                'price' => 6000000000,
                'location' => 'Kelapa Gading, Jakarta Utara',
                'description' => 'Rumah mewah yang sudah terjual. Transaksi selesai bulan lalu.',
                'features' => ['Swimming Pool', 'Garden', 'Carport 3 Mobil', 'Security 24/7'],
                'specs' => [
                    'Luas Tanah' => '350 m²',
                    'Luas Bangunan' => '300 m²',
                    'Kamar Tidur' => '5',
                    'Kamar Mandi' => '4',
                    'Lantai' => '2',
                    'Sertifikat' => 'SHM',
                ],
                'status' => PropertyStatus::SOLD,
            ],
            [
                'title' => 'Apartemen Terjual di Thamrin',
                'price' => 4500000000,
                'location' => 'Thamrin, Jakarta Pusat',
                'description' => 'Apartemen premium yang sudah terjual.',
                'features' => ['Fully Furnished', 'City View', 'Gym', 'Swimming Pool'],
                'specs' => [
                    'Luas Bangunan' => '150 m²',
                    'Kamar Tidur' => '3',
                    'Kamar Mandi' => '2',
                    'Lantai' => '28',
                    'Sertifikat' => 'Strata Title',
                ],
                'status' => PropertyStatus::SOLD,
            ],
            [
                'title' => 'Villa Terjual di Seminyak Bali',
                'price' => 9000000000,
                'location' => 'Seminyak, Bali',
                'description' => 'Villa mewah yang sudah terjual kepada investor asing.',
                'features' => ['Private Pool', 'Beach Access', 'Modern Design', 'Fully Furnished'],
                'specs' => [
                    'Luas Tanah' => '600 m²',
                    'Luas Bangunan' => '400 m²',
                    'Kamar Tidur' => '5',
                    'Kamar Mandi' => '5',
                    'Lantai' => '2',
                    'Sertifikat' => 'Leasehold 30 tahun',
                ],
                'status' => PropertyStatus::SOLD,
            ],
        ];

        foreach ($properties as $propertyData) {
            $slug = Str::slug($propertyData['title']);
            
            // Check if property already exists
            $property = Property::where('slug', $slug)->first();
            
            if (!$property) {
                $property = Property::create([
                    'title' => $propertyData['title'],
                    'slug' => $slug,
                    'price' => $propertyData['price'],
                    'location' => $propertyData['location'],
                    'description' => $propertyData['description'],
                    'features' => $propertyData['features'],
                    'specs' => $propertyData['specs'],
                    'status' => $propertyData['status'],
                ]);

                $this->command->info("Created property: {$propertyData['title']}");
            }
        }

        $this->command->info('Properties seeded successfully!');
        $this->command->info('Total: 20 properties (12 published, 5 draft, 3 sold)');
        $this->command->warn('Note: Sample images not attached. Use media library to upload images manually.');
    }
}
