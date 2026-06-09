<?php
// ══════════════════════════════════════════════════════════════════
// CONTENU DES LEÇONS EXCEL — content_json structuré
// Commande : php artisan tinker → copier-coller ce fichier
// ══════════════════════════════════════════════════════════════════

// ── Leçon 1 : Formules de base ────────────────────────────────────
\App\Models\Lesson::where('slug', 'formules-de-base')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Les formules, le cœur d\'Excel',
                'text'  => 'Excel est bien plus qu\'un tableau — c\'est un moteur de calcul. Les formules transforment vos données brutes en informations utiles : totaux, moyennes, statistiques. Toute formule commence par le signe = et peut référencer des cellules individuelles ou des plages entières.',
                'tip'   => 'Une formule qui référence des cellules se met à jour automatiquement quand les données changent. C\'est l\'avantage fondamental sur une calculatrice.',
            ],
            [
                'type'  => 'definition',
                'term'  => 'Syntaxe d\'une formule Excel',
                'text'  => '=FONCTION(argument1; argument2; ...) — Le signe = déclenche le calcul. Le nom de la fonction est en majuscules. Les arguments sont séparés par des point-virgules (;) en français. Exemple : =SOMME(A1:A10) additionne les cellules A1 jusqu\'à A10.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Saisir votre première formule SOMME',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur la cellule où vous voulez le résultat', 'detail' => 'Par exemple B11, sous une colonne de chiffres en B1:B10.'],
                    ['num' => 2, 'action' => 'Tapez =SOMME(', 'detail' => 'Excel affiche une info-bulle avec la syntaxe de la fonction.'],
                    ['num' => 3, 'action' => 'Sélectionnez la plage à additionner (ex: B1:B10)', 'detail' => 'Cliquez sur B1 et glissez jusqu\'à B10. La plage s\'affiche en surbrillance colorée.'],
                    ['num' => 4, 'action' => 'Tapez ) puis appuyez sur Entrée', 'detail' => 'Le résultat s\'affiche. Raccourci ultra-rapide : sélectionnez la plage puis appuyez sur Alt+= pour insérer SOMME automatiquement !'],
                ],
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Les fonctions statistiques essentielles',
                'rows'  => [
                    ['keys' => '=SOMME(A1:A10)',    'action' => 'Additionne toutes les valeurs de A1 à A10'],
                    ['keys' => '=MOYENNE(A1:A10)',  'action' => 'Calcule la moyenne arithmétique'],
                    ['keys' => '=MAX(A1:A10)',       'action' => 'Retourne la valeur maximale'],
                    ['keys' => '=MIN(A1:A10)',       'action' => 'Retourne la valeur minimale'],
                    ['keys' => '=NB(A1:A10)',        'action' => 'Compte les cellules contenant des nombres'],
                    ['keys' => '=NBVAL(A1:A10)',     'action' => 'Compte les cellules non vides (texte + nombres)'],
                    ['keys' => '=NB.VIDE(A1:A10)',   'action' => 'Compte les cellules vides'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Références absolues vs relatives — concept clé',
                'items' => [
                    ['num' => 1, 'action' => 'Référence RELATIVE : A1 (sans $)', 'detail' => 'Quand vous copiez la formule vers le bas, A1 devient A2, A3... Elle s\'adapte automatiquement. Parfait pour calculer plusieurs lignes en une fois.'],
                    ['num' => 2, 'action' => 'Référence ABSOLUE : $A$1 (avec $)', 'detail' => 'Quand vous copiez, $A$1 reste toujours $A$1. Utile pour un taux de TVA ou un coefficient fixe.'],
                    ['num' => 3, 'action' => 'Référence MIXTE : $A1 ou A$1', 'detail' => '$A1 = colonne A fixe, ligne variable. A$1 = ligne 1 fixe, colonne variable.'],
                    ['num' => 4, 'action' => 'Raccourci : touche F4 après avoir tapé la référence', 'detail' => 'F4 fait le cycle : A1 → $A$1 → A$1 → $A1 → A1. Appuyez plusieurs fois pour choisir le type voulu.'],
                ],
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Erreur #VALEUR! et #NOM?',
                'text'  => '#VALEUR! = vous essayez de faire un calcul sur du texte. #NOM? = nom de fonction mal orthographié (ex: =SOM au lieu de =SOMME). #DIV/0! = division par zéro. #REF! = référence à une cellule supprimée.',
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis clavier Excel essentiels',
                'rows'  => [
                    ['keys' => 'Alt + =',     'action' => 'Insérer automatiquement =SOMME sur la plage détectée'],
                    ['keys' => 'F4',          'action' => 'Basculer entre les types de références ($A$1, A$1, $A1)'],
                    ['keys' => 'Ctrl + Z',    'action' => 'Annuler la dernière action'],
                    ['keys' => 'Ctrl + Entrée', 'action' => 'Valider la formule sans quitter la cellule'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Utiliser =SOMME(), =MOYENNE(), =MAX(), =MIN() sur des plages de cellules',
                    'Distinguer référence relative (A1) et absolue ($A$1) et savoir quand utiliser chacune',
                    'Utiliser F4 pour basculer entre les types de références',
                    'Utiliser Alt+= pour insérer une SOMME automatique rapidement',
                    'Identifier et comprendre les messages d\'erreur (#VALEUR!, #NOM?, #DIV/0!)',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 2 : Formules conditionnelles ───────────────────────────
\App\Models\Lesson::where('slug', 'formules-conditionnelles')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Les formules conditionnelles',
                'text'  => 'Les formules conditionnelles permettent d\'automatiser des décisions : "Si la note est ≥ 10, afficher Reçu, sinon Refusé". Elles sont omniprésentes dans les tableaux de bord professionnels, les bulletins de notes, les rapports de stock, et les tableaux de suivi.',
            ],
            [
                'type'  => 'definition',
                'term'  => 'Syntaxe de la fonction SI',
                'text'  => '=SI(test_logique ; valeur_si_vrai ; valeur_si_faux)\n\nExemple : =SI(B2>=10 ; "Reçu" ; "Refusé")\n\nLe test logique utilise les opérateurs : = (égal), > (supérieur), < (inférieur), >= (supérieur ou égal), <= (inférieur ou égal), <> (différent de).',
            ],
            [
                'type'  => 'steps',
                'title' => 'Construire une formule SI étape par étape',
                'items' => [
                    ['num' => 1, 'action' => 'Identifiez la condition : "Si la valeur en B2 est supérieure ou égale à 10"', 'detail' => 'Traduit en Excel : B2>=10'],
                    ['num' => 2, 'action' => 'Définissez la valeur si VRAI : "Reçu"', 'detail' => 'Les textes doivent être entre guillemets. Les nombres sans guillemets.'],
                    ['num' => 3, 'action' => 'Définissez la valeur si FAUX : "Refusé"', 'detail' => 'Si vous omettez ce 3ème argument, Excel affiche FAUX quand la condition n\'est pas remplie.'],
                    ['num' => 4, 'action' => 'Assemblez : =SI(B2>=10;"Reçu";"Refusé")', 'detail' => 'Copiez vers le bas pour appliquer à toute la colonne automatiquement.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'NB.SI — compter selon un critère',
                'items' => [
                    ['num' => 1, 'action' => 'Syntaxe : =NB.SI(plage ; critère)', 'detail' => 'Compte toutes les cellules dans la plage qui correspondent au critère.'],
                    ['num' => 2, 'action' => 'Exemple texte : =NB.SI(A1:A100;"Paris")', 'detail' => 'Compte le nombre de cellules contenant exactement "Paris".'],
                    ['num' => 3, 'action' => 'Exemple nombre : =NB.SI(B1:B100;">10")', 'detail' => 'Le critère numérique avec opérateur doit être entre guillemets : ">10", "<=5", "<>0".'],
                    ['num' => 4, 'action' => 'Avec joker : =NB.SI(A1:A100;"Casa*")', 'detail' => 'L\'astérisque * remplace n\'importe quelle suite de caractères. "Casa*" trouve "Casablanca", "Casanet"...'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'SOMME.SI — additionner selon un critère',
                'items' => [
                    ['num' => 1, 'action' => 'Syntaxe : =SOMME.SI(plage_critère ; critère ; plage_somme)', 'detail' => 'Additionne les valeurs de plage_somme là où plage_critère correspond au critère.'],
                    ['num' => 2, 'action' => 'Exemple : =SOMME.SI(A1:A100;"Maroc";C1:C100)', 'detail' => 'Additionne les valeurs de C1:C100 uniquement pour les lignes où A1:A100 = "Maroc".'],
                    ['num' => 3, 'action' => 'SOMME.SI.ENS pour plusieurs critères', 'detail' => '=SOMME.SI.ENS(plage_somme ; plage1 ; critère1 ; plage2 ; critère2...) — La plage à sommer est EN PREMIER (différent de SOMME.SI !).'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 ET() et OU() dans les conditions',
                'text'  => 'Pour plusieurs conditions dans un SI : =SI(ET(A1>0;B1>0);"Les deux positifs";"Au moins un négatif"). =ET() exige que TOUTES les conditions soient vraies. =OU() exige qu\'AU MOINS UNE soit vraie.',
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Les fonctions conditionnelles à connaître',
                'rows'  => [
                    ['keys' => '=SI(test;vrai;faux)',                  'action' => 'Condition simple — affiche une valeur selon le test'],
                    ['keys' => '=NB.SI(plage;critère)',                'action' => 'Compte les cellules correspondant au critère'],
                    ['keys' => '=NB.SI.ENS(p1;c1;p2;c2)',            'action' => 'Compte avec plusieurs conditions simultanées'],
                    ['keys' => '=SOMME.SI(p_crit;crit;p_som)',        'action' => 'Additionne selon un critère'],
                    ['keys' => '=SOMME.SI.ENS(p_som;p1;c1;p2;c2)',   'action' => 'Additionne avec plusieurs critères'],
                    ['keys' => '=MOYENNE.SI(plage;critère;p_moy)',    'action' => 'Moyenne selon un critère'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Construire une formule =SI() avec test logique, valeur vrai et valeur faux',
                    'Utiliser =NB.SI() pour compter selon un critère texte ou numérique',
                    'Utiliser =SOMME.SI() pour additionner selon un critère',
                    'Utiliser =ET() et =OU() pour combiner plusieurs conditions dans un SI',
                    'Connaître la différence entre SOMME.SI et SOMME.SI.ENS (ordre des arguments)',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 3 : RECHERCHEV et INDEX/EQUIV ──────────────────────────
\App\Models\Lesson::where('slug', 'recherchev-index-equiv')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Chercher des données dans un tableau',
                'text'  => 'RECHERCHEV est l\'une des fonctions les plus utilisées en entreprise. Elle permet de "croiser" deux tableaux : cherchez un code produit dans une liste et récupérez automatiquement son prix, son nom ou sa catégorie. Indispensable pour les tableaux de bord, facturation et rapports.',
            ],
            [
                'type'  => 'definition',
                'term'  => 'Syntaxe RECHERCHEV',
                'text'  => '=RECHERCHEV(valeur_cherchée ; tableau ; n°_colonne ; FAUX)\n\n• valeur_cherchée : ce que vous cherchez (un code, un nom...)\n• tableau : la plage contenant les données (doit commencer par la colonne de recherche)\n• n°_colonne : numéro de la colonne à retourner (1 = première colonne du tableau)\n• FAUX : correspondance EXACTE (toujours utiliser FAUX pour l\'examen MOS)',
            ],
            [
                'type'  => 'steps',
                'title' => 'Exemple concret : trouver le prix d\'un produit',
                'items' => [
                    ['num' => 1, 'action' => 'Tableau de référence : A1:C100 (col A=Code, col B=Produit, col C=Prix)', 'detail' => 'La colonne de recherche DOIT être la première colonne du tableau dans RECHERCHEV.'],
                    ['num' => 2, 'action' => 'Cellule de recherche : E2 contient le code produit cherché', 'detail' => 'Exemple : E2 = "P001"'],
                    ['num' => 3, 'action' => 'Formule : =RECHERCHEV(E2 ; $A$1:$C$100 ; 3 ; FAUX)', 'detail' => 'Cherche E2 dans la 1ère colonne de $A$1:$C$100, retourne la valeur de la 3ème colonne (Prix). $ pour fixer le tableau lors de la copie.'],
                    ['num' => 4, 'action' => 'Si non trouvé : #N/A s\'affiche', 'detail' => 'Protégez avec SIERREUR : =SIERREUR(RECHERCHEV(...);"Non trouvé") pour afficher un texte à la place de l\'erreur.'],
                ],
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Limitation de RECHERCHEV',
                'text'  => 'RECHERCHEV ne peut chercher QUE dans la première colonne du tableau, et ne peut retourner que des colonnes à DROITE. Si vous avez besoin de chercher dans n\'importe quelle colonne ou de retourner vers la gauche, utilisez INDEX+EQUIV.',
            ],
            [
                'type'  => 'steps',
                'title' => 'INDEX + EQUIV — la combinaison puissante',
                'items' => [
                    ['num' => 1, 'action' => 'EQUIV cherche une valeur et retourne sa POSITION', 'detail' => '=EQUIV("Paris" ; A1:A100 ; 0) → retourne le numéro de ligne où se trouve "Paris". Le 0 = correspondance exacte.'],
                    ['num' => 2, 'action' => 'INDEX retourne la valeur à une POSITION donnée', 'detail' => '=INDEX(C1:C100 ; 5) → retourne la valeur en C5 (5ème élément de la plage C1:C100).'],
                    ['num' => 3, 'action' => 'Combinés : =INDEX(plage_résultat ; EQUIV(valeur ; plage_recherche ; 0))', 'detail' => 'Exemple : =INDEX(C1:C100 ; EQUIV(E2 ; A1:A100 ; 0)) → cherche E2 dans A1:A100, retourne la valeur correspondante en C. Peut chercher dans n\'importe quelle direction !'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 RECHERCHEX dans Excel 365 (moderne)',
                'text'  => 'Excel 365 introduit =RECHERCHEX(valeur ; plage_recherche ; plage_retour) qui remplace RECHERCHEV. Plus simple, plus flexible : peut chercher dans n\'importe quelle colonne, dans les deux sens, et accepte une valeur par défaut si non trouvé.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Construire =RECHERCHEV(valeur ; tableau ; n°col ; FAUX) avec le bon n° de colonne',
                    'Toujours utiliser FAUX (correspondance exacte) sauf cas très spécifique',
                    'Fixer le tableau avec $A$1:$C$100 pour pouvoir copier la formule',
                    'Gérer les erreurs #N/A avec =SIERREUR(RECHERCHEV(...) ; "Non trouvé")',
                    'Connaître la limitation : RECHERCHEV cherche uniquement dans la 1ère colonne',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 4 : Mise en forme conditionnelle ───────────────────────
\App\Models\Lesson::where('slug', 'mfc')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Visualiser les données automatiquement',
                'text'  => 'La Mise en Forme Conditionnelle (MFC) applique automatiquement couleurs, icônes ou barres selon la valeur d\'une cellule. En un coup d\'œil, vous repérez les anomalies, les performances et les tendances — sans créer de graphique.',
                'tip'   => 'La MFC se recalcule en temps réel. Modifiez une valeur et la couleur change immédiatement.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Mettre en rouge les valeurs négatives',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez la plage de cellules (ex: B2:B100)', 'detail' => 'La MFC s\'appliquera à toutes les cellules sélectionnées.'],
                    ['num' => 2, 'action' => 'Onglet Accueil → Mise en forme conditionnelle → Règles de mise en surbrillance', 'detail' => 'Un sous-menu affiche les types de règles prédéfinies.'],
                    ['num' => 3, 'action' => 'Cliquez sur "Inférieur à..."', 'detail' => 'Une boîte de dialogue s\'ouvre avec un champ de valeur.'],
                    ['num' => 4, 'action' => 'Entrez 0, choisissez "Remplissage rouge clair avec texte rouge foncé"', 'detail' => 'Cliquez OK. Toutes les valeurs négatives passent en rouge instantanément.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Barres de données et Nuances de couleurs',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez la plage de valeurs', 'detail' => 'Les barres s\'adapteront aux valeurs min et max de la sélection.'],
                    ['num' => 2, 'action' => 'Onglet Accueil → Mise en forme conditionnelle → Barres de données', 'detail' => 'Choisissez un remplissage uni ou dégradé. Chaque cellule affiche une barre proportionnelle à sa valeur.'],
                    ['num' => 3, 'action' => 'Pour les nuances de couleurs : même menu → Nuances de couleurs', 'detail' => 'Les cellules passent du vert (valeurs hautes) au rouge (valeurs basses) ou l\'inverse selon le jeu choisi.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Créer une règle avec formule personnalisée',
                'items' => [
                    ['num' => 1, 'action' => 'Accueil → Mise en forme conditionnelle → Nouvelle règle', 'detail' => 'Pour des conditions plus complexes qu\'une simple comparaison.'],
                    ['num' => 2, 'action' => 'Choisissez "Utiliser une formule pour déterminer..."', 'detail' => 'Vous pouvez écrire n\'importe quelle formule Excel qui retourne VRAI ou FAUX.'],
                    ['num' => 3, 'action' => 'Exemple : =$C2="En retard" → fond orange pour toute la ligne', 'detail' => 'Le $ sur la colonne C est CRUCIAL : il fixe la référence à la colonne C mais permet à la ligne de varier.'],
                    ['num' => 4, 'action' => 'Cliquez sur Format → choisissez la mise en forme → OK', 'detail' => 'La règle s\'applique à toute la plage sélectionnée selon votre formule.'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Gérer les règles existantes',
                'text'  => 'Onglet Accueil → Mise en forme conditionnelle → Gérer les règles affiche toutes les règles actives sur la sélection. Vous pouvez les modifier, supprimer, ou changer leur priorité (la règle en haut s\'applique en premier).',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Appliquer une règle de mise en surbrillance (supérieur à, inférieur à, entre, égal à)',
                    'Utiliser les barres de données pour visualiser les proportions',
                    'Utiliser les nuances de couleurs (dégradés vert-rouge)',
                    'Créer une règle basée sur une formule personnalisée',
                    'Gérer et modifier les règles existantes (priorité, modification, suppression)',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 5 : Tableaux structurés ────────────────────────────────
\App\Models\Lesson::where('slug', 'tableaux-structures')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Les tableaux structurés — la bonne pratique',
                'text'  => 'Convertir une plage en "Tableau" Excel (avec majuscule) est l\'une des meilleures pratiques. Les tableaux structurés gèrent automatiquement le tri, le filtrage, les formules et le style. Ils s\'étendent seuls quand vous ajoutez des données — fini le problème des formules qui ne couvrent pas les nouvelles lignes.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Convertir une plage en tableau structuré',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez n\'importe où dans la plage de données', 'detail' => 'Excel détecte automatiquement les bordures de la plage.'],
                    ['num' => 2, 'action' => 'Appuyez sur Ctrl+T (ou Onglet Insertion → Tableau)', 'detail' => 'Une boîte de dialogue confirme la plage détectée.'],
                    ['num' => 3, 'action' => 'Vérifiez que "Mon tableau comporte des en-têtes" est coché', 'detail' => 'Si votre première ligne contient les titres de colonnes, cette option doit être cochée.'],
                    ['num' => 4, 'action' => 'Cliquez OK', 'detail' => 'Le tableau est créé avec des filtres automatiques et un style coloré. Un nom automatique est attribué (Tableau1, Tableau2...).'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Ajouter une ligne de totaux',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez dans le tableau pour afficher l\'onglet Création du tableau', 'detail' => 'Cet onglet contextuel n\'apparaît que quand une cellule du tableau est sélectionnée.'],
                    ['num' => 2, 'action' => 'Cochez "Ligne des totaux"', 'detail' => 'Une ligne de totaux s\'ajoute en bas du tableau avec des menus déroulants.'],
                    ['num' => 3, 'action' => 'Cliquez sur la cellule de total d\'une colonne pour choisir le calcul', 'detail' => 'Un menu propose : Somme, Moyenne, Nombre, Max, Min, Aucun... Chaque colonne peut avoir un calcul différent.'],
                ],
            ],
            [
                'type'  => 'definition',
                'term'  => 'Références structurées dans les tableaux',
                'text'  => 'Dans un tableau nommé "Ventes", au lieu d\'écrire =SOMME(C2:C100), vous écrivez =SOMME(Ventes[Montant]). Ces références lisibles s\'adaptent automatiquement quand le tableau grandit. Excel les génère automatiquement quand vous tapez une formule à l\'intérieur d\'un tableau.',
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Nommer votre tableau',
                'text'  => 'Onglet Création du tableau → champ Nom du tableau (en haut à gauche). Donnez un nom descriptif : "Ventes2024", "ListeEmployes". Évitez les espaces et caractères spéciaux. Ce nom sera utilisé dans les formules et les TCD.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Convertir une plage en tableau avec Ctrl+T ou Insertion → Tableau',
                    'Activer la ligne des totaux et choisir le type de calcul par colonne',
                    'Appliquer un style de tableau prédéfini (onglet Création)',
                    'Renommer un tableau avec un nom descriptif',
                    'Ajouter une colonne calculée dans un tableau (la formule se propage automatiquement)',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 6 : Tri et filtres ─────────────────────────────────────
\App\Models\Lesson::where('slug', 'tri-et-filtres')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Trier et filtrer vos données',
                'text'  => 'Le tri et le filtrage sont les outils d\'analyse les plus utilisés au quotidien dans Excel. Le tri réorganise les lignes selon un ordre défini. Le filtre masque les lignes qui ne correspondent pas à vos critères, sans les supprimer.',
                'tip'   => 'Le filtre ne supprime pas les données — il les masque temporairement. Désactivez le filtre pour les retrouver.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Activer les filtres automatiques',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur n\'importe quelle cellule dans vos données', 'detail' => 'Assurez-vous que vos données ont une ligne d\'en-têtes.'],
                    ['num' => 2, 'action' => 'Appuyez sur Ctrl+Maj+L ou Onglet Données → Filtrer', 'detail' => 'Des flèches de filtre apparaissent sur chaque en-tête de colonne.'],
                    ['num' => 3, 'action' => 'Cliquez sur une flèche pour filtrer cette colonne', 'detail' => 'Une liste de valeurs uniques s\'affiche. Décochez les valeurs à masquer.'],
                    ['num' => 4, 'action' => 'Pour supprimer tous les filtres : Données → Effacer', 'detail' => 'Ou appuyez à nouveau sur Ctrl+Maj+L pour désactiver complètement les filtres.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Tri sur plusieurs colonnes',
                'items' => [
                    ['num' => 1, 'action' => 'Onglet Données → Trier', 'detail' => 'La boîte de dialogue Trier s\'ouvre avec un niveau de tri par défaut.'],
                    ['num' => 2, 'action' => 'Configurez le premier niveau : choisissez la colonne et l\'ordre', 'detail' => 'Ex: Trier par "Ville" en ordre Croissant (A→Z).'],
                    ['num' => 3, 'action' => 'Cliquez "Ajouter un niveau" pour un deuxième critère', 'detail' => 'Ex: Ensuite par "Nom" en ordre Croissant. Ainsi, dans chaque ville, les noms sont triés alphabétiquement.'],
                    ['num' => 4, 'action' => 'Cliquez OK', 'detail' => 'Excel trie d\'abord par le niveau 1, puis par le niveau 2 en cas d\'égalité.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Filtres avancés sur les nombres',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez sur la flèche de filtre d\'une colonne numérique', 'detail' => 'Une option "Filtres numériques" apparaît en plus de la liste des valeurs.'],
                    ['num' => 2, 'action' => 'Choisissez "Supérieur à...", "Entre...", "10 premiers..."', 'detail' => '"10 premiers" affiche les 10 valeurs les plus grandes (ou les N premiers selon votre choix).'],
                    ['num' => 3, 'action' => 'Pour les textes : "Filtres textuels" → "Commence par...", "Contient..."', 'detail' => 'Exemple : filtrer tous les noms commençant par "Ben".'],
                ],
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis tri et filtres',
                'rows'  => [
                    ['keys' => 'Ctrl + Maj + L',  'action' => 'Activer/désactiver les filtres automatiques'],
                    ['keys' => 'Alt + ↓',          'action' => 'Ouvrir la liste déroulante de filtre de la cellule active'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Activer/désactiver les filtres avec Ctrl+Maj+L',
                    'Filtrer par valeur spécifique en cochant/décochant dans la liste',
                    'Utiliser les filtres numériques (supérieur à, entre, 10 premiers)',
                    'Trier sur plusieurs colonnes avec des niveaux de tri',
                    'Trier par couleur de cellule ou couleur de police',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 7 : Tableaux croisés dynamiques ────────────────────────
\App\Models\Lesson::where('slug', 'tableaux-croises-dynamiques')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Les TCD — l\'outil d\'analyse le plus puissant d\'Excel',
                'text'  => 'Un Tableau Croisé Dynamique (TCD) résume, analyse et compare des milliers de lignes de données en quelques clics, sans écrire une seule formule. Il répond à des questions comme : "Quel est le chiffre d\'affaires par région et par produit ?" en quelques secondes.',
                'tip'   => 'Le TCD ne modifie pas vos données source — c\'est un résumé interactif qui se recalcule à la demande.',
            ],
            [
                'type'  => 'steps',
                'title' => 'Créer un tableau croisé dynamique',
                'items' => [
                    ['num' => 1, 'action' => 'Cliquez n\'importe où dans vos données source', 'detail' => 'Les données doivent avoir une ligne d\'en-têtes sans colonnes vides.'],
                    ['num' => 2, 'action' => 'Onglet Insertion → Tableau croisé dynamique', 'detail' => 'Excel détecte automatiquement la plage. Choisissez "Nouvelle feuille de calcul".'],
                    ['num' => 3, 'action' => 'Cliquez OK', 'detail' => 'Une nouvelle feuille s\'ouvre avec le TCD vide et le volet des champs à droite.'],
                    ['num' => 4, 'action' => 'Glissez les champs dans les zones : Lignes, Colonnes, Valeurs, Filtres', 'detail' => 'Exemple : "Région" en Lignes, "Produit" en Colonnes, "Ventes" en Valeurs → tableau croisé complet !'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Modifier les calculs dans les Valeurs',
                'items' => [
                    ['num' => 1, 'action' => 'Par défaut : Excel calcule la SOMME des valeurs numériques', 'detail' => 'Pour les textes, il fait un NB (nombre) automatiquement.'],
                    ['num' => 2, 'action' => 'Cliquez droit sur une valeur → Paramètres des champs de valeurs', 'detail' => 'Ou cliquez sur le champ dans la zone Valeurs du volet.'],
                    ['num' => 3, 'action' => 'Choisissez : Somme, Moyenne, Nb, Max, Min, % du total...', 'detail' => '"% du total général" affiche chaque valeur en pourcentage du grand total.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Grouper les dates par mois ou trimestre',
                'items' => [
                    ['num' => 1, 'action' => 'Glissez un champ date en Lignes ou Colonnes', 'detail' => 'Excel peut afficher toutes les dates individuellement — c\'est souvent trop détaillé.'],
                    ['num' => 2, 'action' => 'Clic droit sur une date dans le TCD → Grouper', 'detail' => 'Une boîte de dialogue permet de choisir le niveau de regroupement.'],
                    ['num' => 3, 'action' => 'Sélectionnez Mois, Trimestres, Années (vous pouvez en sélectionner plusieurs)', 'detail' => 'Ctrl+clic pour en sélectionner plusieurs. Ex: Mois + Années pour avoir Jan 2024, Fév 2024...'],
                ],
            ],
            [
                'type'  => 'tip',
                'title' => '💡 Segments (Slicers) — filtres visuels',
                'text'  => 'Onglet Analyse du tableau croisé dynamique → Insérer un segment. Choisissez un champ (ex: Région). Des boutons cliquables apparaissent pour filtrer interactivement le TCD sans utiliser les filtres classiques. Idéal pour les tableaux de bord.',
            ],
            [
                'type'  => 'warning',
                'title' => '⚠️ Actualiser le TCD après modification des données',
                'text'  => 'Si vous modifiez vos données source, le TCD ne se met PAS à jour automatiquement. Clic droit sur le TCD → Actualiser, ou Onglet Analyse → Actualiser. Si vous ajoutez des lignes en dehors de la plage définie, il faut aussi mettre à jour la source.',
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Créer un TCD depuis Insertion → Tableau croisé dynamique',
                    'Glisser les champs dans les bonnes zones (Lignes, Colonnes, Valeurs, Filtres)',
                    'Modifier le type de calcul des Valeurs (Somme → Moyenne, % du total...)',
                    'Grouper les dates par mois, trimestres, années',
                    'Insérer un segment (Slicer) pour des filtres visuels interactifs',
                    'Actualiser le TCD après modification des données source',
                ],
            ],
        ],
    ],
]);

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── Leçon 8 : Graphiques et visualisations ───────────────────────
\App\Models\Lesson::where('slug', 'graphiques-excel')->update([
    'content_json' => [
        'blocks' => [
            [
                'type'  => 'intro',
                'title' => 'Transformer les données en graphiques percutants',
                'text'  => 'Un graphique bien choisi communique en une seconde ce que des colonnes de chiffres ne parviennent pas à transmettre en une minute. Excel propose plus de 20 types de graphiques. Le secret : choisir le bon type selon le message à transmettre.',
            ],
            [
                'type'  => 'definition',
                'term'  => 'Quel graphique pour quel usage ?',
                'text'  => 'Barres/Histogrammes : comparer des catégories. Courbes : montrer une évolution dans le temps. Secteurs (camembert) : montrer des proportions (max 5-6 parts). Nuages de points : montrer une corrélation entre 2 variables. Combiné : superposer 2 types (ex: barres + courbe).',
            ],
            [
                'type'  => 'steps',
                'title' => 'Créer un graphique rapidement',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez les données à représenter (y compris les en-têtes)', 'detail' => 'Utilisez Ctrl+clic pour sélectionner des plages non contiguës.'],
                    ['num' => 2, 'action' => 'Appuyez sur Alt+F1 pour un graphique intégré instantané', 'detail' => 'Ou F11 pour créer le graphique dans une nouvelle feuille. Ou Onglet Insertion → choisir le type.'],
                    ['num' => 3, 'action' => 'Le graphique apparaît avec le type recommandé par Excel', 'detail' => 'Onglet Insertion → Graphiques recommandés pour que Excel suggère le meilleur type selon vos données.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Personnaliser le graphique',
                'items' => [
                    ['num' => 1, 'action' => 'Ajouter un titre : cliquez sur "Titre du graphique" et tapez', 'detail' => 'Ou : Onglet Création → Ajouter un élément de graphique → Titre du graphique.'],
                    ['num' => 2, 'action' => 'Ajouter des étiquettes de données : clic droit sur la série → Ajouter des étiquettes', 'detail' => 'Les valeurs s\'affichent directement sur les barres ou points.'],
                    ['num' => 3, 'action' => 'Changer le style : onglet Création → galerie de styles', 'detail' => 'Des dizaines de combinaisons couleurs/styles prédéfinis disponibles en un clic.'],
                    ['num' => 4, 'action' => 'Modifier le type : clic droit sur le graphique → Modifier le type', 'detail' => 'Passez de barres à courbes sans recréer le graphique.'],
                ],
            ],
            [
                'type'  => 'steps',
                'title' => 'Insérer des Sparklines (mini-graphiques)',
                'items' => [
                    ['num' => 1, 'action' => 'Sélectionnez les cellules où afficher les sparklines', 'detail' => 'Une cellule par sparkline, généralement en fin de ligne.'],
                    ['num' => 2, 'action' => 'Onglet Insertion → Sparklines → Courbe (ou Histogramme, Gain/Perte)', 'detail' => 'Ces mini-graphiques tiennent dans une seule cellule.'],
                    ['num' => 3, 'action' => 'Sélectionnez la plage de données source pour les sparklines', 'detail' => 'Ex: chaque ligne représente l\'évolution mensuelle d\'un commercial.'],
                ],
            ],
            [
                'type'  => 'shortcut_table',
                'title' => 'Raccourcis graphiques',
                'rows'  => [
                    ['keys' => 'Alt + F1',  'action' => 'Créer un graphique intégré dans la feuille actuelle'],
                    ['keys' => 'F11',       'action' => 'Créer un graphique dans une nouvelle feuille dédiée'],
                ],
            ],
            [
                'type'  => 'exam_focus',
                'title' => '🎯 Points clés pour l\'examen MOS Excel',
                'items' => [
                    'Créer un graphique depuis une plage de données sélectionnée',
                    'Choisir le bon type de graphique pour les données',
                    'Ajouter un titre, des étiquettes de données et une légende',
                    'Changer le type de graphique sans le recréer',
                    'Déplacer un graphique vers une nouvelle feuille dédiée',
                    'Insérer des Sparklines (courbe, histogramme) dans des cellules',
                ],
            ],
        ],
    ],
]);

echo "✅ Contenu de toutes les leçons Excel mis à jour avec succès !";
