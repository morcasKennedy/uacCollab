<?php
    $title = 'Affectation des étudiants';
    $page_title = 'UAC collab | ' . $title;

    // Get params
    $parts = explode('-', $_GET['url']);
    $annee = isset($parts[1]) ? $parts[1] : null;
    $promotion = isset($parts[2]) ? $parts[2] : null;

    session_start();
    $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
    if(empty($_SESSION['user']['id']) OR ! isset($_SESSION['user']['id'])) {
        header('location:./login');
        exit;
    }
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
                <div class="row mt-3">
                    <!-- Base Style table start -->
                    <div class="col-sm-12">
                        <?php
                            if(! empty($annee) && ! empty($promotion)) {
                                ?>
                                    <div class="card">
                                        <div class="card-header py-3">
                                            <button data-bs-toggle="modal" data-bs-target="#exampleModalToggle"
                                                class="btn btn-primary">Nouvelle affectation</button>
                                        </div>
                                        <div class="card-body">
                                            <div class="justify-content-end d-flex">
                                            <form class="input-search col-xl-4 col-lg-4 col-md-6 mb-3">
                                                <i data-feather="search" class="icon-search"></i>
                                                <input autocomplete="off" id="search" type="search" placeholder="Recherche. . .">
                                            </form>
                                            </div>
                                            <div class="table-responsive">
                                                <table  class="table table-striped table-bordered table-sm nowrap">
                                                    <thead class="pt-3">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Date & Heure</th>
                                                            <th>Encadreur</th>
                                                            <th>Etudiant</th>
                                                            <th>Promotion</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="container"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            } else {
                                ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <div>
                                                <label for="">Sélectionnez l'année académique</label>
                                                <select id="annee" class="form-select mt-2">
                                                    <option value="" selected disabled>Chargement encours...</option>
                                                </select>
                                            </div>
                                            <div class="my-3">
                                                <label for="">Sélectionnez une promotion</label>
                                                <select id="promotion" class="form-select mt-2">
                                                    <option value="" selected disabled>Chargement encours...</option>
                                                </select>
                                            </div>
                                            <div class="my-3 text-end">
                                                <button class="btn btn-primary" id="next">Suivant</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
    tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Affectation d'un étudiant</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="">Sélectionnez l'enseigant</label>
                    <select id="encadreur" class="form-select mt-2">
                        <option value="" selected disabled>Chargement encours...</option>
                    </select>
                </div>
                <div class="my-4">
                    <label for="">Sélectionnez un étudiant</label>
                    <select id="etudiant" class="form-select mt-2">
                        <option value="" selected disabled>Chargement encours...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="save" class="btn btn-primary" > Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script src="./app/module/controllers/affectation.js" type="module"></script>
<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>