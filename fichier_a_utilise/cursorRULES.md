# 🎓 MOS OFPPT — Cursor Rules & Project Intelligence
> Backend : Laravel 11 · Frontend : React 18 + TypeScript
> Version : 2.0 — Avril 2026

---

## 📌 Identité du projet

- **Nom** : Plateforme MOS OFPPT
- **Slogan** : "Préparez votre Certificat MOS — Maîtrisez Word, Excel et PowerPoint avec l'OFPPT"
- **Type** : SPA React + API REST Laravel
- **Public cible** : Stagiaires OFPPT, candidats libres MOS, formateurs OFPPT
- **Langue de l'interface** : Français
- **Certifications couvertes** : MOS Word 2019/365 · MOS Excel 2019/365 · MOS PowerPoint 365

---

## 🏗️ Stack technique — RESPECTER STRICTEMENT

### Backend (Laravel)
```
Framework    : Laravel 11
Language     : PHP 8.3
Auth         : Laravel Sanctum (tokens API + SPA cookies)
ORM          : Eloquent
DB           : MySQL 8 (ou PostgreSQL 16)
Cache/Queue  : Redis 7
Validation   : Form Requests Laravel
Ressources   : Laravel API Resources (JsonResource)
Mail         : Laravel Mail + Mailable classes
Tests        : Pest PHP
Storage      : Laravel Storage (fichiers Office .docx/.xlsx/.pptx)
```

### Frontend
```
Framework    : React 18 + TypeScript (strict mode)
Build        : Vite 5
Styling      : Tailwind CSS v3
State        : Zustand
Routing      : React Router v6 (lazy loading)
Forms        : React Hook Form + Zod
HTTP         : Axios (intercepteurs Sanctum)
UI           : Shadcn/ui + composants custom
Icons        : Lucide React
Charts       : Recharts
Tests        : Vitest + React Testing Library
```

### Infrastructure
```
Conteneurs   : Docker + Docker Compose
Web server   : Nginx (proxy + SSL)
CI/CD        : GitHub Actions
```

---

## 📁 Structure des dossiers — RESPECTER STRICTEMENT

```
mos-ofppt/
├── backend/                              ← Projet Laravel 11
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   └── PasswordResetController.php
│   │   │   │   ├── LessonController.php
│   │   │   │   ├── ExerciseController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── ProgressController.php
│   │   │   │   ├── BadgeController.php
│   │   │   │   ├── EnrollmentController.php
│   │   │   │   └── Admin/
│   │   │   │       ├── ClassController.php
│   │   │   │       ├── StudentController.php
│   │   │   │       └── ReportController.php
│   │   │   ├── Requests/                 ← Form Requests (validation)
│   │   │   │   ├── Auth/RegisterRequest.php
│   │   │   │   ├── Auth/LoginRequest.php
│   │   │   │   ├── Quiz/SubmitAttemptRequest.php
│   │   │   │   └── Exam/SubmitExamRequest.php
│   │   │   ├── Resources/                ← API Resources (transformation JSON)
│   │   │   │   ├── UserResource.php
│   │   │   │   ├── LessonResource.php
│   │   │   │   ├── QuestionResource.php
│   │   │   │   ├── QuizResource.php
│   │   │   │   └── QuizAttemptResource.php
│   │   │   └── Middleware/
│   │   │       └── EnsureRole.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Lesson.php
│   │   │   ├── Exercise.php
│   │   │   ├── Question.php
│   │   │   ├── Quiz.php
│   │   │   ├── QuizAttempt.php
│   │   │   ├── ExamAttempt.php
│   │   │   ├── Progress.php
│   │   │   ├── Enrollment.php
│   │   │   ├── Badge.php
│   │   │   ├── UserBadge.php
│   │   │   ├── ClassRoom.php             ← "Class" est réservé en PHP !
│   │   │   └── ClassMember.php
│   │   ├── Services/
│   │   │   ├── ExamScoringService.php
│   │   │   ├── BadgeAwardService.php
│   │   │   ├── ProgressService.php
│   │   │   └── XpService.php
│   │   └── Enums/
│   │       ├── AppModule.php             ← WORD | EXCEL | POWERPOINT
│   │       ├── Role.php                  ← STUDENT | TRAINER | ADMIN
│   │       └── Difficulty.php            ← BEGINNER | INTERMEDIATE | ADVANCED
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   │   ├── DatabaseSeeder.php
│   │   │   ├── UserSeeder.php
│   │   │   ├── LessonSeeder.php
│   │   │   ├── QuestionSeeder.php
│   │   │   ├── QuizSeeder.php
│   │   │   └── BadgeSeeder.php
│   │   └── factories/
│   ├── routes/
│   │   ├── api.php                       ← Toutes les routes REST
│   │   └── web.php                       ← Catch-all SPA
│   └── tests/
│       ├── Feature/                      ← Tests Pest endpoints
│       └── Unit/                         ← Tests services
│
├── frontend/                             ← React 18 + TypeScript
│   └── src/
│       ├── pages/ (auth, dashboard, courses, quiz, exam, profile, admin)
│       ├── components/ (ui, layout, course, quiz, exam, common)
│       ├── hooks/       (useAuth, useProgress, useTimer, useQuiz)
│       ├── store/       (authStore.ts, progressStore.ts)
│       ├── services/    (api.ts, auth.service.ts, course.service.ts)
│       └── types/       (index.ts — miroir des modèles Laravel)
│
├── content/                              ← JSON lu par les seeders
│   ├── word/quizzes.json
│   ├── excel/quizzes.json
│   └── powerpoint/quizzes.json
│
├── RULES.md
├── PRD.md
├── docker-compose.yml
└── .env.example
```

