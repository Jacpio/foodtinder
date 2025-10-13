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
            'Meksykańska',
            'Hiszpańska',
            'Amerykańska',
            'Indyjska',
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
        $flavoursMap = Flavour::pluck('id', 'name');
        $dishes = [
            ['name' => 'Spaghetti Bolognese', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'spahgetti.jpg'],
            ['name' => 'Pierogi ruskie', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'pierogi_ruskie.jpg'],
            ['name' => 'Zupa pomidorowa', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zupa_pomidorowa.jpg'],
            ['name' => 'Tacos', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'tacos.jpg'],
            ['name' => 'Panna Cotta', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'panna_cotta.jpg'],
            ['name' => 'Smażone pierożki wonton', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'smazone_pierozki_wonton.jpg'],
            ['name' => 'Lasagne', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'lasagne.jpg'],
            ['name' => 'Krem z brokułów', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'krem_z_brokulow.jpg'],
            ['name' => 'Guacamole', 'category' => 'Przekąski', 'cuisine' => 'Meksykańska', 'flavour' => 'Kwaśny', 'image_url' => 'guacamole.jpg'],
            ['name' => 'Tiramisu', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'tiramisu.jpg'],
            ['name' => 'Pho', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'pho.jpg'],
            ['name' => 'Risotto z grzybami', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'risotto_z_grzybami.jpg'],
            ['name' => 'Placki ziemniaczane', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'placki_ziemniaczane.jpg'],
            ['name' => 'Sernik', 'category' => 'Desery', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'sernik.jpg'],
            ['name' => 'Ramen', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'ramen.jpg'],
            ['name' => 'Quesadilla', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'quesadilla.jpg'],
            ['name' => 'Caprese', 'category' => 'Przekąski', 'cuisine' => 'Włoska', 'flavour' => 'Kwaśny', 'image_url' => 'caprese.jpg'],
            ['name' => 'Sushi', 'category' => 'Dania główne', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'sushi.jpg'],
            ['name' => 'Lody waniliowe', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'lody_waniliowe.jpg'],
            ['name' => 'Bigos', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'bigos.jpg'],
            ['name' => 'Gazpacho', 'category' => 'Zupy', 'cuisine' => 'Hiszpańska', 'flavour' => 'Kwaśny', 'image_url' => 'gazpacho.jpg'],
            ['name' => 'Tortilla de patatas', 'category' => 'Dania główne', 'cuisine' => 'Hiszpańska', 'flavour' => 'Słony', 'image_url' => 'tortilla_de_patatas.jpg'],
            ['name' => 'Pad Thai', 'category' => 'Dania główne', 'cuisine' => 'Azjatycka', 'flavour' => 'Słodki', 'image_url' => 'pad_thai.jpg'],
            ['name' => 'Czekoladowe brownie', 'category' => 'Desery', 'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'czekoladowe_brownie.jpg'],
            ['name' => 'Żurek', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zurek.jpg'],
            ['name' => 'Nachosy z serem', 'category' => 'Przekąski', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'nachosy_z_serem.jpg'],
            ['name' => 'Frittata', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'frittata.jpg'],
            ['name' => 'Kopytka', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'kopytka.jpg'],
            ['name' => 'Makaron z pesto', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'makaron_z_pesto.jpg'],
            ['name' => 'Zupa miso', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'zupa_miso.jpg'],
            ['name' => 'Churros', 'category' => 'Desery', 'cuisine' => 'Hiszpańska', 'flavour' => 'Słodki', 'image_url' => 'churros.jpg'],
            ['name' => 'Spring rolls', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'spring_rolls.jpg'],
            ['name' => 'Sałatka z kurczakiem', 'category' => 'Przekąski', 'cuisine' => 'Amerykańska', 'flavour' => 'Słony', 'image_url' => 'salatka_z_kurczakiem.jpg'],
            ['name' => 'Krem z dyni', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'krem_z_dyni.jpg'],
            ['name' => 'Makaron carbonara', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'makaron_carbonara.jpg'],
            ['name' => 'Gnocchi', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'gnocchi.jpg'],
            ['name' => 'Burrito', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'burrito.jpg'],
            ['name' => 'Krem z kalafiora', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'krem_z_kalafiora.jpg'],
            ['name' => 'Makowiec', 'category' => 'Desery', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'makowiec.jpg'],
            ['name' => 'Kimchi', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Kwaśny', 'image_url' => 'kimchi.jpg'],
            ['name' => 'Curry z kurczakiem', 'category' => 'Dania główne', 'cuisine' => 'Indyjska', 'flavour' => 'Słony', 'image_url' => 'curry_z_kurczakiem.jpg'],
            ['name' => 'Chili con carne', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'chili_con_carne.jpg'],
            ['name' => 'Zupa ogórkowa', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zupa_ogorkowa.jpg'],
            ['name' => 'Brownie z orzechami', 'category' => 'Desery', 'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'brownie_z_orzechami.jpg'],
        ];


        foreach ($dishes as $dishData) {
            $category = Category::where('name', $dishData['category'])->first();
            $cuisine = Cuisine::where('name', $dishData['cuisine'])->first();
            $flavour = Flavour::where('name', $dishData['flavour'])->first();

            if (!$category || !$cuisine) {
                dump("Brakuje kategorii lub kuchni dla dania: " . $dishData['name']);
                dump("Kategoria: " . $dishData['category']);
                dump("Kuchnia: " . $dishData['cuisine']);
                continue;
            }

            Dish::create([
                'name' => $dishData['name'],
                'category_id' => $category->id,
                'cuisine_id' => $cuisine->id,
                'flavour_id' => $flavour->id,
                'description' => $dishData['description'] ?? '',
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
