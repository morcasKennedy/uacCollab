import * as fx from '../functions/functions.js';

$(document).ready(function() {
    // Get promotion & annee from url
    const path = window.location.pathname;
    const parts = path.split("-");

    let annee = 0;
    let promotion = 0;

    if (parts[1]) {
        annee = parts[1];
    }

    if (parts[2]) {
        promotion = parts[2];
    }

    get_data();

    // Save or update affectation btn
    $(document).on('click', '#save', async (e) => {
        e.preventDefault();

        const data = {
          annee: annee,
          promotion: promotion,
          etudiant: fx.get_value('etudiant'),
          encadreur: fx.get_value('encadreur'),
          action: 'save',
        };

        const url = fx.get_controller_url('affectation');
        const status = await fx.save(data, url, 'exampleModalToggle');

        if (status) {
          get_data();
        }
    });

    // get all affectations by annee
    function get_data() {
        const data = {
            annee: annee,
            action: 'load',
        };
        const url = fx.get_controller_url('affectation');
        const container = 'container';
        fx.handle_display({
            data: data, url: url, container: container
        });
    }

    $('#search').on('input', function() {
        const searchValue = $(this).val().trim(); // Récupérer la valeur de recherche
        const data = {
            annee: annee,
            action: 'load',
        };

        const url = fx.get_controller_url('affectation');
        const container = 'container';

        // Appeler la fonction handle_display avec les critères de recherche
        fx.handle_display({
            data: data,
            url: url,
            container: container,
            searchQuery: searchValue
        });
    });

    // Next event
    $(document).on('click', '#next', function() {
        const annee = fx.get_value('annee');
        const promotion = fx.get_value('promotion');
        if(annee && promotion) {
            fx.redirect('./affectations-' + annee + '-' + promotion);
        } else {
            fx.show_message('Veuillez compléter les champs marqués par <b class="star">*</b>' + annee, 'info', 10);
        }
    });
});