<?php

namespace Database\Seeders;

use App\Models\PostArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        PostArticle::factory()->count(100000)->create();
    }
}
