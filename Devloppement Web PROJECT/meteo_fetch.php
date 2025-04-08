<?php
require_once "include/functions.inc.php";

header('Content-Type: application/json');

$region = $_GET['region'] ?? null;
$departement = $_GET['departement'] ?? null;

if ($region) {
    $departements = getDepartements($region);
    echo json_encode(['departements' => $departements]);
} elseif ($departement) {
    $villes = getVilles($departement);
    echo json_encode(['villes' => $villes]);
} else {
    echo json_encode(['error' => 'Aucun paramètre fourni']);
}
?>