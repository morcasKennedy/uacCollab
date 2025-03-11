<?php
    $page_title = 'UAC collab | Home Page';
    ob_start();
    function generateDarkColor() {
        // Génère des valeurs entre 0 et 150 pour éviter les couleurs trop claires
        $r = rand(0, 150);
        $g = rand(0, 150);
        $b = rand(0, 150);

        // Convertit en format hexadécimal
        return sprintf("#%02X%02X%02X", $r, $g, $b);
    }
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
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start" style="background: <?=generateDarkColor();?>;">
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
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start">
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
                    </div><div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start">
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
                    </div><div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start">
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
                    </div><div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start">
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
                    </div><div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 p-2">
                        <div class="card p-0">
                            <div class="card-hearder p-3 bg-img text-start">
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

<a href="" class="floating-btn">+</a>

<script src="./app/module/controllers/home.js" type="module"></script>
<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>