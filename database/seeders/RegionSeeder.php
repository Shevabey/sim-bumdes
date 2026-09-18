<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::create(['id_region' => 'REG-3404', 'level' => 'provinsi', 'nama' => 'DI Yogyakarta']);
        Region::create(['id_region' => 'REG-3404-04', 'level' => 'kota', 'parent_id' => 'REG-3404', 'nama' => 'Kabupaten Sleman']);
        Region::create(['id_region' => 'REG-3404-04-070', 'level' => 'kecamatan', 'parent_id' => 'REG-3404-04', 'nama' => 'Minggir']);

        $kelurahan = [
            ['id_region' => 'REG-3404-04-070-001', 'nama' => 'Sendangagung', 'is_koordinator' => false],
            ['id_region' => 'REG-3404-04-070-002', 'nama' => 'Sendangarum', 'is_koordinator' => false],
            ['id_region' => 'REG-3404-04-070-003', 'nama' => 'Sendangmulyo', 'is_koordinator' => false],
            ['id_region' => 'REG-3404-04-070-004', 'nama' => 'Sendangrejo', 'is_koordinator' => false],
            ['id_region' => 'REG-3404-04-070-005', 'nama' => 'Sendangsari', 'is_koordinator' => true],
        ];

        foreach ($kelurahan as $k) {
            Region::create([
                'id_region' => $k['id_region'],
                'level' => 'kelurahan',
                'parent_id' => 'REG-3404-04-070',
                'nama' => $k['nama'],
                'is_koordinator' => $k['is_koordinator'],
            ]);
        }
    }
}
