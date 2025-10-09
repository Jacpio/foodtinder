<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryWeight;
use App\Models\Cuisine;
use App\Models\CuisineWeight;
use App\Models\Dish;
use App\Models\Flavour;
use App\Models\FlavourWeight;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $categories = [
            'Zupy',
            'Dania główne',
            'Desery',
            'Przekąski'
        ];
        foreach ($categories as $catName) {
            $categoriesArray[] = Category::create(['name' => $catName]);
        }

        $cuisines = [
            'Włoska',
            'Polska',
            'Azjatycka',
            'Meksykańska'
        ];

        foreach ($cuisines as $cuisineName) {
            $cuisinesArray[] = Cuisine::create(['name' => $cuisineName]);
        }


        $flavours = [
            'Słodki',
            'Kwaśny',
            'Słony',
            'Gorzki',
        ];

        foreach ($flavours as $flavourName) {
            $flavoursArray[] = Flavour::create(['name' => $flavourName]);
        }

        $dishes = [
            ['name' => 'Spaghetti Bolognese', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'description' => 'Klasyczne włoskie spaghetti z sosem mięsnym', 'image_url' => null],
            ['name' => 'Pierogi ruskie', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'description' => 'Pierogi z serem i ziemniakami', 'image_url' => null],
            ['name' => 'Zupa pomidorowa', 'category' => 'Zupy', 'cuisine' => 'Polska', 'description' => 'Klasyczna zupa pomidorowa z ryżem', 'image_url' => null],
            ['name' => 'Tacos', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'description' => 'Tradycyjne meksykańskie tacos', 'image_url' => null],
            ['name' => 'Panna Cotta', 'category' => 'Desery', 'cuisine' => 'Włoska', 'description' => 'Deser na bazie śmietany i żelatyny', 'image_url' => null],
            ['name' => 'Smażone pierożki wonton', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'description' => 'Chińskie pierożki smażone na złoto', 'image_url' => null],
        ];

        foreach ($dishes as $dishData) {
            $category = Category::where('name', $dishData['category'])->first();
            $cuisine = Cuisine::where('name', $dishData['cuisine'])->first();

            Dish::create([
                'name' => $dishData['name'],
                'category_id' => $category->id,
                'cuisine_id' => $cuisine->id,
                'description' => $dishData['description'],
                'image_url' => $dishData['image_url'],
            ]);
        }
        foreach ($categoriesArray as $category) {
            CategoryWeight::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'weight' => 0,
            ]);
        }


        foreach ($cuisinesArray as $cuisine) {
            CuisineWeight::create([
                'user_id' => $user->id,
                'cuisine_id' => $cuisine->id,
                'weight' => 0,
            ]);
        }

        foreach ($flavoursArray as $flavour) {
            FlavourWeight::create([
                'user_id' => $user->id,
                'flavour_id' => $flavour->id,
                'weight' => 0,
            ]);
        }

    }
}
