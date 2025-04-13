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
            case 'save':
                header('Content-Type: application/json');
                $response = [];
                try{
                    // Get data from form
                    $title = htmlspecialchars($_POST['title']);
                    $description = htmlspecialchars($_POST['description']);
                    $etudiant = htmlspecialchars($_POST['etudiant']);
                    $user_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                    $backround = Functions::generate_color();
                    $running = 0;

                    $etudiant_noms = $API->get_etudiant_id($etudiant) ?? null;
                    $etudiant_email = $API->get_etudiant_email($etudiant) ?? null;
                    $objet = "Confirmation de création de projet";
                    $content = "
                        Bonjour <b>$etudiant_noms</b>,
                        Nous avons le plaisir de vous informer que votre projet a été créé avec succès.
                        <ul>
                            <li><b>Titre</b>: $title</li>
                            <li><b>Description</b>: $description</li>
                        </ul>
                        Pour plus d’informations, cliquez sur le lien ci-dessous : <a   href='http://localhost/uacCollab/projects'>En savoir plus</a> pour en savoir plus.
                        <br>
                        <br>
                        <a style='
                            text-align: center;
                            color: white;
                            border: 0;
                            outline: 0;
                            border-radius: 5px;
                            background: #007bff;
                            padding: 5px 30px;
                            text-decoration: none;
                            padding: 10px 30px;
                            margin: 10px 0;
                        ' href='http://localhost/uacCollab/projects'>Ouvrir avec UAC collab<a>
                        <br>
                        <br>
                        Cordialement,
                        <a href='mailto:uaccolla@gmail.com'>uaccolla</a>
                    ";

                    if(! empty($title) && ! empty($description) && ! empty($etudiant)) {
                        $project->Project($title, $description, $etudiant, $user_id, $backround, $running);

                        if(! empty($project->verify())) {
                            $id_restaure = null;
                            $status = null;
                            foreach($project->verify() as $row) {
                                $id_restaure = $row->id;
                                $status = $row->status;
                            }

                            // if the status is 0, restore the pro$project
                            // else, display a message that the student has already been affected
                            if($status == '0') {
                                $project->restaure($id_restaure);
                                $_SESSION['user']['sub_role'] = 'Directeur';
                                $response['status'] = 'success';
                                $response['content'] = 'Le projet crée avec succès';
                                Functions::send_mail($etudiant_email, $etudiant_noms, $objet, $content);
                            } else {
                                $response['status'] = 'info';
                                $response['content'] = 'Cet projet a déjà été creé';
                            }
                        } else {
                            // insert the project
                            if($project->create()) {
                                $_SESSION['user']['sub_role'] = 'Directeur';
                                $response['status'] = 'success';
                                $response['content'] = 'Le projet crée avec succès';
                                $project_id = $project->get_last_project();
                                $project->create_encadreur($project_id, $user_id, true);
                                Functions::send_mail($etudiant_email, $etudiant_noms, $objet, $content);
                            } else {
                                $response['status'] = 'error';
                                $response['content'] = 'Erreur lors de l\'enregistrement de projet';
                            }
                        }
                    } else {
                        // display an error message if the fields are empty
                        $response['status'] = 'info';
                        $response['content'] = 'Veuillez compléter les champs marqués par <b class="star">*</b>';
                    }
                } catch(Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
                print json_encode($response);
            break;
            case 'load':
                $annee= $API->get_last_year();
                $encadreur_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                $result = $project->get_all($annee);
                $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                $count = false;
                $limit = 0;
                if(! empty($result)) {
                    foreach($result as $data) {
                        // Filter project for encadreurs
                        if($data->encadreur_id == $encadreur_id && $role == 'encadreur') {
                            $count = true;
                            ?>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                                    <div class="card p-0 ">
                                        <div class="card-hearder p-3 bg-img text-start " style="background: <?=$data->backgroud ?>;">
                                            <div>
                                                <div class="one-truncate"><h4 class="text-white"><?=$data->titre ?> </h4></div>
                                                <b class="text-white one-truncate"><?=$data->nom . ' ' . $data->postnom . ' ' . $data->prenom ?> </b>
                                                <small><b class="one-truncate prom"><?=$data->promotion ?></b></small>
                                            </div>
                                        </div>
                                        <div class="card-icon d-flex justify-content-end px-3">
                                            <img src="assets/etudiants/<?=$data->image ?>" class="img">
                                        </div>
                                        <div class="ml-auto card-body px-3 pt-0 pb-3" style="min-height: 11vh;">
                                            <span class="text-muted custom-truncate"><?=$data->description ?></span>
                                        </div>
                                        <div class="card-footer p-3">
                                            <a onclick="redirect('./openProjects-<?=$data->id ?>')" class="mx-2 text-primary">Ouvrir</a>
                                            <a onclick="redirect('./chat-<?=$data->id ?>')" class="mx-2 text-primary">Conversations</a>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            // Filter project for students
                        } elseif($data->id_inscription == $encadreur_id && $role == 'etudiant') {
                            $count = true;
                            $limit += 1;
                            if($limit > 1) {
                                continue;
                            }
                            ?>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                                    <div class="card p-0 ">
                                        <div class="card-hearder p-3 bg-img text-start " style="background: <?=$data->backgroud ?>;">
                                            <div>
                                                <div class="one-truncate"><h4 class="text-white"><?=$data->titre ?> </h4></div>
                                                <b class="text-white one-truncate"><?=$data->nom . ' ' . $data->postnom . ' ' . $data->prenom ?> </b>
                                                <small><b class="one-truncate prom"><?=$data->promotion ?></b></small>
                                            </div>
                                        </div>
                                        <div class="card-icon d-flex justify-content-end px-3">
                                            <img src="assets/etudiants/<?=$data->image ?>" class="img">
                                        </div>
                                        <div class="ml-auto card-body px-3 pt-0 pb-3" style="min-height: 11vh;">
                                            <span class="text-muted custom-truncate"><?=$data->description ?></span>
                                        </div>
                                        <div class="card-footer p-3">
                                            <a onclick="redirect('./openProjects-<?=$data->id ?>')" class="mx-2 text-primary">Ouvrir</a>
                                            <a onclick="redirect('./chat-<?=$data->id ?>')" class="mx-2 text-primary">Conversations</a>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    }
                }
                // Verifier si l'utilisateur connecter ne participer pas a un projet alors on va lui demander de creer un projet si possible
                if(! $count) {
                    ?>
                        <div class="container mt-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 30vh;">
                            <img src="assets/themes/data.png"
                                alt="Aucune donnée trouvée"
                                class="img-fluid mb-4"
                                style="max-width: 200px;">
                                <h4 class="text-muted fw-bold">Aucun projet trouvé.</h4>
                                <p class="text-secondary text-center">Nous n'avons trouvé aucune information correspondant à la liste de vos projets.</p>
                                <?php
                                    if($role == 'etudiant') {
                                        ?><p class="text-secondary text-center"><b class="text-primary">Veuillez contacter votre directeur.</b></p><?php
                                    } else {
                                        ?><a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalToggle" class="btn btn-primary mt-3">Créer un nouveau projet</a><?php
                                    }
                                ?>
                        </div>
                    <?php
                }
            break;
            case 'get_conversation':
                $annee = $API->get_last_year();
                $encadreur_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                $result = $project->get_all($annee);
                $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                $count = false;
                $limit = 0;
                if(! empty($result)) {
                    foreach($result as $data) {
                        $id_project = $data->id;
                        // Filter project for encadreurs
                        if($data->encadreur_id == $encadreur_id && $role == 'encadreur') {
                            $count = true;
                            ?>
                               <a onclick="redirect('./chat-<?=$data->id ?>')" class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="assets/etudiants/<?=$data->image ?>" alt="user-image"
                                                class="user-avtar">
                                        </div>
                                        <?php
                                            if(! empty($chat->get_last_conversation($data->id))) {
                                                foreach($chat->get_last_conversation($data->id) as $row) {
                                                    $auteur = '';
                                                    if($role == $row->role && $encadreur_id == $row->auteur) {
                                                        $auteur = 'Vous';
                                                    } elseif($row->role == 'encadreur') {
                                                        $auteur = $API->get_encadreur_id($row->auteur);
                                                    } elseif($row->role == 'etudiant') {
                                                        $auteur = $API->get_etudiant_id($row->auteur);
                                                    }
                                                    ?>
                                                        <div class="flex-grow-1 ms-1">
                                                            <span class="float-end text-muted"><?=Functions::local_time($row->date, $user_timezone) ?></span>
                                                            <p class="text-body mb-1"><b><?=$data->titre ?></b></p>
                                                            <?php
                                                                $count_message = $chat->count($id_project, $encadreur_id, $role);
                                                                if(! empty($count_message)) {
                                                                    ?><span class="float-end circle "><?=$count_message ?></span><?php
                                                                }
                                                            ?>
                                                            <span class="text-muted"><b><?=$auteur ?>:</b> <?=! empty($row->fichier) ? '<i class="bi bi-paperclip"></i> fichier' : $row->contenu ?></span>
                                                        </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                    <div class="flex-grow-1 ms-1">
                                                        <span class="float-end text-muted"></span>
                                                        <p class="text-body mb-1"><b><?=$data->titre ?></b></p>
                                                        <span class="text-muted"><b>Aucun message</b></span>
                                                    </div>
                                                <?php
                                            }
                                        ?>

                                    </div>
                                </a>
                            <?php
                        }

                        // Filter project for students
                        if($data->etudiant == $encadreur_id && $role == 'etudiant') {
                            $count = true;
                            $limit ++;
                            if($limit > 1) {
                                continue;
                            }
                            ?>
                               <a onclick="redirect('./chat-<?=$data->id ?>')" class="list-group-item list-group-item-action">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="assets/etudiants/<?=$data->image ?>" alt="user-image"
                                                class="user-avtar">
                                        </div>
                                        <?php
                                            if(! empty($chat->get_last_conversation($data->id))) {
                                                foreach($chat->get_last_conversation($data->id) as $row) {
                                                    $auteur = '';
                                                    if($role == $row->role && $encadreur_id == $row->auteur) {
                                                        $auteur = 'Vous';
                                                    } elseif($row->role == 'encadreur') {
                                                        $auteur = $API->get_encadreur_id($row->auteur);
                                                    } elseif($row->role == 'etudiant') {
                                                        $auteur = $API->get_etudiant_id($row->auteur);
                                                    }
                                                    ?>
                                                        <div class="flex-grow-1 ms-1">
                                                            <span class="float-end text-muted"><?=Functions::local_time($row->date, $user_timezone) ?></span>
                                                            <p class="text-body mb-1"><b><?=$data->titre ?></b></p>
                                                            <?php
                                                                $count_message = $chat->count($id_project, $encadreur_id, $role);
                                                                if(! empty($count_message)) {
                                                                    ?><span class="float-end circle "><?=$count_message ?></span><?php
                                                                }
                                                            ?>
                                                            <span class="text-muted"><b><?=$auteur ?>:</b> <?=! empty($row->fichier) ? '<i class="bi bi-paperclip"></i> fichier' : $row->contenu ?></span>
                                                        </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                    <div class="flex-grow-1 ms-1">
                                                        <span class="float-end text-muted"></span>
                                                        <p class="text-body mb-1"><b><?=$data->titre ?></b></p>
                                                        <span class="text-muted"><b>Aucun message </b></span>
                                                    </div>
                                                <?php
                                            }
                                        ?>

                                    </div>
                                </a>
                            <?php
                        }
                    }
                }
                $sub_role = ! empty($_SESSION['user']['sub_role']) ? $_SESSION['user']['sub_role'] : '';
                // Verifier si l'utilisateur connecter ne participer pas a un projet alors on va lui demander de creer un projet si possible
                if(! $count && $sub_role == 'encadreur') {
                    ?>
                        <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 30vh;">
                            <img src="assets/themes/chat.png"
                                alt="Aucune donnée trouvée"
                                class="img-fluid"
                                style="max-width: 70px;">
                                <h4 class="text-muted fw-bold">Aucune conversation trouvée.</h4>
                                <p class="text-secondary text-center">Nous n'avons trouvé aucune de vos conversations. <br> <span class="text-primary">Veuillez contacter votre directeur</span></p>
                        </div>
                    <?php
                }
            break;

            case 'get_conversation_group':
                $encadreur_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';

                ?>
                    <a onclick="redirect('./chat-0')" class="list-group-item list-group-item-action ">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="./assets/images/groupe.png" alt="user-image"
                                    class="user-avtar">
                            </div>
                            <?php
                                if(! empty($chat->get_last_conversation(0))) {
                                    foreach($chat->get_last_conversation(0) as $row) {
                                        $auteur = '';
                                        if($role == $row->role && $encadreur_id == $row->auteur) {
                                            $auteur = 'Vous';
                                        } elseif($row->role == 'encadreur') {
                                            $auteur = $API->get_encadreur_id($row->auteur);
                                        } elseif($row->role == 'etudiant') {
                                            $auteur = $API->get_etudiant_id($row->auteur);
                                        }
                                        ?>
                                            <div class="flex-grow-1 ms-1">
                                                <span class="float-end text-muted"><?=Functions::local_time($row->date, $user_timezone) ?></span>
                                                <p class="text-body mb-1"><b>Groupe</b></p>
                                                <?php
                                                    $count_message = $chat->count(0, $encadreur_id, $role);
                                                    if(! empty($count_message)) {
                                                        ?><span class="float-end circle "><?=$count_message ?></span><?php
                                                    }
                                                ?>
                                                <span class="text-muted"><b><?=$auteur ?>:</b> <?=! empty($row->fichier) ? '<i class="bi bi-paperclip"></i> fichier' : $row->contenu ?></span>
                                            </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                        <div class="flex-grow-1 ms-1">
                                            <span class="float-end text-muted text-sm"></span>
                                            <p class="text-body mb-1"><b>Groupe</b></p>
                                            <span class="text-muted text-sm">Aucun message</span>
                                        </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </a>
                <?php
            break;
            case 'get_count_convesation':
                $auteur = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                $result = $chat->count_conversation($auteur, $role);

                $is_not = 0;
                foreach($result as $data) {
                    $is_not += 1;
                }
                if(! empty($is_not) && $is_not > 0){
                    ?><small class="notification"><b><?=$is_not ?></b></small><?php
                }
            break;
        }

    }
