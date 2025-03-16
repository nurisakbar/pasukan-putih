<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class KunjunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $createdAt = $faker->dateTimeBetween('-20 days', 'now');
        
        for ($i = 0; $i < 100; $i++) {
            DB::table('kunjungans')->insert([
                'id'            => Str::uuid()->toString(),
                'tanggal'       => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'pasien_id'     => DB::table('pasiens')->inRandomOrder()->value('id'),
                'user_id'       => DB::table('users')->inRandomOrder()->value('id'),
                'hasil_periksa' => $faker->sentence,
                'jenis'         => $faker->randomElement(['Rencana', 'Lanjutan']),
                'status'        => $faker->randomElement(['Sudah','Belum']),
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);
        }
    }
}
