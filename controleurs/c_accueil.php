<?php
/**
 * Gestion de l'accueil
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer, TS, Missika TM
 *
 */

$estVisiteurConnecte = estVisiteurConnecte();
$estComptableConnecte = estComptableConnecte();

if ($estVisiteurConnecte) {
include
   'vues/v_accueilVisiteur.php';
}
 elseif
   ($estComptableConnecte){
    include 'vues/v_accueilComptable.php';
} else {
   include 'vues/v_connexion.php';
}