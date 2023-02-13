<?php
/**
 * Controleur validFrais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    TS, Missika Tamar Menouha
 * @date version 2023
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$mois = getMois(date('d/m/Y'));
$moisPrecedent = getMoisPrecedent($mois);

if (!$uc) {
    $uc = 'validFrais';
}
switch ($action) {
    case 'choixVM':
        //Récupération des visiteurs BDD
        $lesVisiteurs=$pdo->getLesVisiteurs();
        $lesCles1=array_keys($lesVisiteurs);
        $visiteurASelectionner=$lesCles1[0];
        
        //Récupération des mois BDD
        $lesMois = getLesDouzeDerniersMois($mois);
        $lesCles2=array_keys($lesMois);
        $moisASelectionner=$lesCles2[0];
        
        include 'vues/v_listeVisiteursMois.php';
        break;
    
    case 'afficheFrais':
        // Récupération des données de la vue
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        
        //Récupération des visiteurs BDD
        $lesVisiteurs=$pdo->getLesVisiteurs();
        $visiteurASelectionner=$idVisiteur;

        //Récupération des mois BDD
        $lesMois = getLesDouzeDerniersMois($mois);
        $moisASelectionner=$leMois;
        
        // Récupération des autres données BDD
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        //$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $nbJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $leMois);
        $condition= $pdo-> estPremierFraisMois($idVisiteur, $leMois);
        
        if($condition){
            ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
            include 'vues/v_erreurs.php';
            include 'vues/v_listeVisiteursMois.php';
        }
        else{
            //include 'vues/v_afficheFrais.php';
            include 'vues/v_aF.php';
        }
        break;
        
    case 'validerMajFraisForfait':
        // Récupération des données de la vue
        $dateHF=filter_input(INPUT_POST, 'dateHF', FILTER_SANITIZE_STRING);
        $montantHF=filter_input(INPUT_POST, 'montantHF', FILTER_SANITIZE_STRING);
        $libelleHF=filter_input(INPUT_POST, 'libelleHF', FILTER_SANITIZE_STRING);
        $idFHF=filter_input(INPUT_POST, 'idFHF', FILTER_SANITIZE_STRING);
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        
        // Récupération des données BDD
        $lesVisiteurs=$pdo->getLesVisiteurs();
        $visiteurASelectionner=$idVisiteur;
        $lesMois = getLesDouzeDerniersMois($mois);
        $moisASelectionner=$leMois;
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois); 
        
        if(isset($_POST['corrigerFF'])){
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
                ajouterErreur('La modification a bien été prise en compte');
                include 'vues/v_erreurs.php';  
            } else {
                ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
            } 
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
            //include 'vues/v_afficheFrais.php';
            include 'vues/v_aF.php';
            
        }
        elseif(isset($_POST['corrigerFHF'])){
            valideInfosFrais($dateHF, $libelleHF, $montantHF);
            if (nbErreurs() != 0) {
                include 'vues/v_erreurs.php';
            } else {
                 $pdo->majFraisHorsForfait($idVisiteur,$leMois,$libelleHF,$dateHF, $montantHF);
            }
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
            ajouterErreur('La modification a bien été prise en compte');
            include 'vues/v_erreurs.php';  
            //include 'vues/v_afficheFrais.php';
            include 'vues/v_aF.php';
            
        }
        elseif(isset($_POST['reporterFHF'])){
            $leMoisA=getMois(date('d/m/Y'));
            $pdo->supprimerFraisHorsForfait($idFHF); //supprime le fhf du mois initial
            $libelleHF = 'refusé '.$libelleHF;
            $pdo->creeFHFReporté($idVisiteur,$leMoisA,$libelleHF,$dateHF,$montantHF);
            ajouterErreur('Le frais a bien été reporté');
            include 'vues/v_erreurs.php';  
            //include 'vues/v_afficheFrais.php';
            include 'vues/v_aF.php';
            
        }
        elseif(isset($_POST['valider'])){
            $etat='VA';
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);
            $pdo->majNbJustificatifs($idVisiteur, $leMois, $nbJustificatifs);
            $sommeHF=$pdo->montantHF($idVisiteur,$leMois);
            $totalHF=$sommeHF[0][0];
            $sommeFF=$pdo->montantFF($idVisiteur,$leMois);
            $totalFF=$sommeFF[0][0];
            $montantTotal=$totalHF+$totalFF;
            $pdo->total($idVisiteur,$leMois,$montantTotal);
            include 'vues/v_retourAccueil.php';
        }         
        break;
}


