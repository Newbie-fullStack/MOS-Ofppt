# 🎓 MOS OFPPT — Backend Laravel 11 + Frontend React 18

> Plateforme de préparation au Certificat MOS — Word, Excel, PowerPoint

---

## 🚀 Démarrage rapide

### Prérequis
- PHP 8.3+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `json`, `redis`
- [Composer](https://getcomposer.org)
- [Node.js 20+](https://nodejs.org)
- [Docker Desktop](https://www.docker.com) (pour MySQL + Redis)

---

### Installation Backend (Laravel)

```bash
cd backend

# 1. Installer les dépendances PHP
composer install

# 2. Configuration
cp .env.example .env
php artisan key:generate

# 3. Démarrer MySQL + Redis via Docker
docker-compose up -d

# 4. Migrations + Seed (tables + données)
php artisan migrate
php artisan db:seed

# 5. Lier le stockage public
php artisan storage:link

# 6. Lancer le serveur
php artisan serve --port=8000
# → API disponible sur http://localhost:8000/api/v1
```

### Installation Frontend (React)

```bash
cd frontend

npm install
cp .env.example .env   # VITE_API_URL=http://localhost:8000
npm run dev
# → App disponible sur http://localhost:5173
```

---

## 🔑 Comptes de test

| Email | Mot de passe | Rôle |
|---|---|---|
| apprenant@mos-ofppt.ma | Test1234! | Apprenant |
| formateur@mos-ofppt.ma | Test1234! | Formateur |
| admin@mos-ofppt.ma | Test1234! | Admin |

---

## 📊 Contenu inclus après seed

| Élément | Quantité |
|---|---|
| Questions QCM Word | 50 |
| Questions QCM Excel | 50 |
| Questions QCM PowerPoint | 50 |
| Leçons (Word + Excel + PPT) | 21 |
| Quiz complets | 3 |
| Examens blancs | 3 |
| Badges | 14 |

---

## 🛠️ Commandes Laravel utiles

```bash
# Développement quotidien
php artisan serve --port=8000         # Démarrer l'API
php artisan tinker                     # REPL Eloquent
php artisan route:list --path=api/v1  # Voir toutes les routes
php artisan optimize:clear            # Vider caches

# Base de données
php artisan migrate                   # Appliquer les migrations
php artisan migrate:fresh --seed      # Reset complet + seed
php artisan db:seed                   # Seed uniquement
php artisan db:seed --class=QuestionSeeder  # Seeder spécifique

# Générer des fichiers
php artisan make:model NomModel -mfsc # Modèle + migration + factory + seeder + controller
php artisan make:request NomRequest   # Form Request
php artisan make:resource NomResource # API Resource
php artisan make:service NomService   # Service (custom)

# Tests Pest
php artisan test                      # Tous les tests
php artisan test --filter=AuthTest    # Test spécifique
php artisan test --coverage           # Avec couverture de code

# Docker
docker-compose up -d                  # Démarrer MySQL + Redis
docker-compose --profile tools up -d  # + phpMyAdmin (port 8080) + MailHog (port 8025)
docker-compose down                   # Arrêter
docker-compose down -v                # Reset complet (⚠️ supprime données)
```

---

## 📁 Fichiers à placer dans le projet

| Fichier généré | Destination dans le projet |
|---|---|
| `RULES.md` | Racine → renommer en `.cursorrules` |
| `Enums.php` | Séparer en `app/Enums/AppModule.php`, `Role.php`, `Difficulty.php` |
| `Models.php` | Séparer en `app/Models/*.php` (un fichier par classe) |
| `migrations.php` | Séparer en `database/migrations/` (un fichier par table) |
| `seeders.php` | Séparer en `database/seeders/*.php` |
| `Controllers_Services.php` | Séparer selon les namespaces indiqués |
| `api.php` | `routes/api.php` |
| `.env.example` | `backend/.env.example` |
| `docker-compose.yml` | Racine du projet |
| `word_quizzes.json` | `content/word/quizzes.json` |
| `excel_quizzes.json` | `content/excel/quizzes.json` |
| `powerpoint_quizzes.json` | `content/powerpoint/quizzes.json` |

---

## 🌐 Endpoints API principaux

```
POST  /api/v1/auth/register
POST  /api/v1/auth/login
GET   /api/v1/user                     (auth)
GET   /api/v1/modules                  (auth)
GET   /api/v1/modules/word/lessons     (auth)
GET   /api/v1/quizzes/word             (auth)
POST  /api/v1/quizzes/{id}/attempt     (auth)
GET   /api/v1/exam/word                (auth)
POST  /api/v1/exam/word/submit         (auth)
GET   /api/v1/progress                 (auth)
PATCH /api/v1/progress/{lesson}        (auth)
```

---

## 🧪 Tests Pest — exemples

```bash
php artisan test tests/Feature/Auth/
php artisan test tests/Feature/Quiz/
php artisan test --coverage --min=80
```

---

*Laravel 11 · PHP 8.3 · MySQL 8 · Redis 7 · React 18 · Vite 5*
*Version 2.0 — Avril 2026 — MOS OFPPT*
