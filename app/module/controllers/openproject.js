import * as fx from '../functions/functions.js';
let refreshInterval = null;  // Variable globale pour stocker le setInterval
// Fonction pour démarrer le rafraîchissement
function startRefreshing() {
    if (refreshInterval) {
        clearInterval(refreshInterval); // S'assurer qu'on ne crée pas plusieurs intervalles
    }
    refreshInterval = setInterval(() => {
        const id = fx.get_value('version');
        if (id){
            get_title(id);
            get_comment(id);
        }
    }, 5000); // Rafraîchissement toutes les 5 secondes
}

$(document).ready(() => {
    // Get project id from url
    const path = window.location.pathname;
    const parts = path.split("-");
    let id_project = 0;
    if (parts[1]){
        id_project = parts[1];
    }
    get_conversation_group();
    get_conversation();
    get_count_convesation();
    get_version();
    get_data();
    get_project();
    get_encadreur();

    // handller for to save or update data
    $(document).on('click', '#save', async (e) => {
        e.preventDefault();
        let commentaire = fx.get_value('commentaire');
        let fichier = fx.get_file('fichier');
        if (!commentaire || !fichier){
            fx.show_message('Tous les champs sont obligatoires', 'info');
            return;
        }
        const formData = {
            commentaire: commentaire,
            fichier: fichier,
            id_project: id_project,
            action: 'save'
        }
        // get path to controller files
        const url = fx.get_controller_url('project-file');
        // connection my form with controller
        const status = await fx.save(formData, url, 'correctionModal', '#save');
        if(status) {
            get_data();
            get_version();
            get_project();
            $('#data_version_commentaire_file').html('chargement en cours');
        }
    });

    setInterval(()=> {
        const id = fx.get_value('version');
        if (id){
            get_title(id);
        }
    }, 500);

    setInterval(()=> {
        const id = fx.get_value('version');
        if (id){
            get_data_version_file(id);
            get_data_version(id);
        }
    }, 3000);

    $(document).on('click', '.like', async function(e) {
        e.preventDefault();
        let commentId = $(this).data("id");
        const formData = {
            commentId: commentId,
            action: 'save_like'
        }
        // get path to controller files
        const url = fx.get_controller_url('project-file');
        // connection my form with controller
        const status = await fx.send(formData, url, 'correctionModal');
        if(status) {
            const id = fx.get_value('version');
            if(id){
                get_title(id);
            }
            const id_file = fx.get_value('id_file');
            if(id_file){
                get_comment(id_file);
            }
        }
    });

    startRefreshing();
    // Stopper le rafraîchissement lorsqu'on clique sur "Répondre"
    $(document).on('click', '.reponse', function(e) {
        e.preventDefault();
        let commentId = $(this).data("id");
        const container = $('#zone-reponse-' + commentId);
        // Masquer tous les autres formulaires et retirer la classe visible
        $(".reponse-form").hide().removeClass("form-visible");
        // Afficher le formulaire du commentaire cliqué et ajouter une classe pour dire qu’il est actif
        $("#reponse-form-" + commentId).show().addClass("form-visible");
        // Vérifier si les réponses sont déjà chargées
        if (!container.is(':visible')) {
            $.ajax({
                type: 'POST',
                url: fx.get_controller_url('project-file'), // Ton fichier contrôleur
                data: {
                    action: 'get_reponses',
                    id_commentaire: commentId
                },
                success: function(response) {
                    container.html(response).slideDown();
                },
                error: function(xhr, status, error) {
                    alert("Une erreur s'est produite : " + xhr.responseText);
                    console.error(xhr);
                }
            });
        } else {
            // Si les réponses sont déjà visibles, ne rien faire
            container.slideUp();
        }
        // Optionnel : stop le rafraîchissement automatique si nécessaire
        clearInterval(refreshInterval);
    });

    // Envoi de la réponse et redémarrage du rafraîchissement
    $(document).on('click', '.envoyer-reponse', async function(e) {
        e.preventDefault();
        let commentId = $(this).data("id"); // D'abord récupérer l'ID du commentaire
        let reponse = $("#reponse-text-" + commentId).val();
        // Préparation des données à envoyer
        const formData = {
            version: fx.get_value('version'),
            action: 'envoyer-reponse',
            id_commentaire: commentId,
            reponse: reponse
        };
        const url = fx.get_controller_url('project-file');
        // Envoi des données via fx.send
        const status = await fx.send(formData, url, null, '.envoyer-reponse');
        if (status) {
            // Vider le champ et cacher le formulaire
            $("#reponse-text-" + commentId).val('');
            $("#reponse-form-" + commentId).hide().removeClass('form-visible');
            // Recharger les commentaires
            const id = fx.get_value('version');
            if(id){
                get_title(id);
            }
            const id_file = fx.get_value('id_file');
            if(id_file){
                get_comment(id_file);
            }
        }
    });
    // save or update comment
    $(document).on('click', '#save_commentaire', async (e) => {
        e.preventDefault();
        const formData = {
            description: fx.get_value('description'),
            version: fx.get_value('id_file'),
            action: 'save_commentaire'
        }
        // get path to controller files
        const url = fx.get_controller_url('project-file');
        // connection my form with controller
        const status = await fx.send(formData, url, 'correctionModal', '#save_commentaire');
        if(status) {
            get_data();
            get_project();
            $('#description').val('');

            const id = fx.get_value('version');
            get_title(id);
            get_data_version(id);
            get_data_version_file(id);
        }
    });

    // save or update comment
    $(document).on('click', '#save_collaborate', async (e) => {
        e.preventDefault();
        const formData = {
            encadreur: fx.get_value('encadreur'),
            id_project: id_project,
            action: 'save_collaborate'
        }
        // get path to controller files
        const url = fx.get_controller_url('project-file');
        // connection my form with controller
        await fx.save(formData, url, 'encadreurModal', '#save_collaborate');
    });

    // get all project file by project
    function get_data() {
        const data = {
            id_project: id_project,
            action: 'load',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'container';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }
    // get project
    function get_project() {
        const data = {
            id_project: id_project,
            action: 'title_project_student',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'title_project';
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
    // get encadreur
    function get_encadreur() {
        const data = {
            action: 'get_encadreur'
        };
        const url = fx.get_controller_url('project-file');
        fx.fill_select(url, data, 'encadreur');
    }
    //get title of file project
    function get_title(id){
        const data = {
            id_project: id_project,
            version: id,
            action: 'get_title',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'get_title_comment';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    function get_comment(id) {
        // S’il y a une réponse en cours, on ne recharge pas les commentaires
        if ($(".reponse-form.form-visible").length > 0) {
            console.log("Réponse en cours → pas de refresh des commentaires.");
            return;
        }
        const data = {
            id_project: id_project,
            version: id,
            action: 'get_comment',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'get_comment';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }
    //get data of version
    function get_data_version(id){
        const data = {
            id_project: id_project,
            version: id,
            action: 'data_version',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'data_version_commentaire';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }
     //get file of version
     function get_data_version_file(id){
        const data = {
            id_project: id_project,
            version: id,
            action: 'data_version_file',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'data_version_commentaire_file';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }
    // get etudiant
    function get_version() {
        const data = {
            id_project: id_project,
            action: 'get_version'
        };
        const url = fx.get_controller_url('project-file');
        fx.fill_select(url, data, 'version');
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
    $('#version').on('change', function(){
        let id = $(this).val();
        get_title(id);

        setTimeout(()=>{
            let id_file = fx.get_value('id_file');
            $('#get_comment').html('<span class="loading-2"></span>');
            $('#get_title_comment').html('<div class="py-4"><span class="loading-2"></span></div><hr>');

            if (id_file){
                get_comment(id_file);
            }

        }, 200)

    });

    setTimeout(()=>{
        const id = fx.get_value('version');
        get_title(id);
        get_data_version(id);
        get_data_version_file(id);
    }, 100);

    setInterval(()=> {
        const id_file = fx.get_value('id_file');
        get_conversation();
        get_conversation_group();
        get_count_convesation();
        if(id_file){
            get_comment(id_file);
        }
    }, 3000);

});