---

## 🗄️ Base de données MySQL — Conventions

- Tables : **snake_case pluriel** (`quiz_attempts`, `user_badges`)
- Colonnes : **snake_case** (`first_name`, `correct_index`, `app_module`)
- Clés étrangères : `{modele_singulier}_id` (`user_id`, `quiz_id`)
- Énums : stockés en **VARCHAR** avec les valeurs `'WORD'`, `'BEGINNER'`, etc.
- Timestamps : toujours `created_at` + `updated_at` (sauf tables pivot)

### Tables & colonnes clés
```
users               id, email, password, first_name, last_name, role(STUDENT|TRAINER|ADMIN),
                    avatar_url, xp_points(0), streak_days(0), last_login_at, is_active(true)

lessons             id, slug(unique), app_module, title, description, order, duration_min,
                    difficulty, objectives(JSON), mos_objectives(JSON), content_json(JSON),
                    thumbnail_url, is_published(false)

questions           id, app_module, domain, difficulty, question_text, options(JSON array 4 items),
                    correct_index(0-3), explanation, mos_objective, is_active(true)

quizzes             id, app_module, title, description, duration_min(15), passing_score(70),
                    is_exam_mode(false), is_published(true)

quiz_questions      quiz_id FK, question_id FK, order — PRIMARY KEY(quiz_id, question_id)

quiz_attempts       id, user_id FK, quiz_id FK, score, total_questions, correct_questions,
                    answers(JSON), duration_sec, passed(bool), completed_at

exam_attempts       id, user_id FK, app_module, score, total_questions, correct_questions,
                    answers(JSON), passed(bool), started_at, completed_at, duration_sec

progress            user_id FK, lesson_id FK — PRIMARY KEY composite
                    completed(false), completed_at, time_spent_sec(0)

enrollments         user_id FK, app_module — PRIMARY KEY composite
                    enrolled_at, target_date

badges              id, name, description, icon_url, condition(string), xp_reward(50)
user_badges         user_id FK, badge_id FK — PRIMARY KEY composite, earned_at

class_rooms         id, name, description, trainer_id FK, code(unique), is_active(true)
class_members       class_room_id FK, user_id FK — PRIMARY KEY composite, joined_at
```

---

## 🌐 Routes API — `routes/api.php`

