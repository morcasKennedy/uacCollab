<?php
    require_once 'app/module/functions/functions.php';
    $title = 'Mes projets';
    $page_title = 'UAC collab | ' . $title;
    ob_start();
    session_start();
    $role = ! empty($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
    if(empty($_SESSION['user']['id']) OR ! isset($_SESSION['user']['id'])) {
        header('location:./login');
        exit;
    }
    require_once 'includes/chat-style.php'
?>

<title><?=$page_title ?></title>
<div class="pc-container">
    <div class="pc-conten py-3">
        <div class="chat-container">
            <div class="chat-header" id="header">

            </div>
            <div class="chat-box" id="chat_container" ></div>
            <!-- Bloc pour la prévisualisation -->
            <div id="filePreview" style="display:none; margin-top: 10px;">

                <div id="imagePreview" style="display:none;">
                    <img id="previewImg" src="" alt="Image prévisualisée"
                        style="max-width: 200px; max-height: 200px;" />
                </div>
                <div id="videoPreview" style="display:none;">
                    <video id="previewVideo" controls style="max-width: 200px; max-height: 200px;">
                        <source id="videoSource" src="" type="video/mp4">
                    </video>
                </div>
                <span id="fileName" class="one-truncate mt-2 text-primary"></span>
            </div>
            <div class="chat-input">
                <i class="bi bi-paperclip media-icon"></i>
                <input type="file" id="fileInput" style="display:none" />
                <input autocomplete="off" type="text" id="message" placeholder="Écrire un message..." autofocus/>
                <button id="save"><i class="bi bi-send-fill rotate-icon"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="imageModal">
    <span class="close">&times;</span>
    <img id="modalImg" src="" alt="">
</div>

<script src="./app/module/controllers/chat.js" type="module"></script>
<?php
    require_once 'includes/chat-js.php';
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>