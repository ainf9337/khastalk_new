<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\BehaviourLog;
use App\Models\Message;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clear existing data (keep the 4 test accounts) ────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('behaviour_logs')->truncate();
        DB::table('messages')->truncate();
        DB::table('emergency_alerts')->truncate();
        DB::table('rpi_goals')->truncate();
        DB::table('rpi_documents')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('student_profiles')->truncate();
        DB::table('students')->truncate();
        DB::table('classes')->truncate();

        // Delete non-test users only
        User::whereNotIn('email', [
            'admin@khastalk.com',
            'teacher@khastalk.com',
            'parent@khastalk.com',
            'senior@khastalk.com',
        ])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Old data cleared. Keeping 4 test accounts.');

        // ── Teachers ──────────────────────────────────────────────
        $teachers = [
            [
                'name'  => 'Cikgu Novianti Binti Zulkifli',
                'email' => 'teacher@khastalk.com',
                // Already exists — fetch instead of create
            ],
            [
                'name'  => 'Cikgu Salmiah Binti Abdul Rahim',
                'email' => 'salmiah@khastalk.com',
            ],
            [
                'name'  => 'Cikgu Hafizah Binti Mohd Noor',
                'email' => 'hafizah@khastalk.com',
            ],
            [
                'name'  => 'Cikgu Rozita Binti Ismail',
                'email' => 'rozita@khastalk.com',
            ],
            [
                'name'  => 'Cikgu Aminah Binti Hassan',
                'email' => 'aminah@khastalk.com',
            ],
        ];

        $teacherModels = [];

        // Teacher 0 — existing account
        $teacherModels[] = User::where('email', 'teacher@khastalk.com')->first();

        // Teachers 1–4 — create new
        foreach (array_slice($teachers, 1) as $t) {
            $teacherModels[] = User::create([
                'name'     => $t['name'],
                'email'    => $t['email'],
                'password' => Hash::make('password'),
                'role'     => 'teacher',
                'phone_number'    => '01' . rand(1,9) . '-' . rand(1000000,9999999),
            ]);
        }

        $this->command->info('✓ Teachers ready.');

        // ── Classes ───────────────────────────────────────────────
        $classNames = ['MATAHARI', 'MAWAR', 'MELATI', 'MUTIARA', 'MELUR'];
        $classModels = [];

        foreach ($classNames as $i => $name) {
            $classModels[$name] = ClassRoom::create([
                'class_name'    => 'PPKI ' . $name,
                'teacher_id'    => $teacherModels[$i]->id,
                'academic_year' => 2026,
            ]);
        }

        $this->command->info('✓ 5 classes created.');

        // ── Parents data ──────────────────────────────────────────
        // email => [name, phone_number]
        $parentsData = [
            'm-12473842@moe-dl.edu.my'      => ['Puan Saman Binti Mohd Ali',        '0111-2345678'],
            'mudatm78@gmail.com'             => ['Encik Tuan Muda Bin Abdullah',      '0122-3456789'],
            'ismailnurain56@gmail.com'       => ['Puan Nurain Binti Ismail',          '0133-4567890'],
            'nurulhudaa373@gmail.com'        => ['Puan Nurul Huda Binti Kamarull',    '0144-5678901'],
            'noridayuahmadi93@gmail.com'     => ['Puan Noriday Binti Ahmad',          '0155-6789012'],
            'azmanyusof100673@gmail.com'     => ['Encik Azman Bin Yusof',             '0166-7890123'],
            'kedai.kasut2012@gmail.com'      => ['Encik Abdul Manaf Bin Zahari',      '0177-8901234'],
            'norhidar@gmail.com'             => ['Puan Hidar Binti Mohd Nor',         '0188-9012345'],
            'balkissitinur@gmail.com'        => ['Puan Balkis Binti Nур',             '0199-0123456'],
            'nurkyraammara@gmail.com'        => ['Puan Kyra Binti Mohd Fikri',        '0111-1234567'],
            'rosmanizaniza38@gmail.com'      => ['Puan Rosmani Binti Mohd Hafiz',     '0122-2345678'],
            'sitizaliza@ilkkm.edu.my'        => ['Puan Siti Zaliza Binti Sharifudin', '0133-3456789'],
            'aidillenny670@gmail.com'        => ['Puan Lenny Binti Aidil Fitri',      '0144-4567890'],
            'nurfaiza.ija@gmail.com'         => ['Puan Nurfaiza Binti Ija',           '0155-5678901'],
            'adnanfahmi6426@gmail.com'       => ['Encik Adnan Bin Fahmi',             '0166-6789012'],
            'hafizrealty@gmail.com'          => ['Encik Hafiz Bin Zakaria',           '0177-7890123'],
            'wafiyyawarda25@gmail.com'       => ['Puan Wafiyya Binti Warda',          '0188-8901234'],
            'carldukea@gmail.com'            => ['Encik Norazamuddin Bin Abdullah',   '0199-9012345'],
            'anisbaidura90@gmail.com'        => ['Puan Anis Baidura Binti Zulkifli',  '0111-0123456'],
            'mamirrullahak@umsc.my'          => ['Encik Amirrullah Bin Mohd Akmal',   '0122-1234567'],
            'nadyawahab02@gmail.com'         => ['Puan Nadya Binti Wahab',            '0133-2345678'],
            'marlinatann@gmail.com'          => ['Puan Marlina Binti Termizi',        '0144-3456789'],
            'norlizaomar7@gmail.com'         => ['Puan Norliza Binti Omar',           '0155-4567890'],
            'rohaidaar@gmail.com'            => ['Puan Rohaida Binti Ar',             '0166-5678901'],
            'azureenyienshamsudin@gmail.com' => ['Puan Azureen Binti Shamsuddin',     '0177-6789012'],
            'nibybaby679@gmail.com'          => ['Puan Niba Binti Muhammad Nader',    '0188-7890123'],
            'zubirzack90@gmail.com'          => ['Encik Zubir Bin Zack',              '0199-8901234'],
            'hajjahnoraini223@gmail.com'     => ['Hajjah Noraini Binti Mustapha',     '0111-9012345'],
            'sakinah.rashid@gmail.com'       => ['Puan Sakinah Binti Rashid',         '0122-0123456'],
            'zilanorzila76@gmail.com'        => ['Puan Norzila Binti Ahmadi',         '0133-1234567'],
            'mmohdismail30@gmail.com'        => ['Encik Mohd Ismail Bin Mohd Noor',   '0144-2345678'],
            'nurulashikin.nam@gmail.com'     => ['Puan Nurul Ashikin Binti Mohd Wuzilah', '0155-3456789'],
            'noorfatmamuhamad80@gmail.com'   => ['Puan Noorfatma Binti Muhammad',     '0166-4567890'],
            'asriaziz9025@gmail.com'         => ['Encik Asri Bin Aziz',               '0177-5678901'],
            'atikahaziq95@gmail.com'         => ['Puan Atikah Binti Muhammad Haziq',  '0188-6789012'],
            'azmira0512@gmail.com'           => ['Puan Azmira Binti Abdul Rasyid',    '0199-7890123'],
            'zakaria28675@gmail.com'         => ['Encik Zakaria Bin Abdullah',        '0111-8901234'],
            'n.athiqallah@gmail.com'         => ['Puan Athiqah Binti Muhammad Muizzuddin', '0122-9012345'],
            'norazrida.shaari@gmail.com'     => ['Puan Norazrida Binti Shaari',       '0133-0123456'],
            'faradiana.nasir@gmail.com'      => ['Puan Faradiana Binti Nasir',        '0144-1234567'],
            'radiyatulaswani82@gmail.com'    => ['Puan Radiyatul Aswani Binti Mohd',  '0155-2345678'],
            'naura050415@gmail.com'          => ['Puan Naura Binti Aszemin',          '0166-3456789'],
            'sitizuriaabdulaziz@gmail.com'   => ['Puan Siti Zuria Binti Abdul Aziz',  '0177-4567890'],
            'saidathuliqma@gmail.com'        => ['Puan Saidatul Iqma Binti Mohd',     '0188-5678901'],
            'qilasudin@gmail.com'            => ['Puan Aqila Binti Sudin',            '0199-6789012'],
            'duren4321@gmail.com'            => ['Encik Durenazni Bin Abdullah',      '0111-7890123'],
            'marriduan@gmail.com'            => ['Encik Irwan Bin Marriduan',         '0122-8901234'],
            'guava_abc@yahoo.com'            => ['Puan Rosnah Binti Hamdan',          '0133-9012345'],
            'mydinumie85@gmail.com'          => ['Puan Mie Binti Mydin',              '0144-0123456'],
            'taufiqdanielita@gmail.com'      => ['Encik Taufiq Bin Daniel',           '0155-1234567'],
            'muhamadhanisbazil@gmail.com'    => ['Encik Muhamad Hanis Bin Bazil',     '0166-2345678'],
            'mrayyanhazieq12@gmail.com'      => ['Puan Hazieq Binti Mustakim',        '0177-3456789'],
            'anies.anuar88@gmail.com'        => ['Encik Anies Bin Anuar',             '0188-4567890'],
            'anwarkamiluddin90@gmail.com'    => ['Encik Anwar Bin Kamiluddin',        '0199-5678901'],
            'riamurnikaidir@gmail.com'       => ['Puan Ria Murni Binti Kaidir',       '0111-6789012'],
            'quryatulaini12@gmail.com'       => ['Puan Quryatul Aini Binti Ahmad',    '0122-7890123'],
            'rohanihamzah1974@gmail.com'     => ['Puan Rohani Binti Hamzah',          '0133-8901234'],
            'atikah.adibah93@gmail.com'      => ['Puan Atikah Adibah Binti Abbu Nur', '0144-9012345'],
            'manruzimah5033@gmail.com'       => ['Puan Manruzimah Binti Mohd Rizal',  '0155-0123456'],
            'naim180902@gmail.com'           => ['Encik Najurudin Bin Mohd Naim',     '0166-1234567'],
            'kapisahmx@gmail.com'            => ['Puan Kapisah Binti Mohd Amin',      '0177-2345678'],
            'm55284779@gmail.com'            => ['Encik Mohd Sabri Bin Abdullah',     '0188-3456789'],
        ];

        // Create parent users
        $parentModels = [];
        foreach ($parentsData as $email => $data) {
            // Check if it's the test parent account
            if ($email === 'parent@khastalk.com') {
                $parentModels[$email] = User::where('email', 'parent@khastalk.com')->first();
                continue;
            }
            $parentModels[$email] = User::create([
                'name'     => $data[0],
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => 'parent',
                'phone_number'    => $data[1],
            ]);
        }

        $this->command->info('✓ ' . count($parentModels) . ' parent accounts created.');

        // ── Students data ─────────────────────────────────────────
        // [name, class, parent_email, sensory_triggers, calming_strategies, communication_level]
        $studentsData = [

            // ── MATAHARI ──────────────────────────────────────────
            ['Muhammad Amirul Rasyidi Bin Mat Saman', 'MATAHARI', 'm-12473842@moe-dl.edu.my',
                'Noise, Overstimulation',
                'Verbal reminder, Redirect activity',
                'Verbal'],

            ['Mohamad Muaz Zaffri Bin Abdul Manaf', 'MATAHARI', 'kedai.kasut2012@gmail.com',
                'No known triggers',
                'Calming corner, Quiet time',
                'Verbal'],

            ['Mohammad Aqifuddin Bin Mohamad Hafizullah', 'MATAHARI', 'rosmanizaniza38@gmail.com',
                'No known triggers',
                'Calm redirection',
                'Verbal'],

            ['Humaira Binti Hafiz', 'MATAHARI', 'hafizrealty@gmail.com',
                'No known triggers',
                'Human touch, Sensory break',
                'Verbal'],

            ['Ilham Anas Ilman Bin Norazamuddin', 'MATAHARI', 'carldukea@gmail.com',
                'No known triggers',
                'Calm redirection, Verbal reassurance',
                'Verbal'],

            ['Zhafran Bin Zubir', 'MATAHARI', 'zubirzack90@gmail.com',
                'No known triggers',
                'Space to calm down',
                'Verbal'],

            ['Luthfi Bin Abdullah', 'MATAHARI', 'hajjahnoraini223@gmail.com',
                'Noise, Strong smells',
                'Praise and reward, Sensory break',
                'Verbal'],

            ['Arif Aswad Bin Norazmi', 'MATAHARI', 'sakinah.rashid@gmail.com',
                'Loud noise, Sensory overload',
                'Isolated sensory room, Allow meltdown to pass 5-15 min',
                'Verbal'],

            ['Muhammad Adam Mikhail Bin Ahmadi', 'MATAHARI', 'zilanorzila76@gmail.com',
                'Unmet needs',
                'Verbal communication, Calm environment',
                'Verbal'],

            ['Muhammad Azzamy Syauqi Bin Durenazni', 'MATAHARI', 'duren4321@gmail.com',
                'No known triggers',
                'Space to calm down',
                'Verbal'],

            ['Muhammad Nur Ilman Bin Irwan', 'MATAHARI', 'marriduan@gmail.com',
                'No known triggers',
                'Calm communication',
                'Limited verbal'],

            ['Xayd Bin Rosnah', 'MATAHARI', 'guava_abc@yahoo.com',
                'Picky eating',
                'Distraction, Favourite activity',
                'Verbal'],

            ['Muhammad Syazani Bin Mydin', 'MATAHARI', 'mydinumie85@gmail.com',
                'Loud sounds, Bright lights, Touch sensitivity',
                'Maintain safe distance during tantrum, Gentle approach',
                'Verbal'],

            ['Ahmed Hanif Zakir Bin Nurrul Iman', 'MATAHARI', 'taufiqdanielita@gmail.com',
                'No known triggers',
                'Calm redirection',
                'Verbal'],

            ['Muhamad Harith Hamizan', 'MATAHARI', 'muhamadhanisbazil@gmail.com',
                'No known triggers',
                'Praise, Humour, Physical affection',
                'Verbal'],

            // ── MAWAR ─────────────────────────────────────────────
            ['Tuan Muhammad Ammar Ziqri Bin Tuan Muda', 'MAWAR', 'mudatm78@gmail.com',
                'Noise, Crowded spaces',
                'Fulfil needs, Distract with preferred activity',
                'Verbal'],

            ['Nurhumaira Aisyah Binti Ismail', 'MAWAR', 'ismailnurain56@gmail.com',
                'No known triggers',
                'Allow to self-regulate',
                'Verbal'],

            ['Muhammad Anas Miqail Bin Muhd Kamarull', 'MAWAR', 'nurulhudaa373@gmail.com',
                'No known triggers',
                'Calm redirection',
                'Verbal'],

            ['Nur Imelda Adliana Binti Aidil Fitri', 'MAWAR', 'aidillenny670@gmail.com',
                'Cats and animals with fur',
                'Mention teacher name, Calm redirection',
                'Verbal'],

            ['Muhammad Hud Bin Muhammad Asyraf', 'MAWAR', 'nurfaiza.ija@gmail.com',
                'Plastic food sounds, Smell of food',
                'Identify cause, Provide food or drink',
                'Limited verbal'],

            ['Muhammad Ammar Zarif Bin Zulkifli', 'MAWAR', 'anisbaidura90@gmail.com',
                'No known triggers',
                'Physical comfort, Hug',
                'Verbal'],

            ['Brian Andra Ashaal Bin Syukur', 'MAWAR', 'nadyawahab02@gmail.com',
                'Routine changes',
                'Maintain routine, Calm verbal reassurance',
                'Verbal'],

            ['Mohamad Faiq Harraz Bin Anies', 'MAWAR', 'anies.anuar88@gmail.com',
                'No known triggers',
                'Remove from class, Contact parents',
                'Verbal'],

            ['Muhammad Rayyan Harraz Bin Mustakim', 'MAWAR', 'mrayyanhazieq12@gmail.com',
                'No known triggers',
                'Mention father name, Calm environment',
                'Limited verbal'],

            ['Muaz Bin Ibrahim', 'MAWAR', 'norazrida.shaari@gmail.com',
                'No known triggers',
                'Redirect attention',
                'Verbal'],

            ['Aidan Harith Bin Fahmi Hakim', 'MAWAR', 'faradiana.nasir@gmail.com',
                'Loud noise',
                'Calm environment, Reassurance',
                'Verbal'],

            // ── MELATI ────────────────────────────────────────────
            ['Nor Jannah Umairah Binti Mohamad Izwan', 'MELATI', 'noridayuahmadi93@gmail.com',
                'No known triggers',
                'Calm redirection',
                'Verbal'],

            ['Nur Maya Alfira Binti Muhd Kamarull', 'MELATI', 'nurulhudaa373@gmail.com',
                'No known triggers',
                'Space to self-regulate',
                'Verbal'],

            ['Ali Imran Bin Rosmaini', 'MELATI', 'norhidar@gmail.com',
                'Loud noise',
                'Gentle stroking, Soft voice',
                'Verbal'],

            ['Muhammad Al Fateh Bin Mohd Sharifudin', 'MELATI', 'sitizaliza@ilkkm.edu.my',
                'No known triggers',
                'Calm environment, Walking activity',
                'Verbal'],

            ['Muhammad Zainal Awadi Bin Norliza', 'MELATI', 'norlizaomar7@gmail.com',
                'No known triggers',
                'Allow alone time to self-regulate',
                'Verbal'],

            ['Nur Safiya Qaisara Binti Khairudin', 'MELATI', 'quryatulaini12@gmail.com',
                'Very loud noise, Darkness',
                'Gentle physical comfort, Rattan stick as deterrent',
                'Verbal'],

            ['Muhammad Zaim Fayyadh Bin Radiyatul', 'MELATI', 'radiyatulaswani82@gmail.com',
                'Sudden sounds',
                'Calm approach',
                'Verbal'],

            ['Ahmad Airil Bin Aszemin', 'MELATI', 'naura050415@gmail.com',
                'No known triggers',
                'Calm environment',
                'Verbal'],

            ['Muhammad Zahin Bin Zakaria', 'MELATI', 'sitizuriaabdulaziz@gmail.com',
                'No known triggers',
                'Calm redirection, Verbal reminder',
                'Verbal'],

            ['Puteri Diana Binti Saidatul', 'MELATI', 'saidathuliqma@gmail.com',
                'No known triggers',
                'Allow to calm down, Gentle questioning',
                'Verbal'],

            ['Nur Alya Damia Binti Mohd Amin', 'MELATI', 'kapisahmx@gmail.com',
                'No known triggers',
                'Gentle persuasion',
                'Verbal'],

            ['Muhammad Syarafuddin Bin Mohd Sabri', 'MELATI', 'm55284779@gmail.com',
                'Very loud voices, Crowded environments',
                'Gentle calm voice, Reassurance',
                'Verbal'],

            // ── MUTIARA ───────────────────────────────────────────
            ['Muhammad Hariez Danish Bin Azman', 'MUTIARA', 'azmanyusof100673@gmail.com',
                'Loud noise',
                'Gentle persuasion, Zikir and doa',
                'Verbal'],

            ['Muhammad Zarif Wafi Bin Mohamad Fikri', 'MUTIARA', 'nurkyraammara@gmail.com',
                'Very loud sounds',
                'Hug, Back massage',
                'Verbal'],

            ['Alesha Zahra Jawi Binti Bokhari', 'MUTIARA', 'rohaidaar@gmail.com',
                'Certain sounds, Wet clothing, Textures',
                'Complete activity first then reward, Verbal reassurance',
                'Verbal'],

            ['Akid Ziqri Bin Shamsuddin', 'MUTIARA', 'azureenyienshamsudin@gmail.com',
                'Loud noise',
                'Distract with preferred item, Food reward',
                'Non-verbal'],

            ['Muhammad Nadem Bin Muhammad Nader', 'MUTIARA', 'nibybaby679@gmail.com',
                'No known triggers',
                'Hug, Remove from crowd',
                'Non-verbal'],

            ['Noor Ayra Maisarah Binti Mohd Ismail', 'MUTIARA', 'mmohdismail30@gmail.com',
                'No known triggers',
                'Balloon reward, Gentle persuasion',
                'Verbal'],

            ['Muhammad Aisy Wafi Bin Mohd Wuzilah', 'MUTIARA', 'nurulashikin.nam@gmail.com',
                'Hot weather, Bright light',
                'Hug, Food or toy reward',
                'Verbal'],

            ['Nur Falisha Binti Mohd Faizun', 'MUTIARA', 'noorfatmamuhamad80@gmail.com',
                'Dirt and mess',
                'Separate from others, Allow space to calm',
                'Verbal'],

            ['Muhammad Adam Rizqi Bin Mohd Nor Asri', 'MUTIARA', 'asriaziz9025@gmail.com',
                'Vacuum cleaner sound',
                'Gentle persuasion',
                'Verbal'],

            ['Daeng Iman Uwais Bin Muhammad Haziq', 'MUTIARA', 'atikahaziq95@gmail.com',
                'Collar shirts, Rough fabric',
                'Physical affection, Verbal reassurance',
                'Non-verbal'],

            ['Muhammad Ibrahim Bin Mohd Abdul Rasyid', 'MUTIARA', 'azmira0512@gmail.com',
                'Thunder sounds, Slimy textures',
                'Separate from friends, Allow to self-regulate',
                'Verbal'],

            ['Aleeya Humaira Binti Zakaria', 'MUTIARA', 'zakaria28675@gmail.com',
                'Strangers, Loud environment',
                'Ask what she wants, Tickle to calm, Hug',
                'Limited verbal'],

            ['Madeeha Amanina Binti Muhammad Muizzuddin', 'MUTIARA', 'n.athiqallah@gmail.com',
                'No known triggers',
                'Safe touch, Back pat',
                'Non-verbal'],

            ['Muhamad Daniel Bin Muhamad Termizi', 'MUTIARA', 'marlinatann@gmail.com',
                'No known triggers',
                'Calm talk, Hug, Repeated instructions with demo',
                'Verbal'],

            ['Muhammad Aryan Rauf Bin Najurudin', 'MUTIARA', 'naim180902@gmail.com',
                'No known triggers',
                'Calm environment, No specific tantrum issues',
                'Verbal'],

            // ── MELUR ─────────────────────────────────────────────
            ['Mazryo Zainuddin Bin Balkis', 'MELUR', 'balkissitinur@gmail.com',
                'No known triggers',
                'Hug, Positive praise',
                'Verbal'],

            ['Muhammad Amirul Afiq Bin Mohd Adnan', 'MELUR', 'adnanfahmi6426@gmail.com',
                'Loud sounds',
                'Hold hands, Physical comfort',
                'Verbal'],

            ['Muhamad Alfatih Bin Wafiyya', 'MELUR', 'wafiyyawarda25@gmail.com',
                'No known triggers',
                'Tight hug',
                'Verbal'],

            ['Ilham Arraiz Idlan Bin Norazamuddin', 'MELUR', 'carldukea@gmail.com',
                'Hunger, Stomach pain',
                'Offer food or drink, Tummy rub with oil',
                'Verbal'],

            ['Muhammad Amir Zayyan Bin Mohamad Amirrullah', 'MELUR', 'mamirrullahak@umsc.my',
                'No known triggers',
                'Gentle speech, Calm approach',
                'Limited verbal'],

            ['Ali Al Juffri Bin Mohd Rizal', 'MELUR', 'manruzimah5033@gmail.com',
                'Loud sounds',
                'Chocolate or candy, Tight hug',
                'Verbal'],

            ['Ariff Muaz Bin Abbu Nur Suffi', 'MELUR', 'atikah.adibah93@gmail.com',
                'No known triggers',
                'Calm verbal reassurance, Gentle hug',
                'Verbal'],

            ['Mohamad Faiq Darwisy Bin Mohamad Faizal', 'MELUR', 'anies.anuar88@gmail.com',
                'Thunder, Heavy rain',
                'Contact parents, Remove from class',
                'Verbal'],

            ['Hafiy Anaqi Bin Abdul Rahman', 'MELUR', 'anwarkamiluddin90@gmail.com',
                'No known triggers',
                'Milk, Handphone, Azan or salawat song',
                'Limited verbal'],

            ['Nur Taliyah Zulaikha Binti Muhamad Azrul', 'MELUR', 'riamurnikaidir@gmail.com',
                'Loud sounds',
                'Hold hands, Say sorry while holding hands, Back pat',
                'Verbal'],

            ['Siti Qalesya Binti Ahmad Azzad', 'MELUR', 'quryatulaini12@gmail.com',
                'Sensitive to sound',
                'Tickle, Divert attention, Humour',
                'Selective mutism'],

            ['Nuraisyah Umairah Binti Abd Malik', 'MELUR', 'rohanihamzah1974@gmail.com',
                'Loud noise',
                'Offer favourite food like ice cream',
                'Verbal'],

            ['Muhammad Adiarizki Bin Sudin', 'MELUR', 'qilasudin@gmail.com',
                'Thunder, Renovation noise, Hot environment',
                'Back rub, Ask why crying',
                'Verbal'],
        ];

        // ── Create students ───────────────────────────────────────
        $studentModels = [];

        foreach ($studentsData as $sd) {
            [$name, $className, $parentEmail,
             $triggers, $strategies, $commLevel] = $sd;

            $class     = $classModels[$className];
            $teacher   = User::find($class->teacher_id);
            $parent    = $parentModels[$parentEmail] ?? null;

            $student = Student::create([
                'name'       => $name,
                'class_id'   => $class->id,
                'teacher_id' => $teacher->id,
                'parent_id'  => $parent?->id,
                'diagnosis'  => 'Autism',
            ]);

            StudentProfile::create([
                'student_id'          => $student->id,
                'sensory_triggers'    => $triggers,
                'calming_strategies'  => $strategies,
                'communication_level' => $commLevel,
                'medical_info'        => 'No known medical issues',
            ]);

            $studentModels[] = $student;
        }

        $this->command->info('✓ ' . count($studentModels) . ' students enrolled with profiles.');

        // ── Behaviour logs (May & June 2026) ──────────────────────
        $behaviourTypes = [
            'Meltdown', 'Tantrum', 'Stimming',
            'Aggression', 'Self-injury', 'Refusal',
        ];

        $triggerOptions = [
            'Loud noise / sensory', 'Transition', 'Routine change',
            'Demand placed', 'Peer interaction', 'Hunger / tired',
        ];

        $responseOptions = [
            'Sensory break', 'Calming corner', 'Verbal redirect',
            'Fidget tool', 'Deep breathing', 'Physical support',
        ];

        $logCount = 0;

        foreach ($studentModels as $student) {
            $teacher = User::find($student->teacher_id);

            // May 2026 — 2 to 4 logs per student
            $mayLogs = rand(2, 4);
            for ($i = 0; $i < $mayLogs; $i++) {
                $day  = rand(1, 29);
                $hour = rand(8, 15);
                $date = Carbon::create(2026, 5, $day, $hour, rand(0,59), 0);

                BehaviourLog::create([
                    'student_id'       => $student->id,
                    'teacher_id'       => $teacher->id,
                    'behaviour_type'   => $behaviourTypes[array_rand($behaviourTypes)],
                    'severity'         => rand(1, 5),
                    'duration'         => ['< 5 minutes','5 – 10 minutes','10 – 20 minutes'][rand(0,2)],
                    'triggers'         => implode(', ', array_slice(
                                            $triggerOptions,
                                            rand(0, 4),
                                            rand(1, 2)
                                         )),
                    'teacher_response' => implode(', ', array_slice(
                                            $responseOptions,
                                            rand(0, 4),
                                            rand(1, 2)
                                         )),
                    'resolved'         => rand(0, 10) > 2, // 70% resolved
                    'notes'            => null,
                    'logged_at'        => $date,
                    'created_at'       => $date,
                    'updated_at'       => $date,
                ]);

                $logCount++;
            }

            // June 2026 — 1 to 3 logs per student
            $juneLogs = rand(1, 3);
            for ($i = 0; $i < $juneLogs; $i++) {
                $day  = rand(1, 25);
                $hour = rand(8, 15);
                $date = Carbon::create(2026, 6, $day, $hour, rand(0,59), 0);

                BehaviourLog::create([
                    'student_id'       => $student->id,
                    'teacher_id'       => $teacher->id,
                    'behaviour_type'   => $behaviourTypes[array_rand($behaviourTypes)],
                    'severity'         => rand(1, 4),
                    'duration'         => ['< 5 minutes','5 – 10 minutes','10 – 20 minutes'][rand(0,2)],
                    'triggers'         => implode(', ', array_slice(
                                            $triggerOptions,
                                            rand(0, 4),
                                            rand(1, 2)
                                         )),
                    'teacher_response' => implode(', ', array_slice(
                                            $responseOptions,
                                            rand(0, 4),
                                            rand(1, 2)
                                         )),
                    'resolved'         => rand(0, 10) > 2,
                    'notes'            => null,
                    'logged_at'        => $date,
                    'created_at'       => $date,
                    'updated_at'       => $date,
                ]);

                $logCount++;
            }
        }

        $this->command->info("✓ {$logCount} behaviour logs created (May & June 2026).");

        // ── Messages (teacher ↔ parent, May & June 2026) ──────────
        $teacherMessages = [
            'Assalamualaikum, %s telah menunjukkan perkembangan yang baik hari ini.',
            '%s telah berjaya menyiapkan aktiviti lukisan dengan baik hari ini. Tahniah!',
            'Makluman: %s mengalami meltdown kecil hari ini tetapi telah ditenangkan dengan jayanya.',
            '%s aktif dan ceria hari ini. Alhamdulillah.',
            'Saya ingin berkongsi bahawa %s semakin menunjukkan kemajuan dalam kemahiran sosial.',
            'Perkembangan %s minggu ini sangat menggalakkan. Teruskan semangat!',
        ];

        $parentMessages = [
            'Terima kasih cikgu atas makluman. Saya sangat gembira mendengarnya.',
            'Alhamdulillah. Kami akan teruskan usaha di rumah juga.',
            'Terima kasih cikgu. Ada apa-apa yang kami perlu lakukan di rumah?',
            'Syukur. %s nampak lebih tenang sejak kebelakangan ini.',
            'Terima kasih cikgu. Kami sentiasa doakan yang terbaik untuk semua.',
        ];

        $msgCount = 0;

        // Create messages for a subset of students
        foreach ($studentModels as $student) {
            if (!$student->parent_id) continue;
            if (rand(0, 3) === 0) continue; // skip ~25% to vary density

            $teacher = User::find($student->teacher_id);
            $parent  = User::find($student->parent_id);
            if (!$parent) continue;

            $firstName  = explode(' ', $student->name)[0];
            $numThreads = rand(1, 3);

            for ($t = 0; $t < $numThreads; $t++) {
                $month  = rand(5, 6);
                $day    = rand(1, 25);
                $msgTime = Carbon::create(2026, $month, $day, rand(9, 16), rand(0, 59), 0);

                // Teacher sends first
                $tmsg = sprintf(
                    $teacherMessages[array_rand($teacherMessages)],
                    $firstName
                );
                Message::create([
                    'sender_id'   => $teacher->id,
                    'receiver_id' => $parent->id,
                    'student_id'  => $student->id,
                    'content'     => $tmsg,
                    'is_read'     => true,
                    'created_at'  => $msgTime,
                    'updated_at'  => $msgTime,
                ]);
                $msgCount++;

                // Parent replies
                $pmsg = sprintf(
                    $parentMessages[array_rand($parentMessages)],
                    $firstName
                );
                $replyTime = $msgTime->copy()->addMinutes(rand(15, 120));
                Message::create([
                    'sender_id'   => $parent->id,
                    'receiver_id' => $teacher->id,
                    'student_id'  => $student->id,
                    'content'     => $pmsg,
                    'is_read'     => rand(0, 1) === 1,
                    'created_at'  => $replyTime,
                    'updated_at'  => $replyTime,
                ]);
                $msgCount++;
            }
        }

        $this->command->info("✓ {$msgCount} messages created.");
        $this->command->info('');
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  KHAS-Talk seeder complete!');
        $this->command->info('  5 classes · 5 teachers · ' . count($parentModels) . ' parents');
        $this->command->info('  ' . count($studentModels) . ' students · ' . $logCount . ' logs · ' . $msgCount . ' messages');
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  Test accounts (all passwords: password)');
        $this->command->info('  admin@khastalk.com     → Admin');
        $this->command->info('  teacher@khastalk.com   → Cikgu Novianti (MATAHARI)');
        $this->command->info('  parent@khastalk.com    → Puan Rohani');
        $this->command->info('  senior@khastalk.com    → Pn Siti Hajar');
        $this->command->info('════════════════════════════════════════');
    }
}
