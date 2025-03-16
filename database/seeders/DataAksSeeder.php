<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DataAksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        
        for ($i = 0; $i < 100; $i++) {
            $createdAt = $faker->dateTimeBetween('-10 days', 'now');
            $kunjunganId = DB::table('kunjungans')->inRandomOrder()->value('id');
            $pasien = DB::table('pasiens')->where('id', DB::table('kunjungans')->where('id', $kunjunganId)->value('pasien_id'))->first();
            
            $babControl = $faker->numberBetween(0, 2);
            $bakControl = $faker->numberBetween(0, 2);
            $eating = $faker->numberBetween(0, 2);
            $stairs = $faker->numberBetween(0, 2);
            $bathing = $faker->numberBetween(0, 2);
            $transfer = $faker->numberBetween(0, 2);
            $walking = $faker->numberBetween(0, 2);
            $dressing = $faker->numberBetween(0, 2);
            $grooming = $faker->numberBetween(0, 2);
            $toiletUse = $faker->numberBetween(0, 2);
            
            $totalScore = $babControl + $bakControl + $eating + $stairs + $bathing + $transfer + $walking + $dressing + $grooming + $toiletUse;
            
            $butuhOrang = $faker->numberBetween(0, 1);
            $pendampingTetap = $faker->numberBetween(0, 1);
            
            $sasaranHomeService = ($pasien->jenis_ktp === 'DKI' && $butuhOrang === 1 && $pendampingTetap === 1 && $totalScore < 9) ? 1 : 0;
            
            DB::table('skrining_adl')->insert([
                'id'                  => Str::uuid()->toString(),
                'kunjungan_id'        => $kunjunganId,
                'pasien_id'           => $pasien->id,
                'bab_control'         => $babControl,
                'bak_control'         => $bakControl,
                'eating'              => $eating,
                'stairs'              => $stairs,
                'bathing'             => $bathing,
                'transfer'            => $transfer,
                'walking'             => $walking,
                'dressing'            => $dressing,
                'grooming'            => $grooming,
                'toilet_use'          => $toiletUse,
                'total_score'         => $totalScore,
                'butuh_orang'         => $butuhOrang,
                'pendamping_tetap'    => $pendampingTetap,
                'sasaran_home_service'=> $sasaranHomeService,
                'pemeriksa_id'        => DB::table('users')->inRandomOrder()->value('id'),
                'created_at'          => $createdAt,
                'updated_at'          => $createdAt,
            ]);
        }
    }
}
