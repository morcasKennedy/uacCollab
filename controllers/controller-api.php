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
                    $remember = htmlspecialchars($_POST['remember']);

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
                        $expiration = time() + (30 * 24 * 60 * 60); // 30 jours pour que le setcookie expire

                        // Authenticate users (supervisors).
                        if(! empty($result)) {
                            foreach($result as $row) {
                                if($row->mot_de_passe == $password) {
                                    $last_year = $API->get_last_year();
                                    $sub_role = '';
                                    if($API->get_admin($row->id, $last_year)) {
                                        $_SESSION['user']['sub_role'] = 'Directeur';
                                        $sub_role = 'Directeur';
                                    } else {
                                        $_SESSION['user']['sub_role'] = 'encadreur';
                                        $sub_role = 'encadreur';
                                    }

                                    $_SESSION['user']['id'] = $row->id;
                                    $_SESSION['user']['role'] = 'encadreur';
                                    $_SESSION['user']['name'] = $row->nom . ' ' . $row->prenom;
                                    $_SESSION['user']['path'] = $row->image;

                                    if($remember) {
                                        setcookie('sub_role', $sub_role, $expiration, "/");
                                        setcookie('user_id', $row->id, $expiration, "/");
                                        setcookie('user_role', 'encadreur', $expiration, "/");
                                        setcookie('user_name', $row->nom . ' ' . $row->prenom, $expiration, "/");
                                        setcookie('user_path', $row->image, $expiration, "/");
                                    } else {
                                        setcookie("sub_role", "", time() - 3600, "/");
                                        setcookie("user_id", "", time() - 3600, "/");
                                        setcookie("user_role", "", time() - 3600, "/");
                                        setcookie("user_name", "", time() - 3600, "/");
                                        setcookie("user_path", "", time() - 3600, "/");
                                    }

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

                                    if($remember) {
                                        setcookie('user_id', $row->id, $expiration, "/");
                                        setcookie('user_role', 'etudiant', $expiration, "/");
                                        setcookie('user_name', $row->nom . ' ' . $row->prenom, $expiration, "/");
                                        setcookie('user_path', $row->image,$expiration, "/");
                                    } else {
                                        setcookie("user_id", "", time() - 3600, "/");
                                        setcookie("user_role", "", time() - 3600, "/");
                                        setcookie("user_name", "", time() - 3600, "/");
                                        setcookie("user_path", "", time() - 3600, "/");
                                    }

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