<?php
/**
 * Vue suivi du paiement des fiches 
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    beth sefer, TS, Missika TM
 */
 
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$mois = getMois(date('d/m/Y'));
$lesVisiteurs=$pdo->getLesVisiteursDontFicheVA();
$lesMois = $pdo->getLesMoisDontFicheVA();

if (!$uc) {
    $uc = 'suiviPaiement';
}
switch ($action) {
    case 'choixFiche':
        //Récupération des visiteurs BDD
        $lesCles1=array_keys($lesVisiteurs);
        $visiteurASelectionner=$lesCles1[0];
        
        //Récupération des mois BDD  
        $lesCles2=array_keys($lesMois);
        $moisASelectionner=$lesCles2[0];
        include 'vues/v_choixFiche.php';
        break;
    
    case 'afficheFrais':
       // Récupération des données de la vue
       $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
       $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
       
       $visiteurASelectionner=$idVisiteur;  
       $moisASelectionner = $leMois;
       $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
       $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
       $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
       
       $numAnnee = substr($leMois, 0, 4);
       $numMois = substr($leMois, 4, 2);
       
       if(!is_array($lesInfosFicheFrais)){
            ajouterErreur('Pas de fiche de frais validée pour ce visiteur ce mois');
            include 'vues/v_erreurs.php';
            include 'vues/v_choixFiche.php';
        }
        else{
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
            include 'vues/v_etatFrais.php';
            include 'vues/v_miseEnPaiement.php';
        }
        break;
    case 'paiement':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING); 
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on recupere ce qui a ete selectionné ds la liste deroulante de nummois(qui se trouve dans v_listemois).
        
        $etat='RB';
        $pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);
        ajouterErreur('La fiche a été remboursée');
        include 'vues/v_erreurs.php';  
        include 'vues/v_retourAccueil.php';
        break;
}

