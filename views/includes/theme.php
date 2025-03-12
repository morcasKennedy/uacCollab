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


    <style>
    .img-collab {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 10px;
    }

    .floating-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        position: fixed;
        background: #3498DB;
        color: #fff;
        bottom: 20px;
        right: 20px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: bold;
    }

    .bg-img {
        /* background: green; */
        width: 100%;
        min-height: 80px;
        background-position: center;
        background-size: cover;
        border-radius: 6px;
        display: flex;
        color: #fff;
        /* background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/themes/uac.png'); */
    }

    .img {
        margin-top: -30px;
        width: 60px;
        height: 60px;
        border: 2px solid #007bff;
        border-radius: 50px;
        cursor: pointer;
    }

    .logout-btn {
        width: 100%;
        bottom: 0;
        text-align: center;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px 0;
        background: #3498DB;
        color: white;
    }

    .bg-asside {
        background: #2D3E50;
        color: #fff;
    }

    .bg-asside i,
    .bg-asside a span {
        color: #fff;
    }

    .t-bg-success {
        background: #007bff;
        color: #fff;
    }
    a {
        cursor: pointer;
    }
    .star {
        font-size: 1.2rem;
        color: red;
    }
    #base-style_length, #base-style_filter {
        display: none;
    }
    .input-search {
        display:flex;
        /* width:100%; */
        padding: 4px;
        font-size:1rem;
        /* font-weight:400; */
        line-height:1.5;
        color:var(--bs-body-color);
        -webkit-appearance:none;
        -moz-appearance:none;
        appearance:none;
        background-color: var(--bs-body-bg);
        background-clip:padding-box;
        border: 1px solid #CED4DA;
        border-radius:var(--bs-border-radius);
        justify-content: center;
        align-items: center;
        transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out
    }
    .input-search:focus {
        border: 1px solid #1890FF;
    }
    .input-search input {
        border: 0;
        width: 100%;
        padding-left: 6px;
    }
    .input-search input:focus, .input-search .form-control {
        border: 0;
        outline: 0;
    }

    </style>

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
            <div class="me-auto fw-semibold" id="title">Bootstrap</div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

</body>

</html>