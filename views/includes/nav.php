<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar ">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="./"
                class="b-brand text-dark d-flex justify-content-between align-items-center">
                <!-- ========   Change your logo from here   ============ -->
                <img src="./assets/themes/logo.png" class="img-collab" alt="logo">
                <b>UAC collab</b>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item active">
                    <a onclick="redirect('./')" class="pc-link">
                        <span class="pc-micon"><i class="bi bi-house"></i></span>
                        <b><span class="pc-mtext">Accueil</span></b>
                    </a>
                </li>
                <li class="pc-item">
                    <a onclick="redirect('./affectations')" class="pc-link">
                        <span class="pc-micon"><i class="bi bi-bookmarks"></i></span>
                        <span class="pc-mtext">Affectation</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a onclick="redirect('./projects')"class="pc-link">
                        <span class="pc-micon"><i class="bi bi-sign-no-parking"></i></span>
                        <span class="pc-mtext">Projets</span>
                    </a>
                </li>

            </ul>
            <a onclick="redirect('./login-logout')" class="logout-btn text-white">Deconnexion</a>
        </div>
    </div>
</nav>
<script>function redirect(url) {window.location.href = url;}</script>
<!-- [ Sidebar Menu ] end -->