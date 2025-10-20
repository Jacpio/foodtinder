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
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $admin = Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole($admin);

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
            ['name' => 'Spaghetti Bolognese', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'spahgetti.jpg', 'description' => 'Klasyczny makaron z sosem pomidorowo-mięsnym duszonym z ziołami i warzywami.'],
            ['name' => 'Pierogi ruskie', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'pierogi_ruskie.jpg', 'description' => 'Delikatne pierogi z farszem z ziemniaków i twarogu, podawane z cebulką.'],
            ['name' => 'Zupa pomidorowa', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zupa_pomidorowa.jpg', 'description' => 'Aromatyczna zupa na bulionie z dojrzałych pomidorów, często z ryżem lub makaronem.'],
            ['name' => 'Tacos', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'tacos.jpg', 'description' => 'Małe kukurydziane tortille wypełnione mięsem lub warzywami z salsą i limonką.'],
            ['name' => 'Panna Cotta', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'panna_cotta.jpg', 'description' => 'Jedwabisty włoski deser z gotowanej śmietanki, podawany z sosem owocowym.'],
            ['name' => 'Smażone pierożki wonton', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'smazone_pierozki_wonton.jpg', 'description' => 'Chrupiące azjatyckie pierożki smażone na złoto, nadziewane mięsem lub warzywami.'],
            ['name' => 'Lasagne', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'lasagne.jpg', 'description' => 'Warstwowy makaron zapiekany z sosem bolońskim, beszamelowym i serem.'],
            ['name' => 'Krem z brokułów', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'krem_z_brokulow.jpg', 'description' => 'Gęsta, aksamitna zupa zmiksowana z brokułów, często z dodatkiem śmietanki.'],
            ['name' => 'Guacamole', 'category' => 'Przekąski', 'cuisine' => 'Meksykańska', 'flavour' => 'Kwaśny', 'image_url' => 'guacamole.jpg', 'description' => 'Kremowa pasta z dojrzałego awokado z limonką, cebulą i kolendrą.'],
            ['name' => 'Tiramisu', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'tiramisu.jpg', 'description' => 'Deser na bazie biszkoptów nasączonych kawą i kremu z mascarpone, posypany kakao.'],
            ['name' => 'Pho', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'pho.jpg', 'description' => 'Wietnamska zupa na klarownym bulionie z makaronem ryżowym, ziołami i mięsem.'],
            ['name' => 'Risotto z grzybami', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'risotto_z_grzybami.jpg', 'description' => 'Kremowy ryż arborio powoli duszony z bulionem i aromatycznymi leśnymi grzybami.'],
            ['name' => 'Placki ziemniaczane', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'placki_ziemniaczane.jpg', 'description' => 'Chrupiące placki z tartych ziemniaków, smażone na złoto, podawane ze śmietaną.'],
            ['name' => 'Sernik', 'category' => 'Desery', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'sernik.jpg', 'description' => 'Puszysty wypiek z twarogu na kruchym spodzie, często z wanilią lub cytryną.'],
            ['name' => 'Ramen', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'ramen.jpg', 'description' => 'Japońska zupa z esencjonalnym bulionem, makaronem pszenicznym i licznymi dodatkami.'],
            ['name' => 'Quesadilla', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'quesadilla.jpg', 'description' => 'Złożona tortilla zapiekana z roztopionym serem i ulubionymi dodatkami.'],
            ['name' => 'Caprese', 'category' => 'Przekąski', 'cuisine' => 'Włoska', 'flavour' => 'Kwaśny', 'image_url' => 'caprese.jpg', 'description' => 'Sałatka z pomidorów, mozzarelli i bazylii skropiona oliwą i octem balsamicznym.'],
            ['name' => 'Sushi', 'category' => 'Dania główne', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'sushi.jpg', 'description' => 'Ryż zaprawiony octem ryżowym z rybą, warzywami lub jajkiem, rolowany lub formowany.'],
            ['name' => 'Lody waniliowe', 'category' => 'Desery', 'cuisine' => 'Włoska', 'flavour' => 'Słodki', 'image_url' => 'lody_waniliowe.jpg', 'description' => 'Klasyczne, kremowe lody o naturalnym aromacie wanilii.'],
            ['name' => 'Bigos', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'bigos.jpg', 'description' => 'Długo duszona kapusta z mięsem i kiełbasą, doprawiona śliwkami i przyprawami.'],
            ['name' => 'Gazpacho', 'category' => 'Zupy', 'cuisine' => 'Hiszpańska', 'flavour' => 'Kwaśny', 'image_url' => 'gazpacho.jpg', 'description' => 'Hiszpański chłodnik z surowych pomidorów i warzyw, podawany mocno schłodzony.'],
            ['name' => 'Tortilla de patatas', 'category' => 'Dania główne', 'cuisine' => 'Hiszpańska', 'flavour' => 'Słony', 'image_url' => 'tortilla_de_patatas.jpg', 'description' => 'Hiszpański omlet z jajek, ziemniaków i cebuli, smażony na patelni.'],
            ['name' => 'Pad Thai', 'category' => 'Dania główne', 'cuisine' => 'Azjatycka', 'flavour' => 'Słodki', 'image_url' => 'pad_thai.jpg', 'description' => 'Smażony makaron ryżowy z tamaryndowcem, kiełkami, orzeszkami i jajkiem.'],
            ['name' => 'Czekoladowe brownie', 'category' => 'Desery', 'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'czekoladowe_brownie.jpg', 'description' => 'Wilgotne, intensywnie czekoladowe ciasto o zwartej, fudgy konsystencji.'],
            ['name' => 'Żurek', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zurek.jpg', 'description' => 'Kwaśna zupa na zakwasie żytnim z białą kiełbasą, ziemniakami i jajkiem.'],
            ['name' => 'Nachosy z serem', 'category' => 'Przekąski', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'nachosy_z_serem.jpg', 'description' => 'Chrupiące trójkąty tortilli zapiekane z serem i pikantnymi dodatkami.'],
            ['name' => 'Frittata', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'frittata.jpg', 'description' => 'Włoski omlet pieczony z warzywami, serem lub wędliną, podawany na ciepło.'],
            ['name' => 'Kopytka', 'category' => 'Dania główne', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'kopytka.jpg', 'description' => 'Miękkie kluseczki ziemniaczane formowane w romby, serwowane z masłem lub sosem.'],
            ['name' => 'Makaron z pesto', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'makaron_z_pesto.jpg', 'description' => 'Makaron wymieszany z aromatycznym sosem z bazylii, orzeszków i parmezanu.'],
            ['name' => 'Zupa miso', 'category' => 'Zupy', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'zupa_miso.jpg', 'description' => 'Lekka japońska zupa na dashi z pastą miso, tofu i wodorostami.'],
            ['name' => 'Churros', 'category' => 'Desery', 'cuisine' => 'Hiszpańska', 'flavour' => 'Słodki', 'image_url' => 'churros.jpg', 'description' => 'Smażone pałeczki ciasta posypane cukrem, często serwowane z sosem czekoladowym.'],
            ['name' => 'Spring rolls', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Słony', 'image_url' => 'spring_rolls.jpg', 'description' => 'Świeże lub smażone ruloniki wypełnione warzywami i mięsem, podawane z dipem.'],
            ['name' => 'Sałatka z kurczakiem', 'category' => 'Przekąski', 'cuisine' => 'Amerykańska', 'flavour' => 'Słony', 'image_url' => 'salatka_z_kurczakiem.jpg', 'description' => 'Syta sałatka z soczystym kurczakiem, warzywami i lekkim dressingiem.'],
            ['name' => 'Krem z dyni', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'krem_z_dyni.jpg', 'description' => 'Aksamitna zupa z pieczonej dyni, doprawiona imbirem lub gałką muszkatołową.'],
            ['name' => 'Makaron carbonara', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'makaron_carbonara.jpg', 'description' => 'Makaron w sosie z jajek, pecorino i guanciale, bez śmietany.'],
            ['name' => 'Gnocchi', 'category' => 'Dania główne', 'cuisine' => 'Włoska', 'flavour' => 'Słony', 'image_url' => 'gnocchi.jpg', 'description' => 'Włoskie kluseczki ziemniaczane podawane z masłem szałwiowym lub sosem.'],
            ['name' => 'Burrito', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'burrito.jpg', 'description' => 'Zawijana pszenna tortilla wypełniona ryżem, fasolą, mięsem i dodatkami.'],
            ['name' => 'Krem z kalafiora', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Słony', 'image_url' => 'krem_z_kalafiora.jpg', 'description' => 'Delikatny, kremowy krem z kalafiora, często z nutą czosnku.'],
            ['name' => 'Makowiec', 'category' => 'Desery', 'cuisine' => 'Polska', 'flavour' => 'Słodki', 'image_url' => 'makowiec.jpg', 'description' => 'Tradycyjna rolada drożdżowa z bogatym nadzieniem z mielonego maku.'],
            ['name' => 'Kimchi', 'category' => 'Przekąski', 'cuisine' => 'Azjatycka', 'flavour' => 'Kwaśny', 'image_url' => 'kimchi.jpg', 'description' => 'Koreańska fermentowana kapusta pekińska z chili, czosnkiem i imbirem.'],
            ['name' => 'Curry z kurczakiem', 'category' => 'Dania główne', 'cuisine' => 'Indyjska', 'flavour' => 'Słony', 'image_url' => 'curry_z_kurczakiem.jpg', 'description' => 'Aromatyczne danie w gęstym sosie curry z kurczakiem i warzywami.'],
            ['name' => 'Chili con carne', 'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony', 'image_url' => 'chili_con_carne.jpg', 'description' => 'Pikantny gulasz z wołowiną, fasolą, pomidorami i przyprawami.'],
            ['name' => 'Zupa ogórkowa', 'category' => 'Zupy', 'cuisine' => 'Polska', 'flavour' => 'Kwaśny', 'image_url' => 'zupa_ogorkowa.jpg', 'description' => 'Polska zupa na wywarze z kiszonych ogórków, ziemniaków i śmietany.'],
            ['name' => 'Brownie z orzechami', 'category' => 'Desery', 'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'brownie_z_orzechami.jpg', 'description' => 'Czekoladowe brownie wzbogacone chrupiącymi orzechami dla tekstury.'],
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
