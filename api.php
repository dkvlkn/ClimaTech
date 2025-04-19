<?php
require_once "include/functions.inc.php";

$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'regions':
        echo json_encode(getRegions());
        break;
    case 'departements':
        $region = $_GET['region'] ?? '';
        echo json_encode(getDepartements($region));
        break;
    case 'villes':
        $departement = $_GET['departement'] ?? '';
        echo json_encode(getVilles($departement));
        break;
    default:
        echo json_encode([]);
}
