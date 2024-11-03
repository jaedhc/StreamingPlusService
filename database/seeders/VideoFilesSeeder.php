<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VideoFiles;

class VideoFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VideoFiles::create(['type' => 'video']);
        VideoFiles::create(['type' => 'thumbnail']);
    }
}
