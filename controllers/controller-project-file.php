<?php
require_once('../config/config.php');
require_once '../app/module/functions/functions.php';
require_once('../models/model-project-file.php');
require_once('../models/model-commentaire.php');
require_once('../models/model-api.php');
require_once('../models/model-project.php');

session_start();

$database = new Connexion();
$db = $database->get_connexion();

$type = ! empty($_SESSION['user']['role']) && $_SESSION['user']['role'] != 'encadreur' ? 'soumission' : 'correction';
$user_id = !empty($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$user_role = !empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;

$project_file = new Project_file($db);
$commentaire_data = new Commentaire($db);
$api = new Api($db);
$projet = new Project($db);

$user_timezone = ! empty($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : 'UTC';

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = htmlspecialchars($_POST['action']);

    switch ($action) {
        case 'save':
            header('Content-Type: application/json');
            $response = [];

            try {
                $commentaire = htmlspecialchars($_POST['commentaire']);
                $projet_id = htmlspecialchars($_POST['id_project']);
                $name_file = 'fichier' ?? null;
                $path = '../assets/projets/';
                $extension = ['doc', 'docx'];
                $newId = $project_file->count();

                // Upload fichier
                $result = Functions::upload_file($name_file, $path, $newId, $extension);
                if (!$result['success']) {
                    $response['status'] = 'info';
                    $response['content'] = $result['message'];
                    echo json_encode($response);
                    exit;
                }

                // Préparation données
                $version = $project_file->get_version_by_project($projet_id);
                $project_file->Project_files($projet_id, $name_file, $user_id, $commentaire, $type, $version);

                // Enregistrement en BDD (une seule fois)
                if ($project_file->create()) {
                    $data_email = $project_file->get_send_email_encadreur($projet_id);
                    if (!empty($data_email)) {
                        foreach ($data_email as $encadreur) {
                            $email = $encadreur->email;
                            $full_name = $encadreur->nom . ' ' . $encadreur->postnom . ' ' . $encadreur->prenom;
                            $titre_project = $encadreur->titre;

                            $subject = "Nouveau fichier ajouté au projet";
                            $body = "
                                Bonjour <strong>{$full_name}</strong>,<br><br>
                                Un nouveau fichier a été ajouté au projet #{$titre_project}.<br>
                                Merci de consulter la plateforme pour plus de détails.<br><br>
                                Cordialement,<br>
                                UAC Collab
                            ";

                            Functions::send_mail($email, $full_name, $subject, $body);
                        }
                    }

                    $response['status'] = 'success';
                    $response['content'] = 'Enregistrement réussi avec succès.';

                } else {
                    $response['status'] = 'error';
                    $response['content'] = 'Erreur lors de l\'enregistrement en base.';
                }
            } catch (Exception $ex) {
                $response['status'] = 'warning';
                $response['content'] = 'Exception: ' . $ex->getMessage();
            }

            echo json_encode($response);
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
                                <td><?= Functions::date_format($row->date) . ', ' . Functions::local_time($row->date, $user_timezone) ?></td>
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
                            ?>
                                <!-- Photo de profil + nom + date -->
                                <div class="post-header">
                                    <img src="assets/etudiants/1.png" alt="Profil" class="avatar">
                                    <div>
                                        <input type="hidden" id="id_file" value="<?=$rows->id?>">
                                        <h5 class="post-author"><?= $rows->nom . " " . $rows->prenom ?></h5>
                                        <small class="post-date"><?= Functions::date_format($rows->dates) . ', ' . Functions::local_time($rows->dates, $user_timezone) ?></small>
                                    </div>
                                </div>
                                <!-- Contenu de la publication -->
                                <div class="post-content">
                                    <?= nl2br($rows->commentaire) ?>
                                </div>
                                <hr>
                                <!-- Zone de commentaires (exemples statiques pour l'instant) -->
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
                }
            } catch (PDOException $e) {
                ?>
                    <div class="text-danger text-center">
                        Erreur : <?= $e->getMessage() ?>
                    </div>
                <?php
            }
        break;
        case 'get_comment':
            try {
                ?>
                    <div class="comments-section">
                        <?php
                            $version = htmlspecialchars($_POST['version']);
                            $comment_list = $commentaire_data->get_comment_by_version($version);
                            if(! empty($comment_list)) {
                                foreach ($comment_list as $liste){
                                    $auteur = " ";
                                    if ($liste->role == 'encadreur'){
                                        $auteur = $api->get_encadreur_id($liste->user);
                                    }elseif ($liste->role == 'etudiant'){
                                        $auteur = $api->get_etudiant_id($liste->user);
                                    }
                                    ?>
                                        <div class="comment">
                                            <img src="assets/etudiants/1.png" alt="Profil" class="comment-avatar">
                                            <div class="comment-details">
                                                <strong><?=$auteur ?></strong>
                                                <small><?= Functions::date_format($liste->dates) . ', ' . Functions::local_time($liste->dates, $user_timezone) ?></small>
                                                <p><?= $liste->contenu ?></p>
                                                <div class="comment-actions">
                                                    <span class="like" data-id="<?=$liste->id ?>">
                                                        <i class="bi bi-heart<?=$commentaire_data->toggle_like($user_id, $liste->id, $user_role)?> text-danger"></i>
                                                        J'aime
                                                        <small class="love"><?=$commentaire_data->count_like($liste->id)?></small>
                                                    </span>
                                                    <span class="reponse" data-id="<?=$liste->id?>">Répondre <?= $commentaire_data->count_reponse($liste->id) ?></span>
                                                    <!-- Nombre de réponses cliquable -->
                                                    <small class="count text-primary" data-id="<?= $liste->id ?>"></small> <br>
                                                </div>
                                                <div class="zone-reponse mt-2" id="zone-reponse-<?= $liste->id ?>" style="display: none;"></div>
                                                <!-- Formulaire de réponse (caché par défaut) -->
                                                <div id="reponse-form-<?=$liste->id?>" class="reponse-form" style="display: none;">
                                                    <textarea id="reponse-text-<?=$liste->id?>" placeholder="Écrivez votre réponse ici..." class="form-control"></textarea>
                                                    <button class="btn btn-primary btn-sm envoyer-reponse mt-2 mb-2" data-id="<?=$liste->id?>">Répondre</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                }
                            } else {
                                ?><b>Aucun commentaire disponible pour cette version</b><?php
                            }
                        ?>
                    </div>
                <?php
            } catch (\Throwable $th) {
                //throw $th;
            }
        break;

        case 'get_reponses':
            $id_parent = htmlspecialchars($_POST['id_commentaire']);
            $reponses = $commentaire_data->get_reponses_by_commentaire($id_parent);

            if ($reponses && count($reponses) > 0) {
                foreach ($reponses as $rps) {
                    if ($rps->role == 'encadreur') {
                        $auteur = $api->get_encadreur_id($rps->user);
                    } elseif ($rps->role == 'etudiant') {
                        $auteur = $api->get_etudiant_id($rps->user);
                    } else {
                        $auteur = 'Inconnu';
                    }
                    ?>
                        <div class="alert alert-secondary mb-1 text-left">
                            <strong><?= $auteur ?></strong><br>
                            <?= $rps->contenu ?><br>
                            <small><?= Functions::date_format($rps->dates) . ', ' . Functions::local_time($rps->dates, $user_timezone) ?></small>
                        </div>
                    <?php
                }
            }
        exit;

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
                                    <label for="fichier">Envoyer le <?= Functions::date_format($rowss->dates) . ', ' . Functions::local_time($rowss->dates, $user_timezone) ?></label>
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
                        Erreur : <?= $e->getMessage() ?>
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
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fileModalLabel"><?= $rowsss->commentaire ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body" >
                                        <div class="mb-3">
                                            <p class="text-muted">Ce fichier ne peut être lu ici, prière de le télécharger svp!</p>
                                            <a href="./assets/projets/<?=$rowsss->fichier?>" download class="btn btn-primary w-100">Télécharger <i class="bi bi-download mx-2"></i></a>
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
                        Erreur : <?= $e->getMessage() ?>
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
                $limit = 0;
                if(! empty($result)) {
                    foreach($result as $data) {
                        $limit ++;
                        if($limit > 1) {
                            continue;
                        }
                        ?>
                            <div class="p-3 bg-cover">
                                <div class="justify-content-between d-flex">
                                    <h3 class="text-white"><?= $data->titre?></h3>
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

            case 'save_commentaire':
                header('Content-Type: application/json');
                $response = [];

                try {
                    $description = htmlspecialchars($_POST['description']);
                    $id_file = htmlspecialchars($_POST['version']);
                    $filtre = 0;
                    if (! empty($description && $id_file)){
                        $commentaire_data->setCommentaire($description,$filtre, $user_id,$id_file, $user_role);
                        if ($commentaire_data->create()){
                            $response['status'] = 'success';
                            $response['content'] = 'enregistrement réussi avec succès';
                        }else{
                            $response['status'] = 'error';
                            $response['content'] = 'echec d\'enregistrement';
                        }
                    }else{
                        $response['status'] = 'info';
                        $response['content'] = 'Compléter le commentaire svp!';
                    }
                    echo json_encode($response);
                } catch (Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception: ' . $ex->getMessage();
                    echo json_encode($response);
                }
            break;
            case 'save_like':
                header('Content-Type: application/json');
                $response = [];
                try {
                    $commentId = htmlspecialchars($_POST['commentId']);
                    $result = $commentaire_data->verify_like_exist($user_id, $commentId, $user_role);
                    if (! empty($result)){
                        $like = 0;
                        $id = null;
                        foreach ($result as $data){
                            if ($data->likes == 0){
                                $like = 1;
                            }else{
                                $like = 0;
                            }
                            $id = $data->id;
                        }
                        $commentaire_data->set_like($id, $like);
                        $response['status'] = 'success';
                        $response['content'] = 'like :' . $like;
                    }else {
                        $commentaire_data->add_liike($user_id, $commentId, $user_role);
                        $response['status'] = 'success';
                        $response['content'] = 'success';
                    }
                    echo json_encode($response);
                } catch (Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception: ' . $ex->getMessage();
                    echo json_encode($response);
                }
            break;

            case 'envoyer-reponse':
                header('Content-Type: application/json');
                $response = [];
                try {
                    $reponse = trim(htmlspecialchars($_POST['reponse'] ?? ''));
                    $id_file = trim(htmlspecialchars($_POST['version'] ?? ''));
                    $filtre = trim(htmlspecialchars($_POST['id_commentaire'] ?? ''));
                    if (!empty($reponse) && !empty($id_file) && !empty($filtre)) {
                        // Enregistrer le commentaire
                        $commentaire_data->setCommentaire($reponse, $filtre, $user_id, $id_file, $user_role);
                        if ($commentaire_data->create()) {
                            $response['status'] = 'success';
                            $response['content'] = 'Enregistrement réussi avec succès';
                        } else {
                            $response['status'] = 'error';
                            $response['content'] = 'Échec de l\'enregistrement';
                        }
                    } else {
                        $response['status'] = 'info';
                        $response['content'] = 'Veuillez compléter tous les champs du formulaire.';
                    }
                    echo json_encode($response);
                } catch (Exception $ex) {
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception : ' . $ex->getMessage();
                    echo json_encode($response);
                }
            break;

            case 'get_encadreur':
                try {
                    $result = $api->get_encadreur();
                    $msg = false;
                    foreach($result as $data) {
                        $msg = true;
                        if($data->id == $user_id){
                            continue;
                        }
                        if ($data->id != $user_id){
                            ?>
                                <option value="<?=$data->id ?>"><?= $data->nom . " " . $data->prenom ?></option>
                            <?php
                        }
                    }
                    if(! $msg) {
                        ?>
                            <option value="">Chargement en cours...</option>
                        <?php
                    }
                }
                catch (Exception $ex) {
                    // En cas d'exception, retourner un message d'avertissement avec le message de l'exception
                    $response['status'] = 'warning';
                    $response['content'] = 'Exception ' . $ex->getMessage();
                }
            break;

            case 'save_collaborate':
                header('Content-Type: application/json');
                $response = [];

                try {
                    $encadreur = htmlspecialchars($_POST['encadreur']);
                    $id_project = htmlspecialchars($_POST['id_project']);

                    if ($projet->get_exist_encadreur_by_project($id_project, $encadreur)){
                        $response['status'] = 'info';
                        $response['content'] = 'Cet enseignant est déjà été ajouté dans ce projet';
                        print json_encode($response);
                        exit;
                    }

                    if (! empty($encadreur && $id_project)){
                        if($projet->create_encadreur($id_project, $encadreur)) {

                            $data_email = $project_file->get_send_email_encadreur_by_id( $encadreur);
                            if (!empty($data_email)) {
                                foreach ($data_email as $encadreur) {
                                    $email = $encadreur->email;
                                    $full_name = $encadreur->nom . ' ' . $encadreur->postnom . ' ' . $encadreur->prenom;

                                    $subject = "Nouveau fichier ajouté au projet";
                                    $body = "
                                        Bonjour <strong>{$full_name}</strong>,<br><br>
                                        Vous avez été ajouté comme encadreur dans un projet...<br>
                                        Merci de consulter la plateforme pour plus de détails.<br><br>
                                        Cordialement,<br>
                                        UAC Collab
                                    ";

                                    Functions::send_mail($email, $full_name, $subject, $body);
                                }
                            }
                            $response['status'] = 'success';
                            $response['content'] = 'Vous avez ajouté un collaborateur de ce projet';
                        } else {
                            $response['status'] = 'error';
                            $response['content'] = 'Erreur lors de création du collaborateur';
                        }
                    }else{
                        $response['status'] = 'info';
                        $response['content'] = 'Comptétez les champs obligatoires.';
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
