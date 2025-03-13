<?php
    require_once 'app/module/functions/functions.php';
    $title = 'Mes projets';
    $page_title = 'UAC collab | ' . $title;
    ob_start();
?>

<title><?=$page_title ?></title>
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10"><?=$page_title ?></h5>
                        </div>
                    </div>
                </div>
                <div class="row" id="container">
                    <div class="col-12 justify-content-center d-flex align-items-center" style="min-height: 70vh;">
                        <h4>Chargement encours...</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<a data-bs-toggle="modal" data-bs-target="#exampleModalToggle" class="floating-btn text-white">+</a>

<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Créer un nouveau projet</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
            <label for="">Entrez le nom du projet</label>
            <input autocomplete="off" id="titre" type="text" class="form-control mt-2" placeholder="Entrez le nom du projet">
        </div>
        <div class="my-3">
            <label for="">Description</label>
            <textarea id="description" class="form-control mt-2" placeholder="Entrez la description du projet"></textarea>
        </div>
        <div class="my-3">
            <label for="">Sélectionnez un étudiant</label>
            <select id="etudiant" class="form-select mt-2">
                <option value="">MORINGA YILA BIENVENU</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button id="save" class="btn btn-primary">Créer</button>
      </div>
    </div>
  </div>
</div>


<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>
<script src="./app/module/controllers/project.js" type="module"></script>