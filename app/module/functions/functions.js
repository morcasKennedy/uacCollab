/**
 * Afficher un message pour notifier l'utilisateur
 * @param {*} content Le contenu du message à afficher à l'utilisateur
 * @param {*} title Le titre du message (optionnel)
 * @param {*} color La couleur du message (success, info, error, warning, default)
 * @param {*} time La durée que le message va rester affiché, en secondes (par défaut 5 secondes)
 */
function show_message(content, color = 'default', time = 5) {
    const toastExample = document.getElementById('toast-example');
    const ftitle = document.getElementById('title');
    const fcontent = document.getElementById('content');
    const ficon = document.getElementById('icon');

    var stitle = '';
    var icon = '';

    // Mise à jour du contenu du message
    fcontent.innerHTML = content;

    // Logique pour définir la couleur, l'icône et le titre selon le type de message
    switch(color) {
      case 'success':
        color = 't-bg-success';
        icon = 'bi-check-circle';
        stitle = 'Success';
        break;
      case 'info':
        color = 'bg-secondary';
        icon = 'bi-info-circle';
        stitle = 'Information';
        break;
      case 'error':
        color = 'bg-danger';
        icon = 'bi-x-octagon';
        stitle = 'Error';
        break;
      case 'warning':
        color = 'bg-warning';
        icon = 'bi-exclamation-triangle';
        stitle = 'Warning';
        break;
      default:
        icon = 'bi-bell';
        stitle = 'Notification';
        break;
    }

    // Mise à jour du titre du toast
    ftitle.innerHTML = stitle;

    // Suppression des anciennes classes de couleur et d'icônes
    toastExample.classList.remove('bg-primary', 'bg-dark', 'bg-danger', 'bg-warning');
    ficon.classList.remove('bi-check-circle', 'bi-info-circle', 'bi-x-octagon', 'bi-exclamation-triangle', 'bi-bell');

    // Ajout de la nouvelle couleur et icône
    toastExample.classList.add(color);
    ficon.classList.add(icon);

    // Initialisation du toast avec autohide activé et un délai de "time" en secondes
    const bootstrapToast = new bootstrap.Toast(toastExample, {
        autohide: true,
        delay: time * 1000 // Le toast disparaîtra après "time" secondes
    });

    // Affichage du toast
    bootstrapToast.show();
}

async function save(data, url, modal = null) {
  try {
    const response = await $.ajax({
      url: url,
      type: 'POST',
      data: data,
      dataType: 'json',
    });

    const { content, status } = response;

    show_message(content, status);

    if (status === 'success') {
      if (modal) {
        setTimeout(() => {
          $('#' + modal).modal('hide');
        }, 10);
      }
      return true;
    }

    return false;

  } catch (error) {
    show_message("Une erreur s'est produite. Veuillez réessayer : " + error, 'error');
    console.error('Erreur de connexion :', error);
    return false;
  }
}

function handle_display(params) {
  const { data, url, container, searchQuery} = params;
  $.ajax({
      url: url,
      type: 'POST',
      data: data,
      success: function (response) {
          $('#' + container).html(response);
          if (searchQuery) {
            filter_data('#' + container, searchQuery);
        }
      },
      error: function (xhr, status, error) {
          // Affiche un message d'erreur en cas d'échec de la requête
          show_message("Une erreur s'est produite, veuillez réessayer. : " + error, 'error');
      }
  });
}

function filter_data(container, searchQuery) {
  // Assurez-vous que container est un objet jQuery
  const $container = $(container); // Convertir en jQuery si nécessaire
  const rows = $container.find('tr'); // Trouver uniquement les lignes dans le tbody
  let found = false; // Variable pour vérifier si des données sont trouvées

  // Applique le filtrage sur chaque ligne du tbody
  rows.each(function() {
      const row = $(this);
      let match = false;

      // Rechercher dans chaque cellule de la ligne (th et td)
      row.find('th, td:not([hidden])').each(function() {
          const cell = $(this);
          if (cell.text().toLowerCase().includes(searchQuery.toLowerCase())) {
              match = true;
          }
      });

      // Afficher ou masquer la ligne en fonction du résultat de la recherche
      row.toggle(match);

      // Si une correspondance est trouvée, mettre à jour la variable `found`
      if (match) {
          found = true;
      }
  });

  // Afficher un message si aucune donnée n'est trouvée
  if (!found) {
      show_message("Aucune donnée ne correspond à votre recherche.", 'warning', 15);
  }
}



/**
 * Fonction pour récupérer la valeur d'un champ de formulaire
 * @param {*} value L'ID du champ dont la valeur doit être récupérée
 * @returns {string} La valeur du champ spécifié
 */
function get_value(value) {
  // Récupère la valeur du champ dont l'ID est passé en paramètre
  return $('#' + value).val(); // Retourne la valeur du champ
}

function get_controller_url(file_name) {
  // Génère l'URL du contrôleur en concaténant le module et le fichier
  return './controllers/controller-' + file_name + '.php';
}

function redirect(url) {window.location.href = url;}

export {show_message, get_value, redirect, save, get_controller_url, handle_display };