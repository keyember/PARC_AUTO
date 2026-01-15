<?php
require_once __DIR__ . '/config/db.php';

// Totaux
$totalVehicules      = $pdo->query("SELECT COUNT(*) AS t FROM vehicule")->fetch()['t'];
$totalProprietaires  = $pdo->query("SELECT COUNT(*) AS t FROM personne")->fetch()['t'];
$totalAmendes        = $pdo->query("SELECT SUM(montant) AS t FROM contravention")->fetch()['t'] ?? 0;
$totalEntretiens     = $pdo->query("SELECT COUNT(*) AS t FROM entretien")->fetch()['t'];

// Véhicules à risque
$sqlRisk = "
SELECT
    v.immatriculation,
    COALESCE(SUM(e.cout), 0)    AS total_entretien,
    COALESCE(SUM(c.montant), 0) AS total_amendes
FROM vehicule v
LEFT JOIN entretien e       ON e.vehicule_id = v.id
LEFT JOIN contravention c   ON c.vehicule_id = v.id
GROUP BY v.id, v.immatriculation
HAVING total_entretien > 300 AND total_amendes > 200
ORDER BY total_entretien DESC
";
$vehiculesRisque = $pdo->query($sqlRisk)->fetchAll();

// Contraventions récentes (4)
$sqlContrav = "
SELECT c.date_contravention,
       v.immatriculation,
       p.nom,
       p.prenom,
       c.montant
FROM contravention c
JOIN vehicule v ON c.vehicule_id = v.id
LEFT JOIN personne p ON c.conducteur_id = p.id
ORDER BY c.date_contravention DESC, c.id DESC
LIMIT 4
";
$contraventions = $pdo->query($sqlContrav)->fetchAll();

// 20 derniers entretiens
$sqlEnt = "
SELECT e.date_entretien,
       e.description,
       e.cout,
       v.immatriculation,
       g.nom AS garage
