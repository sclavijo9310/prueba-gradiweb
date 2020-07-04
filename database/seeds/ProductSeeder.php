<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        DB::table('products')->insert([
            'name' => 'Lavadora + secadora',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-1.jpg'
        ]);

        DB::table('products')->insert([
            'name' => 'Estufa con horno',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-2.webp'
        ]);

        DB::table('products')->insert([
            'name' => 'Nevera',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-3.jpg'
        ]);

        DB::table('products')->insert([
            'name' => 'Televisor de 70"',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-4.webp'
        ]);

        DB::table('products')->insert([
            'name' => 'Purificador de aire',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-5.png'
        ]);

        DB::table('products')->insert([
            'name' => 'Mueble para televisor',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-6.jpg'
        ]);

        DB::table('products')->insert([
            'name' => 'Sala con mesa de centro',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-7.webp'
        ]);

        DB::table('products')->insert([
            'name' => 'Asador a gas',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-8.webp'
        ]);

        DB::table('products')->insert([
            'name' => 'Comedor para exteriores con parasol',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-9.jpg'
        ]);

        DB::table('products')->insert([
            'name' => 'Bicicleta MTB 19"',
            'description' => $faker->text,
            'price' => $faker->numberBetween(250000, 50000000),
            'image' => 'images/product-10.webp'
        ]);
    }
}
