<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Metadonnées pour les slugs utilisés par `php artisan lessons:seed-content`.
     * Le contenu (content_json) est rempli par cette commande, pas ici —
     * ainsi on évite la duplication avec SeedLessonsContent.
     */
    public function run(): void
    {
        $defaults = [
            'thumbnail_url' => null,
            'video_url' => null,
            'is_published' => true,
            'content_json' => ['blocks' => []],
        ];

        $lessons = array_merge($this->wordLessons(), $this->excelLessons(), $this->powerPointLessons());

        foreach ($lessons as $row) {
            $slug = $row['slug'];
            Lesson::firstOrCreate(
                ['slug' => $slug],
                array_merge(
                    [
                        'app_module' => $row['app_module'],
                        'title' => $row['title'],
                        'description' => $row['description'],
                        'order' => $row['order'],
                        'duration_min' => $row['duration_min'],
                        'difficulty' => $row['difficulty'],
                        'objectives' => $row['objectives'],
                        'mos_objectives' => $row['mos_objectives'],
                    ],
                    $defaults
                )
            )->fill([
                'app_module' => $row['app_module'],
                'title' => $row['title'],
                'description' => $row['description'],
                'order' => $row['order'],
                'duration_min' => $row['duration_min'],
                'difficulty' => $row['difficulty'],
                'objectives' => $row['objectives'],
                'mos_objectives' => $row['mos_objectives'],
                'is_published' => true,
            ])->save();
        }
    }

    /** @return list<array<string, mixed>> */
    private function wordLessons(): array
    {
        return [
            [
                'slug' => 'styles-et-titres',
                'app_module' => 'WORD',
                'title' => 'Styles et titres hiérarchiques',
                'description' => 'Galerie des styles, hiérarchie des titres et table des matières automatique.',
                'order' => 1,
                'duration_min' => 20,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Appliquer les styles Titre 1/2/3', 'Créer et mettre à jour une table des matières'],
                'mos_objectives' => ['1.1'],
            ],
            [
                'slug' => 'mise-en-page',
                'app_module' => 'WORD',
                'title' => 'Mise en page',
                'description' => 'Marges, orientation, sauts de page et sections.',
                'order' => 2,
                'duration_min' => 18,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Configurer marges et orientation', 'Gérer les sections'],
                'mos_objectives' => ['1.2'],
            ],
            [
                'slug' => 'en-tetes-pieds-de-page',
                'app_module' => 'WORD',
                'title' => 'En-têtes et pieds de page',
                'description' => 'En-tête/pied différent première page et numéros de page.',
                'order' => 3,
                'duration_min' => 18,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Insérer et personnaliser en-tête et pied', 'Insérer des numéros de page'],
                'mos_objectives' => ['2.2'],
            ],
            [
                'slug' => 'tableaux-word',
                'app_module' => 'WORD',
                'title' => 'Tableaux',
                'description' => 'Création, mise en forme et fusion/split des cellules.',
                'order' => 4,
                'duration_min' => 22,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Créer un tableau et appliquer un style prédéfini', 'Fusionner et fractionner les cellules'],
                'mos_objectives' => ['3.3'],
            ],
            [
                'slug' => 'images-word',
                'app_module' => 'WORD',
                'title' => 'Images et graphiques SmartArt',
                'description' => 'Insertion, disposition et légende des illustrations.',
                'order' => 5,
                'duration_min' => 20,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Insérer et recadrer des images', 'Utiliser disposition et SmartArt'],
                'mos_objectives' => ['3.5'],
            ],
            [
                'slug' => 'publipostage',
                'app_module' => 'WORD',
                'title' => 'Publipostage',
                'description' => 'Fusion et publipostage avec source de données Excel.',
                'order' => 6,
                'duration_min' => 28,
                'difficulty' => 'ADVANCED',
                'objectives' => ['Associer une source de données', 'Insérer des champs de fusion'],
                'mos_objectives' => ['7.5'],
            ],
            [
                'slug' => 'suivi-modifications',
                'app_module' => 'WORD',
                'title' => 'Révision et protection',
                'description' => 'Suivi des modifications et protection du document.',
                'order' => 7,
                'duration_min' => 20,
                'difficulty' => 'ADVANCED',
                'objectives' => ['Activer révisions et commentaires', 'Accepter/rejeter et protéger le document'],
                'mos_objectives' => ['8.7'],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function excelLessons(): array
    {
        return [
            [
                'slug' => 'formules-de-base',
                'app_module' => 'EXCEL',
                'title' => 'Formules de base',
                'description' => 'SOMME, MOYENNE, MAX, MIN et références.',
                'order' => 1,
                'duration_min' => 22,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Saisir des formules avec références relatives/absolues', 'Utiliser Alt+= pour SOMME'],
                'mos_objectives' => ['3.6'],
            ],
            [
                'slug' => 'formules-conditionnelles',
                'app_module' => 'EXCEL',
                'title' => 'Formules conditionnelles',
                'description' => 'SI imbriqués et choix multiples.',
                'order' => 2,
                'duration_min' => 24,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Construire SI sur une ou plusieurs conditions', 'Éviter les erreurs de logique imbriquée'],
                'mos_objectives' => ['6.9'],
            ],
            [
                'slug' => 'recherchev-index-equiv',
                'app_module' => 'EXCEL',
                'title' => 'RECHERCHEV, INDEX, EQUIV',
                'description' => 'Jointures analogues avec les fonctions de recherche.',
                'order' => 3,
                'duration_min' => 28,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Utiliser RECHERCHEV avec colonne résultante', 'Combiner INDEX et EQUIV'],
                'mos_objectives' => ['6.17'],
            ],
            [
                'slug' => 'mfc',
                'app_module' => 'EXCEL',
                'title' => 'Mise en forme conditionnelle',
                'description' => 'Jeux de règles, barres de données et palettes.',
                'order' => 4,
                'duration_min' => 22,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Créer des MFC sur valeurs ou formules', 'Gérer l\'ordre des règles'],
                'mos_objectives' => ['4.2'],
            ],
            [
                'slug' => 'tableaux-structures',
                'app_module' => 'EXCEL',
                'title' => 'Tableaux Excel',
                'description' => 'Insertion de tableau structuré et colonnes calculées.',
                'order' => 5,
                'duration_min' => 20,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Convertir une plage en tableau Excel', 'Utiliser lignes/colonnes de totaux'],
                'mos_objectives' => ['2.13'],
            ],
            [
                'slug' => 'tri-et-filtres',
                'app_module' => 'EXCEL',
                'title' => 'Tri et filtres',
                'description' => 'Filtrages simples et avancés sur listes.',
                'order' => 6,
                'duration_min' => 18,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Tri multi-niveaux', 'Filtres automatiques et spéciaux'],
                'mos_objectives' => ['2.7'],
            ],
            [
                'slug' => 'tableaux-croises-dynamiques',
                'app_module' => 'EXCEL',
                'title' => 'Tableaux croisés dynamiques',
                'description' => 'Construire et mettre à jour un TCD.',
                'order' => 7,
                'duration_min' => 30,
                'difficulty' => 'ADVANCED',
                'objectives' => ['Zones filtre/colonnes/lignes/valeurs', 'Regroupements et filtre de rapport'],
                'mos_objectives' => ['9.27'],
            ],
            [
                'slug' => 'graphiques-excel',
                'app_module' => 'EXCEL',
                'title' => 'Graphiques',
                'description' => 'Insertion et mise en forme de graphiques recommandés.',
                'order' => 8,
                'duration_min' => 24,
                'difficulty' => 'ADVANCED',
                'objectives' => ['Créer un graphique adapté aux données', 'Modifier séries et éléments de graphique'],
                'mos_objectives' => ['9.43'],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function powerPointLessons(): array
    {
        return [
            [
                'slug' => 'gestion-diapositives',
                'app_module' => 'POWERPOINT',
                'title' => 'Structure et diapositives',
                'description' => 'Ajout, duplication, suppression et organisation des slides.',
                'order' => 1,
                'duration_min' => 18,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Maîtriser le volet des miniatures et la vue Trieuse', 'Sections et masques simplifiés'],
                'mos_objectives' => ['1.13'],
            ],
            [
                'slug' => 'themes-et-couleurs',
                'app_module' => 'POWERPOINT',
                'title' => 'Thèmes et couleurs',
                'description' => 'Thème global, variantes et couleurs de diapositives.',
                'order' => 2,
                'duration_min' => 16,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Appliquer un thème et une variante', 'Personnaliser la palette'],
                'mos_objectives' => ['2.1'],
            ],
            [
                'slug' => 'masque-diapositives',
                'app_module' => 'POWERPOINT',
                'title' => 'Masque des diapositives',
                'description' => 'Logo, pied de page et cohérence sur toutes les diapositives.',
                'order' => 3,
                'duration_min' => 22,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Modifier du texte/objets sur le masque', 'Réutiliser pied de page logo'],
                'mos_objectives' => ['8.43'],
            ],
            [
                'slug' => 'transitions',
                'app_module' => 'POWERPOINT',
                'title' => 'Transitions',
                'description' => 'Transitions entre diapositives et durée.',
                'order' => 4,
                'duration_min' => 14,
                'difficulty' => 'BEGINNER',
                'objectives' => ['Appliquer des transitions sobres MOS', 'Régler la durée et le son'],
                'mos_objectives' => ['11.61'],
            ],
            [
                'slug' => 'animations',
                'app_module' => 'POWERPOINT',
                'title' => 'Animations',
                'description' => 'Animation d’entrée, d’accentuation et de sortie.',
                'order' => 5,
                'duration_min' => 22,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Volet Animation et chronologie', 'Régler l’ordre et le démarrage'],
                'mos_objectives' => ['11.71'],
            ],
            [
                'slug' => 'images-ppt',
                'app_module' => 'POWERPOINT',
                'title' => 'Images et multimédia',
                'description' => 'Insertion, recadrage et compression des médias.',
                'order' => 6,
                'duration_min' => 20,
                'difficulty' => 'INTERMEDIATE',
                'objectives' => ['Insérer une image depuis un fichier ou en ligne', 'Compression et disposition'],
                'mos_objectives' => ['4.41'],
            ],
        ];
    }
}
