<?php
    // Require the configuration file
    require_once('../config/config.php');
    require_once '../app/module/functions/functions.php';
    require_once('../models/model-project.php');
    require_once('../models/model-chat.php');
    require_once('../models/model-api.php');

    $database = new Connexion();
    $db = $database->get_connexion();

    session_start();

    $user_timezone = ! empty($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : 'UTC';

    $project = new Project($db);
    $chat = new Message($db);
    $API = new Api($db);

    if(isset($_POST['action']) && ! empty($_POST['action'])) {
        $action = htmlspecialchars($_POST['action']);
        switch($action){
            case'get_header':
                $id_project = htmlspecialchars($_POST['id_project']);
                $resultat = $project->get_by_id($id_project);
                $limit = 0;
                if(! empty($resultat)) {
                    foreach($resultat as $data) {
                        $limit ++;
                        if($limit > 1) {
                            continue;
                        }
                        ?>
                            <img src="assets/etudiants/<?=$data->image ?>"
                            alt="User Avatar">
                            <div>
                                <div class="user-name one-truncate"><?=$data->titre ?></div>
                                <div class="user-status one-truncate"><?=$data->description ?></div>
                            </div>
                        <?php
                    }
                } else {
                    ?>
                        <img src="./assets/images/groupe.png"
                        alt="User Avatar">
                        <div>
                            <div class="user-name">Groupe</div>
                            <div class="user-status">Actif</div>
                        </div>
                    <?php
                }

            break;
            case 'save':
                header('Content-Type: application/json');
                $response = [];
                try{
                    $message = htmlspecialchars($_POST['message']);
                    $user_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                    $user_role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                    $id_project = htmlspecialchars($_POST['id_project']);

                    // On recupere le directeur pour ajouter dans le message lors qu'il s'agit de message de groupe
                    $directeur = 0;
                    if($id_project == 0) {
                        if(! empty($_SESSION['user']['sub_role']) && $_SESSION['user']['sub_role'] == 'Directeur') {
                            $directeur = $user_id;
                        } elseif($user_role == 'etudiant' && ! empty($user_role)) {
                            $sub_id_project = '';
                            $project->Project(null, null, $user_id);
                            foreach($project->verify() as $data) {
                                $sub_id_project = $data->id;
                            }

                            foreach($API->get_admin_by_project($sub_id_project) as $data) {
                                $directeur = $data->encadreur;
                            }
                        }
                    }

                    $file = 'file' ?? '';
                    $folder = '../assets/medias/';
                    $ext = [
                        "mp4",
                        "webm",
                        "webp",
                        "jpg",
                        "jpeg",
                        "png",
                        "gif",
                        "pdf",
                        "docx",
                        "doc",
                        "csv",
                        "txt",
                        "xlsx",
                        "mp3",
                        "m4a",
                        "wav",
                        'apk'
                    ];

                    $size = 50 * 1024 * 1024; // 5 Mo


                    // if(! empty($chat->get_message_no_repondu($id_project, $directeur))) {
                    //     $response['status'] = 'info';
                    //     $response['content'] = 'exist directeur: ' . $directeur . ' project: ' . $id_project;
                    //     print json_encode($response);
                    //     exit;
                    // } else {
                    //     $response['status'] = 'info';
                    //     $response['content'] = 'n\'existe pas directeur: ' . $directeur . ' project: ' . $id_project;
                    //     print json_encode($response);
                    //     exit;
                    // }

                    $res = Functions::upload_file($file, $folder, null, $ext, $size);
                    if($res['success']) {
                        $file = $res['message'];
                        $chat->Message($message, $file, $id_project, $user_id, $user_role, $directeur);
                        if($chat->insert()) {
                            /**
                             * Apres le message envoyer, on va inserer les suivi de message pour savoir si il y a des gens
                             * Qui ont lus ou pas
                             * 1. On recuper d'abord l'etudiant pour l'ajouter dans cette liste
                             * 2. On recuper les encadreurs aussi
                             * 3. On inser dans la table qui fait cette suivie
                             */
                            $message_id = $db->lastInsertId();
                            // Get etudiant or encadreur qui participe au projet
                           if($id_project == 0) {
                                $id_encadreur = $directeur;
                                $role_enc = 'encadreur';
                                $chat->suivi_message($message_id, $id_project, $id_encadreur, $role_enc);
                                $chat->insert_suivi();
                                $resultat_etud = $project->get_student_directeur($directeur);

                           } else {
                                $resultat_etud = $project->get_student_project($id_project);
                                $resultat_enc = $project->get_users_project($id_project);
                           }
                            $id_etud = 0;
                            $id_encadreur = 0;
                            foreach($resultat_etud as $data) {
                                $id_etud = $data->etudiant;
                                $role_etud = 'etudiant';
                                $chat->suivi_message($message_id, $id_project, $id_etud, $role_etud);
                                $chat->insert_suivi();
                            }

                            if($id_project != 0) {
                                foreach($resultat_enc as $data) {
                                    $id_encadreur = $data->encadreur;
                                    $role_enc = 'encadreur';
                                    $chat->suivi_message($message_id, $id_project, $id_encadreur, $role_enc);
                                    $chat->insert_suivi();
                                }
                            }

                            $response['status'] = 'success';
                            $response['content'] = 'success';
                        } else {
                            $response['status'] = 'error';
                            $response['content'] = 'Echec d\'envoie, veuillez reesseyer';
                        }

                    } else {
                        $response['status'] = 'error';
                        $response['content'] = $res['message'];
                        print json_encode($response);
                        exit;
                    }

                } catch(Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
                print json_encode($response);
            break;
            case 'get_chat':
                try {
                    $user_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                    $user_role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                    $id_project = htmlspecialchars($_POST['id_project']);

                    // On recupere le directeur pour ajouter dans le message lors qu'il s'agit de message de groupe
                    $directeur = 0;
                    if($id_project == 0) {
                        if(! empty($_SESSION['user']['sub_role']) && $_SESSION['user']['sub_role'] == 'Directeur') {
                            $directeur = $user_id;
                        } elseif($user_role == 'etudiant' && ! empty($user_role)) {
                            $sub_id_project = '';
                            $project->Project(null, null, $user_id);
                            foreach($project->verify() as $data) {
                                $sub_id_project = $data->id;
                            }

                            foreach($API->get_admin_by_project($sub_id_project) as $data) {
                                $directeur = $data->encadreur;
                            }
                        }
                    }

                    $resultat = [];
                    if($id_project == 0 ) {
                        $resultat = $chat->get_group($id_project, $directeur);
                    } else {
                        $resultat = $chat->get_all($id_project);
                    }

                    if(! empty($resultat)) {
                        $lastDate = null; // Variable pour stocker la dernière date affichée
                        $date = '';
                        $today = date('Y-m-d'); // Date d'aujourd'hui
                        $yesterday = date('Y-m-d', strtotime('-1 day')); // Hier
                        $dayBeforeYesterday = date('Y-m-d', strtotime('-2 days')); // Avant-hier

                        $chat->set_status($id_project, $user_id, $user_role);

                        foreach($resultat as $data) {
                            $date = substr($data->date, 0, 10);

                            if ($lastDate !== $date) {
                            // Déterminer l'affichage de la date
                            if ($date === $today) {
                                $dateAffichee = "Aujourd'hui";
                            } elseif ($date === $yesterday) {
                                $dateAffichee = "Hier";
                            } elseif ($date === $dayBeforeYesterday) {
                                $dateAffichee = "Avant-hier";
                            } else {
                                $dateAffichee = Functions::date_format($date, 4); // Afficher la date normalement
                            }

                            $time = $data->date;

                            echo "<div class='date'><strong>{$dateAffichee}</strong></div>";
                            $lastDate = $date; // Mettre à jour la dernière date affichée
                        }
                        $time = $data->date;
                            // Si c'est le message que j'ai envoyer
                            if($data->auteur == $user_id && $data->role == $user_role) {
                                // On verifie si il y a aucun fichier
                                if($data->fichier == '') {
                                    ?>
                                        <div class="message sent">
                                            <div class="content">
                                                <div><?=$data->contenu ?></div>
                                                <!-- Affichage de l'heure d'envoi -->
                                                <div class="message-time"><?=Functions::local_time($time, $user_timezone) ?><i class="bi bi-check2<?=$chat->message_read($data->id) ?>"></i></div>
                                            </div>
                                        </div>
                                    <?php
                                } else {
                                    // Vérifier si un fichier est présent
                                    if (!empty($data->fichier)) {
                                        // Récupérer l'extension du fichier
                                        $extension = pathinfo($data->fichier, PATHINFO_EXTENSION);
                                        // Vérifier le type de fichier
                                        if (in_array($extension, ['webm', 'mp4'])) {
                                            // Vidéo
                                            ?>
                                                <div class="message sent">
                                                    <div class="content content-img " >

                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <img   src="assets/themes/videos.png" class="bg-video">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-outline-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-outline-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time"><?=Functions::local_time($time, $user_timezone) ?> <i class="bi bi-check2<?=$chat->message_read($data->id) ?>"></i></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } elseif (in_array($extension, ['webp', 'jpg', 'jpeg', 'png', 'gif'])) {
                                            // Image
                                            ?>
                                                <div class="message sent">
                                                    <div class="content content-img " >
                                                        <img src="assets/medias/<?=$data->fichier ?>" alt="">
                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-outline-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-outline-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time"><?=Functions::local_time($time, $user_timezone) ?> <i class="bi bi-check2<?=$chat->message_read($data->id) ?>"></i></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } elseif(in_array($extension, ["mp3","m4a","wav"])) {
                                            ?>
                                                <div class="message sent">
                                                    <div class="content content-img" >
                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <img   src="assets/themes/audio.png" class="bg-video">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-outline-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-outline-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time"><?=Functions::local_time($time, $user_timezone) ?> <i class="bi bi-check2<?=$chat->message_read($data->id) ?>"></i></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } else {
                                            // Autres fichiers
                                            ?>
                                                <div class="message sent">
                                                    <div class="content content-document" >
                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-outline-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-outline-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time "><?=Functions::local_time($time, $user_timezone) ?> <i class="bi bi-check2<?=$chat->message_read($data->id) ?>"></i></div>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    }

                                }
                            } else {
                                // Le message que j'ai recu
                                $last_year = $API->get_last_year();
                                // Filter les auteur
                                $sub_role = ! empty($_SESSION['user']['sub_role']) ? $_SESSION['user']['sub_role'] : '';
                                $auteur = '';
                                if($data->role == 'encadreur') {
                                    $admin = $API->get_admin($data->auteur, $last_year) > 0 ? 'Admin' : 'Encadreur';
                                    $auteur = $API->get_encadreur_id($data->auteur). ' ' . '<small class="text-dark">'. $admin . '</small>';
                                } elseif($data->role == 'etudiant') {
                                    $auteur = $API->get_etudiant_id($data->auteur);
                                } else {
                                    $auteur = 'Inconu';
                                }
                                // On verifie si il y a aucun fichier
                                if($data->fichier == '') {
                                    ?>
                                        <div class="message received">
                                            <div class="content">
                                            <div class="user-name user-desc"><?=$auteur ?></div>
                                                <div><?=$data->contenu ?></div>
                                                <!-- Affichage de l'heure d'envoi -->
                                                <div class="message-time text-muted"><?=Functions::local_time($time, $user_timezone) ?></div>
                                            </div>
                                        </div>
                                    <?php
                                } else {
                                    // Vérifier si un fichier est présent
                                    if (!empty($data->fichier)) {
                                        // Récupérer l'extension du fichier
                                        $extension = pathinfo($data->fichier, PATHINFO_EXTENSION);
                                        // Vérifier le type de fichier
                                        if (in_array($extension, ['webm', 'mp4'])) {
                                            // Vidéo
                                            ?>
                                                <div class="message received">
                                                    <div class="content content-img " >
                                                    <div class="user-name user-desc mb-2"><?=$auteur ?></div>

                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <img   src="assets/themes/videos.png" class="bg-video">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time text-muted"><?=Functions::local_time($time, $user_timezone) ?></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } elseif (in_array($extension, ['webp', 'jpg', 'jpeg', 'png', 'gif'])) {
                                            // Image
                                            ?>
                                                <div class="message received">
                                                    <div class="content content-img" >
                                                        <img src="assets/medias/<?=$data->fichier ?>" alt="">
                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time text-muted"><?=Functions::local_time($time, $user_timezone) ?></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } elseif(in_array($extension, ["mp3","m4a","wav"])) {
                                            ?>
                                                <div class="message received">
                                                    <div class="content content-img" >
                                                        <div class="justify-content-between align-items-center shadow p-2 mt-2">
                                                            <img   src="assets/themes/audio.png" class="bg-video">
                                                            <span class="one-truncate"><?=$data->fichier ?></span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time text-muted"><?=Functions::local_time($time, $user_timezone) ?></div>
                                                    </div>
                                                </div>
                                            <?php
                                        } else {
                                            // Autres fichiers
                                            ?>
                                                <div class="message received">
                                                    <div class="content content-document" >
                                                        <div class="user-name user-desc"><?=$auteur ?></div>
                                                        <div class="justify-content-between align-items-center shadow mt-2 p-3">
                                                            <span>
                                                                <b class="one-truncate"><?=$data->fichier ?></b>
                                                            </span>
                                                            <a href="assets/medias/<?=$data->fichier ?>"  class=" btn btn-sm btn-light my-2 mx-1">Ouvrir <i class="bi bi-eye mx-1"></i></a>
                                                            <a href="assets/medias/<?=$data->fichier ?>" download="" class=" btn btn-sm btn-light my-2 mx-1">Télécharger <i class="bi bi-download mx-1"></i></a>
                                                            <div class="mt-2"><?=$data->contenu ?></div>
                                                        </div>
                                                        <div class="message-time text-muted"><?=Functions::local_time($time, $user_timezone) ?></div>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        ?>
                            <div class=" d-flex align-items-center justify-content-center" style="min-height: 50vh;">
                                <div class="text-center">
                                    <img src="assets/themes/chat.png"
                                    alt="Aucune donnée trouvée"
                                    class="img-fluid"
                                    style="max-width: 100px;">
                                    <h4 class="text-muted fw-bold">Aucun message.</h4>
                                    <p class="text-secondary text-center">Nous n'avons trouvé aucune de vos conversations.</p>
                                </div>
                            </div>
                        <?php
                    }
                } catch(Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
        }
    }