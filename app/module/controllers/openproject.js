import * as fx from '../functions/functions.js';

$(document).ready(() => {

    // Get project id from url
    const path = window.location.pathname;
    const parts = path.split("-");

    let id_project = 0;
    if (parts[1]){
        id_project = parts[1];
    }

    // show the correction changement
    //load_corrections();

    // handller for to save or update data
    $(document).on('click', '#save', async (e) => {
        e.preventDefault();

        const formData = {
            commentaire: fx.get_value('commentaire'),
            fichier: fx.get_file('fichier'),
            id_project: id_project,
            action: 'save'
        }

        // get path to controller files
        const url = fx.get_controller_url('project-file');

        // connection my form with controller 
        const status = await fx.save(formData, url, 'correctionModal');
        if(status) {
            get_data();
            get_version();
            get_project();

            const id = fx.get_value('version');
            get_title(id);
        }
    });

    // save or update comment
    $(document).on('click', '#save_commentaire', async (e) => {
        e.preventDefault();

        const formData = {
            description: fx.get_value('description'),
            action: 'save_commentaire'
        }

        // get path to controller files
        const url = fx.get_controller_url('project-file');

        // connection my form with controller 
        const status = await fx.save(formData, url, 'correctionModal');
        if(status) {
            get_data();
            get_version();
            get_project();
            $('#description').val('');

            const id = fx.get_value('version');
            get_title(id);
            get_data_version(id);
            get_data_version_file(id);
        }
    });

    get_data();
    get_project();
    setInterval(()=>{
        const id = fx.get_value('version');
        get_title(id);
        get_data_version(id);
        get_data_version_file(id);
    }, 100);
    get_version();

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

    
    //get title of file project
    function get_title(id){
        const data = {
            id_project: id_project,
            version: id,
            action: 'get_title',
        };
        const url = fx.get_controller_url('project-file');
        const container = 'title_commentaire';
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

        $('#version').on('change', function(){
            let id = $(this).val();
            get_title(id);
        });


});
