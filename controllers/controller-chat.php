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

    $project = new Project($db);
    $chat = new Message($db);
    $API = new Api($db);

    if(isset($_POST['action']) && ! empty($_POST['action'])) {
        $action = htmlspecialchars($_POST['action']);
        switch($action){
            case'get_header':
                $id_project = htmlspecialchars($_POST['id_project']);
                $resultat = $project->get_by_id($id_project);
                if(! empty($resultat)) {
                    foreach($resultat as $data) {
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
                        <img src="https://img.freepik.com/vecteurs-libre/illustration-du-jeune-homme-souriant_1308-174669.jpg"
                        alt="User Avatar">
                        <div>
                            <div class="user-name">No result</div>
                            <div class="user-status">No result</div>
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
                        "wav"
                    ];

                    $size = 5 * 1024 * 1024; // 5 Mo

                    $res = Functions::upload_file($file, $folder, null, $ext, $size);
                    if($res['success']) {
                        $file = $res['message'];
                        $chat->Message($message, $file, $id_project, $user_id, $user_role);
                        if($chat->insert()) {
                            $response['status'] = 'success';
                            $response['content'] = 'Message envoye';
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
                    $resultat = $chat->get_all($id_project);
                    if(! empty($resultat)) {
                        $lastDate = null; // Variable pour stocker la dernière date affichée
                        $date = '';
                        $today = date('Y-m-d'); // Date d'aujourd'hui
                        $yesterday = date('Y-m-d', strtotime('-1 day')); // Hier
                        $dayBeforeYesterday = date('Y-m-d', strtotime('-2 days')); // Avant-hier

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

                            $time = substr($data->date, 11, 5);

                            echo "<div class='date'><strong>{$dateAffichee}</strong></div>";
                            $lastDate = $date; // Mettre à jour la dernière date affichée
                        }
                            // Si c'est le message que j'ai envoyer
                            if($data->auteur == $user_id && $data->role == $user_role) {
                                // On verifie si il y a aucun fichier
                                if($data->fichier == '') {
                                    ?>
                                        <div class="message sent">
                                            <div class="content">
                                                <div><?=$data->contenu ?></div>
                                                <!-- Affichage de l'heure d'envoi -->
                                                <div class="message-time"><?=$time ?></div>
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
                                                        <div class="message-time"><?=$time ?></div>
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
                                                        <div class="message-time"><?=$time ?></div>
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
                                                        <div class="message-time"><?=$time ?></div>
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
                                                        <div class="message-time "><?=$time ?></div>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    }

                                }
                            } else {
                                // Le message que j'ai recu
                                // Filter les auteur
                                $auteur = '';
                                if($data->role == 'encadreur') {
                                    $auteur = $API->get_encadreur_id($data->auteur);
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
                                                <div class="message-time text-muted"><?=$time ?></div>
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
                                                        <div class="message-time text-muted"><?=$time ?></div>
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
                                                        <div class="message-time text-muted"><?=$time ?></div>
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
                                                        <div class="message-time text-muted"><?=$time ?></div>
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
                                                        <div class="message-time text-muted"><?=$time ?></div>
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