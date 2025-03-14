<?php
    $page_title = 'UAC collab | Login';
    ob_start();
    session_start();

    $url = $_GET['url'] ?? '';

    // Se doconnecter si il cliquer sur le bouton logout
    if(str_contains($url, 'logout')) {
      session_destroy();
      header('location:./login');
    }

    // Se connecter lors que use a ete connecter pour la premiere fois
    if(! empty($_SESSION['user']['id'])) {
      header('location:./');
      exit;
    }

?>

<title><?=$page_title ?></title>
<!-- [ Main Content ] start -->
<div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="#">
          <img src="./assets/themes/logo.png" class="img-collab" alt="logo">
          <b>UAC collab</b>
          </a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Login</b></h3>
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Adresse e-mail ou téléphone <span class="text-danger">*</span></label>
              <input autocomplete="off" type="text" id="email" class="form-control" placeholder="exemple@uaconline.edu.cd">
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
              <input type="password" id="password" class="form-control" placeholder="****">
            </div>
            <div class="d-flex mt-1 justify-content-between">
              <div class="form-check">
                <input autocomplete="off" class="form-check-input input-primary" type="checkbox" id="customCheckc1" >
                <label class="form-check-label text-muted" for="customCheckc1">Rester connecté ?</label>
              </div>
            </div>
            <div class="d-grid mt-4">
              <button type="button" id="login" class="btn btn-primary">Se connecter</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

<script src="./app/module/controllers/users.js" type="module"></script>
<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme2.php';
?>