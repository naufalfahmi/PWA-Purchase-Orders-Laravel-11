<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => '3',
                'nama_supplier' => 'AGEN GULA MERAH',
                'alamat_supplier' => 'JL. RAYA GULA MERAH NO. 123',
                'telp' => '021-1234567',
                'fax' => '021-1234568',
                'email' => 'agen@gulamerah.com',
            ],
            [
                'kode_supplier' => 'AG6630',
                'nama_supplier' => 'AGROMAS ALAM LESTARI',
                'alamat_supplier' => 'JL. TAMAN SARI NO. 45',
                'telp' => '021-2345678',
                'fax' => '021-2345679',
                'email' => 'info@agromas.com',
            ],
            [
                'kode_supplier' => 'AICE52',
                'nama_supplier' => 'AICE ICE CREAM',
                'alamat_supplier' => 'JL. ES KRIM NO. 67',
                'telp' => '021-3456789',
                'fax' => '021-3456790',
                'email' => 'sales@aice.com',
            ],
            [
                'kode_supplier' => '220007',
                'nama_supplier' => 'AINIE SNACK',
                'alamat_supplier' => 'PARAKAN CIOMAS, BOGOR',
                'telp' => '81295096190',
                'fax' => '0251-123456',
                'email' => 'order@ainie.com',
            ],
            [
                'kode_supplier' => 'HT1',
                'nama_supplier' => 'AMDAL MEGAH SARI',
                'alamat_supplier' => 'JL. MEGAH SARI NO. 89',
                'telp' => '021-4567890',
                'fax' => '021-4567891',
                'email' => 'info@amdal.com',
            ],
            [
                'kode_supplier' => '50',
                'nama_supplier' => 'BANGKOK',
                'alamat_supplier' => 'JL. BANGKOK NO. 101, BOGOR',
                'telp' => '2518380421',
                'fax' => '0251-8380422',
                'email' => 'sales@bangkok.com',
            ],
            [
                'kode_supplier' => '240503',
                'nama_supplier' => 'BOGOR ERATEL',
                'alamat_supplier' => 'JL AHMAD YANI BOGOR',
                'telp' => '0251-5678901',
                'fax' => '0251-5678902',
                'email' => 'info@bogoreratel.com',
            ],
            [
                'kode_supplier' => 'C001',
                'nama_supplier' => 'CAHAYA MAKMUR',
                'alamat_supplier' => 'JL. CAHAYA NO. 111',
                'telp' => '021-6789012',
                'fax' => '021-6789013',
                'email' => 'sales@cahaya.com',
            ],
            [
                'kode_supplier' => 'D002',
                'nama_supplier' => 'DUTA PRIMA',
                'alamat_supplier' => 'JL. DUTA NO. 222',
                'telp' => '021-7890123',
                'fax' => '021-7890124',
                'email' => 'order@duta.com',
            ],
            [
                'kode_supplier' => 'E003',
                'nama_supplier' => 'EKA SEJAHTERA',
                'alamat_supplier' => 'JL. EKA NO. 333',
                'telp' => '021-8901234',
                'fax' => '021-8901235',
                'email' => 'info@eka.com',
            ],
            [
                'kode_supplier' => 'F004',
                'nama_supplier' => 'FAJAR INDAH',
                'alamat_supplier' => 'JL. FAJAR NO. 444',
                'telp' => '021-9012345',
                'fax' => '021-9012346',
                'email' => 'sales@fajar.com',
            ],
            [
                'kode_supplier' => 'G005',
                'nama_supplier' => 'GEMILANG JAYA',
                'alamat_supplier' => 'JL. GEMILANG NO. 555',
                'telp' => '021-0123456',
                'fax' => '021-0123457',
                'email' => 'order@gemilang.com',
            ],
            [
                'kode_supplier' => 'H006',
                'nama_supplier' => 'HARMONI SELARAS',
                'alamat_supplier' => 'JL. HARMONI NO. 666',
                'telp' => '021-1234567',
                'fax' => '021-1234568',
                'email' => 'info@harmoni.com',
            ],
            [
                'kode_supplier' => 'I007',
                'nama_supplier' => 'INDONESIA MAKMUR',
                'alamat_supplier' => 'JL. INDONESIA NO. 777',
                'telp' => '021-2345678',
                'fax' => '021-2345679',
                'email' => 'sales@indonesia.com',
            ],
            [
                'kode_supplier' => 'J008',
                'nama_supplier' => 'JAYA ABADI',
                'alamat_supplier' => 'JL. JAYA NO. 888',
                'telp' => '021-3456789',
                'fax' => '021-3456790',
                'email' => 'order@jaya.com',
            ],
            // Additional Suppliers
            [
                'kode_supplier' => 'SP001',
                'nama_supplier' => 'SUPPLIER MINUMAN',
                'alamat_supplier' => 'JL SUDIRMAN NO 123 JAKARTA',
                'telp' => '0215551234',
                'fax' => '0215551235',
                'email' => 'minuman@supplier.com',
            ],
            [
                'kode_supplier' => 'SP002',
                'nama_supplier' => 'TOKO ROTI BAKERY',
                'alamat_supplier' => 'JL GATOT SUBROTO NO 456 BANDUNG',
                'telp' => '0225556789',
                'fax' => '0225556790',
                'email' => 'bakery@tokeroti.com',
            ],
            [
                'kode_supplier' => 'SP003',
                'nama_supplier' => 'DISTRIBUTOR KOSMETIK',
                'alamat_supplier' => 'JL THAMRIN NO 789 SURABAYA',
                'telp' => '0315559012',
                'fax' => '0315559013',
                'email' => 'kosmetik@distributor.com',
            ],
            [
                'kode_supplier' => 'SP004',
                'nama_supplier' => 'SUPPLIER PERALATAN RUMAH',
                'alamat_supplier' => 'JL PASAR BARU NO 321 YOGYAKARTA',
                'telp' => '0274555678',
                'fax' => '0274555679',
                'email' => 'rumah@supplier.com',
            ],
            [
                'kode_supplier' => 'SP005',
                'nama_supplier' => 'DISTRIBUTOR OLAHRAGA',
                'alamat_supplier' => 'JL KEMANG RAYA NO 654 JAKARTA',
                'telp' => '0215553456',
                'fax' => '0215553457',
                'email' => 'olahraga@distributor.com',
            ],
            [
                'kode_supplier' => 'SP006',
                'nama_supplier' => 'SUPPLIER BUKU DAN ALAT TULIS',
                'alamat_supplier' => 'JL PETA NO 987 BANDUNG',
                'telp' => '0225557890',
                'fax' => '0225557891',
                'email' => 'buku@supplier.com',
            ],
            [
                'kode_supplier' => 'SP007',
                'nama_supplier' => 'DISTRIBUTOR MAKANAN RINGAN',
                'alamat_supplier' => 'JL DIPONEGORO NO 147 SEMARANG',
                'telp' => '0245552468',
                'fax' => '0245552469',
                'email' => 'ringan@distributor.com',
            ],
            [
                'kode_supplier' => 'SP008',
                'nama_supplier' => 'SUPPLIER KESEHATAN',
                'alamat_supplier' => 'JL KESEHATAN NO 258 MEDAN',
                'telp' => '0615551357',
                'fax' => '0615551358',
                'email' => 'kesehatan@supplier.com',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['kode_supplier' => $supplier['kode_supplier']],
                $supplier
            );
        }
    }
}
