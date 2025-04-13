import * as fx from '../functions/functions.js';

$(document).ready(()=> {

    const path = window.location.pathname;
    const parts = path.split("-");

    let id_project = 0;

    if (parts[1]) {
        id_project = parts[1];
    }

    get_conversation_header();
    get_conversation();
    get_conversation_group();
    get_count_convesation();
    setTimeout(() => {
        get_chat();
    }, 100);

    // Send message
    $(document).on('click', '#save', (e) => {
        e.preventDefault();
        let message = fx.get_value('message');
        let file = fx.get_file('fileInput');
        if(! file && ! message) {
            fx.show_message('Veuillez ajouter un message ou joindre un fichier', 'info');
        } else {
            send();
        }
    });

    $("#message").keydown(function(event) {
        if (event.key === "Enter") { // Vérifie si la touche pressée est "Enter"
            event.preventDefault(); // Empêche le saut de ligne (utile pour les textarea)
            let message = fx.get_value('message');
            let file = fx.get_file('fileInput');
            if(! file && ! message) {
                fx.show_message('Veuillez ajouter un message ou joindre un fichier', 'info');
            } else {
                send();
            }
        }
    });


    async function send() {
        const data = {
            message: fx.get_value('message'),
            file: fx.get_file('fileInput'),
            id_project: id_project,
            action: 'save'
        };

        const url = fx.get_controller_url('chat');
        const status = await fx.insert(data, url, null, '#save');

        if (status) {
            clear_preview();
            setTimeout(() => {
                get_chat();
            }, 10);
        }
    }



    function get_conversation() {
        const data = {
            action: 'get_conversation',
        };
        const url = fx.get_controller_url('project');
        const container = 'conversation';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    function get_conversation_header() {
        const data = {
            id_project: id_project,
            action: 'get_header',
        };
        const url = fx.get_controller_url('chat');
        const container = 'header';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    function get_conversation_group() {
        const data = {
            action: 'get_conversation_group',
        };
        const url = fx.get_controller_url('project');
        const container = 'conversation-group';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    function get_chat() {
        const data = {
            id_project: id_project,
            action: 'get_chat',
        };
        const url = fx.get_controller_url('chat');
        const container = 'chat_container';
        fx.handle_display({
            data: data, url: url, container: container
        });
        scrollToBottom(); // Auto scroll on new messages
    }

    function scrollToBottom() {
        var chatBox = $("#chat_container");
        chatBox.css('overflow-y', 'auto')
        chatBox.scrollTop(chatBox.prop("scrollHeight"));
    }

    function get_count_convesation() {
        const data = {
            action: 'get_count_convesation',
        };
        const url = fx.get_controller_url('project');
        const container = 'count_convesation';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    setInterval(get_chat, 2000);
    setInterval(()=>{
        get_conversation();
        get_conversation_group();
        get_count_convesation();
    }, 3000);

    function clear_preview() {
        $('#message').val('');
        $('#fileInput').val('');
        $('#filePreview').css('display', 'none');
        $('#imagePreview').css('display', 'none');
        $('#videoPreview').css('display', 'none');
    }

});

