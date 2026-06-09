<?php
// ══════════════════════════════════════════════════════════════════
// SEEDERS LARAVEL — MOS OFPPT
// Chaque classe dans son propre fichier dans database/seeders/
// Commande : php artisan db:seed
// ══════════════════════════════════════════════════════════════════

// ── DatabaseSeeder.php ────────────────────────────────────────────

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LessonSeeder::class,
            QuestionSeeder::class,
            QuizSeeder::class,
            BadgeSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅  Seed terminé !');
        $this->command->table(
            ['Table', 'Enregistrements'],
            [
                ['users',         \App\Models\User::count()],
                ['lessons',       \App\Models\Lesson::count()],
                ['questions',     \App\Models\Question::count()],
                ['quizzes',       \App\Models\Quiz::count()],
                ['quiz_questions',\DB::table('quiz_questions')->count()],
                ['badges',        \App\Models\Badge::count()],
            ]
        );
        $this->command->info('');
        $this->command->info('🔑  Comptes de test :');
        $this->command->info('   apprenant@mos-ofppt.ma  /  Test1234!');
        $this->command->info('   formateur@mos-ofppt.ma  /  Test1234!');
        $this->command->info('   admin@mos-ofppt.ma      /  Test1234!');
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── UserSeeder.php ────────────────────────────────────────────────

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email'      => 'apprenant@mos-ofppt.ma',
                'password'   => Hash::make('Test1234!'),
                'first_name' => 'Ahmed',
                'last_name'  => 'Benali',
                'role'       => 'STUDENT',
            ],
            [
                'email'      => 'formateur@mos-ofppt.ma',
                'password'   => Hash::make('Test1234!'),
                'first_name' => 'Fatima',
                'last_name'  => 'Zahra',
                'role'       => 'TRAINER',
            ],
            [
                'email'      => 'admin@mos-ofppt.ma',
                'password'   => Hash::make('Test1234!'),
                'first_name' => 'Admin',
                'last_name'  => 'OFPPT',
                'role'       => 'ADMIN',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('   👤 3 utilisateurs créés');
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── QuestionSeeder.php ────────────────────────────────────────────

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    // Mappage difficulté int → string
    private function mapDifficulty(int $d): string
    {
        return match($d) {
            1       => 'BEGINNER',
            2       => 'INTERMEDIATE',
            default => 'ADVANCED',
        };
    }

    // Chemin vers les fichiers JSON (depuis le dossier backend/)
    private function loadJson(string $module): array
    {
        $path = base_path("../../content/{$module}/quizzes.json");

        if (!file_exists($path)) {
            $this->command->warn("   ⚠️  Fichier non trouvé : {$path}");
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function run(): void
    {
        $modules = ['word', 'excel', 'powerpoint'];
        $total   = 0;

        foreach ($modules as $module) {
            $questions = $this->loadJson($module);

            foreach ($questions as $q) {
                Question::updateOrCreate(
                    ['id' => $q['id']],
                    [
                        'app_module'    => strtoupper($module === 'powerpoint' ? 'POWERPOINT' : $module),
                        'domain'        => $q['domain'],
                        'difficulty'    => $this->mapDifficulty($q['difficulty']),
                        'question_text' => $q['questionText'],
                        'options'       => $q['options'],          // Cast JSON auto
                        'correct_index' => $q['correctIndex'],
                        'explanation'   => $q['explanation'],
                        'mos_objective' => $q['mosObjective'] ?? null,
                    ]
                );
                $total++;
            }

            $this->command->info("   ✅ " . count($questions) . " questions {$module} chargées");
        }

        $this->command->info("   ❓ Total : {$total} questions");
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── QuizSeeder.php ────────────────────────────────────────────────

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['key' => 'WORD',        'label' => 'Word'],
            ['key' => 'EXCEL',       'label' => 'Excel'],
            ['key' => 'POWERPOINT',  'label' => 'PowerPoint'],
        ];

        foreach ($modules as $mod) {
            $module = $mod['key'];
            $label  = $mod['label'];

            // Récupérer toutes les questions du module
            $questionIds = Question::where('app_module', $module)
                                   ->where('is_active', true)
                                   ->pluck('id')
                                   ->toArray();

            if (empty($questionIds)) {
                $this->command->warn("   ⚠️  Aucune question pour {$label} — relancer QuestionSeeder d'abord");
                continue;
            }

            // ── Quiz complet (révision) ──────────────────────────
            $fullId = 'quiz-' . strtolower($module) . '-full';
            Quiz::updateOrCreate(
                ['id' => $fullId],
                [
                    'app_module'    => $module,
                    'title'         => "Quiz complet — {$label}",
                    'description'   => "Testez toutes vos connaissances sur Microsoft {$label} pour la certification MOS.",
                    'duration_min'  => 30,
                    'passing_score' => 70,
                    'is_exam_mode'  => false,
                    'is_published'  => true,
                ]
            );

            // Attacher toutes les questions
            $this->attachQuestions($fullId, $questionIds);

            // ── Examen blanc (50 min, 35 questions max) ──────────
            $examId    = 'exam-' . strtolower($module) . '-blanc';
            $examQIds  = array_slice($questionIds, 0, 35);

            Quiz::updateOrCreate(
                ['id' => $examId],
                [
                    'app_module'    => $module,
                    'title'         => "Examen blanc — {$label} MOS",
                    'description'   => "Simulation officielle MOS {$label}. " . count($examQIds) . " questions en 50 minutes. Conditions réelles.",
                    'duration_min'  => 50,
                    'passing_score' => 70,
                    'is_exam_mode'  => true,
                    'is_published'  => true,
                ]
            );

            $this->attachQuestions($examId, $examQIds);

            $this->command->info("   📝 {$label} — quiz ({$fullId}: " . count($questionIds) . "q) + examen blanc ({$examId}: " . count($examQIds) . "q)");
        }
    }

    private function attachQuestions(string $quizId, array $questionIds): void
    {
        // Supprimer les anciennes liaisons
        DB::table('quiz_questions')->where('quiz_id', $quizId)->delete();

        // Réinsérer
        $rows = [];
        foreach ($questionIds as $order => $qId) {
            $rows[] = [
                'quiz_id'     => $quizId,
                'question_id' => $qId,
                'order'       => $order + 1,
            ];
        }

        DB::table('quiz_questions')->insert($rows);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── BadgeSeeder.php ───────────────────────────────────────────────

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ── Word
            ['id' => 'badge-word-first',     'name' => 'Premier Pas Word',       'description' => 'Terminer votre première leçon Word',                   'icon_url' => '/badges/word-first.svg',   'condition' => 'word_lesson_1',        'xp_reward' => 25],
            ['id' => 'badge-word-quiz',      'name' => 'Quiz Word Parfait',      'description' => 'Obtenir 100% à un quiz Word',                          'icon_url' => '/badges/word-quiz.svg',    'condition' => 'word_quiz_perfect',    'xp_reward' => 50],
            ['id' => 'badge-word-certified', 'name' => 'Word Certifié',          'description' => "Passer l'examen blanc Word avec 80%+",                 'icon_url' => '/badges/word-cert.svg',    'condition' => 'exam_pass_word',       'xp_reward' => 200],
            // ── Excel
            ['id' => 'badge-excel-first',    'name' => 'Premier Pas Excel',      'description' => 'Terminer votre première leçon Excel',                  'icon_url' => '/badges/excel-first.svg',  'condition' => 'excel_lesson_1',       'xp_reward' => 25],
            ['id' => 'badge-excel-formules', 'name' => 'Maître des Formules',    'description' => 'Réussir tous les exercices de formules Excel',         'icon_url' => '/badges/excel-form.svg',   'condition' => 'excel_formulas_all',   'xp_reward' => 75],
            ['id' => 'badge-excel-certified','name' => 'Excel Certifié',         'description' => "Passer l'examen blanc Excel avec 80%+",                'icon_url' => '/badges/excel-cert.svg',   'condition' => 'exam_pass_excel',      'xp_reward' => 200],
            // ── PowerPoint
            ['id' => 'badge-ppt-first',      'name' => 'Premier Pas PowerPoint', 'description' => 'Terminer votre première leçon PowerPoint',             'icon_url' => '/badges/ppt-first.svg',    'condition' => 'ppt_lesson_1',         'xp_reward' => 25],
            ['id' => 'badge-ppt-certified',  'name' => 'PowerPoint Certifié',   'description' => "Passer l'examen blanc PowerPoint avec 80%+",            'icon_url' => '/badges/ppt-cert.svg',     'condition' => 'exam_pass_ppt',        'xp_reward' => 200],
            // ── Généraux
            ['id' => 'badge-triple-crown',   'name' => 'Triple Couronne MOS',   'description' => 'Obtenir les 3 certifications MOS',                     'icon_url' => '/badges/triple.svg',       'condition' => 'all_three_certified',  'xp_reward' => 500],
            ['id' => 'badge-streak-7',       'name' => 'Semaine Consécutive',   'description' => 'Se connecter 7 jours de suite',                        'icon_url' => '/badges/streak-7.svg',     'condition' => 'streak_7',             'xp_reward' => 30],
            ['id' => 'badge-streak-30',      'name' => 'Mois Consécutif',       'description' => 'Se connecter 30 jours de suite',                       'icon_url' => '/badges/streak-30.svg',    'condition' => 'streak_30',            'xp_reward' => 150],
            ['id' => 'badge-speed-quiz',     'name' => "Rapide comme l'éclair", 'description' => 'Finir un quiz < 5 min avec 80%+',                      'icon_url' => '/badges/speed.svg',        'condition' => 'quiz_fast_80',         'xp_reward' => 60],
            ['id' => 'badge-first-exam',     'name' => 'Premier Examen Blanc',  'description' => 'Passer votre premier examen blanc',                    'icon_url' => '/badges/first-exam.svg',   'condition' => 'first_exam_attempt',   'xp_reward' => 50],
            ['id' => 'badge-top-scorer',     'name' => 'Top Scorer',            'description' => 'Meilleur score de votre classe sur un examen',         'icon_url' => '/badges/top-scorer.svg',   'condition' => 'class_top_score',      'xp_reward' => 100],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['id' => $badge['id']], $badge);
        }

        $this->command->info('   🏅 ' . count($badges) . ' badges créés');
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── LessonSeeder.php ──────────────────────────────────────────────

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $lessons = [
            // ── WORD ──────────────────────────────────────────────
            ['slug' => 'styles-et-titres',             'app_module' => 'WORD',       'title' => 'Styles et titres hiérarchiques',     'description' => 'Structurez vos documents avec les styles Titre 1, 2, 3',          'order' => 1,  'duration_min' => 20, 'difficulty' => 'BEGINNER',     'objectives' => ['Appliquer les styles Titre', 'Créer une table des matières'],           'mos_objectives' => ['1.1.1', '1.1.2']],
            ['slug' => 'mise-en-page',                 'app_module' => 'WORD',       'title' => 'Mise en page et marges',             'description' => 'Maîtrisez les marges, orientations et tailles de page',            'order' => 2,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Modifier les marges', "Changer l'orientation"],                         'mos_objectives' => ['2.1.1']],
            ['slug' => 'en-tetes-pieds-de-page',       'app_module' => 'WORD',       'title' => 'En-têtes et pieds de page',          'description' => 'Numéros de page et informations récurrentes',                      'order' => 3,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Insérer numéros de page', 'Première page différente'],                 'mos_objectives' => ['2.2.1']],
            ['slug' => 'tableaux-word',                'app_module' => 'WORD',       'title' => 'Tableaux dans Word',                 'description' => 'Créez et formatez des tableaux professionnels',                    'order' => 4,  'duration_min' => 20, 'difficulty' => 'BEGINNER',     'objectives' => ['Insérer un tableau', 'Fusionner des cellules'],                         'mos_objectives' => ['3.1.1']],
            ['slug' => 'images-word',                  'app_module' => 'WORD',       'title' => 'Images et illustrations',            'description' => 'Insérez et formatez des images dans vos documents',               'order' => 5,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Insérer une image', 'Habillage du texte'],                               'mos_objectives' => ['3.2.1']],
            ['slug' => 'publipostage',                 'app_module' => 'WORD',       'title' => 'Publipostage',                       'description' => 'Créez des courriers personnalisés en masse',                       'order' => 6,  'duration_min' => 30, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Connecter une source de données', 'Insérer des champs de fusion'],     'mos_objectives' => ['4.1.1']],
            ['slug' => 'suivi-modifications',          'app_module' => 'WORD',       'title' => 'Suivi des modifications',            'description' => 'Collaborez avec le suivi des modifications',                       'order' => 7,  'duration_min' => 20, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Activer le suivi', 'Accepter/refuser'],                                 'mos_objectives' => ['5.1.1']],
            // ── EXCEL ─────────────────────────────────────────────
            ['slug' => 'formules-de-base',             'app_module' => 'EXCEL',      'title' => 'Formules de base',                   'description' => 'SOMME, MOYENNE, MAX, MIN et références absolues/relatives',        'order' => 1,  'duration_min' => 20, 'difficulty' => 'BEGINNER',     'objectives' => ['Utiliser SOMME et MOYENNE', 'Références absolues et relatives'],        'mos_objectives' => ['4.1.1']],
            ['slug' => 'formules-conditionnelles',     'app_module' => 'EXCEL',      'title' => 'Formules conditionnelles',           'description' => 'SI, NB.SI, SOMME.SI et leurs variantes ENS',                       'order' => 2,  'duration_min' => 25, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Maîtriser la fonction SI', 'NB.SI et SOMME.SI'],                       'mos_objectives' => ['4.2.1']],
            ['slug' => 'recherchev-index-equiv',       'app_module' => 'EXCEL',      'title' => 'RECHERCHEV et INDEX/EQUIV',          'description' => 'Recherchez des données dans vos tableaux',                         'order' => 3,  'duration_min' => 30, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Utiliser RECHERCHEV', 'Combiner INDEX et EQUIV'],                      'mos_objectives' => ['4.3.1']],
            ['slug' => 'mfc',                          'app_module' => 'EXCEL',      'title' => 'Mise en forme conditionnelle',       'description' => 'Visualisez vos données avec des règles automatiques',              'order' => 4,  'duration_min' => 20, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Créer des règles MFC', 'Barres de données'],                           'mos_objectives' => ['2.3.1']],
            ['slug' => 'tableaux-structures',          'app_module' => 'EXCEL',      'title' => 'Tableaux structurés',                'description' => 'Convertir une plage en tableau Excel pour faciliter l\'analyse',  'order' => 5,  'duration_min' => 20, 'difficulty' => 'BEGINNER',     'objectives' => ['Créer un tableau structuré', 'Ligne de totaux'],                       'mos_objectives' => ['3.1.1']],
            ['slug' => 'tri-et-filtres',               'app_module' => 'EXCEL',      'title' => 'Tri et filtres',                     'description' => 'Organisez et filtrez vos données efficacement',                    'order' => 6,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Activer les filtres', 'Tri multi-colonnes'],                            'mos_objectives' => ['3.2.1']],
            ['slug' => 'tableaux-croises-dynamiques',  'app_module' => 'EXCEL',      'title' => 'Tableaux croisés dynamiques',        'description' => 'Analysez et résumez vos données avec les TCD',                     'order' => 7,  'duration_min' => 35, 'difficulty' => 'ADVANCED',     'objectives' => ['Créer un TCD', 'Grouper les données par date'],                         'mos_objectives' => ['5.1.1']],
            ['slug' => 'graphiques-excel',             'app_module' => 'EXCEL',      'title' => 'Graphiques et visualisations',       'description' => 'Créez des graphiques professionnels',                              'order' => 8,  'duration_min' => 25, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Créer des graphiques', 'Ajouter des étiquettes'],                      'mos_objectives' => ['5.2.1']],
            // ── POWERPOINT ────────────────────────────────────────
            ['slug' => 'gestion-diapositives',         'app_module' => 'POWERPOINT', 'title' => 'Gestion des diapositives',           'description' => 'Créer, dupliquer, réorganiser les diapositives',                  'order' => 1,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Insérer et dupliquer', 'Changer la disposition'],                      'mos_objectives' => ['1.1.1']],
            ['slug' => 'themes-et-couleurs',           'app_module' => 'POWERPOINT', 'title' => 'Thèmes et couleurs',                 'description' => 'Donnez un style professionnel à vos présentations',               'order' => 2,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Appliquer un thème', 'Modifier les couleurs'],                         'mos_objectives' => ['1.2.1']],
            ['slug' => 'masque-diapositives',          'app_module' => 'POWERPOINT', 'title' => 'Masque des diapositives',            'description' => 'Contrôlez la mise en forme globale de votre présentation',        'order' => 3,  'duration_min' => 25, 'difficulty' => 'INTERMEDIATE', 'objectives' => ['Modifier le masque', 'Créer des dispositions'],                        'mos_objectives' => ['1.3.1']],
            ['slug' => 'transitions',                  'app_module' => 'POWERPOINT', 'title' => 'Transitions entre diapositives',     'description' => 'Ajoutez des effets visuels entre les diapositives',               'order' => 4,  'duration_min' => 15, 'difficulty' => 'BEGINNER',     'objectives' => ['Ajouter des transitions', 'Appliquer à toutes'],                       'mos_objectives' => ['2.1.1']],
            ['slug' => 'animations',                   'app_module' => 'POWERPOINT', 'title' => 'Animations et effets',               'description' => "Animez vos éléments pour capter l'attention",                      'order' => 5,  'duration_min' => 25, 'difficulty' => 'INTERMEDIATE', 'objectives' => ["Animations d'entrée", 'Minutage et ordre'],                            'mos_objectives' => ['2.2.1']],
            ['slug' => 'images-ppt',                   'app_module' => 'POWERPOINT', 'title' => 'Images et illustrations',            'description' => 'Insérez, rognez et stylisez vos images',                          'order' => 6,  'duration_min' => 20, 'difficulty' => 'BEGINNER',     'objectives' => ['Insérer et rogner', "Supprimer l'arrière-plan"],                       'mos_objectives' => ['3.1.1']],
        ];

        foreach ($lessons as $data) {
            Lesson::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['content_json' => ['blocks' => []], 'is_published' => true])
            );
        }

        $this->command->info('   📚 ' . count($lessons) . ' leçons créées (Word: 7, Excel: 8, PPT: 6)');
    }
}
