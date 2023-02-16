<?php
/**
 * Controleur validFrais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    TS, Missika Tamar Menouha
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesVisiteurs=$pdo->getLesVisiteurs();
$moisA = getMois(date('d/m/Y'));
$lesMois = getLesDouzeDerniersMois($moisA);

if (!$uc) {
    $uc = 'validFrais';
}
switch ($action) {
    case 'choixVM':
        //Récupération des visiteurs BDD
        $lesCles1=array_keys($lesVisiteurs);
        $visiteurASelectionner=$lesCles1[0];
        
        //Récupération des mois BDD
        $lesCles2=array_keys($lesMois);
        $moisASelectionner=$lesCles2[0];
        
        include 'vues/v_listeVisiteursMois.php';
        break;
    
    case 'afficheFrais':
        // Récupération des données de la vue
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $moisV = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        
        // Récupération des  données BDD
        $moisASelectionner=$moisV;
        $visiteurASelectionner=$idVisiteur;
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $moisV);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $moisV);
        $condition= $pdo-> estPremierFraisMois($idVisiteur, $moisV);
        
        
        if($condition){
            ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
            include 'vues/v_erreurs.php';
            include 'vues/v_listeVisiteursMois.php';
        }
        else{
            $nbJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $moisV);
            include 'vues/v_afficheFrais.php';
        }
        break;
        
    case 'FraisForfait':
        // Récupération des données de la vue
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $moisV = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        
        // Récupération des données BDD
        $visiteurASelectionner=$idVisiteur;
        $moisASelectionner=$moisV;
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $moisV); 
        $nbJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $moisV);
        
        if (lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idVisiteur, $moisV, $lesFrais);
            ajouterErreur('La modification de vos frais forfait a bien été prise en compte');
            include 'vues/v_erreurs.php';
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $moisV);
            include 'vues/v_afficheFrais.php';
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        } 
        
        
        break;
        
    case 'FraisHorsForfait':
        // Récupération des données de la vue
        $moisV = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $dateHF=filter_input(INPUT_POST, 'dateHF', FILTER_SANITIZE_STRING);
        $montantHF=filter_input(INPUT_POST, 'montantHF', FILTER_SANITIZE_STRING);
        $libelleHF=filter_input(INPUT_POST, 'libelleHF', FILTER_SANITIZE_STRING);
        $idFHF=filter_input(INPUT_POST, 'idFHF', FILTER_SANITIZE_STRING);
        
        // Récupération des données BDD
        $visiteurASelectionner=$idVisiteur;
        $moisASelectionner=$moisV;
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $moisV);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $moisV); 
        $nbJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $moisV);
        
        if(isset($_POST['corriger'])){
            valideInfosFrais($dateHF, $libelleHF, $montantHF);
            if (nbErreurs() != 0) {
                include 'vues/v_erreurs.php';
            } else {
                 $pdo->majFraisHorsForfait(
                     $idVisiteur,
                     $moisV,
                     $libelleHF,
                     $dateHF,
                     $montantHF
                    );
            }
            ajouterErreur('La modification de vos frais hors forfait a bien été prise en compte');
            include 'vues/v_erreurs.php';
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $moisV);
            include 'vues/v_afficheFrais.php';
            
            
        }
        elseif(isset($_POST['reporterFHF'])){
            //$pdo->supprimerFraisHorsForfait($idFHF); //supprime le fhf du mois initial
            $libelleHF = 'refusé '.$libelleHF;
            $condition= $pdo-> estPremierFraisMois($idVisiteur, $moisA);
            
            if ($condition) {
                $pdo->creeNouvellesLignesFrais($idVisiteur, $moisA);
            }
            $pdo->creeFHFReporté($idVisiteur,$moisA,$libelleHF,$dateHF,$montantHF);
            ajouterErreur('Le frais a bien été reporté');
            include 'vues/v_erreurs.php';  
            include 'vues/v_afficheFrais.php';
            
        }
        break;
    case 'validerFiche': 
        $moisV = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        
        $etat='VA';
        $pdo->majEtatFicheFrais($idVisiteur, $moisV, $etat);
        
        $sommeHF=$pdo->getMontantHF($idVisiteur,$moisV);
        $totalHF=$sommeHF[0][0];
        
        $sommeFF=$pdo->getMontantFF($idVisiteur,$moisV);
        $totalFF=$sommeFF[0][0];
        
        $montantTotal=$totalHF+$totalFF;
        $pdo->majTotal($idVisiteur,$moisV,$montantTotal);
        include 'vues/v_retourAccueil.php';
        break;
}