```php
// Préfixe /api/v1 configuré dans bootstrap/app.php
// Route::prefix('v1')->group(function () { ... })

// ── Public ─────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register',       [AuthController::class, 'register']);
    Route::post('/login',          [AuthController::class, 'login']);
    Route::post('/forgot-password',[PasswordResetController::class, 'sendLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
});

// ── Protégées par Sanctum ──────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout',    [AuthController::class, 'logout']);
    Route::get('/user',            [AuthController::class, 'me']);
    Route::put('/user',            [AuthController::class, 'update']);
    Route::get('/user/stats',      [UserController::class, 'stats']);
    Route::get('/user/badges',     [BadgeController::class, 'index']);

    Route::get('/modules',                           [LessonController::class, 'modules']);
    Route::get('/modules/{module}/lessons',          [LessonController::class, 'index']);
    Route::get('/modules/{module}/lessons/{slug}',   [LessonController::class, 'show']);
    Route::post('/modules/{module}/enroll',          [EnrollmentController::class, 'store']);
    Route::get('/modules/{module}/exercises',        [ExerciseController::class, 'index']);
    Route::get('/exercises/{exercise}/download',     [ExerciseController::class, 'download']);

    Route::get('/quizzes/{module}',         [QuizController::class, 'index']);
    Route::get('/quizzes/{quiz}',           [QuizController::class, 'show']);
    Route::post('/quizzes/{quiz}/attempt',  [QuizController::class, 'attempt']);

    Route::get('/exam/{module}',            [ExamController::class, 'start']);
    Route::post('/exam/{module}/submit',    [ExamController::class, 'submit']);

    Route::get('/progress',                 [ProgressController::class, 'index']);
    Route::patch('/progress/{lesson}',      [ProgressController::class, 'update']);

    // ── Admin / Formateur ──────────────────────────────────
    Route::middleware('role:trainer,admin')->prefix('admin')->group(function () {
        Route::get('/students',              [Admin\StudentController::class, 'index']);
        Route::get('/students/{user}',       [Admin\StudentController::class, 'show']);
        Route::apiResource('/classes',       Admin\ClassController::class);
        Route::get('/reports',              Admin\ReportController::class);
    });
});
```

---

## ⚙️ Conventions Laravel

### Controllers — slim, délèguent aux Services
```php
// Toujours retourner un ApiResource ou response()->json()
// Jamais de logique métier directement dans le controller

public function attempt(SubmitAttemptRequest $request, Quiz $quiz): JsonResponse
{
    $attempt = $this->scoringService->scoreQuiz($quiz, $request->validated(), auth()->user());
    $this->badgeService->checkAndAward(auth()->user());
    return new QuizAttemptResource($attempt);
}
```

### Format réponse JSON uniforme
```json
// Succès simple
{ "data": { "id": "...", "score": 85 }, "message": "Quiz soumis avec succès" }

// Collection paginée
{ "data": [...], "meta": { "current_page": 1, "total": 50, "per_page": 20 } }

// Erreur validation (422 auto par Laravel)
{ "message": "Les données sont invalides.", "errors": { "email": ["L'email est requis."] } }

// Erreur métier
{ "message": "Module non disponible.", "error": "MODULE_NOT_ENROLLED" }
```

### Modèles Eloquent
```php
// Toujours définir $fillable, $casts, et les relations
protected $casts = [
    'app_module'   => AppModule::class,      // Enum PHP 8.1
    'difficulty'   => Difficulty::class,
    'options'      => 'array',               // JSON → array automatique
    'objectives'   => 'array',
    'answers'      => 'array',
    'content_json' => 'array',
    'passed'       => 'boolean',
    'is_published' => 'boolean',
];
```

### Seeders — lecture depuis /content JSON
```php
public function run(): void
{
    // Chemin depuis le dossier backend/
    $path = base_path('../../content/word/quizzes.json');
    $questions = json_decode(file_get_contents($path), true);

    foreach ($questions as $q) {
        Question::updateOrCreate(
            ['id' => $q['id']],
            [
                'app_module'    => $q['appModule'],
                'domain'        => $q['domain'],
                'difficulty'    => match($q['difficulty']) {
                    1 => 'BEGINNER', 2 => 'INTERMEDIATE', default => 'ADVANCED'
                },
                'question_text' => $q['questionText'],
                'options'       => $q['options'],
                'correct_index' => $q['correctIndex'],
                'explanation'   => $q['explanation'],
                'mos_objective' => $q['mosObjective'] ?? null,
            ]
        );
    }
}
```

