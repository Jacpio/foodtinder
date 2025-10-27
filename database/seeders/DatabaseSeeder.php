<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Parameter;
use App\Models\ParameterWeight;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );
        $user->assignRole($adminRole);

        $categories = ['Zupy','Dania główne','Desery','Przekąski'];
        $cuisines   = ['Włoska','Polska','Azjatycka','Meksykańska','Hiszpańska','Amerykańska','Indyjska'];
        $flavours   = ['Słodki','Kwaśny','Słony','Gorzki'];

        $categoryParams = [];
        $cuisineParams  = [];
        $flavourParams  = [];
        $allParameterIds = [];

        foreach ($categories as $name) {
            $p = Parameter::firstOrCreate(
                ['name' => $name, 'type' => 'category'],
                ['value' => 1, 'is_active' => true]
            );
            $categoryParams[$name] = $p->id;
            $allParameterIds[] = $p->id;
        }

        foreach ($cuisines as $name) {
            $p = Parameter::firstOrCreate(
                ['name' => $name, 'type' => 'cuisine'],
                ['value' => 1, 'is_active' => true]
            );
            $cuisineParams[$name]  = $p->id;
            $allParameterIds[] = $p->id;
        }

        foreach ($flavours as $name) {
            $p = Parameter::firstOrCreate(
                ['name' => $name, 'type' => 'flavour'],
                ['value' => 1, 'is_active' => true]
            );
            $flavourParams[$name]  = $p->id;
            $allParameterIds[] = $p->id;
        }

        foreach (array_unique($allParameterIds) as $pid) {
            ParameterWeight::firstOrCreate(
                ['user_id' => $user->id, 'parameter_id' => $pid],
                ['weight' => 0.0]
            );
        }

        $dishes = [
            ['name' => 'Spaghetti Bolognese',      'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'spahgetti.jpg',              'description' => 'Klasyczny makaron z sosem pomidorowo-mięsnym duszonym z ziołami i warzywami.', 'is_vegan' => false],
            ['name' => 'Pierogi ruskie',           'category' => 'Dania główne', 'cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'pierogi_ruskie.jpg',         'description' => 'Delikatne pierogi z farszem z ziemniaków i twarogu, podawane z cebulką.',       'is_vegan' => false],
            ['name' => 'Zupa pomidorowa',          'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Kwaśny', 'image_url' => 'zupa_pomidorowa.jpg',        'description' => 'Aromatyczna zupa na bulionie z dojrzałych pomidorów, często z ryżem lub makaronem.', 'is_vegan' => false],
            ['name' => 'Tacos',                    'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'tacos.jpg',                  'description' => 'Małe kukurydziane tortille wypełnione mięsem lub warzywami z salsą i limonką.', 'is_vegan' => false],
            ['name' => 'Panna Cotta',              'category' => 'Desery',       'cuisine' => 'Włoska',      'flavour' => 'Słodki', 'image_url' => 'panna_cotta.jpg',            'description' => 'Jedwabisty włoski deser z gotowanej śmietanki, podawany z sosem owocowym.',     'is_vegan' => false],
            ['name' => 'Smażone pierożki wonton',  'category' => 'Przekąski',    'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'smazone_pierozki_wonton.jpg','description' => 'Chrupiące azjatyckie pierożki smażone na złoto, nadziewane mięsem lub warzywami.', 'is_vegan' => false],
            ['name' => 'Lasagne',                  'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'lasagne.jpg',                'description' => 'Warstwowy makaron zapiekany z sosem bolońskim, beszamelowym i serem.',         'is_vegan' => false],
            ['name' => 'Krem z brokułów',          'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'krem_z_brokulow.jpg',        'description' => 'Gęsta, aksamitna zupa zmiksowana z brokułów, często z dodatkiem śmietanki.',     'is_vegan' => false],
            ['name' => 'Guacamole',                'category' => 'Przekąski',    'cuisine' => 'Meksykańska', 'flavour' => 'Kwaśny', 'image_url' => 'guacamole.jpg',              'description' => 'Kremowa pasta z dojrzałego awokado z limonką, cebulą i kolendrą.',             'is_vegan' => true],
            ['name' => 'Tiramisu',                 'category' => 'Desery',       'cuisine' => 'Włoska',      'flavour' => 'Słodki', 'image_url' => 'tiramisu.jpg',               'description' => 'Deser na bazie biszkoptów nasączonych kawą i kremu z mascarpone, posypany kakao.', 'is_vegan' => false],
            ['name' => 'Pho',                      'category' => 'Zupy',         'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'pho.jpg',                    'description' => 'Wietnamska zupa na klarownym bulionie z makaronem ryżowym, ziołami i mięsem.',  'is_vegan' => false],
            ['name' => 'Risotto z grzybami',       'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'risotto_z_grzybami.jpg',     'description' => 'Kremowy ryż arborio powoli duszony z bulionem i aromatycznymi leśnymi grzybami.', 'is_vegan' => false],
            ['name' => 'Placki ziemniaczane',      'category' => 'Dania główne', 'cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'placki_ziemniaczane.jpg',    'description' => 'Chrupiące placki z tartych ziemniaków, smażone na złoto, podawane ze śmietaną.', 'is_vegan' => false],
            ['name' => 'Sernik',                   'category' => 'Desery',       'cuisine' => 'Polska',      'flavour' => 'Słodki', 'image_url' => 'sernik.jpg',                 'description' => 'Puszysty wypiek z twarogu na kruchym spodzie, często z wanilią lub cytryną.',   'is_vegan' => false],
            ['name' => 'Ramen',                    'category' => 'Zupy',         'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'ramen.jpg',                  'description' => 'Japońska zupa z esencjonalnym bulionem, makaronem pszenicznym i licznymi dodatkami.', 'is_vegan' => false],
            ['name' => 'Quesadilla',               'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'quesadilla.jpg',             'description' => 'Złożona tortilla zapiekana z roztopionym serem i ulubionymi dodatkami.',       'is_vegan' => false],
            ['name' => 'Caprese',                  'category' => 'Przekąski',    'cuisine' => 'Włoska',      'flavour' => 'Kwaśny', 'image_url' => 'caprese.jpg',                'description' => 'Sałatka z pomidorów, mozzarelli i bazylii skropiona oliwą i octem balsamicznym.', 'is_vegan' => false],
            ['name' => 'Sushi',                    'category' => 'Dania główne', 'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'sushi.jpg',                  'description' => 'Ryż zaprawiony octem ryżowym z rybą, warzywami lub jajkiem, rolowany lub formowany.', 'is_vegan' => false],
            ['name' => 'Lody waniliowe',           'category' => 'Desery',       'cuisine' => 'Włoska',      'flavour' => 'Słodki', 'image_url' => 'lody_waniliowe.jpg',         'description' => 'Klasyczne, kremowe lody o naturalnym aromacie wanilii.',                      'is_vegan' => false],
            ['name' => 'Bigos',                    'category' => 'Dania główne', 'cuisine' => 'Polska',      'flavour' => 'Kwaśny', 'image_url' => 'bigos.jpg',                   'description' => 'Długo duszona kapusta z mięsem i kiełbasą, doprawiona śliwkami i przyprawami.', 'is_vegan' => false],
            ['name' => 'Gazpacho',                 'category' => 'Zupy',         'cuisine' => 'Hiszpańska',  'flavour' => 'Kwaśny', 'image_url' => 'gazpacho.jpg',               'description' => 'Hiszpański chłodnik z surowych pomidorów i warzyw, podawany mocno schłodzony.', 'is_vegan' => true],
            ['name' => 'Tortilla de patatas',      'category' => 'Dania główne', 'cuisine' => 'Hiszpańska',  'flavour' => 'Słony',  'image_url' => 'tortilla_de_patatas.jpg',    'description' => 'Hiszpański omlet z jajek, ziemniaków i cebuli, smażony na patelni.',          'is_vegan' => false],
            ['name' => 'Pad Thai',                 'category' => 'Dania główne', 'cuisine' => 'Azjatycka',   'flavour' => 'Słodki', 'image_url' => 'pad_thai.jpg',               'description' => 'Smażony makaron ryżowy z tamaryndowcem, kiełkami, orzeszkami i jajkiem.',     'is_vegan' => false],
            ['name' => 'Czekoladowe brownie',      'category' => 'Desery',       'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'czekoladowe_brownie.jpg',    'description' => 'Wilgotne, intensywnie czekoladowe ciasto o zwartej, fudgy konsystencji.',      'is_vegan' => false],
            ['name' => 'Żurek',                    'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Kwaśny', 'image_url' => 'zurek.jpg',                   'description' => 'Kwaśna zupa na zakwasie żytnim z białą kiełbasą, ziemniakami i jajkiem.',       'is_vegan' => false],
            ['name' => 'Nachosy z serem',          'category' => 'Przekąski',    'cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'nachosy_z_serem.jpg',        'description' => 'Chrupiące trójkąty tortilli zapiekane z serem i pikantnymi dodatkami.',       'is_vegan' => false],
            ['name' => 'Frittata',                 'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'frittata.jpg',               'description' => 'Włoski omlet pieczony z warzywami, serem lub wędliną, podawany na ciepło.',   'is_vegan' => false],
            ['name' => 'Kopytka',                  'category' => 'Dania główne', 'cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'kopytka.jpg',                'description' => 'Miękkie kluseczki ziemniaczane formowane w romby, serwowane z masłem lub sosem.', 'is_vegan' => false],
            ['name' => 'Makaron z pesto',          'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'makaron_z_pesto.jpg',        'description' => 'Makaron wymieszany z aromatycznym sosem z bazylii, orzeszków i parmezanu.',    'is_vegan' => false],
            ['name' => 'Zupa miso',                'category' => 'Zupy',         'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'zupa_miso.jpg',              'description' => 'Lekka japońska zupa na dashi z pastą miso, tofu i wodorostami.',               'is_vegan' => false],
            ['name' => 'Churros',                  'category' => 'Desery',       'cuisine' => 'Hiszpańska',  'flavour' => 'Słodki', 'image_url' => 'churros.jpg',                'description' => 'Smażone pałeczki ciasta posypane cukrem, często serwowane z sosem czekoladowym.', 'is_vegan' => false],
            ['name' => 'Spring rolls',             'category' => 'Przekąski',    'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'spring_rolls.jpg',           'description' => 'Świeże lub smażone ruloniki wypełnione warzywami i mięsem, podawane z dipem.', 'is_vegan' => false],
            ['name' => 'Sałatka z kurczakiem',     'category' => 'Przekąski',    'cuisine' => 'Amerykańska', 'flavour' => 'Słony',  'image_url' => 'salatka_z_kurczakiem.jpg',   'description' => 'Syta sałatka z soczystym kurczakiem, warzywami i lekkim dressingiem.',         'is_vegan' => false],
            ['name' => 'Krem z dyni',              'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Słodki', 'image_url' => 'krem_z_dyni.jpg',            'description' => 'Aksamitna zupa z pieczonej dyni, doprawiona imbirem lub gałką muszkatołową.',   'is_vegan' => false],
            ['name' => 'Makaron carbonara',        'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'makaron_carbonara.jpg',      'description' => 'Makaron w sosie z jajek, pecorino i guanciale, bez śmietany.',                 'is_vegan' => false],
            ['name' => 'Gnocchi',                  'category' => 'Dania główne', 'cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'gnocchi.jpg',                'description' => 'Włoskie kluseczki ziemniaczane podawane z masłem szałwiowym lub sosem.',       'is_vegan' => false],
            ['name' => 'Burrito',                  'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'burrito.jpg',                'description' => 'Zawijana pszenna tortilla wypełniona ryżem, fasolą, mięsem i dodatkami.',      'is_vegan' => false],
            ['name' => 'Krem z kalafiora',         'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'krem_z_kalafiora.jpg',       'description' => 'Delikatny, kremowy krem z kalafiora, często z nutą czosnku.',                   'is_vegan' => false],
            ['name' => 'Makowiec',                 'category' => 'Desery',       'cuisine' => 'Polska',      'flavour' => 'Słodki', 'image_url' => 'makowiec.jpg',               'description' => 'Tradycyjna rolada drożdżowa z bogatym nadzieniem z mielonego maku.',            'is_vegan' => false],
            ['name' => 'Kimchi',                   'category' => 'Przekąski',    'cuisine' => 'Azjatycka',   'flavour' => 'Kwaśny', 'image_url' => 'kimchi.jpg',                 'description' => 'Koreańska fermentowana kapusta pekińska z chili, czosnkiem i imbirem.',         'is_vegan' => false],
            ['name' => 'Curry z kurczakiem',       'category' => 'Dania główne', 'cuisine' => 'Indyjska',    'flavour' => 'Słony',  'image_url' => 'curry_z_kurczakiem.jpg',     'description' => 'Aromatyczne danie w gęstym sosie curry z kurczakiem i warzywami.',              'is_vegan' => false],
            ['name' => 'Chili con carne',          'category' => 'Dania główne', 'cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'chili_con_carne.jpg',        'description' => 'Pikantny gulasz z wołowiną, fasolą, pomidorami i przyprawami.',                 'is_vegan' => false],
            ['name' => 'Zupa ogórkowa',            'category' => 'Zupy',         'cuisine' => 'Polska',      'flavour' => 'Kwaśny', 'image_url' => 'zupa_ogorkowa.jpg',          'description' => 'Polska zupa na wywarze z kiszonych ogórków, ziemniaków i śmietany.',            'is_vegan' => false],
            ['name' => 'Brownie z orzechami',      'category' => 'Desery',       'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'brownie_z_orzechami.jpg',    'description' => 'Czekoladowe brownie wzbogacone chrupiącymi orzechami dla tekstury.',            'is_vegan' => false],
            ['name' => 'Hummus',                    'category' => 'Przekąski',   'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'hummus.jpg',                       'description' => 'Aksamitna pasta z ciecierzycy z tahini, czosnkiem i cytryną.', 'is_vegan' => true],
            ['name' => 'Falafel',                        'category' => 'Przekąski',   'cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'falafel.jpg',                      'description' => 'Chrupiące kotleciki z ciecierzycy smażone na złoto.', 'is_vegan' => true],
            ['name' => 'Tabbouleh',                      'category' => 'Przekąski',   'cuisine' => 'Azjatycka',   'flavour' => 'Kwaśny', 'image_url' => 'tabbouleh.jpg',                    'description' => 'Orzeźwiająca sałatka z bulguru, natki pietruszki i cytryny.', 'is_vegan' => true],
            ['name' => 'Curry warzywne z ciecierzycą',   'category' => 'Dania główne','cuisine' => 'Indyjska',    'flavour' => 'Słony',  'image_url' => 'curry_z_ciecierzyca.jpg',          'description' => 'Aromatyczne curry na mleku kokosowym z warzywami i ciecierzycą.', 'is_vegan' => true],
            ['name' => 'Dahl z czerwonej soczewicy',     'category' => 'Zupy',        'cuisine' => 'Indyjska',    'flavour' => 'Słony',  'image_url' => 'dahl_czerwona_soczewica.jpg',      'description' => 'Gęsta, rozgrzewająca zupa z soczewicy z przyprawami.', 'is_vegan' => true],
            ['name' => 'Stir-fry tofu z warzywami',      'category' => 'Dania główne','cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'stir_fry_tofu_warzywa.jpg',        'description' => 'Szybko smażone tofu z chrupiącymi warzywami i sosem sojowym.', 'is_vegan' => true],
            ['name' => 'Pad Thai wegański',              'category' => 'Dania główne','cuisine' => 'Azjatycka',   'flavour' => 'Słony',  'image_url' => 'pad_thai_vegan.jpg',               'description' => 'Makaron ryżowy z tofu, kiełkami i orzeszkami, bez składników odzwierzęcych.', 'is_vegan' => true],
            ['name' => 'Pasta arrabbiata',               'category' => 'Dania główne','cuisine' => 'Włoska',      'flavour' => 'Kwaśny', 'image_url' => 'pasta_arrabbiata.jpg',             'description' => 'Pikantny sos pomidorowy z czosnkiem i papryczką chili.', 'is_vegan' => true],
            ['name' => 'Pizza Margherita (wegańska)',    'category' => 'Dania główne','cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'pizza_margherita.jpg',       'description' => 'Cienkie ciasto, sos pomidorowy, bazylia i wegański ser.', 'is_vegan' => true],
            ['name' => 'Risotto z dynią i szałwią',      'category' => 'Dania główne','cuisine' => 'Włoska',      'flavour' => 'Słony',  'image_url' => 'risotto_dynia_szalwia.jpg',        'description' => 'Kremowe risotto z pieczoną dynią i szałwią na oliwie.', 'is_vegan' => true],
            ['name' => 'Chili sin carne',                'category' => 'Dania główne','cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'chili_sin_carne.jpg',             'description' => 'Pikantna potrawka z fasoli, kukurydzy i pomidorów bez mięsa.', 'is_vegan' => true],
            ['name' => 'Burrito warzywne',               'category' => 'Dania główne','cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'burrito_warzywne.jpg',             'description' => 'Pszenna tortilla z ryżem, fasolą, warzywami i salsą.', 'is_vegan' => true],
            ['name' => 'Tacos z jackfruitem',            'category' => 'Dania główne','cuisine' => 'Meksykańska', 'flavour' => 'Słony',  'image_url' => 'tacos_jackfruit.jpg',             'description' => 'Soczysty jackfruit w przyprawach jako roślinny „szarpany” farsz.', 'is_vegan' => true],
            ['name' => 'Paella warzywna',                'category' => 'Dania główne','cuisine' => 'Hiszpańska',  'flavour' => 'Słony',  'image_url' => 'paella_warzywna.jpg',              'description' => 'Sycący ryż z szafranem i mieszanką sezonowych warzyw.', 'is_vegan' => true],
            ['name' => 'Burger z ciecierzycy',           'category' => 'Dania główne','cuisine' => 'Amerykańska', 'flavour' => 'Słony',  'image_url' => 'burger_ciecierzyca.jpg',          'description' => 'Roślinny kotlet z ciecierzycy w bułce z warzywami i sosem.', 'is_vegan' => true],
            ['name' => 'Buddha bowl',                    'category' => 'Dania główne','cuisine' => 'Amerykańska', 'flavour' => 'Słony',  'image_url' => 'buddha_bowl.jpg',                 'description' => 'Miska pełna kaszy, warzyw, strączków i kremowego dressingu.', 'is_vegan' => true],
            ['name' => 'Gulasz z boczniaków',            'category' => 'Dania główne','cuisine' => 'Polska',      'flavour' => 'Słony',  'image_url' => 'gulasz_z_boczniakow.jpg',         'description' => 'Aromatyczny gulasz z boczniaków w ziołowym sosie.', 'is_vegan' => true],
            ['name' => 'Krem z pieczonej papryki',       'category' => 'Zupy',        'cuisine' => 'Hiszpańska',  'flavour' => 'Słony',  'image_url' => 'krem_pieczona_papryka.jpg',       'description' => 'Aksamitna zupa z pieczonej papryki i pomidorów.', 'is_vegan' => true],
            ['name' => 'Lody kokosowe',                  'category' => 'Desery',      'cuisine' => 'Włoska',      'flavour' => 'Słodki', 'image_url' => 'lody_kokosowe.jpg',             'description' => 'Śmietankowe lody na bazie mleka kokosowego.', 'is_vegan' => true],
            ['name' => 'Brownie z batatów',              'category' => 'Desery',      'cuisine' => 'Amerykańska', 'flavour' => 'Słodki', 'image_url' => 'brownie_bataty.jpg',             'description' => 'Wilgotne brownie z batatów i kakao, bez nabiału i jaj.', 'is_vegan' => true],
        ];


        foreach ($dishes as $row) {
            if (
                !isset($categoryParams[$row['category']]) ||
                !isset($cuisineParams[$row['cuisine']]) ||
                !isset($flavourParams[$row['flavour']])
            ) {
                dump("Brakuje parametru (category/cuisine/flavour) dla dania: {$row['name']}");
                continue;
            }

            $dish = Dish::firstOrCreate(
                ['name' => $row['name']],
                [
                    'description' => $row['description'] ?? '',
                    'image_url'   => $row['image_url'] ?? null,
                    'is_vegan' => $row['is_vegan'] ?? 0,
                ]
            );

            $toAttach = [
                $categoryParams[$row['category']],
                $cuisineParams[$row['cuisine']],
                $flavourParams[$row['flavour']],
            ];

            $dish->parameters()->syncWithoutDetaching($toAttach);
        }
    }
}
