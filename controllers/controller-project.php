<?php
    // Require the configuration file
    require_once('../config/config.php');
    require_once '../app/module/functions/functions.php';
    require_once('../models/model-project.php');

    $database = new Connexion();
    $db = $database->get_connexion();

    session_start();

    $project = new Project($db);

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
                                $response['status'] = 'success';
                                $response['content'] = 'Le projet crée avec succès';
                            } else {
                                $response['status'] = 'info';
                                $response['content'] = 'Cet projet a déjà été creé';
                            }
                        } else {
                            // insert the pro$project
                            if($project->create()) {
                                $response['status'] = 'success';
                                $response['content'] = 'Le projet crée avec succès';

                                $project_id = $project->get_last_project();
                                $project->create_encadreur($project_id, $user_id);
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

                    print json_encode($response);
                } catch(Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;
            case 'load':
                $encadreur_id = ! empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                $result = $project->get_all();
                $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
                $count = false;
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
                                        </div>
                                    </div>
                                </div>
                            <?php
                            // Filter project for students
                        } elseif($data->id_inscription == $encadreur_id && $role == 'etudiant') {
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
        }
    }
