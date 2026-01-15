<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Parc Auto - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

</body>

</html>

<?php

// Usage : php generate_data.php > seed_parc_auto.sql

$nbPersonnes      = 100;
$nbCommunes       = 20;
$nbGarages        = 40;
$nbVehicules      = 120;
$nbEntretiens     = 120;
$nbAssurances     = 120;
$nbContraventions = 150;

$nomFamilles = ['Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau', 'Laurent', 'Simon', 'Michel', 'Lefebvre', 'Leroy', 'Roux', 'David', 'Bertrand', 'Morel', 'Fournier', 'Girard'];
$prenoms = ['Jean', 'Marie', 'Pierre', 'Luc', 'Julie', 'Sophie', 'Paul', 'Claire', 'Nicolas', 'Emma', 'Lucas', 'Laura', 'Hugo', 'Manon', 'Thomas', 'Camille', 'Louis', 'Sarah', 'Antoine', 'Chloe'];

$marquesModeles = [
    ['Renault', 'Clio'],
    ['Renault', 'Megane'],
    ['Peugeot', '208'],
    ['Peugeot', '308'],
    ['Citroen', 'C3'],
    ['Citroen', 'C4'],
    ['Tesla', 'Model 3'],
    ['Tesla', 'Model Y'],
    ['Dacia', 'Duster'],
    ['Volkswagen', 'Golf'],
    ['Volkswagen', 'Polo'],
    ['Toyota', 'Yaris'],
    ['Toyota', 'Corolla'],
];

$energies = ['Essence', 'Diesel', 'Electrique', 'Hybride', 'GPL'];

$assureurs = ['MAIF', 'AXA', 'Matmut', 'GMF', 'Allianz', 'MACIF', 'Groupama'];

$communeNomsBase = [
    'Chaumont',
    'Saint-Dizier',
    'Joinville',
    'Langres',
    'Reims',
    'Troyes',
    'Nancy',
    'Metz',
    'Bar-le-Duc',
    'Chateauvillain',
    'Vitry-le-Francois',
    'Nogent',
    'Wassy',
    'Bourbonne',
    'Montier-en-Der'
];

// Noms types de garages réalistes (indépendants, centres auto, ateliers)
$garagePrefixes = [
    'Garage du Centre',
    'Garage de la Gare',
    'Garage des Acacias',
    'Garage Saint-Christophe',
    'Atelier Auto Service',
    'Atelier Mécanique',
    'Centre Auto 2000',
    'Top Auto Services',
    'Garage de la Plaine',
    'Carrosserie du Parc',
    'Meca Plus',
    'Auto Rapide',
    'Garage des 4 Routes',
    'Garage de la Vallée',
    'Atelier du Pont',
    'Garage des Nations'
];

// Types d’entretien réalistes
$typesEntretiens = [
    'Révision périodique avec vidange et changement du filtre à huile',
    'Vidange moteur + contrôle des niveaux et filtres',
    'Remplacement des plaquettes et contrôle des disques de frein',
    'Changement des pneus avant avec équilibrage',
    'Changement des pneus arrière avec géométrie',
    'Contrôle technique complet',
    'Pré-contrôle technique et corrections mineures',
    'Remplacement de la courroie de distribution',
    'Remplacement batterie et contrôle du circuit de charge',
    'Diagnostic électronique et effacement des défauts',
    'Recharge climatisation et contrôle d’étanchéité',
    'Remplacement des amortisseurs avant',
    'Remplacement des amortisseurs arrière',
    'Remplacement des bougies et filtre à air',
    'Remplacement du kit embrayage',
    'Contrôle du système de freinage et purge du liquide',
];

// Helpers
function randTel()
{
    $t = '0';
    for ($i = 0; $i < 9; $i++) $t .= rand(0, 9);
    return $t;
}

function randDate($yearStart = 2022, $yearEnd = 2024)
{
    $y = rand($yearStart, $yearEnd);
    $m = rand(1, 12);
    $d = rand(1, 28); // simplification
    return sprintf('%04d-%02d-%02d', $y, $m, $d);
}

function randImmat()
{
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return $letters[rand(0, 25)] . $letters[rand(0, 25)] . '-' .
        rand(100, 999) . '-' .
        $letters[rand(0, 25)] . $letters[rand(0, 25)];
}

// Début du script SQL
echo "-- Jeu de données parc_auto généré automatiquement\n\n";

// PERSONNE
echo "-- PERSONNES\n";
for ($i = 1; $i <= $nbPersonnes; $i++) {
    global $nomFamilles, $prenoms;
    $nom    = $nomFamilles[array_rand($nomFamilles)];
    $prenom = $prenoms[array_rand($prenoms)];
    $tel    = randTel();
    echo "INSERT INTO personne (nom, prenom, tel) VALUES ('" .
        addslashes($nom) . "', '" .
        addslashes($prenom) . "', '" .
        addslashes($tel) . "');\n";
}
echo "\n";

