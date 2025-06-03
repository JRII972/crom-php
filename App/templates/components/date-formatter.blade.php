{{-- Composant pour formater les dates avec Carbon --}}
@php
use Carbon\Carbon;

$dateObj = null;

// Si $date est une chaîne, la convertir en objet Carbon
if (isset($date)) {
    if (is_string($date)) {
        $dateObj = Carbon::parse($date);
    } elseif ($date instanceof Carbon) {
        $dateObj = $date;
    }
}

// Format par défaut si non spécifié
$displayFormat = $format ?? 'j F Y';

// Si format personnalisé avec 'age'
$formattedDate = '';
if ($dateObj) {
    if (strpos($displayFormat, 'age') !== false) {
        // Calculer l'âge
        $age = $dateObj->age;
        // Remplacer 'age' par l'âge calculé
        $formattedDate = str_replace('age', $age, $displayFormat);
        // Formater le reste de la date
        $formattedDate = $dateObj->translatedFormat(str_replace('age ans', '', $formattedDate));
        // Réinsérer l'âge
        $formattedDate = str_replace($age, $age . ' ans', $formattedDate);
    } else {
        // Format standard
        $formattedDate = $dateObj->translatedFormat($displayFormat);
    }
}
@endphp

{!! $formattedDate !!}
