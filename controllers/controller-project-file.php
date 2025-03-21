<?php
require_once('../config/config.php');
require_once '../app/module/functions/functions.php';
require_once('../models/model-project-file.php');
require_once('../models/model-commentaire.php');

session_start();

$database = new Connexion();
$db = $database->get_connexion();

$type = ! empty($_SESSION['user']['role']) && $_SESSION['user']['role'] != 'encadreur' ? 'soumission' : 'correction';

$project_file = new Project_file($db);
$commentaire_data = new Commentaire($db);



if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = htmlspecialchars($_POST['action']);

    switch ($action) {

        case 'save':
            header('Content-Type: application/json');
            $response = [];
            
            try {
                $commentaire = htmlspecialchars($_POST['commentaire']);
                $projet_id = htmlspecialchars($_POST['id_project']);
                $user_id = !empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

                $name_file = 'fichier' ?? null;
                $path = '../assets/projets/';
                $extension = ['doc', 'docx'];

                $version = $project_file->get_version_by_project($projet_id);

                $project_file->Project_files($projet_id,$name_file, $user_id,$commentaire, $type, $version);


                if ($project_file->create()){
                    $last_id = $db->lastInsertId();

                    $result = Functions::upload_file($name_file, $path, $last_id, $extension);

                    $insertStatus = false;

                    if ($result['success']){
                        $insertStatus = true;
                    }else{
                        $response['status'] = 'info';
                        $response['content'] = $result['message'];
                    }

                    if ($insertStatus){
                        if ($project_file->update_file($result['message'],$last_id)){
                            $response['status'] = 'success';
                            $response['content'] = 'enregistrement réussi avec succès';
                        }else{
                            $response['status'] = 'error';
                            $response['content'] = 'echec d\'envoi du fichier';
                        }
                        
                    }else{
                        $response['status'] = 'error';
                        $response['content'] = 'echec d\'envoi du fichier';
                    }
                    
                }else{
                    $response['status'] = 'error';
                        $response['content'] = 'Erreur lors de l\'enregistrement en base.';
                }
                

                echo json_encode($response);

            } catch (Exception $ex) {
                $response['status'] = 'warning';
                $response['content'] = 'Exception: ' . $ex->getMessage();
                echo json_encode($response);
            }

            break;

            // get all project_files
            case 'load':
                try {
                    $id_project = htmlspecialchars($_POST['id_project']);
                    $result = $project_file->get_all($id_project);
                    
                    if (!empty($result)) {
                        $i = 1;
                        foreach ($result as $row) {
                            ?>
                            <tr>
                                <th><?= $i++ ?></th>
                                <td><?= htmlspecialchars($row->commentaire) ?></td>
                                <td><?= Functions::date_format($row->date) . ', ' . substr($row->date, 11, 5) ?></td>
                                <td><?= htmlspecialchars($row->type) ?></td>
                                
                                <td class="text-center">
                                    <a href="#" class="text-primary"><i class="bi bi-pencil-square me-3"></i></a>
                                    <a href="#" class="text-danger"><i class="bi bi-trash me-3"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">Aucun résultat trouvé</td>
                        </tr>
                        <?php
                    }
            
                } catch (PDOException $e) {
                    echo '<tr><td colspan="6" class="text-danger">Erreur : ' . $e->getMessage() . '</td></tr>';
                }
            break;

            case 'get_title':
                try {
                    // Vérification et sécurisation des données en entrée
                    if (isset($_POST['id_project'], $_POST['version'])) {
                        $id_project = htmlspecialchars($_POST['id_project']);
                        $version = htmlspecialchars($_POST['version']);
            
                        // On récupère les résultats depuis le modèle
                        $results = $project_file->get_title($id_project, $version);
            
                        if (!empty($results)) {
                            foreach ($results as $rows) {
                                // On stocke l'ID dans la session (attention : une seule valeur si plusieurs résultats)
                                $_SESSION['file']['id'] = $rows->id;
                                $comment_list = $commentaire_data->get_by_id_file($_SESSION['file']['id'])
                                ?>
            
                                <!-- Bloc commentaire -->
                                <div class="post-container">
                                    <!-- Photo de profil + nom + date -->
                                    <div class="post-header">
                                        <img src="assets/etudiants/1.png" alt="Profil" class="avatar">
                                        <div>
                                            <h5 class="post-author">Utilisateur</h5>
                                            <small class="post-date"><?= htmlspecialchars($rows->dates) ?></small>
                                        </div>
                                    </div>

                                    <!-- Contenu de la publication -->
                                    <div class="post-content">
                                        <?= nl2br(htmlspecialchars($rows->commentaire)) ?>
                                    </div>

                                    <!-- Actions sur la publication -->
                                    <div class="post-actions">
                                        <button class="btn-action">👍 J'aime</button>
                                        <button class="btn-action">💬 Commenter</button>
                                    </div>

                                    <!-- Zone de commentaires (exemples statiques pour l'instant) -->
                                    <div class="comments-section">
                                        <?php 
                                            foreach ($comment_list as $liste){
                                                ?>
                                                    <div class="comment">
                                                        <img src="assets/etudiants/1.png" alt="Profil" class="comment-avatar">
                                                        <div class="comment-details">
                                                            <strong><?= $liste->nom . " " . $liste->prenom  ?></strong> <small><?= date('H:i', strtotime($liste->dates)) ?></small>
                                                            <p><?= $liste->contenu ?></p>
                                                            <div class="comment-actions">
                                                                <span>👍 J'aime</span>
                                                                <span>Répondre</span> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                            }
                                        ?>
                                        
                                    </div>
                                </div>


            
                                <?php
                            }
                        } else {
                            // Si aucun résultat trouvé
                            ?>
                            <div class="text-center text-muted">
                                Aucun résultat trouvé
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="text-center text-warning">
                            Paramètres manquants
                        </div>
                        <?php
                    }
            
                } catch (PDOException $e) {
                    ?>
                    <div class="text-danger text-center">
                        Erreur : <?= htmlspecialchars($e->getMessage()) ?>
                    </div>
                    <?php
                }
                break;

                case 'data_version':
                    try {
                        // Vérification et sécurisation des données en entrée
                        if (isset($_POST['id_project'], $_POST['version'])) {
                            $id_project = htmlspecialchars($_POST['id_project']);
                            $version = htmlspecialchars($_POST['version']);
                
                            // On récupère les résultats depuis le modèle
                            $resultss = $project_file->get_title($id_project, $version);
                
                            if (!empty($resultss)) {
                                foreach ($resultss as $rowss) {
                                    ?>
                                       
                                        <div class="mb-3">
                                            <label for="commentaire"><?= $rowss->commentaire ?></label>
                                            <p></p>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fichier">Type de fichier <?= $rowss->type ?></label>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fichier">Envoyer le <?= Functions::date_format($rowss->dates) . ', ' . substr($rowss->dates, 11, 5) ?></label>
                                        </div>

                                        <div class="mb-3">
                                            <label for="fichier">Version : <?= $rowss->version ?></label>
                                        </div>
                
                                    <?php
                                }
                            } else {
                                // Si aucun résultat trouvé
                                ?>
                                <div class="text-center text-muted">
                                    Aucun résultat trouvé
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="text-center text-warning">
                                Paramètres manquants
                            </div>
                            <?php
                        }
                
                    } catch (PDOException $e) {
                        ?>
                        <div class="text-danger text-center">
                            Erreur : <?= htmlspecialchars($e->getMessage()) ?>
                        </div>
                        <?php
                    }
                    break;

                    case 'data_version_file':
                        try {
                            // Vérification et sécurisation des données en entrée
                            if (isset($_POST['id_project'], $_POST['version'])) {
                                $id_project = htmlspecialchars($_POST['id_project']);
                                $version = htmlspecialchars($_POST['version']);
                    
                                // On récupère les résultats depuis le modèle
                                $resultsss = $project_file->get_title($id_project, $version);
                    
                                if (!empty($resultsss)) {
                                    foreach ($resultsss as $rowsss) {
                                        ?>
                                           
                                            <div class="mb-3">
                                                <label for="commentaire"><?= $rowsss->commentaire ?></label>
                                                <p></p>
                                            </div>
                    
                                        <?php
                                    }
                                } else {
                                    // Si aucun résultat trouvé
                                    ?>
                                    <div class="text-center text-muted">
                                        Aucun résultat trouvé
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="text-center text-warning">
                                    Paramètres manquants
                                </div>
                                <?php
                            }
                    
                        } catch (PDOException $e) {
                            ?>
                            <div class="text-danger text-center">
                                Erreur : <?= htmlspecialchars($e->getMessage()) ?>
                            </div>
                            <?php
                        }
                        break;
            
            
            case 'get_version':
                try {
                    $id_project = htmlspecialchars($_POST['id_project']);
                    $result = $project_file->get_version($id_project);
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        ?><option value="<?=$data->version ?>"><?= 'V ' . $data->version ?></option><?php
                    }
                    if(! $msg) {
                        ?><option value="">Aucun résultat trouvé</option><?php
                    }
                }
                catch (Exception $ex) {
                    // En cas d'exception, retourner un message d'avertissement avec le message de l'exception
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;

            case 'title_project_student':
                $id_project = htmlspecialchars($_POST['id_project']);
                $result = $project_file->get_project_by_id($id_project);
                if(! empty($result)) {
                    foreach($result as $data) {
                        ?>
                            <div class="p-3 bg-cover">
                                <div class="justify-content-between d-flex">
                                    <h3 class="text-white"><?= $data->titre?></h3>
                                    <span >Version number</span>
                                </div>
                                <p><b><?= $data->nom . " " . $data->postnom . " " . $data->prenom ?></b></p>
                                <b><?= $data->promotion ?></b>
                                <p><?= $data->description ?></p>
                            </div>
                            <div class="card-icon d-flex justify-content-end px-3">
                                <img src="assets/etudiants/1.png" class="img2">
                            </div>
                        <?php
                    }
                } else {
                    ?>
                        <div class="container mt-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 30vh;">
                        <img src="assets/themes/data.png"
                            alt="Aucune donnée trouvée"
                            class="img-fluid mb-4"
                            style="max-width: 200px;">
                            <h4 class="text-muted fw-bold">Aucun projet trouvé.</h4>
                            <p class="text-secondary text-center">Nous n'avons trouvé aucune information correspondant à la liste de vos projets.</p>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalToggle" class="btn btn-primary mt-3">Créer un nouveau projet</a>
                    </div>

                    <?php
                }
            break;
            
        }
    }

    if (isset($_POST['action']) && !empty($_POST['action'])) {
        $action = htmlspecialchars($_POST['action']);
    
        switch ($action) {
    
            case 'save_commentaire':
                header('Content-Type: application/json');
                $response = [];
                
                try {
                    $description = htmlspecialchars($_POST['description']);
                    $id_file = !empty($_SESSION['file']['id']) ? $_SESSION['file']['id'] : 0;
                    $user_id = !empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
                    $role = !empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;
                    $filtre = null;

                    $commentaire_data->setCommentaire($description,$filtre, $user_id,$id_file, $role);
    
    
                    if ($commentaire_data->create()){
                        $response['status'] = 'success';
                        $response['content'] = 'enregistrement réussi avec succès';
                    }else{
                        $response['status'] = 'error';
                        $response['content'] = 'echec d\'enregistrement';
                    }
                        
                    echo json_encode($response);
    
                } catch (Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception: ' . $ex->getMessage();
                    echo json_encode($response);
                }
    
                break;
                
            }
        }