---

## 🎨 Frontend — Conventions React/TypeScript

### Config Axios (Sanctum)
```typescript
// src/services/api.ts
const api = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL}/api/v1`,
  withCredentials: true,              // REQUIS pour Sanctum SPA
  headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
})

// Ajouter le token Bearer si stocké (mode token API)
api.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Intercepteur 401 → déconnexion automatique
api.interceptors.response.use(null, (error) => {
  if (error.response?.status === 401) useAuthStore.getState().logout()
  return Promise.reject(error)
})
```

### Types TypeScript (miroir des modèles Laravel)
```typescript
export type AppModule = 'WORD' | 'EXCEL' | 'POWERPOINT'
export type Role      = 'STUDENT' | 'TRAINER' | 'ADMIN'
export type Difficulty= 'BEGINNER' | 'INTERMEDIATE' | 'ADVANCED'

export interface User {
  id: string; email: string; firstName: string; lastName: string
  role: Role; xpPoints: number; streakDays: number; avatarUrl?: string
}
export interface Lesson {
  id: string; slug: string; appModule: AppModule; title: string
  description: string; order: number; durationMin: number; difficulty: Difficulty
  objectives: string[]; isPublished: boolean
}
export interface Question {
  id: string; appModule: AppModule; domain: string; difficulty: Difficulty
  questionText: string; options: string[]
  correctIndex?: number      // Masqué pendant l'examen
  explanation?: string; mosObjective?: string
}
```

---

## 🔐 Sécurité

- `Hash::make()` bcrypt pour les mots de passe
- Sanctum : tokens pour mobile, cookies httpOnly pour SPA web
- `throttle:5,1` sur `/login` et `/register`
- Middleware `EnsureRole` sur toutes les routes admin
- `config/cors.php` : `allowed_origins` whitelist uniquement
- Fichiers uploads : `storage/app/private/` (jamais public direct)

---

## 🧪 Tests Pest PHP

```php
// tests/Feature/Auth/LoginTest.php
it('retourne un token pour des identifiants valides', function () {
    $user = User::factory()->create(['password' => Hash::make('Test1234!')]);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email, 'password' => 'Test1234!',
    ])->assertOk()->assertJsonStructure(['data' => ['token', 'user']]);
});

it('refuse les mauvais identifiants', function () {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'faux@test.ma', 'password' => 'wrong',
    ])->assertUnauthorized();
});

// tests/Feature/Quiz/QuizAttemptTest.php
it('soumet une tentative et calcule le score', function () {
    $user = User::factory()->create();
    $quiz = Quiz::factory()->hasQuestions(10)->create();

    $this->actingAs($user)
         ->postJson("/api/v1/quizzes/{$quiz->id}/attempt", [
             'answers' => array_fill(0, 10, 0),
             'duration_sec' => 300,
         ])->assertOk()->assertJsonPath('data.passed', false);
});
```

---

## 🚀 Commandes de développement

```bash
# ── Backend Laravel ──────────────────────────────────────────
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --port=8000
php artisan storage:link

# Utiles quotidiennement
php artisan optimize:clear            # Vider caches config/route/view
php artisan route:list --path=api     # Voir toutes les routes API
php artisan tinker                    # REPL Eloquent interactif
php artisan test                      # Tests Pest
php artisan make:model NomModel -mfsc # Modèle + migration + factory + seeder + controller

# ── Frontend React ───────────────────────────────────────────
cd frontend
npm install && npm run dev            # http://localhost:5173

# ── Docker (MySQL + Redis + phpMyAdmin) ──────────────────────
docker-compose up -d
docker-compose down -v                # Reset complet
```

---

## 🗺️ Roadmap

| Phase | Features | Statut |
|-------|----------|--------|
| MVP   | Auth Sanctum, profil, leçons Word, quiz, dashboard | 🔴 À faire |
| Core  | Modules Excel+PPT, exercices, examen blanc, formateur | 🔴 À faire |
| +     | Gamification XP/badges, analytics, notifications | 🔴 À faire |
| Opt.  | PWA, export PDF, intégration Certiport | 🔴 À faire |

---
*Version 2.0 — Avril 2026 — Laravel 11 + React 18*
