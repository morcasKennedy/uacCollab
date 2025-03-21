<style>
    .post-container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 20px;
    width: 100%; /* ou auto selon ta maquette */
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
}

.post-author {
    margin: 0;
    font-size: 16px;
}

.post-date {
    color: #777;
    font-size: 12px;
}

.post-content {
    margin-bottom: 10px;
    font-size: 15px;
}

.post-actions {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
}

.btn-action {
    background: none;
    border: none;
    color: #65676b;
    font-size: 12px; /* Taille du texte réduite */
    font-weight: 500; /* Poids léger du texte */
    padding: 4px 8px; /* Un padding léger pour un bouton discret */
    cursor: pointer;
}

.btn-action:hover {
    color: #1877f2; /* Change de couleur au survol pour montrer que c'est cliquable */
    text-decoration: underline;
}


.comments-section {
    margin-top: 10px;
}

.comment {
    display: flex;
    align-items: flex-start;
    margin-bottom: 10px;
}

.comment-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
}

.comment-details {
    background-color: #f0f2f5;
    padding: 8px 12px;
    border-radius: 18px;
    max-width: 400px;
}

.comment-details strong {
    font-size: 13px;
}

.comment-details small {
    color: #777;
    margin-left: 5px;
    font-size: 11px;
}

.comment-details p {
    margin: 5px 0 0;
    font-size: 14px;
}

.comment-actions {
    display: flex;
    gap: 10px;
    font-size: 12px;
    color: #65676b;
    margin-top: 5px;
}

.comment-actions span {
    cursor: pointer;
}

.comment-actions span:hover {
    color: #1877f2;
}


</style>