<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        
        $faker = Faker::create('id_ID'); 
        $province = Province::where('name', 'Jawa Barat')->first();
        $regency = Regency::where('name', 'Kabupaten Bandung')->first();
        $district = District::where('name', 'Cimenyan')->where('regency_id', $regency->id)->first();
        $village = Village::where('name', 'Ciburial')->where('district_id', $district->id)->first();
        $createdAt = $faker->dateTimeBetween('-3 days', 'now');
        $jenisKtp = $faker->randomElement(['DKI', 'Non DKI']);
        $tanggalLahir = $faker->date('Y-m-d', '-18 years');

        DB::table('pasiens')->insert([
            [
                'id' => Str::uuid()->toString(),
                'nik' => '3201123456789012',
                'name' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'Jenis_ktp' => 'DKI',
                'tanggal_lahir' => $tanggalLahir,
                'alamat' => 'Jl. Merdeka No. 10',
                'province_id' => $province->id,
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'nik' => '3201134567890123',
                'name' => 'Siti Aminah',
                'jenis_kelamin' => 'Perempuan',
                'Jenis_ktp' => 'Non DKI',
                'tanggal_lahir' => $tanggalLahir,
                'alamat' => 'Jl. Sudirman No. 45',
                'province_id' => $province->id,
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}