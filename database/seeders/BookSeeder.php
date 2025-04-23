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
                'title' =>$faker->sentence(3),
                'author' => $faker->name,
                'published_year' => $faker->year,
                'isbn' => $faker->isbn13,
                'price'=>$faker->randomFloat(2,5,100),
                'genre' => $faker->randomElement(['Fiction', 'Non-Fiction', 'Mystery', 'Sci-Fi', 'Fantasy']),
                'quantity' => $faker->numberBetween(1, 100),
                'description' => $faker->paragraph,
            ]);
        }
    }
}
