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
    max-height: 90vh;
    min-height: 90vh;
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
    background-color: #3498DB;
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
    color: rgb(218, 221, 221);
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
    color: #3498DB;
    cursor: pointer;
}

.rotate-icon {
    font-size: 1.7rem;
    display: inline-block;
    transform: rotate(45deg);
}

.media-icon {
    font-size: 1.5rem;
    display: inline-block;
    margin-top: 5px;
    cursor: pointer;
    margin-left: 10px;
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

#filePreview {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    bottom: 60px;
    max-width: 220px;
    position: fixed;
    background-color: #f9f9f9;
    margin-left: 10px;
}

#imagePreview img {
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    max-width: 200px;
}

#videoPreview video {
    max-width: 200px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.content-img,
.content-document {
    width: 300px;
}

.content-img img, .content-img video {
    width: 100%;
    height: 300px;
    object-fit: cover;
    background: #fff;
    border-radius: 5px;

}
.content .bg-video {
    width: 30px;
    height: 30px;
}
.user-desc {
    color: #3498DB;
    font-size: 0.8rem;
    border-bottom: 2px solid #ccc;
    margin-bottom: 5px;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    padding: 0 5px;
    padding-bottom: 5px;
}

@media (max-width:1193px) {
    .chat-input button {
        margin-right: 50px;
    }
    .content-img img, .content-img video {
        width: 100%;
    }
}

@media (max-width: 1025px) {
    .content-img img, .content-img video {
        width: 100%;
    }
    .chat-input {
        width: 100%;
        padding: 0 5px;
    }
    .chat-input button {
        margin-right: 0px;
    }
    .media-icon {
        padding: 0;
    }
}

@media (max-width: 700px) {
    /* Masquer la barre de défilement mais permettre le défilement */
    body {
        overflow: scroll; /* Permet de faire défiler la page */
    }

    /* Masquer la barre de défilement dans les navigateurs Webkit */
    body::-webkit-scrollbar {
        display: none;
    }
    audio {
        background: none;
        appearance: none;
        border: none;
        outline: none;
        width: 100%;
        margin-top: 5px;
    }

    .content-img img {
        height: 200px;
        object-fit: cover;
    }
    .content-img img, .content-img video {
        width: 100%;
    }
    .message {
        max-width: 85%;
    }
}
</style>