<?php
    require_once 'app/module/functions/functions.php';
    require_once 'config/config.php';
    require_once 'models/model-project-file.php';

    // Get params
    $parts = explode('-', $_GET['url']);
    $project_id = isset($parts[1]) ? $parts[1] : null;

    $db = (new Connexion())->get_connexion();

    $project_files = new Project_file($db);
    $project_files_by_project = $project_files->get_all($project_id);

    
    $title = 'Mes projets';
    $page_title = 'UAC collab | ' . $title;
    ob_start();
?>

<title><?=$page_title ?></title>
<!-- [ Main Content ] start -->


<div class="pc-container">
    <div class="pc-content pt-0 px-0">
        <div id="title_project">

        </div>
        <!-- data there -->
        <div class="container p-3">
            <div class="card mt-3">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-primary my-1">Collaborateur</button>
                            <button class="btn btn-primary my-1" data-bs-toggle="modal"
                                data-bs-target="#correctionModal">Correction</button>
                            <button class="btn btn-primary my-1"  data-bs-toggle="modal"
                            data-bs-target="#detailModal">Détails</button>
                            <button class="btn btn-primary my-1"  data-bs-toggle="modal"
                            data-bs-target="#fileModal">Voir le fichier</button>
                        </div>
                        <form class="col-xl-4 col-lg-4 col-md-6 mb-3">
                            <select class="form-select" name="" id="version">
                                <option value="">Chargement en cours...</option>
                            </select>
                        </form>
                    </div>

                </div>
                <div class="card-body">
                    <!-- showing a title of project file -->
                    <h1><span id="title_commentaire"></span></h1>
                    <div class="modal-body">
                        <div class="mb-3">
                            <textarea class="form-control" id="description" name="description" placeholder="Votre commentaire..." rows="3" required></textarea>
                        </div>


                        <button type="button" id="save_commentaire" class="btn btn-primary">Envoyer</button>
                    </div>


                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal de correction -->
<div class="modal fade" id="correctionModal" tabindex="-1" aria-labelledby="correctionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une correction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="commentaire">Commentaire</label>
                    <textarea class="form-control" id="commentaire" placeholder="Votre commentaire..." rows="3"
                        required></textarea>
                </div>

                <div class="mb-3">
                    <label for="fichier">Fichier de correction</label>
                    <input class="form-control" type="file" id="fichier" required>
                </div>

                <button id="save" class="btn btn-primary">Envoyer</button>
            </div>
        </div>
    </div>
</div>

<!-- detail du fichier -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="correctionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du fichier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="data_version_commentaire">
                <!-- data of version -->
            </div>
        </div>
    </div>
</div>

<!-- Telechargement de fichier -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="correctionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Le fichier de cette version</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="data_version_commentaire_file">
                <!-- data of version -->
            </div>
        </div>
    </div>
</div>


<?php
$page_content = ob_get_clean();
require_once 'views/includes/theme.php';
?>
<script src="./app/module/controllers/openproject.js" type="module"></script>