// COMMUNE
echo "-- COMMUNES\n";
for ($i = 1; $i <= $nbCommunes; $i++) {
    global $communeNomsBase;
    $base = $communeNomsBase[array_rand($communeNomsBase)];
    $suffix = rand(1, 50);
    $nom = $base . '-' . $suffix;
    echo "INSERT INTO commune (nom) VALUES ('" . addslashes($nom) . "');\n";
}
echo "\n";

// GARAGE
echo "-- GARAGES\n";
for ($i = 1; $i <= $nbGarages; $i++) {
    global $garagePrefixes, $nbCommunes;
    $prefix = $garagePrefixes[array_rand($garagePrefixes)];
    // On ajoute le nom de la commune pour varier
    $communeId = rand(1, $nbCommunes);
    $nom = $prefix . ' ' . $communeId;
    echo "INSERT INTO garage (nom, commune_id) VALUES ('" .
        addslashes($nom) . "', " . $communeId . ");\n";
}
echo "\n";

// VEHICULE
echo "-- VEHICULES\n";
for ($i = 1; $i <= $nbVehicules; $i++) {
    global $marquesModeles, $energies, $nbPersonnes;
    $mm = $marquesModeles[array_rand($marquesModeles)];
    $marque = $mm[0];
    $modele = $mm[1];
    $energie = $energies[array_rand($energies)];
    $annee = rand(2008, 2024);
    $immat = randImmat();
    $proprioId = rand(1, $nbPersonnes);

    echo "INSERT INTO vehicule (immatriculation, marque, modele, energie, annee, proprietaire_id) VALUES ('" .
        addslashes($immat) . "', '" .
        addslashes($marque) . "', '" .
        addslashes($modele) . "', '" .
        addslashes($energie) . "', " .
        $annee . ", " . $proprioId . ");\n";
}
echo "\n";

// ENTRETIEN
echo "-- ENTRETIENS\n";
for ($i = 1; $i <= $nbEntretiens; $i++) {
    global $typesEntretiens, $nbVehicules, $nbGarages;
    $vehiculeId = rand(1, $nbVehicules);
    $garageId   = rand(1, $nbGarages);
    $date       = randDate(2022, 2024);
    $desc       = $typesEntretiens[array_rand($typesEntretiens)];
    // Coût cohérent selon le type (large plage simple)
    $coutMinMax = [
        80,
        250   // vidange, révision
    ];
    $cout = rand(80, 900);
    echo "INSERT INTO entretien (vehicule_id, garage_id, date_entretien, description, cout) VALUES (" .
        $vehiculeId . ", " . $garageId . ", '" .
        $date . "', '" . addslashes($desc) . "', " .
        $cout . ".00);\n";
}
echo "\n";

// ASSURANCE
echo "-- ASSURANCES\n";
for ($i = 1; $i <= $nbAssurances; $i++) {
    global $assureurs, $nbVehicules;
    $vehiculeId = rand(1, $nbVehicules);
    $assureur   = $assureurs[array_rand($assureurs)];
    $numero     = "POL-" . date('Y') . "-" . sprintf('%04d', $i);

    $deb = randDate(2023, 2024);
    // fin 1 an plus tard (simplifié)
    $yearFin = intval(substr($deb, 0, 4)) + 1;
    $fin = $yearFin . substr($deb, 4);

    echo "INSERT INTO assurance (numero, assureur, date_debut, date_fin, vehicule_id) VALUES ('" .
        addslashes($numero) . "', '" .
        addslashes($assureur) . "', '" .
        $deb . "', '" .
        $fin . "', " .
        $vehiculeId . ");\n";
}
echo "\n";

// CONTRAVENTION
echo "-- CONTRAVENTIONS\n";
$montantsPossibles = [45.00, 68.00, 90.00, 135.00];
for ($i = 1; $i <= $nbContraventions; $i++) {
    global $nbVehicules, $nbPersonnes, $nbCommunes, $montantsPossibles;
    $vehiculeId   = rand(1, $nbVehicules);
    $conducteurId = rand(1, $nbPersonnes);
    $lieuId       = rand(1, $nbCommunes);
    $date         = randDate(2023, 2024);
    $montant      = $montantsPossibles[array_rand($montantsPossibles)];

    echo "INSERT INTO contravention (date_contravention, montant, vehicule_id, conducteur_id, lieu_id) VALUES ('" .
        $date . "', " .
        number_format($montant, 2, '.', '') . ", " .
        $vehiculeId . ", " .
        $conducteurId . ", " .
        $lieuId . ");\n";
}
echo "\n";
