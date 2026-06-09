<?php
// ══════════════════════════════════════════════════════════════════
// backend/app/Console/Commands/SeedLessonsContent.php
// Commande : php artisan lessons:seed-content
// Met à jour le contenu des 21 leçons (Word + Excel + PowerPoint)
// ══════════════════════════════════════════════════════════════════

namespace App\Console\Commands;

use App\Models\Lesson;
use Illuminate\Console\Command;

class SeedLessonsContent extends Command
{
    protected $signature   = 'lessons:seed-content {--module= : word, excel ou powerpoint (défaut: tous)}';
    protected $description = 'Remplit le contenu (content_json) de toutes les leçons MOS';

    public function handle(): int
    {
        $module = strtolower($this->option('module') ?? 'all');

        $this->info('');
        $this->info('📚  Mise à jour du contenu des leçons MOS OFPPT');
        $this->info(str_repeat('─', 50));

        $updated = 0;

        if ($module === 'all' || $module === 'word') {
            $updated += $this->seedWord();
        }
        if ($module === 'all' || $module === 'excel') {
            $updated += $this->seedExcel();
        }
        if ($module === 'all' || $module === 'powerpoint') {
            $updated += $this->seedPowerPoint();
        }

        $this->info('');
        $this->info("✅  {$updated} leçons mises à jour avec succès !");
        $this->info('');

        return self::SUCCESS;
    }

    // ── WORD ──────────────────────────────────────────────────────

    private function seedWord(): int
    {
        $this->line('  <fg=blue>W</> Leçons Word...');
        $lessons = $this->wordLessons();

        foreach ($lessons as $slug => $content) {
            $rows = Lesson::where('slug', $slug)->update(['content_json' => $content]);
            $status = $rows > 0 ? '<fg=green>✓</>' : '<fg=red>✗ non trouvée</>';
            $this->line("     {$status} {$slug}");
        }

        return count($lessons);
    }

    // ── EXCEL ─────────────────────────────────────────────────────

    private function seedExcel(): int
    {
        $this->line('  <fg=green>X</> Leçons Excel...');
        $lessons = $this->excelLessons();

        foreach ($lessons as $slug => $content) {
            $rows = Lesson::where('slug', $slug)->update(['content_json' => $content]);
            $status = $rows > 0 ? '<fg=green>✓</>' : '<fg=red>✗ non trouvée</>';
            $this->line("     {$status} {$slug}");
        }

        return count($lessons);
    }

    // ── POWERPOINT ────────────────────────────────────────────────

    private function seedPowerPoint(): int
    {
        $this->line('  <fg=red>P</> Leçons PowerPoint...');
        $lessons = $this->powerpointLessons();

        foreach ($lessons as $slug => $content) {
            $rows = Lesson::where('slug', $slug)->update(['content_json' => $content]);
            $status = $rows > 0 ? '<fg=green>✓</>' : '<fg=red>✗ non trouvée</>';
            $this->line("     {$status} {$slug}");
        }

        return count($lessons);
    }

    // ══════════════════════════════════════════════════════════════
    // CONTENU WORD
    // ══════════════════════════════════════════════════════════════

