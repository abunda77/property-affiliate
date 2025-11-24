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
                'features' => [
                    ['feature' => 'Swimming Pool'],
                    ['feature' => 'Garden'],
                    ['feature' => 'Carport 3 Mobil'],
                    ['feature' => 'Security 24/7'],
                    ['feature' => 'Smart Home System'],
                    ['feature' => 'Rooftop Terrace'],
                ],
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
                'features' => [
                    ['feature' => 'Infinity Pool'],
                    ['feature' => 'Rice Field View'],
                    ['feature' => 'Tropical Garden'],
                    ['feature' => 'Open Kitchen'],
                    ['feature' => 'Gazebo'],
                    ['feature' => 'Parking Area'],
                ],
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
                'features' => [
                    ['feature' => 'City View'],
                    ['feature' => 'Fully Furnished'],
                    ['feature' => 'Gym Access'],
                    ['feature' => 'Swimming Pool'],
                    ['feature' => 'Sky Lounge'],
                    ['feature' => 'Concierge Service'],
                ],
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
                'features' => [
                    ['feature' => 'Clubhouse'],
                    ['feature' => 'Jogging Track'],
                    ['feature' => 'Playground'],
                    ['feature' => 'Security 24/7'],
                    ['feature' => 'CCTV'],
                    ['feature' => 'One Gate System'],
                ],
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
                'features' => [
                    ['feature' => 'Private Pool'],
                    ['feature' => 'Rooftop Garden'],
                    ['feature' => 'Smart Home'],
                    ['feature' => 'Carport 2 Mobil'],
                    ['feature' => 'Maid Room'],
                    ['feature' => 'Storage Room'],
                ],
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
                'features' => [
                    ['feature' => 'Large Swimming Pool'],
                    ['feature' => 'Tropical Garden'],
                    ['feature' => 'Gazebo'],
                    ['feature' => 'Home Theater'],
                    ['feature' => 'Wine Cellar'],
                    ['feature' => 'Gym Room'],
                ],
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
                'features' => [
                    ['feature' => 'Fully Furnished'],
                    ['feature' => 'AC'],
                    ['feature' => 'WiFi'],
                    ['feature' => 'Laundry'],
                    ['feature' => 'Pantry'],
                    ['feature' => 'CCTV'],
                ],
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
                'features' => [
                    ['feature' => 'Classic Architecture'],
                    ['feature' => 'Large Garden'],
                    ['feature' => 'High Ceiling'],
                    ['feature' => 'Carport 4 Mobil'],
                    ['feature' => 'Servant Quarter'],
                    ['feature' => 'Security Post'],
                ],
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
                'features' => [
                    ['feature' => 'Modern Design'],
                    ['feature' => 'Open Space'],
                    ['feature' => 'Carport 2 Mobil'],
                    ['feature' => 'Garden'],
                    ['feature' => 'CCTV'],
                    ['feature' => 'Smart Lock'],
                ],
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
                'features' => [
                    ['feature' => 'Beach Access'],
                    ['feature' => 'Infinity Pool'],
                    ['feature' => 'Ocean View'],
                    ['feature' => 'Outdoor Shower'],
                    ['feature' => 'BBQ Area'],
                    ['feature' => 'Parking'],
                ],
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
                'features' => [
                    ['feature' => 'Garden'],
                    ['feature' => 'Carport 2 Mobil'],
                    ['feature' => 'Maid Room'],
                    ['feature' => 'Storage'],
                    ['feature' => 'CCTV'],
                    ['feature' => 'Water Heater'],
                ],
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
                'features' => [
                    ['feature' => 'Private Lift'],
                    ['feature' => 'Rooftop Pool'],
                    ['feature' => '360 City View'],
                    ['feature' => 'Fully Furnished'],
                    ['feature' => 'Smart Home'],
                    ['feature' => 'Concierge 24/7'],
                ],
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
                'features' => [
                    ['feature' => 'Carport'],
                    ['feature' => 'Garden'],
                    ['feature' => 'CCTV'],
                ],
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
                'features' => [
                    ['feature' => 'Furnished'],
                    ['feature' => 'City View'],
                    ['feature' => 'Swimming Pool'],
                ],
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
                'features' => [
                    ['feature' => '3 Floors'],
                    ['feature' => 'Wide Frontage'],
                    ['feature' => 'Parking Area'],
                ],
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
                'features' => [
                    ['feature' => 'Flat Land'],
                    ['feature' => 'Ready to Build'],
                    ['feature' => 'Access Road'],
                ],
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
                'features' => [
                    ['feature' => 'Carport'],
                    ['feature' => 'Small Garden'],
                ],
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
                'features' => [
                    ['feature' => 'Swimming Pool'],
                    ['feature' => 'Garden'],
                    ['feature' => 'Carport 3 Mobil'],
                    ['feature' => 'Security 24/7'],
                ],
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
                'features' => [
                    ['feature' => 'Fully Furnished'],
                    ['feature' => 'City View'],
                    ['feature' => 'Gym'],
                    ['feature' => 'Swimming Pool'],
                ],
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
                'features' => [
                    ['feature' => 'Private Pool'],
                    ['feature' => 'Beach Access'],
                    ['feature' => 'Modern Design'],
                    ['feature' => 'Fully Furnished'],
                ],
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

            if (! $property) {
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
