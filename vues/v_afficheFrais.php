<?php
/**
 * Vue Affichage de Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Missika Tamar Menouha
 * @date version 2023
 */
?>

<form method="post" 
              action="index.php?uc=validFrais&action=validerMajFraisForfait" 
              role="form">
<div class="col-md-4">
            
            <?php//liste déroulante des visiteurs?>
            
            <div class="form-group" style="display: inline-block"> 
                <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        if ($id == $visiteurASelectionner) {
                            ?>
                            <option selected value="<?php echo $id ?>">
                                <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $id ?>">
                                <?php echo $nom . ' ' . $prenom ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>
            
            <?php//liste déroulante des mois?>
            
            &nbsp;<div class="form-group" style="display: inline-block">
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($lesMois as $unMois) {
                        $mois = $unMois['mois'];
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>  
        
</div><br><br><br><br> 

<div class="row">    
    <h2 style="color:orange">&nbsp;Valider la fiche de frais</h2>
    <h3>&nbsp;&nbsp;Eléments forfaitisés</h3>
    <div class="col-md-4">  
        
            <fieldset>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?> 
                <input id="corrigerFF" name="corrigerFF" type="submit" value="Corriger" class="btn btn-success"/>  
                <input id="réinitialiserFF" name="réinitialiserFF" type="reset" value="Reinitialiser" class="btn btn-danger"/>
            </fieldset>
        
    </div>
</div>


<hr>
 

<div class="panel panel-info-me">
    <div class="panel-heading">Descriptif des éléments hors forfait </div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th>
            <th></th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant'];
            $idFHF = $unFraisHorsForfait['id'];?>
            
            
                <tr>
                    <td><input name="dateHF" type="text" id="txtDateHF" class="form-control" value="<?php echo $date ?>"></td>
                    <td><input name="libelleHF" type="text" id="txtLibelleHF" class="form-control" value="<?php echo $libelle ?>"></td>
                    <td><input name="montantHF" type="text" id="txtMontantHF" class="form-control" value="<?php echo $montant ?>">
                        <input name="idFHF" type="hidden" id="idFHF" class="form-control" value="<?php echo $idFHF ?>"></td>
                    <td><input id="corrigerFHF" name="corrigerFHF" type="submit" value="Corriger" class="btn btn-success"/>  
                        <input id="reporterFHF" name="reporterFHF" type="submit" value="Reporter" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment reporter ce frais?');"/>
            
                    </td>               
                </tr>
         
        <?php
        }
        ?>
    </table>
</div>
                    
Nombre de justificatifs: <input type="text" id="nbJust" name="nbJust" class="form-control-me" value="<?php echo $nbJustificatifs ?> "><br><br> 
    <input id="valider" name="valider" type="submit" value="Valider" class="btn btn-success"/>
</form>
    