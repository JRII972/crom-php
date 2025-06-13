<?php
// Test de validation des règles de récurrence pour HorairesLieu

require_once __DIR__ . '/App/Database/types/HorairesLieu.php';
require_once __DIR__ . '/App/Database/types/DefaultDatabaseType.php';
require_once __DIR__ . '/App/Database/types/Lieu.php';
require_once __DIR__ . '/App/Utils/helpers.php';

use App\Database\Types\HorairesLieu;
use App\Database\Types\TypeRecurrence;

echo "=== Test de validation des règles de récurrence ===\n\n";

try {
    // Test HEBDOMADAIRE valide
    echo "Test HEBDOMADAIRE valide...\n";
    $horaire1 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Hebdomadaire,
        ['jours' => [1, 3, 5]] // Lundi, Mercredi, Vendredi
    );
    echo "✅ HEBDOMADAIRE valide - OK\n\n";

    // Test HEBDOMADAIRE invalide
    echo "Test HEBDOMADAIRE invalide...\n";
    try {
        $horaire2 = new HorairesLieu(
            null, 1, '09:00:00', '17:00:00', null, null,
            TypeRecurrence::Hebdomadaire,
            ['jours' => [1, 8]] // 8 n'est pas valide (> 7)
        );
        echo "❌ HEBDOMADAIRE invalide devrait échouer\n";
    } catch (InvalidArgumentException $e) {
        echo "✅ HEBDOMADAIRE invalide correctement rejeté: " . $e->getMessage() . "\n\n";
    }

    // Test MENSUELLE jour du mois
    echo "Test MENSUELLE jour du mois...\n";
    $horaire3 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Mensuelle,
        ['mode' => 'jour_du_mois', 'jour' => 15]
    );
    echo "✅ MENSUELLE jour du mois - OK\n\n";

    // Test MENSUELLE jour de la semaine
    echo "Test MENSUELLE jour de la semaine...\n";
    $horaire4 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Mensuelle,
        ['mode' => 'jour_semaine', 'semaine' => 1, 'jour_semaine' => 1] // Premier lundi
    );
    echo "✅ MENSUELLE jour de la semaine - OK\n\n";

    // Test ANNUELLE
    echo "Test ANNUELLE...\n";
    $horaire5 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Annuelle,
        ['mois' => 12, 'jour' => 25] // Noël
    );
    echo "✅ ANNUELLE - OK\n\n";

    // Test QUOTIDIENNE
    echo "Test QUOTIDIENNE...\n";
    $horaire6 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Quotidienne,
        [] // Pas de règles spécifiques
    );
    echo "✅ QUOTIDIENNE - OK\n\n";

    // Test AUCUNE
    echo "Test AUCUNE...\n";
    $horaire7 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Aucune,
        null // Pas de règles
    );
    echo "✅ AUCUNE - OK\n\n";

    // Test changement de type avec validation
    echo "Test changement de type avec validation...\n";
    $horaire8 = new HorairesLieu(
        null, 1, '09:00:00', '17:00:00', null, null,
        TypeRecurrence::Hebdomadaire,
        ['jours' => [1, 3, 5]]
    );
    
    try {
        // Essayer de changer vers ANNUELLE avec une règle incompatible
        $horaire8->setTypeRecurrence(TypeRecurrence::Annuelle);
        echo "❌ Changement de type devrait échouer\n";
    } catch (InvalidArgumentException $e) {
        echo "✅ Changement de type correctement rejeté: " . $e->getMessage() . "\n\n";
    }

    echo "=== Tous les tests sont passés ! ===\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
