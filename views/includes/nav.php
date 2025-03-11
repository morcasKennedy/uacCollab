<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar ">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="../dashboard/index.html"
                class="b-brand text-dark d-flex justify-content-between align-items-center">
                <!-- ========   Change your logo from here   ============ -->
                <img src="./assets/themes/logo.png" class="img-collab" alt="logo">
                <b>UAC collab</b>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item bg-primary">
                    <a onclick="redirect('./')" class="pc-link text-white">
                        <span class="pc-micon"><i class="ti ti-dashboard text-white"></i></span>
                        <b><span class="pc-mtext">Accueil</span></b>
                    </a>
                </li>
                <li class="pc-item">
                    <a onclick="redirect('./projects')"class="pc-link">
                        <span class="pc-micon"><i class="ti ti-typography"></i></span>
                        <span class="pc-mtext">Projets</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="../elements/bc_color.html" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-color-swatch"></i></span>
                        <span class="pc-mtext">Encadreurs</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="../elements/bc_color.html" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-color-swatch"></i></span>
                        <span class="pc-mtext">Soumettre</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="../elements/bc_color.html" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-color-swatch"></i></span>
                        <span class="pc-mtext">Correction</span>
                    </a>
                </li>

            </ul>
            <a href="" class="logout-btn">Deconnexion</a>
        </div>
    </div>
</nav>
<script>function redirect(url) {window.location.href = url;}</script>
<!-- [ Sidebar Menu ] end -->