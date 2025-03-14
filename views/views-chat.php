<?php
    require_once 'app/module/functions/functions.php';
    $title = 'Mes projets';
    $page_title = 'UAC collab | ' . $title;
    ob_start();
?>

<title><?=$page_title ?></title>
<!-- [ Main Content ] start -->
<style>
    .pc-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .pc-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        background-color: #fff;
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        color: #333;
        position: fixed;
        top: 60px;
        width: 100%;
        z-index: 1000;
    }

    .chat-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .user-name {
        font-weight: bold;
    }

    .user-status {
        font-size: 12px;
    }

    .chat-box {
        flex-grow: 1;
        padding: 20px;
        overflow-y: hidden;
        display: flex;
        flex-direction: column;
        margin-top: 50px;
        padding-bottom: 65px;
        min-height: 100vh;
    }

    .message {
        padding: 8px;
        border-radius: 5px;
        margin-bottom: 5px;
        max-width: 60%;
        display: flex;
        align-items: center;
    }

    .received {
        background-color: #F0F0F0;
        align-self: flex-start;
        flex-direction: row;
    }

    .sent {
        background-color: #007bff;
        align-self: flex-end;
        flex-direction: row-reverse;
        color: #fff;
    }

    .message img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .message .content {
        display: flex;
        flex-direction: column;
    }

    .message-time {
        font-size: 12px;
        color:rgb(218, 221, 221);
        margin-top: 5px;
        text-align: right;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ddd;
        background-color: #fff;
        position: fixed;
        bottom: 0;
        width: 79%;
        z-index: 1000;
    }

    .chat-input input {
        flex-grow: 1;
        padding: 5px 15px;
        border: 0 solid #ddd;
        /* border-radius: 5px; */
        width: 90%;
        background: transparent;
    }

    .chat-input button {
        padding: 5px 15px;
        border: none;
        background-color: transparent;
        color: white;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        color: #007bff;
        cursor: pointer;
    }

    .rotate-icon {
        font-size: 1.5rem;
        display: inline-block;
        transform: rotate(45deg);
    }

    #sidebar-hide {
        display: none;
    }

    .user-avatar {
        margin-top: -50px;
        margin-left: -20px;
        padding: 4px;
        width: 90px;
        height: 90px;
        object-fit: cover;
        border: 2px solid #007bff;
        background: #fff;
    }

    @media (max-width:1193px) {
        .chat-input button {
            margin-right: 50px;
        }
    }

    @media (max-width: 1025px) {
        .chat-input {
            width: 100%;
        }
        body {
            overflow: hidden;
        }
        .chat-input button {
            margin-right: 0px;
        }
    }
</style>

<div class="pc-container">
    <div class="pc-conten py-3">
        <div class="chat-container">
            <div class="chat-header">
                <img src="https://img.freepik.com/vecteurs-libre/illustration-du-jeune-homme-souriant_1308-174669.jpg" alt="User Avatar">
                <div>
                    <div class="user-name">Nom de l'utilisateur</div>
                    <div class="user-status">En ligne</div>
                </div>
            </div>
            <div class="chat-box">
                <!-- Message reçu -->
                <div class="message received">
                    <img src="https://img.freepik.com/vecteurs-libre/illustration-du-jeune-homme-souriant_1308-174669.jpg" class="user-avatar" alt="User Avatar">
                    <div class="content">
                        <!-- <div class="user-name user-desc">Nom de l'utilisateur</div> -->
                        <div>Bonjour ! Comment ça va Bonjour ! Comment ça va Bonjour ! Comment ça va Bonjour ! Comment ça va ?</div>
                        <!-- Affichage de l'heure de réception -->
                        <div class="message-time text-muted"><?= date('H:i') ?></div>
                    </div>
                </div>
                <!-- Message envoyé -->
                <div class="message sent">
                    <div class="content">
                        <div>Ça va bien, merci !</div>
                        <!-- Affichage de l'heure d'envoi -->
                        <div class="message-time"><?= date('H:i') ?></div>
                    </div>
                </div>

            </div>
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Écrire un message...">
                <button id="sendButton"><i class="bi bi-send-fill rotate-icon" ></i></button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("messageInput").addEventListener("keydown", function(event) {
        let message = document.getElementById("messageInput").value;
    if (event.key === "Enter") {  // Vérifie si la touche pressée est "Enter"
        event.preventDefault();   // Empêche le saut de ligne (utile pour les textarea)
        alert(message);
    }
});

</script>
<?php
    $page_content = ob_get_clean();
    require_once 'views/includes/theme.php';
?>
