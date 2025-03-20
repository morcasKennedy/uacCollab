import * as fx from '../functions/functions.js';

$(document).ready(()=> {
    get_etudiant_by_encadreur();
    get_data();
    get_conversation();
    // Save or update project btn
    $(document).on('click', '#save', async (e) => {
        e.preventDefault();

        const data = {
            title: fx.get_value('titre'),
            description: fx.get_value('description'),
            etudiant: fx.get_value('etudiant'),
            action: 'save'
        };

        const url = fx.get_controller_url('project');
        const status = await fx.save(data, url, 'exampleModalToggle');

        if (status) {
            get_data();
        }
    });

    // get all project by directeur
    function get_data() {
        const data = {
            action: 'load',
        };
        const url = fx.get_controller_url('project');
        const container = 'container';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    // Get the students affected for a project associated with a supervisor and an academic year.
    function get_etudiant_by_encadreur() {
        const data = {
            action: 'get_etudiant_by_encadreur'
        };

        const url = fx.get_controller_url('api');
        fx.fill_select(url, data, 'etudiant');
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

});

