<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach(range(1,10) as $index){
            Book::create([
                'title' => $faker->sentence(3),  // Tạo tiêu đề sách giả
                'author' => $faker->name,  // Tạo tên tác giả giả
                'published_year' => $faker->year,  // Tạo năm xuất bản giả
            ]);
        }
    }
}
