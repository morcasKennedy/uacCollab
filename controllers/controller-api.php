<?php
    // Require the configuration file
    require_once('../config/config.php');
    require_once '../app/module/functions/functions.php';
    require_once('../models/model-api.php');

    session_start();

    $database = new Connexion();
    $db = $database->get_connexion();

    $API = new Api($db);

    if(isset($_POST['action']) && ! empty($_POST['action'])) {
        $action = htmlspecialchars($_POST['action']);
        switch($action){
            case 'get_annee':
                try {
                    $result = $API->get_annee();
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->id ?>"><?=$data->description ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
            case 'get_promotion':
                try {
                    $result = $API->get_promotion();
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->id ?>"><?=$data->description . ' ' . $data->description_departement ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
            case 'get_encadreur':
                try {
                    $result = $API->get_encadreur();
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->id ?>"><?=$data->nom . ' ' . $data->postnom . ' ' . $data->prenom ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
            case 'get_etudiant':
                try {
                    $annee = htmlspecialchars($_POST['annee']);
                    $promotion = htmlspecialchars($_POST['promotion']);
                    $result = $API->get_etudiant($annee, $promotion);
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->id ?>"><?=$data->nom . ' ' . $data->postnom . ' ' . $data->prenom ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
            // Log all users
            case 'login':
                header('Content-Type: application/json');
                $response = [];
                try {
                    $email = htmlspecialchars($_POST['email']);
                    $password = htmlspecialchars($_POST['password']);

                    // Check if it is an email address, a phone number, or a matriculation number.
                    $label = '';
                    if(is_numeric($email)) {
                        $label = 'Le numéro de téléphone ';
                    } elseif(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $label = 'L\'adresse email ';
                    } else {
                        $label = 'Le matricule ';
                    }

                    // Check if the user has filled in the required fields.
                    if(! empty($email) && ! empty($password)) {
                        $result = $API->log_users($email);
                        $result2 = $API->log_etudiants($email);

                        // Authenticate users (supervisors).
                        if(! empty($result)) {
                            foreach($result as $row) {
                                if($row->mot_de_passe == $password) {
                                    $last_year = $API->get_last_year();
                                    if($API->get_admin($row->id, $last_year)) {
                                        $_SESSION['user']['sub_role'] = 'Directeur';
                                    } else {
                                        $_SESSION['user']['sub_role'] = 'encadreur';
                                    }

                                    $_SESSION['user']['id'] = $row->id;
                                    $_SESSION['user']['role'] = 'encadreur';
                                    $_SESSION['user']['name'] = $row->nom . ' ' . $row->prenom;
                                    $_SESSION['user']['path'] = $row->image;
                                    $response['status'] = 'success';
                                    $response['content'] = 'Connexion reussie';

                                } else {
                                    $response['status'] = 'error';
                                    $response['content'] = 'Le mot de passe que vous avez tapé est incorrect, veuillez réessayer.';
                                }
                            }
                            // Authenticate student.
                        } elseif(! empty($result2)) {
                            foreach($result2 as $row) {
                                if($row->mot_de_passe == $password) {
                                    $_SESSION['user']['id'] = $row->id;
                                    $_SESSION['user']['role'] = 'etudiant';
                                    $_SESSION['user']['name'] = $row->nom . ' ' . $row->prenom;
                                    $_SESSION['user']['path'] = $row->image;
                                    $response['status'] = 'success';
                                    $response['content'] = 'Connexion reussie';
                                } else {
                                    $response['status'] = 'error';
                                    $response['content'] = 'Le mot de passe que vous avez tapé est incorrect, veuillez réessayer.';
                                }
                            }
                        } else {
                            $response['status'] = 'error';
                            $response['content'] = $label . ' que vous avez entré est incorrect, veuillez réessayer';
                        }
                    } else {
                        $response['status'] = 'info';
                        $response['content'] = 'Veuillez compléter les champs marqués par <b class="star">*</b>';
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
                print json_encode($response);
            break;

            // Get the students affected for a project associated with a supervisor and an academic year.
            case 'get_etudiant_by_encadreur':
                try {
                    $encadreur = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                    $year = $API->get_last_year();
                    $result = $API->get_etudiant_by_year($encadreur, $year);
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->id ?>"><?=$data->nom . ' ' . $data->postnom . ' ' . $data->prenom . ' ' . $data->promotion ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // In case of an exception, return a warning message with the exception message.
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
        }
    }