<script>
// Sélectionner les éléments
const paperclipIcon = document.querySelector(".bi-paperclip");
const fileInput = document.getElementById("fileInput");
const filePreview = document.getElementById("filePreview");
const fileNameElement = document.getElementById("fileName");
const imagePreview = document.getElementById("imagePreview");
const videoPreview = document.getElementById("videoPreview");
const previewImg = document.getElementById("previewImg");
const previewVideo = document.getElementById("previewVideo");
const videoSource = document.getElementById("videoSource");

// Ouvrir le champ input de fichier lorsque l'icône est cliquée
paperclipIcon.addEventListener("click", function() {
    fileInput.click();
});

// Lorsque le fichier est sélectionné
fileInput.addEventListener("change", function(event) {
    const file = event.target.files[0];
    // Si un fichier est sélectionné
    if (file) {
        // Afficher le nom du fichier dans le bloc de prévisualisation
        fileNameElement.textContent = file.name;

        // Vérifier si c'est une image ou une vidéo
        const fileType = file.type;
        filePreview.style.display = "block"; // Afficher la prévisualisation du fichier

        if (fileType.startsWith("image")) {
            // Prévisualiser l'image
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = "block";
            };
            reader.readAsDataURL(file);

            // Cacher la prévisualisation vidéo si l'image est sélectionnée
            videoPreview.style.display = "none";
        } else if (fileType.startsWith("video")) {
            // Prévisualiser la vidéo
            const videoURL = URL.createObjectURL(file);
            videoSource.src = videoURL;
            previewVideo.load();
            videoPreview.style.display = "block";

            // Cacher la prévisualisation image si la vidéo est sélectionnée
            imagePreview.style.display = "none";
        } else {
            // Si ce n'est ni une image ni une vidéo
            imagePreview.style.display = "none";
            videoPreview.style.display = "none";
        }
    } else {
        // Cacher la prévisualisation si aucun fichier n'est sélectionné
        filePreview.style.display = "none";
        imagePreview.style.display = "none";
        videoPreview.style.display = "none";
    }
});

</script>