<?php

namespace Database\Seeders;

use App\Models\ZktecoSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZktecoSettingsTabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ZktecoSettings::create(
            [
                'username' => 'admin',
                'password' => 'ciptakanjuara123',
                'notes' => 'untuk login ke bioTime',
                'is_login' => true,
            ]
        );
    }
}
