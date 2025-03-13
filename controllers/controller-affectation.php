<?php
    // Require the configuration file
    require_once('../config/config.php');
    require_once '../app/module/functions/functions.php';
    require_once('../models/model-affectation.php');

    $database = new Connexion();
    $db = $database->get_connexion();

    $affectation = new Affectation($db);

    if(isset($_POST['action']) && ! empty($_POST['action'])) {
        $action = htmlspecialchars($_POST['action']);

        switch($action){
            // save or update affectation
            case 'save':
                header('Content-Type: application/json');
                $response = [];
                // get date from the form
                $annee = htmlspecialchars($_POST['annee']);
                $promotion = htmlspecialchars($_POST['promotion']);
                $etudiant = htmlspecialchars($_POST['etudiant']);
                $encadreur = htmlspecialchars($_POST['encadreur']);

                // make sure the fields are not empty
                if(! empty($etudiant) && ! empty($encadreur)) {
                    $affectation->affectation($etudiant, $encadreur, $annee, $promotion);
                    // check if the affectation already exists in the database by calling the verify method
                    if(! empty($affectation->verify())) {
                        $id_restaure = null;
                        $status = null;
                        foreach($affectation->verify() as $row) {
                            $id_restaure = $row->id;
                            $status = $row->status;
                        }

                        // if the status is 0, restore the affectation
                        // else, display a message that the student has already been affected
                        if($status == '0') {
                            $affectation->restaure($id_restaure);
                            $response['status'] = 'success';
                            $response['content'] = 'Affectation enregistrée avec succès';
                        } else {
                            $response['status'] = 'info';
                            $response['content'] = 'Cet étudiant a déjà été affecté';
                        }
                    } else {
                        // insert the affectation
                        if($affectation->insert()) {
                            $response['status'] = 'success';
                            $response['content'] = 'Affectation enregistrée avec succès';
                        } else {
                            $response['status'] = 'error';
                            $response['content'] = 'Erreur lors de l\'enregistrement de l\'affectation';
                        }
                    }
                } else {
                    // display an error message if the fields are empty
                    $response['status'] = 'info';
                    $response['content'] = 'Veuillez compléter les champs marqués par <b class="star">*</b>';
                }
                print json_encode($response);
            break;
            // get all affectations
            case 'load':
                try{
                    $annee = htmlspecialchars($_POST['annee']);
                    $promotion = htmlspecialchars($_POST['promotion']);
                    $result = $affectation->get_all($annee, $promotion);
                    if(! empty($result)) {
                        $i = 1;
                        foreach($result as $row) {
                            ?>
                                <tr>
                                    <th><?=$i++ ?></th>
                                    <td><?=Functions::date_format($row->date) . ', ' . substr($row->date, 11, 5) ?></td>
                                    <td><?=$row->encadreur_noms ?></td>
                                    <td><?=$row->etudiant_noms ?></td>
                                    <td><?=$row->promotion_description ?></td>
                                    <td>
                                    <a href="" class="text-primary"><i
                                            class="bi bi-pencil-square me-3"></i></a>
                                    <a href="" class="text-danger"><i class="bi bi-trash me-3"></i></a>
                                    </td>
                                </tr>
                            <?php
                        }
                    } else {
                        ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucun résultat trouvé</td>
                            </tr>
                        <?php
                    }
                }
                catch(PDOException $e) {
                    echo $e->getMessage();
                }
            break;
        }
    }