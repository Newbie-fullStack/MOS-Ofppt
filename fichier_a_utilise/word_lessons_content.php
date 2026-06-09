<?php
// ══════════════════════════════════════════════════════════════════
// CONTENU DES LEÇONS WORD — content_json structuré
// Utiliser dans : database/seeders/LessonSeeder.php
// Ou via : php artisan tinker → Lesson::where('slug','styles-et-titres')->update([...])
// ══════════════════════════════════════════════════════════════════

// ── SCRIPT ARTISAN TINKER (copier-coller dans tinker) ─────────────
// php artisan tinker

// Leçon 1 : Styles et titres hiérarchiques
\App\Models\Lesson::where('slug', 'styles-et-titres')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Pourquoi utiliser les styles ?',
                'text'  => 'Les styles sont la base de tout document Word professionnel. Ils permettent d\'appliquer une mise en forme cohérente en un clic, de générer une table des matières automatique et de naviguer facilement dans un long document. Sans styles, vous passez des heures à reformater manuellement.',
                'tip'   => 'Un document bien structuré avec des styles prend 2× moins de temps à mettre en forme qu\'un document sans styles.',
            ],
            [
                'type'  => 'definition',
                'term'  => 'Style de paragraphe',
                'text'  => 'Un style est un ensemble de propriétés de mise en forme (police, taille, couleur, espacement) sauvegardées sous un nom. Exemple : "Titre 1" = Calibri 16pt Gras Bleu.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Comment appliquer le style Titre 1',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur le paragraphe à mettre en forme (ou sélectionnez-le)', 'detail' => 'Il suffit de placer le curseur dans le paragraphe, pas besoin de tout sélectionner.'],
                    ['num' => 2, 'action' => 'Allez dans l\'onglet Accueil', 'detail' => 'C\'est le premier onglet du ruban Word.'],
                    ['num' => 3, 'action' => 'Dans le groupe Styles, cliquez sur "Titre 1"', 'detail' => 'La galerie des styles affiche les styles disponibles. Faites défiler si nécessaire.'],
                    ['num' => 4, 'action' => 'Le paragraphe prend immédiatement la mise en forme du style', 'detail' => 'Raccourci clavier : Ctrl+Alt+1 pour Titre 1, Ctrl+Alt+2 pour Titre 2, Ctrl+Alt+3 pour Titre 3.'],
                ],
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Erreur fréquente à l\'examen MOS',
                'text'  => 'Ne confondez pas "Titre 1" (style) avec du texte mis en gras manuellement. Pour l\'examen, il faut OBLIGATOIREMENT utiliser les styles — le formatage manuel ne sera pas reconnu.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Créer une table des matières automatique',
                'items' => [
                    ['num' => 1, 'action' => 'Appliquez les styles Titre 1, Titre 2, Titre 3 à vos titres', 'detail' => 'La table des matières se basera sur ces styles pour se générer.'],
                    ['num' => 2, 'action' => 'Placez le curseur à l\'endroit souhaité (début du document)', 'detail' => 'Généralement après la page de titre.'],
                    ['num' => 3, 'action' => 'Onglet Références → Table des matières → Table automatique 1', 'detail' => 'Word génère instantanément la TdM avec les numéros de page.'],
                    ['num' => 4, 'action' => 'Pour mettre à jour : clic droit → Mettre à jour les champs', 'detail' => 'Choisissez "Mettre à jour toute la table" après avoir modifié le document.'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Modifier un style existant',
                'text'  => 'Clic droit sur le style dans la galerie → Modifier. Changez la police, la taille, la couleur… Tous les paragraphes utilisant ce style se mettent à jour automatiquement dans tout le document !',
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis clavier essentiels',
                'rows'  => [
                    ['keys' => 'Ctrl+Alt+1', 'action' => 'Appliquer le style Titre 1'],
                    ['keys' => 'Ctrl+Alt+2', 'action' => 'Appliquer le style Titre 2'],
                    ['keys' => 'Ctrl+Alt+3', 'action' => 'Appliquer le style Titre 3'],
                    ['keys' => 'Ctrl+Espace', 'action' => 'Effacer la mise en forme de caractère'],
                    ['keys' => 'Ctrl+Q',      'action' => 'Effacer la mise en forme de paragraphe'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Appliquer les styles Titre 1/2/3 en utilisant la galerie Styles (pas le formatage manuel)',
                    'Créer une table des matières via Références → Table des matières',
                    'Mettre à jour la TdM après modification du document',
                    'Modifier un style existant pour qu\'il s\'applique à tout le document',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 2 : Mise en page et marges
\App\Models\Lesson::where('slug', 'mise-en-page')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'La mise en page dans Word',
                'text'  => 'La mise en page définit l\'apparence physique du document : taille des marges, orientation (portrait/paysage), taille du papier. Ces paramètres affectent l\'impression et la présentation professionnelle de vos documents.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Modifier les marges',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur l\'onglet Mise en page (ou Disposition)', 'detail' => 'Le nom varie selon la version de Word.'],
                    ['num' => 2, 'action' => 'Dans le groupe Mise en page, cliquez sur Marges', 'detail' => 'Une galerie de marges prédéfinies s\'affiche (Normal, Étroit, Large, etc.).'],
                    ['num' => 3, 'action' => 'Choisissez un préréglage ou cliquez sur Marges personnalisées', 'detail' => 'Marges personnalisées ouvre une boîte de dialogue pour entrer des valeurs précises en cm.'],
                ],
            ],
            [
                'type'  => 'definition',
                'term'  => 'Marges standard recommandées',
                'text'  => 'Marges normales Word : Haut 2,54 cm, Bas 2,54 cm, Gauche 3,17 cm, Droite 3,17 cm. Pour un document professionnel : 2,5 cm de tous les côtés. Pour l\'impression recto-verso : utiliser les marges "En miroir".',
            ],
            [
                'type'  => 'steps',
                'title' => 'Changer l\'orientation de la page',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Mise en page → Orientation', 'detail' => 'Deux options : Portrait (vertical) ou Paysage (horizontal).'],
                    ['num' => 2, 'action' => 'Choisissez Portrait ou Paysage', 'detail' => 'S\'applique à tout le document par défaut.'],
                    ['num' => 3, 'action' => 'Pour une seule page : utilisez les sections', 'detail' => 'Insérez un saut de section avant et après, puis changez l\'orientation de cette section uniquement.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer un saut de page',
                'items' => [
                    ['num' => 1, 'action' => 'Placez le curseur où vous voulez le saut', 'detail' => 'En fin de chapitre, avant un nouveau titre, etc.'],
                    ['num' => 2, 'action' => 'Utilisez Ctrl+Entrée', 'detail' => 'C\'est la méthode la plus rapide. Évitez d\'appuyer sur Entrée plusieurs fois !'],
                    ['num' => 3, 'action' => 'Ou : Onglet Insertion → Pages → Saut de page', 'detail' => 'Même résultat via le menu.'],
                ],
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Ne jamais faire ça',
                'text'  => 'Appuyer sur Entrée plusieurs fois pour aller à la page suivante est une très mauvaise pratique. Si vous ajoutez du texte, tout se décale. Utilisez TOUJOURS Ctrl+Entrée pour insérer un vrai saut de page.',
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis mise en page',
                'rows'  => [
                    ['keys' => 'Ctrl+Entrée',  'action' => 'Insérer un saut de page'],
                    ['keys' => 'Ctrl+Maj+Entrée', 'action' => 'Insérer un saut de colonne'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Modifier les marges via Mise en page → Marges',
                    'Changer l\'orientation (Portrait/Paysage)',
                    'Insérer un saut de page avec Ctrl+Entrée (jamais avec des Entrées répétés)',
                    'Utiliser les sections pour des mises en page différentes dans un même document',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 3 : En-têtes et pieds de page
\App\Models\Lesson::where('slug', 'en-tetes-pieds-de-page')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'En-têtes et pieds de page',
                'text'  => 'L\'en-tête apparaît en haut de chaque page, le pied de page en bas. Ils contiennent généralement : numéros de page, nom du document, logo, date. Une fois configurés, ils se répètent automatiquement sur toutes les pages.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer un numéro de page',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Insertion → En-tête et pied de page → Numéro de page', 'detail' => 'Ne pas taper le chiffre manuellement — il ne s\'incrémenterait pas.'],
                    ['num' => 2, 'action' => 'Choisissez la position : Haut de page, Bas de page, etc.', 'detail' => 'Une galerie de styles de numérotation s\'affiche.'],
                    ['num' => 3, 'action' => 'Sélectionnez le style souhaité', 'detail' => 'Le numéro s\'insère automatiquement et s\'incrémente sur chaque page.'],
                    ['num' => 4, 'action' => 'Double-cliquez hors de l\'en-tête pour revenir au document', 'detail' => 'Ou cliquez sur Fermer l\'en-tête et le pied de page.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Première page différente (sans numéro)',
                'items' => [
                    ['num' => 1, 'action' => 'Double-cliquez sur l\'en-tête pour entrer en mode édition', 'detail' => 'L\'onglet contextuel Création apparaît.'],
                    ['num' => 2, 'action' => 'Dans l\'onglet Création, cochez "Première page différente"', 'detail' => 'L\'en-tête de la première page devient indépendant des autres.'],
                    ['num' => 3, 'action' => 'La première page n\'aura plus de numéro', 'detail' => 'Pour commencer la numérotation à 1 sur la 2ème page : Format des numéros → Commencer à 0.'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Pages paires et impaires différentes',
                'text'  => 'Pour l\'impression recto-verso, cochez "Pages paires et impaires différentes" dans l\'onglet Création. Vous pouvez ainsi placer le numéro à droite sur les pages impaires et à gauche sur les pages paires.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Insérer un numéro de page via Insertion → Numéro de page (jamais manuellement)',
                    'Activer "Première page différente" pour une page de garde sans numéro',
                    'Modifier le format des numéros (i, ii, iii ou 1, 2, 3)',
                    'Ajouter du texte, une date ou un logo dans l\'en-tête',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 4 : Tableaux dans Word
\App\Models\Lesson::where('slug', 'tableaux-word')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Les tableaux dans Word',
                'text'  => 'Les tableaux permettent d\'organiser l\'information en lignes et colonnes. Ils sont essentiels pour les rapports, CV, comparatifs et formulaires. Word offre de nombreux outils pour créer et formater des tableaux professionnels.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer un tableau',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Insertion → Tableau', 'detail' => 'Une grille interactive s\'affiche.'],
                    ['num' => 2, 'action' => 'Survolez la grille pour choisir le nombre de colonnes et lignes', 'detail' => 'Ou cliquez sur "Insérer un tableau" pour entrer des valeurs précises.'],
                    ['num' => 3, 'action' => 'Cliquez pour confirmer', 'detail' => 'Le tableau s\'insère avec des colonnes de largeur égale.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Fusionner des cellules',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez les cellules à fusionner', 'detail' => 'Clic + glisser sur les cellules adjacentes.'],
                    ['num' => 2, 'action' => 'Clic droit → Fusionner les cellules', 'detail' => 'Ou onglet Disposition → Fusionner les cellules.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Répéter l\'en-tête sur chaque page',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez la ligne d\'en-tête du tableau', 'detail' => 'Cliquez sur la première ligne.'],
                    ['num' => 2, 'action' => 'Onglet Disposition → Données → Répéter les lignes d\'en-tête', 'detail' => 'La ligne d\'en-tête apparaîtra automatiquement en haut de chaque page.'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Ajuster la largeur des colonnes',
                'text'  => 'Double-cliquez sur le bord d\'une colonne pour ajuster automatiquement sa largeur au contenu. Ou faites glisser le bord pour la régler manuellement. Onglet Disposition → Ajustement automatique → Ajuster au contenu.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Insérer un tableau avec le nombre exact de lignes et colonnes demandé',
                    'Fusionner des cellules pour créer des en-têtes',
                    'Appliquer un style de tableau prédéfini (onglet Création du tableau)',
                    'Répéter les lignes d\'en-tête sur chaque page pour les longs tableaux',
                    'Trier le contenu d\'un tableau (onglet Disposition → Trier)',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 5 : Images et illustrations
\App\Models\Lesson::where('slug', 'images-word')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Insérer des images dans Word',
                'text'  => 'Les images enrichissent vos documents et les rendent plus visuels. Word permet d\'insérer des photos, des icônes, des formes et des graphiques SmartArt, puis de les positionner précisément par rapport au texte.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer une image depuis l\'ordinateur',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Insertion → Images → Ce périphérique', 'detail' => 'L\'explorateur de fichiers s\'ouvre.'],
                    ['num' => 2, 'action' => 'Naviguez jusqu\'à l\'image et double-cliquez dessus', 'detail' => 'Formats supportés : JPG, PNG, GIF, BMP, etc.'],
                    ['num' => 3, 'action' => 'L\'image s\'insère "En ligne avec le texte" par défaut', 'detail' => 'Elle se comporte comme un caractère de texte — pas encore flottante.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Configurer l\'habillage du texte',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur l\'image pour la sélectionner', 'detail' => 'Des poignées de redimensionnement apparaissent.'],
                    ['num' => 2, 'action' => 'Cliquez sur l\'icône d\'habillage (à droite de l\'image)', 'detail' => 'Ou : onglet Format → Habillage du texte.'],
                    ['num' => 3, 'action' => 'Choisissez le type d\'habillage', 'detail' => '"Carré" : texte autour du cadre. "Rapproché" : texte suit les contours. "Derrière le texte" : image en fond.'],
                ],
            ],
            [
                'type'  => 'definition',
                'term'  => 'Types d\'habillage du texte',
                'text'  => 'En ligne (défaut), Carré (texte autour du rectangle), Rapproché (texte suit les contours), Au travers, Haut et bas, Derrière le texte, Devant le texte. Pour l\'examen MOS, connaître Carré et En ligne est suffisant.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Insérer une image via Insertion → Images → Ce périphérique',
                    'Modifier l\'habillage du texte (Carré, En ligne, etc.)',
                    'Rogner une image : onglet Format → Rogner',
                    'Redimensionner proportionnellement (maintenir Maj en faisant glisser un coin)',
                    'Ajouter un texte alternatif pour l\'accessibilité',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 6 : Publipostage
\App\Models\Lesson::where('slug', 'publipostage')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Le publipostage (Mail Merge)',
                'text'  => 'Le publipostage permet de créer automatiquement des dizaines ou centaines de lettres, étiquettes ou emails personnalisés en combinant un document modèle avec une liste de destinataires (Excel, Access, CSV). Indispensable pour les convocations, attestations, courriers en masse.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Étapes du publipostage — Vue d\'ensemble',
                'items' => [
                    ['num' => 1, 'action' => 'Créer le document principal (lettre modèle)', 'detail' => 'Rédigez votre lettre avec des espaces pour les données variables.'],
                    ['num' => 2, 'action' => 'Connecter la source de données', 'detail' => 'Onglet Publipostage → Sélection des destinataires → Utiliser une liste existante → choisir le fichier Excel.'],
                    ['num' => 3, 'action' => 'Insérer les champs de fusion', 'detail' => 'Onglet Publipostage → Insérer un champ de fusion → choisir la colonne (ex: Prénom, Ville).'],
                    ['num' => 4, 'action' => 'Prévisualiser les résultats', 'detail' => 'Onglet Publipostage → Aperçu des résultats. Naviguez entre les enregistrements.'],
                    ['num' => 5, 'action' => 'Terminer et fusionner', 'detail' => 'Onglet Publipostage → Terminer et fusionner → Modifier les documents individuels (ou Imprimer).'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Préparer le fichier Excel source',
                'text'  => 'La première ligne du fichier Excel DOIT contenir les en-têtes de colonnes (Prénom, Nom, Ville, Email…). Ces en-têtes deviendront les noms des champs de fusion. Assurez-vous qu\'il n\'y a pas de lignes vides au début.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Filtrer les destinataires',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Publipostage → Modifier la liste des destinataires', 'detail' => 'Une boîte de dialogue affiche tous les enregistrements.'],
                    ['num' => 2, 'action' => 'Cliquez sur Filtre pour ajouter une condition', 'detail' => 'Ex: Ville = "Casablanca" → seuls les destinataires de Casablanca seront fusionnés.'],
                ],
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Format des champs de fusion',
                'text'  => 'Les champs de fusion apparaissent entre guillemets doubles : «Prénom». Ne les tapez PAS manuellement — utilisez toujours Insertion → Champ de fusion pour les insérer correctement.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Connecter un fichier Excel comme source de données',
                    'Insérer des champs de fusion via l\'onglet Publipostage',
                    'Prévisualiser les résultats avant fusion',
                    'Filtrer les destinataires selon un critère',
                    'Terminer et fusionner vers un nouveau document',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Leçon 7 : Suivi des modifications
\App\Models\Lesson::where('slug', 'suivi-modifications')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Le suivi des modifications',
                'text'  => 'Le suivi des modifications permet à plusieurs personnes de collaborer sur un document tout en gardant une trace de chaque changement. Chaque modification est annotée avec le nom de l\'auteur et la date. Indispensable pour la relecture professionnelle.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Activer le suivi des modifications',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Révision → Suivi des modifications', 'detail' => 'Raccourci : Ctrl+Maj+E. Le bouton s\'allume pour indiquer que le suivi est actif.'],
                    ['num' => 2, 'action' => 'Toutes les modifications suivantes seront tracées', 'detail' => 'Texte ajouté : souligné. Texte supprimé : barré. La couleur varie selon l\'auteur.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Accepter ou refuser les modifications',
                'items' => [
                    ['num' => 1, 'action' => 'Pour accepter UNE modification : clic droit → Accepter', 'detail' => 'Ou onglet Révision → Accepter → Accepter cette modification.'],
                    ['num' => 2, 'action' => 'Pour accepter TOUTES les modifications d\'un coup', 'detail' => 'Onglet Révision → Accepter → Accepter toutes les modifications.'],
                    ['num' => 3, 'action' => 'Pour refuser une modification', 'detail' => 'Clic droit → Refuser, ou onglet Révision → Refuser.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer un commentaire',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez le texte à commenter', 'detail' => 'Ou placez simplement le curseur à l\'emplacement.'],
                    ['num' => 2, 'action' => 'Onglet Révision → Nouveau commentaire (ou Ctrl+Alt+M)', 'detail' => 'Un bullon apparaît dans la marge droite.'],
                    ['num' => 3, 'action' => 'Tapez votre commentaire', 'detail' => 'Le commentaire est lié au texte sélectionné et indique votre nom.'],
                ],
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis révision',
                'rows'  => [
                    ['keys' => 'Ctrl+Maj+E', 'action' => 'Activer/désactiver le suivi des modifications'],
                    ['keys' => 'Ctrl+Alt+M', 'action' => 'Insérer un nouveau commentaire'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS',
                'items' => [
                    'Activer/désactiver le suivi avec Ctrl+Maj+E',
                    'Accepter ou refuser des modifications individuellement ou toutes à la fois',
                    'Insérer, répondre et supprimer des commentaires',
                    'Afficher ou masquer les modifications (Onglet Révision → Afficher les marques)',
                ],
            ],
        ],
    ],
]);

echo "✅ Contenu de toutes les leçons Word mis à jour avec succès !";