    private function wordLessons(): array
    {
        return [

        'styles-et-titres' => ['blocks' => [
            ['type'=>'intro','title'=>'Pourquoi utiliser les styles ?','text'=>'Les styles sont la base de tout document Word professionnel. Ils permettent d\'appliquer une mise en forme cohérente en un clic, de générer une table des matières automatique et de naviguer facilement dans un long document.','tip'=>'Un document bien structuré avec des styles prend 2× moins de temps à mettre en forme.'],
            ['type'=>'definition','term'=>'Style de paragraphe','text'=>'Un style est un ensemble de propriétés de mise en forme sauvegardées sous un nom. Exemple : "Titre 1" = Calibri 16pt Gras Bleu. Modifier le style met à jour tous les paragraphes qui l\'utilisent.'],
            ['type'=>'steps','title'=>'Appliquer le style Titre 1','items'=>[
                ['num'=>1,'action'=>'Cliquez dans le paragraphe à mettre en forme','detail'=>'Il suffit de placer le curseur — pas besoin de tout sélectionner.'],
                ['num'=>2,'action'=>'Onglet Accueil → groupe Styles → cliquez sur "Titre 1"','detail'=>'La galerie des styles est dans l\'onglet Accueil. Raccourci : Ctrl+Alt+1.'],
                ['num'=>3,'action'=>'Répétez pour Titre 2 (Ctrl+Alt+2) et Titre 3 (Ctrl+Alt+3)','detail'=>'La hiérarchie Titre 1 > Titre 2 > Titre 3 structure votre document comme un plan.'],
            ]],
            ['type'=>'steps','title'=>'Créer une table des matières automatique','items'=>[
                ['num'=>1,'action'=>'Appliquez les styles Titre 1/2/3 à tous vos titres','detail'=>'La TdM se basera sur ces styles pour se générer.'],
                ['num'=>2,'action'=>'Placez le curseur en début de document','detail'=>'Généralement après la page de garde.'],
                ['num'=>3,'action'=>'Onglet Références → Table des matières → Table automatique 1','detail'=>'La table se génère instantanément avec les numéros de page.'],
                ['num'=>4,'action'=>'Mise à jour : clic droit → Mettre à jour les champs','detail'=>'Choisissez "Mettre à jour toute la table" après modification.'],
            ]],
            ['type'=>'warning','title'=>'⚠️ Erreur fréquente à l\'examen MOS','text'=>'Ne confondez pas "Titre 1" (style) et du texte mis en gras manuellement. L\'examen MOS exige l\'utilisation des styles — le formatage manuel ne sera pas reconnu.'],
            ['type'=>'shortcut_table','title'=>'Raccourcis clavier styles','rows'=>[
                ['keys'=>'Ctrl + Alt + 1','action'=>'Appliquer le style Titre 1'],
                ['keys'=>'Ctrl + Alt + 2','action'=>'Appliquer le style Titre 2'],
                ['keys'=>'Ctrl + Alt + 3','action'=>'Appliquer le style Titre 3'],
                ['keys'=>'Ctrl + Espace', 'action'=>'Effacer la mise en forme de caractère'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Appliquer les styles Titre 1/2/3 via la galerie Styles (pas le formatage manuel)',
                'Créer une table des matières via Références → Table des matières',
                'Mettre à jour la TdM après modification du document',
                'Modifier un style existant (clic droit sur le style → Modifier)',
            ]],
        ]],

        'mise-en-page' => ['blocks' => [
            ['type'=>'intro','title'=>'La mise en page dans Word','text'=>'La mise en page définit l\'apparence physique du document : marges, orientation, taille du papier. Ces paramètres affectent l\'impression et la présentation professionnelle.'],
            ['type'=>'steps','title'=>'Modifier les marges','items'=>[
                ['num'=>1,'action'=>'Onglet Mise en page → Marges','detail'=>'Une galerie de marges prédéfinies s\'affiche : Normal, Étroit, Large, En miroir...'],
                ['num'=>2,'action'=>'Choisissez un préréglage ou Marges personnalisées','detail'=>'Marges personnalisées : boîte de dialogue avec valeurs précises en cm.'],
            ]],
            ['type'=>'steps','title'=>'Changer l\'orientation','items'=>[
                ['num'=>1,'action'=>'Onglet Mise en page → Orientation','detail'=>'Portrait (vertical, défaut) ou Paysage (horizontal).'],
                ['num'=>2,'action'=>'Pour une seule page : utilisez les sections','detail'=>'Insérez un saut de section avant et après, changez l\'orientation de cette section.'],
            ]],
            ['type'=>'steps','title'=>'Insérer un saut de page','items'=>[
                ['num'=>1,'action'=>'Placez le curseur où vous voulez le saut','detail'=>'En fin de chapitre, avant un nouveau titre...'],
                ['num'=>2,'action'=>'Appuyez sur Ctrl+Entrée','detail'=>'C\'est la méthode correcte. Évitez les Entrées répétées !'],
            ]],
            ['type'=>'warning','title'=>'⚠️ Ne jamais faire ça','text'=>'Appuyer sur Entrée plusieurs fois pour aller à la page suivante est une mauvaise pratique. Si vous ajoutez du texte, tout se décale. Utilisez TOUJOURS Ctrl+Entrée.'],
            ['type'=>'shortcut_table','title'=>'Raccourcis mise en page','rows'=>[
                ['keys'=>'Ctrl + Entrée','action'=>'Insérer un saut de page'],
                ['keys'=>'Ctrl + Maj + Entrée','action'=>'Insérer un saut de colonne'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Modifier les marges via Mise en page → Marges',
                'Changer l\'orientation Portrait/Paysage',
                'Insérer un saut de page avec Ctrl+Entrée',
                'Utiliser les sections pour des mises en page différentes',
            ]],
        ]],

        'en-tetes-pieds-de-page' => ['blocks' => [
            ['type'=>'intro','title'=>'En-têtes et pieds de page','text'=>'L\'en-tête apparaît en haut de chaque page, le pied de page en bas. Ils contiennent numéros de page, nom du document, logo ou date. Une fois configurés, ils se répètent sur toutes les pages automatiquement.'],
            ['type'=>'steps','title'=>'Insérer un numéro de page','items'=>[
                ['num'=>1,'action'=>'Onglet Insertion → En-tête et pied de page → Numéro de page','detail'=>'Ne tapez JAMAIS le chiffre manuellement — il ne s\'incrémenterait pas.'],
                ['num'=>2,'action'=>'Choisissez la position et le style','detail'=>'Bas de page, centré est le plus courant pour les documents professionnels.'],
                ['num'=>3,'action'=>'Double-cliquez hors de l\'en-tête pour revenir au document','detail'=>'Ou : Fermer l\'en-tête et le pied de page dans l\'onglet contextuel.'],
            ]],
            ['type'=>'steps','title'=>'Première page sans numéro','items'=>[
                ['num'=>1,'action'=>'Double-cliquez sur l\'en-tête → onglet Création → cocher "Première page différente"','detail'=>'L\'en-tête de la page 1 devient indépendant.'],
                ['num'=>2,'action'=>'Pour commencer à 1 sur la 2ème page : Format des numéros → Commencer à 0','detail'=>'Ainsi la 2ème page (numérotée 1) sera la première visible.'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Insérer un numéro de page via Insertion → Numéro de page',
                'Activer "Première page différente" pour une page de garde',
                'Modifier le format des numéros (i, ii, iii ou 1, 2, 3)',
                'Ajouter texte, date ou logo dans l\'en-tête',
            ]],
        ]],

        'tableaux-word' => ['blocks' => [
            ['type'=>'intro','title'=>'Les tableaux dans Word','text'=>'Les tableaux permettent d\'organiser l\'information en lignes et colonnes pour les rapports, CV, comparatifs et formulaires. Word offre de nombreux outils pour créer et formater des tableaux professionnels.'],
            ['type'=>'steps','title'=>'Insérer un tableau','items'=>[
                ['num'=>1,'action'=>'Onglet Insertion → Tableau','detail'=>'Survolez la grille pour choisir le nombre de lignes et colonnes.'],
                ['num'=>2,'action'=>'Ou : "Insérer un tableau" pour des valeurs précises','detail'=>'Entrez exactement le nombre de lignes et colonnes voulu.'],
            ]],
            ['type'=>'steps','title'=>'Fusionner des cellules','items'=>[
                ['num'=>1,'action'=>'Sélectionnez les cellules à fusionner','detail'=>'Clic + glisser sur les cellules adjacentes.'],
                ['num'=>2,'action'=>'Clic droit → Fusionner les cellules','detail'=>'Ou onglet Disposition → Fusionner les cellules.'],
            ]],
            ['type'=>'steps','title'=>'Répéter l\'en-tête sur chaque page','items'=>[
                ['num'=>1,'action'=>'Sélectionnez la ligne d\'en-tête','detail'=>'Cliquez sur la première ligne du tableau.'],
                ['num'=>2,'action'=>'Onglet Disposition → Données → Répéter les lignes d\'en-tête','detail'=>'La ligne se répète automatiquement en haut de chaque page imprimée.'],
            ]],
            ['type'=>'tip','title'=>'💡 Tri du tableau','text'=>'Sélectionnez le tableau → Onglet Disposition → Données → Trier. Vous pouvez trier sur une ou plusieurs colonnes par ordre croissant ou décroissant.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Insérer un tableau avec le nombre exact de lignes et colonnes',
                'Fusionner des cellules pour créer des en-têtes',
                'Appliquer un style de tableau prédéfini (onglet Création du tableau)',
                'Répéter les lignes d\'en-tête sur chaque page',
                'Trier le contenu d\'un tableau',
            ]],
        ]],

        'images-word' => ['blocks' => [
            ['type'=>'intro','title'=>'Insérer des images dans Word','text'=>'Les images enrichissent vos documents. Word permet d\'insérer des photos, icônes, formes et SmartArt, puis de les positionner précisément par rapport au texte.'],
            ['type'=>'steps','title'=>'Insérer et positionner une image','items'=>[
                ['num'=>1,'action'=>'Onglet Insertion → Images → Ce périphérique','detail'=>'L\'explorateur de fichiers s\'ouvre. Formats supportés : JPG, PNG, GIF, SVG...'],
                ['num'=>2,'action'=>'L\'image s\'insère "En ligne avec le texte" par défaut','detail'=>'Elle se comporte comme un caractère de texte.'],
                ['num'=>3,'action'=>'Cliquez sur l\'icône d\'habillage → choisissez Carré','detail'=>'L\'image devient flottante et le texte l\'entoure.'],
            ]],
            ['type'=>'steps','title'=>'Rogner une image','items'=>[
                ['num'=>1,'action'=>'Sélectionnez l\'image → Onglet Format → Rogner','detail'=>'Des poignées noires apparaissent aux bords.'],
                ['num'=>2,'action'=>'Glissez les poignées pour définir la zone visible','detail'=>'La zone grisée sera masquée mais pas supprimée.'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Insérer une image via Insertion → Images → Ce périphérique',
                'Modifier l\'habillage du texte (Carré, En ligne, etc.)',
                'Rogner une image avec l\'outil Rogner',
                'Redimensionner proportionnellement (Maj + glisser un coin)',
                'Ajouter un texte alternatif pour l\'accessibilité',
            ]],
        ]],

        'publipostage' => ['blocks' => [
            ['type'=>'intro','title'=>'Le publipostage — courriers personnalisés en masse','text'=>'Le publipostage combine un document modèle avec une liste de destinataires pour créer automatiquement des dizaines de lettres, étiquettes ou emails personnalisés. Indispensable pour les convocations, attestations et courriers en masse.'],
            ['type'=>'steps','title'=>'Les 5 étapes du publipostage','items'=>[
                ['num'=>1,'action'=>'Créer le document principal (la lettre modèle)','detail'=>'Rédigez votre lettre avec des espaces pour les données variables.'],
                ['num'=>2,'action'=>'Onglet Publipostage → Sélection des destinataires → Utiliser une liste existante','detail'=>'Choisissez le fichier Excel source. La 1ère ligne doit contenir les en-têtes de colonnes.'],
                ['num'=>3,'action'=>'Insérer les champs : Onglet Publipostage → Insérer un champ de fusion','detail'=>'Les champs apparaissent entre «guillemets» : «Prénom», «Ville»...'],
                ['num'=>4,'action'=>'Aperçu : Onglet Publipostage → Aperçu des résultats','detail'=>'Naviguez entre les enregistrements pour vérifier le résultat.'],
                ['num'=>5,'action'=>'Onglet Publipostage → Terminer et fusionner → Modifier les documents','detail'=>'Crée un nouveau document avec une lettre par destinataire.'],
            ]],
            ['type'=>'tip','title'=>'💡 Filtrer les destinataires','text'=>'Onglet Publipostage → Modifier la liste des destinataires → Filtre. Exemple : Ville = "Casablanca" pour n\'envoyer qu\'aux destinataires de Casablanca.'],
            ['type'=>'warning','title'=>'⚠️ Format des champs','text'=>'Les champs de fusion «Prénom» ne se tapent PAS manuellement. Utilisez toujours Onglet Publipostage → Insérer un champ de fusion pour les insérer correctement.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Connecter un fichier Excel comme source de données',
                'Insérer des champs de fusion via l\'onglet Publipostage',
                'Prévisualiser les résultats avant fusion',
                'Filtrer les destinataires selon un critère',
                'Terminer et fusionner vers un nouveau document',
            ]],
        ]],

        'suivi-modifications' => ['blocks' => [
            ['type'=>'intro','title'=>'Le suivi des modifications','text'=>'Le suivi des modifications permet à plusieurs personnes de collaborer sur un document en gardant une trace de chaque changement avec le nom de l\'auteur et la date. Indispensable pour la relecture professionnelle.'],
            ['type'=>'steps','title'=>'Activer le suivi','items'=>[
                ['num'=>1,'action'=>'Onglet Révision → Suivi des modifications','detail'=>'Raccourci : Ctrl+Maj+E. Le bouton s\'allume quand le suivi est actif.'],
                ['num'=>2,'action'=>'Toutes les modifications suivantes sont tracées','detail'=>'Ajout = souligné. Suppression = barré. La couleur varie selon l\'auteur.'],
            ]],
            ['type'=>'steps','title'=>'Accepter ou refuser','items'=>[
                ['num'=>1,'action'=>'Accepter UNE modification : clic droit → Accepter','detail'=>'Ou Onglet Révision → Accepter → Cette modification.'],
                ['num'=>2,'action'=>'Accepter TOUTES : Onglet Révision → Accepter → Accepter toutes','detail'=>'Toutes les modifications sont intégrées définitivement.'],
                ['num'=>3,'action'=>'Refuser : clic droit → Refuser (annule la modification)','detail'=>'Le texte revient à son état avant la modification.'],
            ]],
            ['type'=>'steps','title'=>'Insérer un commentaire','items'=>[
                ['num'=>1,'action'=>'Sélectionnez le texte → Onglet Révision → Nouveau commentaire','detail'=>'Raccourci : Ctrl+Alt+M. Un bulle apparaît dans la marge.'],
                ['num'=>2,'action'=>'Tapez votre commentaire dans la bulle','detail'=>'Le commentaire indique votre nom et la date automatiquement.'],
            ]],
            ['type'=>'shortcut_table','title'=>'Raccourcis révision','rows'=>[
                ['keys'=>'Ctrl + Maj + E','action'=>'Activer/désactiver le suivi des modifications'],
                ['keys'=>'Ctrl + Alt + M','action'=>'Insérer un nouveau commentaire'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Word','items'=>[
                'Activer/désactiver le suivi avec Ctrl+Maj+E',
                'Accepter ou refuser individuellement ou toutes les modifications',
                'Insérer, répondre et supprimer des commentaires',
                'Afficher/masquer les modifications (Onglet Révision → Afficher les marques)',
            ]],
        ]],

        ]; // fin wordLessons
    }

    // ══════════════════════════════════════════════════════════════
    // CONTENU EXCEL
    // ══════════════════════════════════════════════════════════════

    private function excelLessons(): array
    {
        return [

        'formules-de-base' => ['blocks' => [
            ['type'=>'intro','title'=>'Les formules, le cœur d\'Excel','text'=>'Excel est un moteur de calcul. Les formules transforment vos données brutes en informations utiles. Toute formule commence par = et peut référencer des cellules ou des plages entières.','tip'=>'Une formule se met à jour automatiquement quand les données changent.'],
            ['type'=>'definition','term'=>'Syntaxe d\'une formule','text'=>'=FONCTION(argument1 ; argument2 ; ...) — Le = déclenche le calcul. Les arguments sont séparés par des ; en français. Exemple : =SOMME(A1:A10) additionne A1 jusqu\'à A10.'],
            ['type'=>'steps','title'=>'Votre première formule SOMME','items'=>[
                ['num'=>1,'action'=>'Cliquez sur la cellule résultat','detail'=>'Par exemple B11, sous une colonne de chiffres en B1:B10.'],
                ['num'=>2,'action'=>'Tapez =SOMME( puis sélectionnez la plage B1:B10','detail'=>'Cliquez sur B1 et glissez jusqu\'à B10. La plage se colore.'],
                ['num'=>3,'action'=>'Tapez ) puis Entrée','detail'=>'Raccourci ultra-rapide : Alt+= insère automatiquement la SOMME sur la plage détectée !'],
            ]],
            ['type'=>'shortcut_table','title'=>'Fonctions statistiques essentielles','rows'=>[
                ['keys'=>'=SOMME(A1:A10)',   'action'=>'Additionne toutes les valeurs de la plage'],
                ['keys'=>'=MOYENNE(A1:A10)', 'action'=>'Calcule la moyenne arithmétique'],
                ['keys'=>'=MAX(A1:A10)',      'action'=>'Retourne la valeur maximale'],
                ['keys'=>'=MIN(A1:A10)',      'action'=>'Retourne la valeur minimale'],
                ['keys'=>'=NB(A1:A10)',       'action'=>'Compte les cellules avec des nombres'],
                ['keys'=>'=NBVAL(A1:A10)',    'action'=>'Compte les cellules non vides'],
            ]],
            ['type'=>'steps','title'=>'Références absolues vs relatives','items'=>[
                ['num'=>1,'action'=>'RELATIVE : A1 — s\'adapte quand vous copiez la formule','detail'=>'Copier vers le bas : A1 → A2 → A3... Parfait pour calculer plusieurs lignes.'],
                ['num'=>2,'action'=>'ABSOLUE : $A$1 — reste fixe à la copie','detail'=>'Utile pour un taux de TVA ou un coefficient fixe appliqué partout.'],
                ['num'=>3,'action'=>'Raccourci F4 : bascule entre les types de références','detail'=>'A1 → $A$1 → A$1 → $A1 → A1. Appuyez après avoir tapé la référence.'],
            ]],
            ['type'=>'shortcut_table','title'=>'Raccourcis clavier Excel','rows'=>[
                ['keys'=>'Alt + =',      'action'=>'Insérer =SOMME automatiquement'],
                ['keys'=>'F4',           'action'=>'Basculer les types de références ($A$1...)'],
                ['keys'=>'Ctrl + Entrée','action'=>'Valider sans quitter la cellule'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Utiliser =SOMME(), =MOYENNE(), =MAX(), =MIN() sur des plages',
                'Distinguer référence relative (A1) et absolue ($A$1)',
                'Utiliser F4 pour basculer entre les types de références',
                'Utiliser Alt+= pour insérer une SOMME automatiquement',
            ]],
        ]],

        'formules-conditionnelles' => ['blocks' => [
            ['type'=>'intro','title'=>'Les formules conditionnelles','text'=>'Les formules conditionnelles automatisent des décisions : "Si la note est ≥ 10, afficher Reçu, sinon Refusé". Omniprésentes dans les tableaux de bord professionnels.'],
            ['type'=>'definition','term'=>'Syntaxe de la fonction SI','text'=>'=SI(test_logique ; valeur_si_vrai ; valeur_si_faux)\n\nExemple : =SI(B2>=10 ; "Reçu" ; "Refusé")\n\nOpérateurs : = > < >= <= <> (différent de)'],
            ['type'=>'steps','title'=>'Construire une formule SI','items'=>[
                ['num'=>1,'action'=>'Identifiez la condition : B2>=10','detail'=>'>= signifie "supérieur ou égal à".'],
                ['num'=>2,'action'=>'Valeur si VRAI : "Reçu" (entre guillemets pour le texte)','detail'=>'Les nombres ne prennent pas de guillemets.'],
                ['num'=>3,'action'=>'Valeur si FAUX : "Refusé"','detail'=>'Si omis, Excel affiche FAUX quand la condition n\'est pas remplie.'],
                ['num'=>4,'action'=>'Résultat : =SI(B2>=10;"Reçu";"Refusé")','detail'=>'Copiez vers le bas pour appliquer à toute la colonne.'],
            ]],
            ['type'=>'steps','title'=>'NB.SI et SOMME.SI','items'=>[
                ['num'=>1,'action'=>'=NB.SI(plage ; critère) — compte selon un critère','detail'=>'Ex: =NB.SI(A1:A100;"Paris") compte les cellules contenant "Paris".'],
                ['num'=>2,'action'=>'Critère numérique entre guillemets : ">10", "<=5"','detail'=>'Ex: =NB.SI(B1:B100;">10") compte les valeurs supérieures à 10.'],
                ['num'=>3,'action'=>'=SOMME.SI(plage_crit ; critère ; plage_somme)','detail'=>'Ex: =SOMME.SI(A1:A100;"Maroc";C1:C100) additionne C où A="Maroc".'],
                ['num'=>4,'action'=>'=SOMME.SI.ENS(p_som ; p1 ; c1 ; p2 ; c2...) — plusieurs critères','detail'=>'La plage à sommer est EN PREMIER — différent de SOMME.SI !'],
            ]],
            ['type'=>'tip','title'=>'💡 ET() et OU() dans les conditions','text'=>'=SI(ET(A1>0;B1>0);"Les deux positifs";"...") — ET() exige que TOUTES les conditions soient vraies. OU() exige qu\'AU MOINS UNE soit vraie.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Construire =SI() avec test logique, valeur vrai et valeur faux',
                'Utiliser =NB.SI() pour compter selon un critère',
                'Utiliser =SOMME.SI() pour additionner selon un critère',
                'Utiliser =ET() et =OU() pour combiner plusieurs conditions',
                'Connaître SOMME.SI.ENS (plage somme en premier !)',
            ]],
        ]],

        'recherchev-index-equiv' => ['blocks' => [
            ['type'=>'intro','title'=>'Chercher des données dans un tableau','text'=>'RECHERCHEV est l\'une des fonctions les plus utilisées en entreprise. Elle permet de "croiser" deux tableaux : cherchez un code et récupérez automatiquement son prix, son nom ou sa catégorie.'],
            ['type'=>'definition','term'=>'Syntaxe RECHERCHEV','text'=>'=RECHERCHEV(valeur_cherchée ; tableau ; n°_colonne ; FAUX)\n\n• valeur_cherchée : ce que vous cherchez\n• tableau : la plage (la colonne de recherche DOIT être la 1ère)\n• n°_colonne : numéro de la colonne à retourner\n• FAUX : correspondance EXACTE (toujours FAUX pour l\'examen MOS)'],
            ['type'=>'steps','title'=>'Exemple : trouver le prix d\'un produit','items'=>[
                ['num'=>1,'action'=>'Tableau A1:C100 (A=Code, B=Produit, C=Prix)','detail'=>'La colonne de recherche (Code) DOIT être la 1ère colonne.'],
                ['num'=>2,'action'=>'E2 contient le code produit recherché','detail'=>'Exemple : E2 = "P001"'],
                ['num'=>3,'action'=>'Formule : =RECHERCHEV(E2 ; $A$1:$C$100 ; 3 ; FAUX)','detail'=>'3 = retourner la 3ème colonne (Prix). $ pour fixer le tableau lors de la copie.'],
                ['num'=>4,'action'=>'Protéger l\'erreur #N/A avec SIERREUR','detail'=>'=SIERREUR(RECHERCHEV(...);"Non trouvé") remplace l\'erreur par un texte lisible.'],
            ]],
            ['type'=>'warning','title'=>'⚠️ Limitation de RECHERCHEV','text'=>'RECHERCHEV cherche UNIQUEMENT dans la 1ère colonne et ne peut retourner que vers la droite. Pour chercher dans n\'importe quelle colonne, utilisez INDEX+EQUIV.'],
            ['type'=>'steps','title'=>'INDEX + EQUIV — plus puissant','items'=>[
                ['num'=>1,'action'=>'=EQUIV("Paris";A1:A100;0) → retourne la position de "Paris"','detail'=>'0 = correspondance exacte. Retourne le numéro de ligne.'],
                ['num'=>2,'action'=>'=INDEX(C1:C100;5) → retourne la valeur en C5','detail'=>'Retourne la valeur à la position spécifiée.'],
                ['num'=>3,'action'=>'Combinés : =INDEX(C1:C100;EQUIV(E2;A1:A100;0))','detail'=>'Peut chercher dans n\'importe quelle direction — gauche ou droite !'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Construire =RECHERCHEV(valeur;tableau;n°col;FAUX)',
                'Toujours utiliser FAUX pour la correspondance exacte',
                'Fixer le tableau avec $ pour copier la formule',
                'Gérer les erreurs #N/A avec =SIERREUR()',
            ]],
        ]],

        'mfc' => ['blocks' => [
            ['type'=>'intro','title'=>'Visualiser les données automatiquement','text'=>'La Mise en Forme Conditionnelle (MFC) applique couleurs, icônes ou barres selon la valeur d\'une cellule. En un coup d\'œil, repérez anomalies, performances et tendances sans créer de graphique.','tip'=>'La MFC se recalcule en temps réel. Modifiez une valeur, la couleur change immédiatement.'],
            ['type'=>'steps','title'=>'Mettre en rouge les valeurs négatives','items'=>[
                ['num'=>1,'action'=>'Sélectionnez la plage de cellules (ex: B2:B100)','detail'=>'La MFC s\'appliquera à toutes les cellules sélectionnées.'],
                ['num'=>2,'action'=>'Accueil → Mise en forme conditionnelle → Règles de mise en surbrillance','detail'=>'Un sous-menu affiche les types de règles prédéfinies.'],
                ['num'=>3,'action'=>'Cliquez sur "Inférieur à..." → entrez 0 → choisissez rouge','detail'=>'Toutes les valeurs négatives passent en rouge instantanément.'],
            ]],
            ['type'=>'steps','title'=>'Barres de données et Nuances de couleurs','items'=>[
                ['num'=>1,'action'=>'Sélectionnez la plage → Accueil → Mise en forme conditionnelle','detail'=>''],
                ['num'=>2,'action'=>'Barres de données : chaque cellule affiche une barre proportionnelle','detail'=>'Visuellement comparable à un mini-graphique intégré dans les cellules.'],
                ['num'=>3,'action'=>'Nuances de couleurs : dégradé vert→rouge selon les valeurs','detail'=>'Vert = valeurs hautes, rouge = valeurs basses (ou l\'inverse).'],
            ]],
            ['type'=>'steps','title'=>'Règle avec formule personnalisée','items'=>[
                ['num'=>1,'action'=>'Accueil → MFC → Nouvelle règle → "Utiliser une formule..."','detail'=>'Pour des conditions complexes.'],
                ['num'=>2,'action'=>'Exemple : =$C2="En retard" → fond orange pour toute la ligne','detail'=>'Le $ sur C est crucial : fixe la colonne mais laisse la ligne variable.'],
                ['num'=>3,'action'=>'Format → choisissez la mise en forme → OK','detail'=>''],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Règles de surbrillance (supérieur à, inférieur à, entre)',
                'Barres de données pour visualiser les proportions',
                'Nuances de couleurs (dégradés)',
                'Règle basée sur une formule personnalisée',
                'Gérer et modifier les règles (priorité, modification, suppression)',
            ]],
        ]],

        'tableaux-structures' => ['blocks' => [
            ['type'=>'intro','title'=>'Les tableaux structurés — la bonne pratique','text'=>'Convertir une plage en "Tableau" Excel est une des meilleures pratiques. Les tableaux gèrent automatiquement le tri, le filtrage et s\'étendent seuls quand vous ajoutez des données.'],
            ['type'=>'steps','title'=>'Convertir en tableau structuré','items'=>[
                ['num'=>1,'action'=>'Cliquez dans la plage de données','detail'=>'Excel détecte automatiquement les bordures.'],
                ['num'=>2,'action'=>'Ctrl+T → vérifier que "Mon tableau comporte des en-têtes" est coché','detail'=>'Ou Onglet Insertion → Tableau.'],
                ['num'=>3,'action'=>'Cliquez OK','detail'=>'Filtres automatiques et style coloré appliqués. Nom attribué : Tableau1...'],
            ]],
            ['type'=>'steps','title'=>'Ajouter une ligne de totaux','items'=>[
                ['num'=>1,'action'=>'Cliquez dans le tableau → Onglet Création → Ligne des totaux','detail'=>'Une ligne avec des menus déroulants s\'ajoute en bas.'],
                ['num'=>2,'action'=>'Cliquez sur la cellule de total pour choisir le calcul','detail'=>'Somme, Moyenne, Nombre, Max, Min, Aucun... Chaque colonne indépendamment.'],
            ]],
            ['type'=>'tip','title'=>'💡 Nommer votre tableau','text'=>'Onglet Création → champ Nom du tableau. Donnez un nom descriptif : "Ventes2024", "ListeEmployes". Ce nom s\'utilise dans les formules : =SOMME(Ventes2024[Montant]).'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Convertir en tableau avec Ctrl+T ou Insertion → Tableau',
                'Activer la ligne des totaux et choisir le calcul par colonne',
                'Appliquer un style de tableau prédéfini',
                'Renommer un tableau',
                'Ajouter une colonne calculée (formule propagée automatiquement)',
            ]],
        ]],

        'tri-et-filtres' => ['blocks' => [
            ['type'=>'intro','title'=>'Trier et filtrer vos données','text'=>'Le tri réorganise les lignes. Le filtre masque temporairement les lignes non voulues sans les supprimer. Ce sont les outils d\'analyse les plus utilisés au quotidien dans Excel.','tip'=>'Le filtre ne supprime pas les données — il les masque. Désactivez-le pour les retrouver.'],
            ['type'=>'steps','title'=>'Activer les filtres automatiques','items'=>[
                ['num'=>1,'action'=>'Cliquez dans vos données → Ctrl+Maj+L','detail'=>'Ou Onglet Données → Filtrer. Des flèches apparaissent sur les en-têtes.'],
                ['num'=>2,'action'=>'Cliquez sur une flèche → décochez les valeurs à masquer','detail'=>'Ou utilisez les "Filtres numériques" / "Filtres textuels" pour des conditions.'],
                ['num'=>3,'action'=>'Pour désactiver : Données → Effacer ou re-appuyer sur Ctrl+Maj+L','detail'=>'Toutes les lignes masquées réapparaissent.'],
            ]],
            ['type'=>'steps','title'=>'Tri sur plusieurs colonnes','items'=>[
                ['num'=>1,'action'=>'Onglet Données → Trier','detail'=>'La boîte de dialogue Trier s\'ouvre.'],
                ['num'=>2,'action'=>'Configurez le 1er niveau : colonne + ordre','detail'=>'Ex: Trier par "Ville" en Croissant (A→Z).'],
                ['num'=>3,'action'=>'"Ajouter un niveau" pour un 2ème critère','detail'=>'Ex: Ensuite par "Nom" en Croissant. Dans chaque ville, les noms sont triés.'],
            ]],
            ['type'=>'shortcut_table','title'=>'Raccourcis tri et filtres','rows'=>[
                ['keys'=>'Ctrl + Maj + L','action'=>'Activer/désactiver les filtres automatiques'],
                ['keys'=>'Alt + ↓',        'action'=>'Ouvrir la liste déroulante de filtre'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Activer/désactiver les filtres avec Ctrl+Maj+L',
                'Filtrer par valeur spécifique dans la liste',
                'Filtres numériques : supérieur à, entre, 10 premiers',
                'Tri sur plusieurs colonnes avec des niveaux de priorité',
                'Trier par couleur de cellule ou police',
            ]],
        ]],

        'tableaux-croises-dynamiques' => ['blocks' => [
            ['type'=>'intro','title'=>'Les TCD — l\'outil d\'analyse le plus puissant','text'=>'Un Tableau Croisé Dynamique résume des milliers de lignes en quelques clics, sans formule. Il répond à des questions comme : "CA par région et par produit ?" en quelques secondes.','tip'=>'Le TCD ne modifie pas vos données source — c\'est un résumé interactif.'],
            ['type'=>'steps','title'=>'Créer un TCD','items'=>[
                ['num'=>1,'action'=>'Cliquez dans vos données → Onglet Insertion → Tableau croisé dynamique','detail'=>'Excel détecte la plage. Choisissez "Nouvelle feuille de calcul".'],
                ['num'=>2,'action'=>'Cliquez OK → nouvelle feuille avec TCD vide','detail'=>'Le volet des champs apparaît à droite.'],
                ['num'=>3,'action'=>'Glissez les champs dans les zones : Lignes, Colonnes, Valeurs, Filtres','detail'=>'Ex: "Région" en Lignes, "Produit" en Colonnes, "Ventes" en Valeurs.'],
            ]],
            ['type'=>'steps','title'=>'Modifier le calcul des Valeurs','items'=>[
                ['num'=>1,'action'=>'Par défaut : Excel calcule la SOMME des valeurs numériques','detail'=>'Pour les textes, il fait un NB automatiquement.'],
                ['num'=>2,'action'=>'Clic droit sur une valeur → Paramètres des champs de valeurs','detail'=>'Choisissez : Somme, Moyenne, Nb, Max, % du total général...'],
            ]],
            ['type'=>'steps','title'=>'Grouper les dates par mois','items'=>[
                ['num'=>1,'action'=>'Glissez un champ date en Lignes ou Colonnes','detail'=>'Toutes les dates individuelles s\'affichent — souvent trop détaillé.'],
                ['num'=>2,'action'=>'Clic droit sur une date dans le TCD → Grouper','detail'=>'Choisissez Mois, Trimestres, Années. Ctrl+clic pour plusieurs.'],
            ]],
            ['type'=>'tip','title'=>'💡 Segments (Slicers)','text'=>'Onglet Analyse → Insérer un segment. Des boutons cliquables filtrent le TCD interactivement. Idéal pour les tableaux de bord.'],
            ['type'=>'warning','title'=>'⚠️ Actualiser après modification des données','text'=>'Le TCD ne se met PAS à jour automatiquement. Clic droit → Actualiser, ou Onglet Analyse → Actualiser.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Créer un TCD depuis Insertion → Tableau croisé dynamique',
                'Glisser les champs dans les bonnes zones',
                'Modifier le type de calcul des Valeurs',
                'Grouper les dates par mois/trimestres/années',
                'Insérer un segment pour des filtres visuels',
                'Actualiser après modification des données source',
            ]],
        ]],

        'graphiques-excel' => ['blocks' => [
            ['type'=>'intro','title'=>'Transformer les données en graphiques percutants','text'=>'Un bon graphique communique en une seconde ce que des colonnes de chiffres ne transmettent pas. Excel propose plus de 20 types. Le secret : choisir le bon type selon le message à transmettre.'],
            ['type'=>'definition','term'=>'Quel graphique pour quel usage ?','text'=>'Barres/Histogrammes : comparer des catégories. Courbes : évolution dans le temps. Secteurs (camembert) : proportions (max 5-6 parts). Nuages de points : corrélation. Combiné : barres + courbe superposées.'],
            ['type'=>'steps','title'=>'Créer un graphique rapidement','items'=>[
                ['num'=>1,'action'=>'Sélectionnez les données y compris les en-têtes','detail'=>'Ctrl+clic pour des plages non contiguës.'],
                ['num'=>2,'action'=>'Alt+F1 pour un graphique intégré instantané','detail'=>'Ou F11 pour une nouvelle feuille graphique. Ou Insertion → type de graphique.'],
                ['num'=>3,'action'=>'Graphiques recommandés : Insertion → Graphiques recommandés','detail'=>'Excel suggère le meilleur type selon vos données.'],
            ]],
            ['type'=>'steps','title'=>'Personnaliser le graphique','items'=>[
                ['num'=>1,'action'=>'Titre : cliquez sur "Titre du graphique" et tapez','detail'=>'Ou Onglet Création → Ajouter un élément → Titre du graphique.'],
                ['num'=>2,'action'=>'Étiquettes de données : clic droit sur la série → Ajouter des étiquettes','detail'=>'Les valeurs s\'affichent directement sur les barres.'],
                ['num'=>3,'action'=>'Changer le type : clic droit → Modifier le type de graphique','detail'=>'Passez de barres à courbes sans recréer le graphique.'],
            ]],
            ['type'=>'steps','title'=>'Sparklines — mini graphiques dans les cellules','items'=>[
                ['num'=>1,'action'=>'Sélectionnez les cellules de destination','detail'=>'Une cellule par sparkline, en fin de ligne généralement.'],
                ['num'=>2,'action'=>'Onglet Insertion → Sparklines → Courbe ou Histogramme','detail'=>'Ces mini-graphiques tiennent dans une seule cellule.'],
                ['num'=>3,'action'=>'Sélectionnez la plage de données source','detail'=>'Chaque ligne représente l\'évolution d\'une entrée.'],
            ]],
            ['type'=>'shortcut_table','title'=>'Raccourcis graphiques','rows'=>[
                ['keys'=>'Alt + F1','action'=>'Graphique intégré dans la feuille actuelle'],
                ['keys'=>'F11',     'action'=>'Graphique dans une nouvelle feuille dédiée'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS Excel','items'=>[
                'Créer un graphique depuis une plage sélectionnée',
                'Choisir le bon type (barres, courbes, secteurs...)',
                'Ajouter titre, étiquettes et légende',
                'Changer le type sans recréer le graphique',
                'Déplacer vers une feuille dédiée',
                'Insérer des Sparklines (courbe, histogramme)',
            ]],
        ]],

        ]; // fin excelLessons
    }

    // ══════════════════════════════════════════════════════════════
    // CONTENU POWERPOINT
    // ══════════════════════════════════════════════════════════════

    private function powerpointLessons(): array
    {
        return [

        'gestion-diapositives' => ['blocks' => [
            ['type'=>'intro','title'=>'Maîtriser la gestion des diapositives','text'=>'Une présentation professionnelle commence par une bonne gestion : créer, organiser, dupliquer, masquer et structurer en sections. PowerPoint offre plusieurs modes d\'affichage pour faciliter ce travail.','tip'=>'Le mode Trieuse (Affichage → Trieuse) est idéal pour réorganiser rapidement une grande présentation.'],
            ['type'=>'steps','title'=>'Insérer et dupliquer','items'=>[
                ['num'=>1,'action'=>'Nouvelle diapositive : Ctrl+M','detail'=>'La flèche sous "Nouvelle diapositive" permet de choisir la disposition.'],
                ['num'=>2,'action'=>'Dupliquer : sélectionnez → Ctrl+D','detail'=>'Ou clic droit → Dupliquer. La copie s\'insère juste après avec le même contenu.'],
                ['num'=>3,'action'=>'Supprimer : sélectionnez → touche Suppr','detail'=>'Ctrl+clic pour plusieurs, Maj+clic pour une plage continue.'],
                ['num'=>4,'action'=>'Réorganiser : glisser-déposer dans le volet des miniatures','detail'=>'En mode Trieuse, c\'est encore plus facile.'],
            ]],
            ['type'=>'steps','title'=>'Masquer une diapositive','items'=>[
                ['num'=>1,'action'=>'Clic droit sur la miniature → Masquer la diapositive','detail'=>'La diapositive reste dans la présentation mais est sautée pendant le diaporama.'],
                ['num'=>2,'action'=>'Le numéro de la slide masquée est barré dans le volet','detail'=>'Re-cliquez sur Masquer pour la démasquer (toggle).'],
            ]],
            ['type'=>'steps','title'=>'Organiser en sections','items'=>[
                ['num'=>1,'action'=>'Clic droit entre deux diapositives → Ajouter une section','detail'=>'Les sections regroupent les diapositives par thème.'],
                ['num'=>2,'action'=>'Double-cliquez sur le nom pour renommer','detail'=>'"Introduction", "Problématique", "Solution", "Conclusion"...'],
                ['num'=>3,'action'=>'Triangle à gauche du nom pour réduire/développer','detail'=>'Utile pour naviguer dans les longues présentations.'],
            ]],
            ['type'=>'shortcut_table','title'=>'Raccourcis diapositives','rows'=>[
                ['keys'=>'Ctrl + M',  'action'=>'Nouvelle diapositive'],
                ['keys'=>'Ctrl + D',  'action'=>'Dupliquer la diapositive'],
                ['keys'=>'Suppr',     'action'=>'Supprimer la diapositive'],
                ['keys'=>'F5',        'action'=>'Diaporama depuis la 1ère diapositive'],
                ['keys'=>'Maj + F5',  'action'=>'Diaporama depuis la diapositive courante'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Insérer une diapositive avec la disposition appropriée',
                'Dupliquer avec Ctrl+D',
                'Réorganiser par glisser-déposer',
                'Masquer/démasquer une diapositive',
                'Créer et nommer des sections',
            ]],
        ]],

        'themes-et-couleurs' => ['blocks' => [
            ['type'=>'intro','title'=>'Un style professionnel en 3 clics','text'=>'Un thème PowerPoint est un ensemble cohérent de couleurs, polices et effets. L\'appliquer transforme instantanément toute la présentation. Les variantes personnalisent le thème sans changer sa structure.','tip'=>'En entreprise, utilisez les couleurs de la charte graphique pour une cohérence visuelle.'],
            ['type'=>'steps','title'=>'Appliquer un thème','items'=>[
                ['num'=>1,'action'=>'Onglet Création → galerie des thèmes','detail'=>'Survolez pour voir l\'aperçu en direct sur toutes les diapositives.'],
                ['num'=>2,'action'=>'Cliquez pour appliquer à toute la présentation','detail'=>'Clic droit → "Appliquer aux diapositives sélectionnées" pour une seule slide.'],
            ]],
            ['type'=>'steps','title'=>'Personnaliser les couleurs','items'=>[
                ['num'=>1,'action'=>'Onglet Création → Variantes → flèche → Couleurs','detail'=>'Des dizaines de palettes prédéfinies. Survolez pour prévisualiser.'],
                ['num'=>2,'action'=>'Personnaliser les couleurs... pour une palette sur mesure','detail'=>'Définissez les 12 couleurs du thème (accents, texte, fond).'],
            ]],
            ['type'=>'steps','title'=>'Modifier les polices','items'=>[
                ['num'=>1,'action'=>'Onglet Création → Variantes → Polices','detail'=>'Combinaisons de polices titre + corps.'],
                ['num'=>2,'action'=>'Personnaliser les polices... pour entrer des noms précis','detail'=>'Les polices s\'appliquent à tous les espaces réservés.'],
            ]],
            ['type'=>'definition','term'=>'Couleurs du thème vs couleurs standard','text'=>'Les couleurs du thème s\'adaptent si vous changez de thème. Les couleurs standard restent fixes. Utilisez TOUJOURS les couleurs du thème pour une présentation professionnelle qui suit les changements.'],
            ['type'=>'tip','title'=>'💡 Modifier le fond','text'=>'Onglet Création → Format de l\'arrière-plan. Couleur unie, dégradé, texture ou image. "Appliquer à toutes" pour toute la présentation.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Appliquer un thème à toute la présentation',
                'Appliquer un thème à une seule diapositive',
                'Modifier la palette de couleurs du thème',
                'Modifier les polices du thème',
                'Modifier l\'arrière-plan des diapositives',
            ]],
        ]],

        'masque-diapositives' => ['blocks' => [
            ['type'=>'intro','title'=>'Le Masque — contrôlez tout en une fois','text'=>'Le Masque est le "modèle maître". Tout ce que vous y placez (logo, couleur, police, zones de texte) apparaît automatiquement sur toutes les diapositives. Modifiez une fois — tout se met à jour.','tip'=>'Pour ajouter un logo sur toutes les slides : mettez-le dans le masque une seule fois.'],
            ['type'=>'steps','title'=>'Accéder au mode Masque','items'=>[
                ['num'=>1,'action'=>'Onglet Affichage → Modes Masque → Masque des diapositives','detail'=>'L\'interface change. Le volet gauche montre la hiérarchie des masques.'],
                ['num'=>2,'action'=>'Grande miniature = masque principal (toutes les slides)','detail'=>'Miniatures en retrait = dispositions individuelles.'],
                ['num'=>3,'action'=>'Modifiez le masque PRINCIPAL pour tout affecter','detail'=>'Modifiez une DISPOSITION pour un type de slide spécifique.'],
            ]],
            ['type'=>'steps','title'=>'Ajouter un logo sur toutes les diapositives','items'=>[
                ['num'=>1,'action'=>'Dans le masque, cliquez sur le masque PRINCIPAL','detail'=>'Tout ce qu\'on y ajoute apparaît partout.'],
                ['num'=>2,'action'=>'Onglet Insertion → Images → insérez votre logo','detail'=>'Placez-le dans le coin souhaité (bas droite courant).'],
                ['num'=>3,'action'=>'Onglet Masque des diapositives → Fermer le mode Masque','detail'=>'Le logo apparaît sur toutes les diapositives.'],
            ]],
            ['type'=>'warning','title'=>'⚠️ Objets du masque non sélectionnables en mode Normal','text'=>'En mode Normal, vous ne pouvez pas cliquer sur les éléments du masque. C\'est voulu ! Pour les modifier, retournez dans Affichage → Masque des diapositives.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Accéder via Affichage → Masque des diapositives',
                'Distinguer masque principal et dispositions',
                'Ajouter logo ou élément sur toutes les slides via le masque',
                'Modifier police et couleur globale depuis le masque',
                'Fermer le mode Masque correctement',
            ]],
        ]],

        'transitions' => ['blocks' => [
            ['type'=>'intro','title'=>'Les transitions entre diapositives','text'=>'Une transition est l\'effet visuel lors du passage d\'une diapositive à la suivante. Bien utilisées, elles fluidifient. Mal utilisées (trop nombreuses, trop spectaculaires), elles distraient.','tip'=>'Règle pro : une seule transition pour toute la présentation, appliquée uniformément.'],
            ['type'=>'steps','title'=>'Ajouter une transition','items'=>[
                ['num'=>1,'action'=>'Sélectionnez la diapositive de DESTINATION (celle qui arrive)','detail'=>'La transition se définit sur la slide qui apparaît, pas celle qui part.'],
                ['num'=>2,'action'=>'Onglet Transitions → choisissez un effet','detail'=>'Survolez pour prévisualiser. Une étoile apparaît sur la miniature.'],
                ['num'=>3,'action'=>'Options d\'effet pour modifier direction/origine','detail'=>'Chaque transition a des options de personnalisation.'],
            ]],
            ['type'=>'steps','title'=>'Appliquer à toutes les diapositives','items'=>[
                ['num'=>1,'action'=>'Réglez la durée (recommandé : 0,5 à 1 seconde)','detail'=>'Évitez les transitions trop lentes — elles ennuient le public.'],
                ['num'=>2,'action'=>'Cliquez sur "Appliquer à toutes"','detail'=>'Toutes les diapositives ont la même transition. Cohérence assurée.'],
            ]],
            ['type'=>'steps','title'=>'Avancement automatique','items'=>[
                ['num'=>1,'action'=>'Section "Passage à la diapositive suivante" → cochez "Après :"','detail'=>'Entrez la durée en secondes.'],
                ['num'=>2,'action'=>'La diapositive avance seule après X secondes','detail'=>'Vous pouvez combiner avec "Au clic" : la première action déclenchera l\'avancement.'],
            ]],
            ['type'=>'warning','title'=>'⚠️ Transitions ≠ Animations','text'=>'Les TRANSITIONS = effet sur la diapositive entière entre deux slides. Les ANIMATIONS = effet sur les objets DANS une diapositive. Ce sont deux onglets distincts dans le ruban.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Appliquer une transition à une diapositive spécifique',
                'Appliquer la même transition à toutes les diapositives',
                'Modifier durée et options d\'effet',
                'Configurer l\'avancement automatique après N secondes',
                'Supprimer une transition (choisir Aucune)',
            ]],
        ]],

        'animations' => ['blocks' => [
            ['type'=>'intro','title'=>'Animer les objets pour guider l\'attention','text'=>'Les animations contrôlent comment les éléments apparaissent, sont mis en valeur ou disparaissent sur une diapositive. Elles guident l\'œil et révèlent l\'information progressivement.','tip'=>'Utilisez les animations pour révéler l\'information graduellement, pas pour impressionner. Moins c\'est souvent plus.'],
            ['type'=>'definition','term'=>'Les 4 catégories d\'animations','text'=>'🟢 ENTRÉE (vert) : comment l\'objet apparaît (Fondu, Apparition, Rotation...)\n🟡 ACCENTUATION (jaune) : effet sur un objet visible (Pulsation, Rotation...)\n🔴 SORTIE (rouge) : comment l\'objet disparaît\n⚫ TRAJECTOIRES : déplacement selon un chemin défini.'],
            ['type'=>'steps','title'=>'Appliquer une animation','items'=>[
                ['num'=>1,'action'=>'Sélectionnez l\'objet → Onglet Animations → choisissez un effet','detail'=>'Effets verts = Entrée. "Autres effets" pour la liste complète.'],
                ['num'=>2,'action'=>'Un numéro apparaît sur l\'objet : son ordre d\'animation','detail'=>'1 = première animation. Le diaporama les déclenche dans l\'ordre.'],
            ]],
            ['type'=>'steps','title'=>'Gérer l\'ordre et le déclencheur','items'=>[
                ['num'=>1,'action'=>'Onglet Animations → Volet Animation (ouvre la liste)','detail'=>'Toutes les animations de la diapositive avec leur ordre.'],
                ['num'=>2,'action'=>'Déclencheur : "Au clic", "Avec la précédente", "Après la précédente"','detail'=>'"Avec" = simultané. "Après" = automatique après la précédente.'],
                ['num'=>3,'action'=>'Changer l\'ordre : glissez dans le volet ou flèches Haut/Bas','detail'=>''],
                ['num'=>4,'action'=>'Durée et Délai dans l\'onglet Animations → groupe Minutage','detail'=>'Durée = temps de l\'animation. Délai = pause avant le démarrage.'],
            ]],
            ['type'=>'steps','title'=>'Animer une liste point par point','items'=>[
                ['num'=>1,'action'=>'Sélectionnez la zone de texte (bord, pas l\'intérieur)','detail'=>'Appliquez une animation d\'entrée.'],
                ['num'=>2,'action'=>'Volet Animation → clic sur l\'animation → Options d\'effet','detail'=>'Onglet "Animation du texte" → "Par paragraphes de 1er niveau".'],
                ['num'=>3,'action'=>'Chaque puce s\'anime séparément au clic','detail'=>'Permet de révéler l\'information point par point sans tout dévoiler.'],
            ]],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Appliquer une animation d\'entrée, d\'accentuation ou de sortie',
                'Utiliser le Volet Animation pour gérer l\'ordre',
                'Modifier le déclencheur (Au clic, Avec, Après la précédente)',
                'Régler durée et délai',
                'Animer une liste pour révéler les puces une par une',
            ]],
        ]],

        'images-ppt' => ['blocks' => [
            ['type'=>'intro','title'=>'Images — le cœur d\'une présentation percutante','text'=>'Une diapositive avec une belle image et peu de texte est 65% plus mémorisable. PowerPoint offre des outils puissants pour insérer, recadrer, styliser et positionner les images professionnellement.','tip'=>'Utilisez des images haute résolution (minimum 1920×1080px). Une image floue nuit à votre crédibilité.'],
            ['type'=>'steps','title'=>'Insérer et positionner','items'=>[
                ['num'=>1,'action'=>'Onglet Insertion → Images → Ce périphérique','detail'=>'Formats supportés : JPG, PNG, GIF, SVG, WEBP...'],
                ['num'=>2,'action'=>'Redimensionnez en maintenant Maj (coins) pour garder les proportions','detail'=>'Sans Maj, vous déformez l\'image.'],
                ['num'=>3,'action'=>'Format → Organiser → Aligner → Centrer sur la diapositive','detail'=>'Pour un centrage horizontal ET vertical parfait.'],
            ]],
            ['type'=>'steps','title'=>'Rogner une image','items'=>[
                ['num'=>1,'action'=>'Sélectionnez → Format de l\'image → Rogner','detail'=>'Des poignées noires apparaissent aux bords.'],
                ['num'=>2,'action'=>'Glissez les poignées pour définir la zone visible','detail'=>'La zone grisée est masquée mais pas supprimée.'],
                ['num'=>3,'action'=>'Rogner à la forme : Format → Rogner → Rogner à la forme','detail'=>'Rognez en cercle, étoile, bannière pour des effets créatifs.'],
            ]],
            ['type'=>'steps','title'=>'Supprimer l\'arrière-plan','items'=>[
                ['num'=>1,'action'=>'Sélectionnez → Format de l\'image → Supprimer l\'arrière-plan','detail'=>'PowerPoint détecte automatiquement l\'arrière-plan (zone rose = à supprimer).'],
                ['num'=>2,'action'=>'"Marquer les zones à conserver" pour affiner','detail'=>'Tracez sur les zones mal identifiées par PowerPoint.'],
                ['num'=>3,'action'=>'"Conserver les modifications" → arrière-plan supprimé','detail'=>'Image sur fond transparent, parfaite pour superposer.'],
            ]],
            ['type'=>'tip','title'=>'💡 Texte alternatif pour l\'accessibilité','text'=>'Clic droit → Modifier le texte de remplacement. Décrivez l\'image en une phrase pour les lecteurs d\'écran. C\'est aussi vérifié par le Vérificateur d\'accessibilité PowerPoint.'],
            ['type'=>'exam_focus','title'=>'🎯 Points clés MOS PowerPoint','items'=>[
                'Insérer et redimensionner proportionnellement (Maj + glisser)',
                'Rogner une image et rogner selon une forme',
                'Supprimer l\'arrière-plan avec l\'outil dédié',
                'Appliquer un style d\'image (ombre, reflet, cadre)',
                'Aligner et distribuer plusieurs images uniformément',
                'Ajouter un texte alternatif pour l\'accessibilité',
            ]],
        ]],

        ]; // fin powerpointLessons
    }
}
