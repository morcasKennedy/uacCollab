<?php
class Commentaire {
    private $db;
    private $date;
    private $contenu;
    private $filtre;
    private $user;
    private $role;
    private $id_file;
    private $status = 1;
    public function __construct($db) {
        $this->db = $db;
        date_default_timezone_set('UTC');
        $this->date = date('Y-m-d H:i:s');
    }
    // Hydrate les propriétés
    public function setCommentaire($contenu = null, $filtre = null, $user = null, $id_file = null, $role = null) {
        $this->contenu = $contenu;
        $this->filtre = $filtre;
        $this->user = $user;
        $this->id_file = $id_file;
        $this->role = $role;
    }
    // Insérer un nouveau commentaire
    public function create() {
        $query = 'INSERT INTO commentaire (dates, contenu, filtre, user, id_file, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $this->date,
            $this->contenu,
            $this->filtre,
            $this->user,
            $this->id_file,
            $this->role,
            $this->status
        ]);
    }
    // Récupérer tous les commentaires actifs
    public function get_all() {
        $query = "SELECT * FROM commentaire WHERE status = ? ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->status]);
        return $stmt->fetchAll();
    }
    // Récupérer un commentaire par ID
    public function get_by_id($id) {
        $query = "SELECT * FROM commentaire WHERE id = ? AND status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id, $this->status]);
        return $stmt->fetch();
    }
    public function get_comment_by_version($version){
        $query = 'SELECT * FROM commentaire WHERE id_file = ? AND status = ? AND filtre = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $version,
            $this->status,
            0
        ]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function get_by_id_file($id) {
        $query = "
            SELECT
                c.*,
                e.nom AS encadreur_nom,
                e.prenom AS encadreur_prenom,
                et.nom AS etudiant_nom,
                et.prenom AS etudiant_prenom
            FROM commentaire c
            LEFT JOIN encadreur e ON (c.user = e.id AND c.role = 'encadreur')
            LEFT JOIN etudiant et ON (c.user = et.id AND c.role = 'etudiant')
            WHERE c.id_file = ?
            AND c.status = ?
            ORDER BY c.id DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id, $this->status]);
        $result = [];
        while ($row = $stmt->fetch()) {
            if ($row->role === 'encadreur') {
                $row['nom'] = $row->encadreur_nom;
                $row->prenom = $row->encadreur_prenom;
            } else if ($row->role === 'etudiant') {
                $row->nom = $row->etudiant_nom;
                $row->prenom = $row->etudiant_prenom;
            } else {
                $row->nom = null;
                $row->prenom = null;
            }
            $result[] = $row;
        }
        return $result;
    }
    // Mettre à jour un commentaire
    public function update($id, $contenu, $filtre) {
        $query = 'UPDATE commentaire SET contenu = ?, filtre = ? WHERE id = ? AND status = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $contenu,
            $filtre,
            $id,
            $this->status
        ]);
    }
    // Supprimer logiquement un commentaire (status à 0)
    public function delete($id) {
        $query = 'UPDATE commentaire SET status = 0 WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    // Restaurer un commentaire (status à 1)
    public function restaure($id) {
        $query = 'UPDATE commentaire SET status = 1 WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    public function add_liike($user, $commentaire, $role){
        $query = 'INSERT INTO likes VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
          null,
          1,
          $user,
          $commentaire,
          $role]);
    }
    public function verify_like_exist($user, $commentaire, $role){
        $query = 'SELECT * FROM likes WHERE user = ? AND commentaire = ? AND role = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $user, $commentaire, $role
        ]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function set_like($id, $like){
        $query = 'UPDATE likes SET likes = ? WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $like,
            $id
        ]);
    }
    public function toggle_like($user, $commentaire, $role){
        $query = 'SELECT COUNT(*) as nb FROM likes WHERE user = ? AND commentaire = ? AND role = ? AND likes = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $user, $commentaire, $role, 1
        ]);
        $result = 0;
        while($row = $stmt->fetch()) {
            $result = $row->nb;
        }
        return $result > 0 ? '-fill' : '';
    }
    public function count_like($commentaire){
        $query = 'SELECT COUNT(*) as nb FROM likes WHERE  commentaire = ?  AND likes = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $commentaire, 1
        ]);
        $result = 0;
        while($row = $stmt->fetch()) {
            $result = $row->nb;
        }
        return $result > 0 ? $result : '';
    }
    public function count_reponse($commentaire_id){
        $query = 'SELECT COUNT(*) as nb FROM commentaire WHERE filtre = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([$commentaire_id]);
        $row = $stmt->fetch();
        return ($row && $row->nb > 0) ? $row->nb : '';
    }
    public function get_reponses_by_commentaire($id_parent) {
        $query = 'SELECT * FROM commentaire WHERE filtre = ? ORDER BY dates DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_parent]);
        return $stmt->fetchAll(PDO::FETCH_OBJ); //  pour récupérer TOUTES les réponses sous forme d'objet
    }
}
