<?php

namespace Database\Seeders;

use App\Models\Data;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'nama' => 'John Doe',
                'alamat' => '123 Main Street',
            ],
            [
                'nama' => 'Jane Smith',
                'alamat' => '456 Elm Street',
            ],
            // Tambahkan data dummy lainnya di sini
        ];

        // Insert data dummy ke tabel "datas"
        foreach ($data as $item) {
            Data::create($item);
        }
    }
}
