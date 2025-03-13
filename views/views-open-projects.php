<?php
    require_once 'app/module/functions/functions.php';
    $title = 'Mes projets';
    $page_title = 'UAC collab | ' . $title;
    ob_start();
?>

<title><?=$page_title ?></title>
<!-- [ Main Content ] start -->


<div class="pc-container">
    <div class="pc-content pt-0 px-0">
        <!-- [ breadcrumb ] start -->
        <div class="p-3 bg-cover">
           <div class="justify-content-between d-flex">
                <h3 class="text-white">Project name</h3>
                <span>Version number</span>
           </div>
            <p><b>User name</b></p>
            <b>Promotion</b>
            <p>Description of project</p>
        </div>
        <div class="card-icon d-flex justify-content-end px-3">
            <img src="assets/etudiants/1.png" class="img2">
        </div>
        <div class="container p-3">
            <div class="card mt-3">
                <div class="card-header py-3">
                    <button class="btn btn-primary my-1">Action 1</button>
                    <button class="btn btn-primary my-1">Action 2</button>
                    <button class="btn btn-primary my-1">Action 3</button>
                    <button class="btn btn-primary my-1">Action 4</button>
                </div>
                <div class="card-body">
                    <div class="justify-content-end d-flex">
                        <form class="input-search col-xl-4 col-lg-4 col-md-6 mb-3">
                            <i data-feather="search" class="icon-search"></i>
                            <input autocomplete="off" id="search" type="search" placeholder="Recherche. . .">
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm nowrap">
                            <thead class="pt-3">
                                <tr>
                                    <th>#</th>
                                    <th>Col 1</th>
                                    <th>Col 2</th>
                                    <th>Col 3</th>
                                    <th>Col 4</th>
                                    <th class="text-center">Col x</th>
                                </tr>
                            </thead>
                            <tbody id=""></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>