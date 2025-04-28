<?php
/**
 * API JSON pour la récupération des localisations (régions, départements, villes).
 *
 * Ce script répond en JSON en fonction du paramètre `action` passé en GET :
 * - action=regions        → liste des régions
 * - action=departements   → liste des départements d'une région donnée
 * - action=villes         → liste des villes d'un département donné
 *
 * PHP version 8+
 *
 * @category API
 * @package  Climatech\API
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/api/localisations.php
 */

// Inclusion des fonctions utilitaires
require_once "include/functions.inc.php";

// Récupération de l'action demandée
$action = $_GET['action'] ?? '';

// Réponse en JSON
header('Content-Type: application/json');

// Traitement des différentes actions
switch ($action) {
    case 'regions':
        /**
         * Retourne la liste des régions disponibles
         *
         * @return string JSON
         */
        echo json_encode(getRegions());
        break;

    case 'departements':
        /**
         * Retourne les départements d'une région donnée
         *
         * @param string $_GET['region'] Nom de la région
         * @return string JSON
         */
        $region = $_GET['region'] ?? '';
        echo json_encode(getDepartements($region));
        break;

    case 'villes':
        /**
         * Retourne les villes d'un département donné
         *
         * @param string $_GET['departement'] Code ou nom du département
         * @return string JSON
         */
        $departement = $_GET['departement'] ?? '';
        echo json_encode(getVilles($departement));
        break;

    default:
        /**
         * Action non reconnue → réponse vide
         *
         * @return string JSON []
         */
        echo json_encode([]);
}
