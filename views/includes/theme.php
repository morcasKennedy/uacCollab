<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords"
        content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">

    <!-- [Favicon] icon -->
    <link rel="icon" href="assets/themes/logo.png" type="image/png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="./assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="./assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="./assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="./assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="./assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="./assets/css/style-preset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/plugins/dataTables.bootstrap5.min.css">
    <?php  require_once 'style.php'; ?>

</head>

<body>

    <?php
        require_once 'nav.php';
        require_once 'header.php';
        print $page_content;
    ?>

    <div class="bs-toast toast fade position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive"
        aria-atomic="true" id="toast-example">
        <div class="toast-header">
            <i class="bi bi-bell me-2" id="icon"></i>
            <div class="me-auto fw-semibold" id="title"></div>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body text-white" id="content">
            Fruitcake chocolate bar tootsie roll gummies jelly beans cake.
        </div>
    </div>

    <script src="./assets/js/plugins/apexcharts.min.js"></script>
    <script src="./assets/js/pages/dashboard-default.js"></script>
    <script src="./assets/js/plugins/popper.min.js"></script>
    <script src="./assets/js/plugins/simplebar.min.js"></script>
    <script src="./assets/js/plugins/bootstrap.min.js"></script>
    <script src="./assets/js/fonts/custom-font.js"></script>
    <script src="./assets/js/pcoded.js"></script>
    <script src="./assets/js/plugins/feather.min.js"></script>

    <script src="./assets/js/jquery.js"></script>
    <script src="./assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="./assets/js/plugins/dataTables.bootstrap5.min.js"></script>

    <script>
    $('#base-style').DataTable();

    // [ no style ]
    $('#no-style').DataTable();

    // [ compact style ]
    $('#compact').DataTable();

    // [ hover style ]
    $('#table-style-hover').DataTable();
    </script>

<script> $(document).ready(function(){$('input[type="password"]').each(function(){let e=$(this);if(!e.parent().hasClass("password-container")){let s=$('<div class="password-container"></div>');e.before(s),s.append(e);let a=$('<i class="eye-icon bi bi-eye"></i>');s.append(a),a.on("click",function(){"password"===e.attr("type")?(e.attr("type","text"),a.removeClass("bi-eye").addClass("bi-eye-slash")):(e.attr("type","password"),a.removeClass("bi-eye-slash").addClass("bi-eye"))})}})});</script>
</body>

</html>