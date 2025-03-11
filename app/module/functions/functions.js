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


export {show_message };