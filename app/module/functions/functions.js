function show_message(e,t="default",r=5){let n=document.getElementById("toast-example"),a=document.getElementById("title"),s=document.getElementById("content"),l=document.getElementById("icon");var i="",o="";switch(s.innerHTML=e,t){case"success":t="t-bg-success",o="bi-check-circle",i="Success";break;case"info":t="bg-secondary",o="bi-info-circle",i="Information";break;case"error":t="bg-danger",o="bi-x-octagon",i="Error";break;case"warning":t="bg-warning",o="bi-exclamation-triangle",i="Warning";break;default:o="bi-bell",i="Notification"}a.innerHTML=i,n.classList.remove("bg-primary","bg-dark","bg-danger","bg-warning","t-bg-success"),l.classList.remove("bi-check-circle","bi-info-circle","bi-x-octagon","bi-exclamation-triangle","bi-bell"),n.classList.add(t),l.classList.add(o);let c=new bootstrap.Toast(n,{autohide:!0,delay:1e3*r});c.show()}async function save(e,r,a=null){let n=$("#save, #login"),t=n.html();try{n.html('<span class="loading"></span> Chargement...').prop("disabled",!0);let s=!1,l=new FormData;Object.keys(e).forEach(r=>{let a=e[r];(a instanceof File||Array.isArray(a)&&a[0]instanceof File)&&(s=!0),Array.isArray(a)?a.forEach((e,a)=>{l.append(`${r}[${a}]`,e)}):l.append(r,a)});let o=s?l:e,p,{content:i,status:c}=await $.ajax({url:r,type:"POST",data:o,dataType:"json",processData:!s,contentType:!s&&"application/x-www-form-urlencoded; charset=UTF-8"});if(show_message(i,c),n.html(t).prop("disabled",!1),"success"===c)return a&&setTimeout(()=>{$("#"+a).modal("hide")},10),!0;return!1}catch(d){return n.html(t).prop("disabled",!1),show_message("Une erreur s'est produite. Veuillez r\xe9essayer : "+d,"error"),console.error("Erreur de connexion :",d),!1}}
async function send(data, url, modalId = null) {
    // Sélectionne les boutons d'action (sauvegarde et connexion)
    let actionButtons = $("#save, #login");
    let originalHtml = actionButtons.html();

    try {
        // Désactive les boutons et affiche un indicateur de chargement
        actionButtons.html('<span class="loading"></span>').prop("disabled", true);

        let hasFile = false;
        let formData = new FormData();

        // Construction des données à envoyer
        Object.keys(data).forEach(key => {
            let value = data[key];

            // Vérifie si la valeur est un fichier
            if (value instanceof File || (Array.isArray(value) && value[0] instanceof File)) {
                hasFile = true;
            }

            // Ajoute les données dans FormData
            if (Array.isArray(value)) {
                value.forEach((item, index) => {
                    formData.append(`${key}[${index}]`, item);
                });
            } else {
                formData.append(key, value);
            }
        });

        // Détermine si les données doivent être envoyées sous forme de FormData ou d'objet simple
        let requestData = hasFile ? formData : data;

        // Envoi de la requête AJAX
        let { content, status } = await $.ajax({
            url: url,
            type: "POST",
            data: requestData,
            dataType: "json",
            processData: !hasFile,
            contentType: hasFile ? false : "application/x-www-form-urlencoded; charset=UTF-8"
        });

        // Affichage du message de retour

        // Réactive les boutons avec leur texte initial
        actionButtons.html(originalHtml).prop("disabled", false);

        if(status != 'success') {
            show_message(content, status);
        }

        // Si la requête a réussi, ferme éventuellement la modal
        if (status === "success") {

            if (modalId) {
                setTimeout(() => {
                    $("#" + modalId).modal("hide");
                }, 10);
            }
            return true;
        }
        return false;

    } catch (error) {
        // Gestion des erreurs
        actionButtons.html(originalHtml).prop("disabled", false);
        show_message("Une erreur s'est produite. Veuillez réessayer : " + error, "error");
        console.error("Erreur de connexion :", error);
        return false;
    }
}
function handle_display(e){let{data:t,url:r,container:n,searchQuery:a}=e;$.ajax({url:r,type:"POST",data:t,dataType:"html",success:function(e){$("#"+n).html(e),a&&filter_data("#"+n,a)},error:function(e,t,r){show_message("Une erreur s'est produite, veuillez r\xe9essayer. : "+r,"error")}})}function filter_data(e,t){let r=$(e),n=r.find("tr"),a=!1;n.each(function(){let e=$(this),r=!1;e.find("th, td:not([hidden])").each(function(){let e=$(this);e.text().toLowerCase().includes(t.toLowerCase())&&(r=!0)}),e.toggle(r),r&&(a=!0)}),a||show_message("Aucune donn\xe9e ne correspond \xe0 votre recherche.","warning",15)}function get_value(e){return $("#"+e).val().trim()}function get_controller_url(e){return"./controllers/controller-"+e+".php"}function redirect(e){window.location.href=e}function fill_select(e,t,r){$.ajax({url:e,type:"POST",data:t,success:function(e){$("#"+r).html(e)},error:function(e,t,r){let n=`Erreur lors de la requ\xeate : ${t}, ${r}`;handle.show_message(n,"error")}})}function get_file(e){return document.getElementById(e).files[0]}export{show_message,get_value,redirect,save,get_controller_url,handle_display,fill_select,get_file, send};