FROM entretien e
JOIN vehicule v ON e.vehicule_id = v.id
JOIN garage g   ON e.garage_id = g.id
ORDER BY e.date_entretien DESC, e.id DESC
LIMIT 20
";
$entretiens = $pdo->query($sqlEnt)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Parc Auto - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 space-y-8">

        <!-- Ligne de stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total véhicules -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex items-center gap-3 p-4">
                    <div class="h-10 w-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-500">
                        🚗
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                            Total Véhicules
                        </p>
                        <p class="text-3xl font-semibold text-slate-900 mt-1">
                            <?= htmlspecialchars($totalVehicules) ?>
                        </p>
                    </div>
                </div>
                <div class="h-1 bg-sky-400"></div>
            </div>

            <!-- Total propriétaires -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex items-center gap-3 p-4">
                    <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-500">
                        👤
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                            Total Propriétaires
                        </p>
                        <p class="text-3xl font-semibold text-slate-900 mt-1">
                            <?= htmlspecialchars($totalProprietaires) ?>
                        </p>
                    </div>
                </div>
                <div class="h-1 bg-emerald-400"></div>
            </div>

            <!-- Amendes -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex items-center gap-3 p-4">
                    <div class="h-10 w-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-500">
                        ⚠️
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                            Amendes en cours
                        </p>
                        <p class="text-3xl font-semibold text-slate-900 mt-1">
                            <?= number_format($totalAmendes, 1, ',', ' ') ?> k €
                        </p>
                    </div>
                </div>
                <div class="h-1 bg-rose-400"></div>
            </div>

            <!-- Entretiens -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex items-center gap-3 p-4">
                    <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
                        🛠️
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                            Entretiens
                        </p>
                        <p class="text-3xl font-semibold text-slate-900 mt-1">
                            <?= htmlspecialchars($totalEntretiens) ?>
                        </p>
                    </div>
                </div>
                <div class="h-1 bg-amber-400"></div>
            </div>
        </div>

        <!-- Bloc milieu -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Véhicules à risque -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="border-b-4 border-rose-400">
                    <div class="flex items-center justify-between px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Véhicules à Risque (>300€ Entr. & >200€ Amendes)
                        </h2>
                        <span class="text-slate-300 text-xl">🧾</span>
                    </div>
                </div>
                <div class="px-5 py-3">
                    <table class="w-full text-xs md:text-sm">
                        <thead class="text-slate-500 border-b">
                            <tr>
                                <th class="py-2 text-left font-medium">IMMATRICULATION</th>
                                <th class="py-2 text-right font-medium">COÛT ENTR.</th>
                                <th class="py-2 text-right font-medium">TOTAL AMENDES</th>
                                <th class="py-2 text-center font-medium">STATUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehiculesRisque as $v): ?>
                                <tr class="border-b last:border-0">
                                    <td class="py-2 text-slate-800">
                                        <?= htmlspecialchars($v['immatriculation']) ?>
                                    </td>
                                    <td class="py-2 text-right text-amber-600 font-semibold">
                                        <?= number_format($v['total_entretien'], 0, ',', ' ') ?> €
                                    </td>
                                    <td class="py-2 text-right text-rose-600 font-semibold">
                                        <?= number_format($v['total_amendes'], 0, ',', ' ') ?> €
                                    </td>
                                    <td class="py-2 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-[11px] font-semibold">
                                            ● Action requise
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($vehiculesRisque)): ?>
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-400">
                                        Aucun véhicule à risque.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contraventions récentes -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="border-b-4 border-amber-400">
                    <div class="flex items-center justify-between px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Contraventions Récentes
                        </h2>
                        <span class="text-slate-300 text-xl">📄</span>
                    </div>
                </div>
                <div class="px-5 py-3">
                    <table class="w-full text-xs md:text-sm">
                        <thead class="text-slate-500 border-b">
                            <tr>
                                <th class="py-2 text-left font-medium">DATE</th>
                                <th class="py-2 text-left font-medium">VÉHICULE</th>
                                <th class="py-2 text-left font-medium">CONDUCTEUR</th>
                                <th class="py-2 text-right font-medium">MONTANT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contraventions as $c): ?>
                                <tr class="border-b last:border-0">
                                    <td class="py-2 text-slate-800">
                                        <?= htmlspecialchars($c['date_contravention']) ?>
                                    </td>
                                    <td class="py-2 text-slate-800">
                                        <?= htmlspecialchars($c['immatriculation']) ?>
                                    </td>
                                    <td class="py-2 text-slate-800">
                                        <?= htmlspecialchars(trim($c['prenom'] . ' ' . $c['nom'])) ?>
                                    </td>
                                    <td class="py-2 text-right text-slate-900">
                                        <?= number_format($c['montant'], 0, ',', ' ') ?> €
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($contraventions)): ?>
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-400">
                                        Aucune contravention.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Derniers entretiens -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="border-b-4 border-amber-400">
                <div class="flex items-center justify-between px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Derniers Entretiens
                    </h2>
                    <span class="text-slate-300 text-xl">🧰</span>
                </div>
            </div>

            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs md:text-sm">
                <?php foreach ($entretiens as $e): ?>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 flex flex-col gap-1">
                        <div class="flex justify-between items-center text-[11px] text-slate-500">
                            <span class="px-2 py-0.5 rounded-full bg-white border border-slate-200 font-medium">
                                <?= htmlspecialchars($e['immatriculation']) ?>
                            </span>
                            <span><?= htmlspecialchars($e['date_entretien']) ?></span>
                        </div>
                        <p class="text-slate-900 font-semibold mt-1">
                            <?= htmlspecialchars($e['description']) ?>
                        </p>
                        <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                            <span>🏭</span><?= htmlspecialchars($e['garage']) ?>
                        </p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            <?= number_format($e['cout'], 0, ',', ' ') ?> €
                        </p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($entretiens)): ?>
                    <p class="text-slate-400 text-sm">
                        Aucun entretien trouvé.
                    </p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>

</html>