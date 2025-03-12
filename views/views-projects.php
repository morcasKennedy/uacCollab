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
                            <h5 class="m-b-10"><?=$title ?></h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start" style="background: <?=Fucntions::generate_color()?>;">
                                <div>
                                    <h4 class="text-white">Title</h4>
                                    <b class="text-white">user</b>
                                </div>
                            </div>
                            <div class="card-icon d-flex justify-content-end px-3">
                                <img src="assets/themes/logo.png" class="img">
                            </div>
                            <div class="ml-auto card-body px-3 pt-2 pb-3">
                                <span class="text-muted">Lorem ipsum dolor sit ametkdkdkdkd consectetur adipisicing
                                    elit. Voluptatem, dolor.</span>
                            </div>
                            <div class="card-footer p-3">
                                <a href="" class="mx-2">Ouvrir</a>
                                <a href="" class="mx-2">Chat now</a>
                            </div>
                        </div>
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
            <input type="text" class="form-control mt-2" placeholder="Entrez le nom du projet">
        </div>
        <div class="my-3">
            <label for="">Description</label>
            <textarea class="form-control mt-2" placeholder="Entrez la description du projet"></textarea>
        </div>
        <div class="my-3">
            <label for="">Sélectionnez un étudiant</label>
            <select class="form-select mt-2">
                <option value="">MORINGA YILA BIENVENU</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Créer</button>
      </div>
    </div>
  </div>
</div>
<script src="./app/module/controllers/home.js" type="module"></script>
<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>