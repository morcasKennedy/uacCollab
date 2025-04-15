<?php
class Project_file {
    private $db;
    private $date;
    private $projet;
    private $fichier;
    private $user;
    private $commentaire;
    private $type;
    private $version;
    private $status = 1;
    public function __construct($db) {
        $this->db = $db;
        date_default_timezone_set('UTC');
        $this->date = date('Y-m-d H:i:s');
    }
    // Hydrate les propriétés
    public function Project_files($projet = null, $fichier = null, $user = null, $commentaire = null, $type = null, $version = null) {
        $this->projet = $projet;
        $this->fichier = $fichier;
        $this->user = $user;
        $this->commentaire = $commentaire;
        $this->type = $type;
        $this->version = $version;
    }
    public function create() {
        $query = 'INSERT INTO fichiers_projet (dates, projet, fichier, user, commentaire, type, version, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $this->date,
            $this->projet,
            $this->fichier,
            $this->user,
            $this->commentaire,
            $this->type,
            $this->version,
            $this->status
        ]);
    }
    public function update_file($fichier, $id){
        $query = 'UPDATE fichiers_projet SET fichier = ? WHERE id = ?';
        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            $fichier, $id
        ]);
    }
    public function get_all($project) {
        $query = "SELECT
                fichiers_projet.id,
                fichiers_projet.dates as date,
                fichiers_projet.projet,
                fichiers_projet.fichier,
                fichiers_projet.user,
                fichiers_projet.commentaire,
                fichiers_projet.type,
                fichiers_projet.version
            FROM
                fichiers_projet
            WHERE
                fichiers_projet.projet = ?
            AND
                fichiers_projet.status = ?
            ORDER BY
                fichiers_projet.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $project,
            $this->status
        ]);
        return $stmt->fetch();
    }
    public function get_version_by_project($project) {
        $query = "SELECT COUNT(version) as version FROM fichiers_projet WHERE projet = ? AND status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project, $this->status]);
        $result = 0;
            while($row = $stmt->fetch()) {
                $result = $row->version;
            }
            return $result + 1;
    }
    public function get_title($project, $version): array {
        $query = "
            SELECT
                fp.*,
                e.nom AS etudiant_nom,
                e.prenom AS etudiant_prenom,
                en.nom AS encadreur_nom,
                en.prenom AS encadreur_prenom
            FROM fichiers_projet fp
            LEFT JOIN etudiant e ON (fp.user = e.id AND fp.type = 'correction')
            LEFT JOIN encadreur en ON (fp.user = en.id AND fp.type = 'soumission')
            WHERE fp.projet = ?
            AND fp.version = ?
            AND fp.status = ?
            ORDER BY fp.version DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project, $version, $this->status]);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($row->type === 'correction') {
                $row->nom = $row->etudiant_nom;
                $row->prenom = $row->etudiant_prenom;
            } else if ($row->type === 'soumission') {
                $row->nom = $row->encadreur_nom;
                $row->prenom = $row->encadreur_prenom;
            } else {
                $row->nom = null;
                $row->prenom = null;
            }
            $result[] = $row;
        }
        return $result;
    }
    public function get_data_version($project, $version): array{
        $query = "SELECT * FROM fichiers_projet WHERE projet = ? AND version = ? AND status = ? ORDER BY version DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project, $version, $this->status]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function get_version($project){
        $query = "SELECT * FROM fichiers_projet WHERE projet = ? AND status = ? ORDER BY version DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project, $this->status]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function get_project_by_id($project) {
        $query = "SELECT
            projet.id AS id,
            projet.dates AS date,
            projet.titre AS titre,
            projet.description AS description,
            projet.etudiant AS etudiant,
            projet.encadreur AS encadreur,
            projet.backgroud AS backgroud,
            projet.running AS running,
            projet.status AS status,
            etudiant.nom AS nom,
            etudiant.postnom AS postnom,
            etudiant.prenom AS prenom,
            etudiant.genre AS genre,
            etudiant.image AS image,
            inscription.id AS id_inscription,
            CONCAT(promotion.description, ' ',  departement.description ) AS promotion,
            projet_encadreur.encadreur AS encadreur_id
        FROM
            projet, etudiant, inscription, promotion, departement, projet_encadreur
        WHERE
            etudiant.id = inscription.etudiant AND
            inscription.id = projet.etudiant AND
            promotion.id = inscription.promotion AND
            departement.id = promotion.departement AND
            projet_encadreur.projet = projet.id AND
            projet.status = ? AND projet.id = ?  GROUP BY projet.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $this->status,
            $project
        ]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function count() {
        $query = "SELECT
             COUNT(*) AS nb
        FROM
            fichiers_projet
        WHERE
            status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $this->status
        ]);
        $result = 1;
        if($res = $stmt->fetch()) {
            $result = $res->nb + 1;
        }
        return $result;
    }
    public function get_project_by_directeur($project){
        $query = "SELECT projet_encadreur.id, projet_encadreur.projet, projet_encadreur.encadreur FROM projet_encadreur WHERE projet_encadreur.projet = ? AND projet_encadreur.status = ? ORDER BY projet_encadreur.id ASC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project, $this->status]);
        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function restaure($id) {
        $query = 'UPDATE fichiers_projet SET status = ? WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$this->status, $id]);
    }

    public function get_send_email_encadreur($projet){
        $query = "SELECT encadreur.nom, encadreur.postnom, encadreur.prenom, encadreur.email, projet.titre FROM encadreur, projet_encadreur, projet WHERE encadreur.id=projet_encadreur.encadreur AND projet.id=projet_encadreur.projet AND projet_encadreur.projet = ?  AND projet_encadreur.status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $projet,
            $this->status
        ]);

        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
    public function get_send_email_encadreur_by_id($encadreur){
        $query = "SELECT encadreur.nom, encadreur.postnom, encadreur.prenom, encadreur.email FROM encadreur WHERE encadreur.id = ? AND encadreur.status = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $encadreur,
            $this->status
        ]);

        $result = [];
        while($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }
}
