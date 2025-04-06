<?php
    class Project {
        private $db;
        private $date;
        private $titre;
        private $description;
        private $etudiant;
        private $encadreur;
        private $backgroud;
        private $running;
        private $status = 1;

        public function __construct($db) {
            $this->db = $db;
            $this->date = date('Y-m-d');
        }


        public function Project($titre = null, $description = null, $etudiant = null, $encadreur = null, $backgroud = null, $running = null) {
            $this->titre = $titre;
            $this->description = $description;
            $this->etudiant = $etudiant;
            $this->encadreur = $encadreur;
            $this->backgroud = $backgroud;
            $this->running = $running;
        }

        public function create() {
            $query = 'INSERT INTO projet VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $this->date,
                $this->titre,
                $this->description,
                $this->etudiant,
                $this->encadreur,
                $this->backgroud,
                $this->running,
                $this->status,
            ]);
        }

        public function get_last_project() {
            $query = 'SELECT *
            FROM
                projet
            WHERE
                status = ?
            ORDER BY
                id DESC
            LIMIT
                1';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
            ]);

            $result = 0;
            while($row = $stmt->fetch()) {
                $result = $row->id;
            }
            return $result;
        }

        public function create_encadreur($projet, $encadreur, $admin = null) {
            $query = 'INSERT INTO projet_encadreur VALUES (?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $projet,
                $encadreur,
                $admin,
                $this->status,
            ]);
        }

        // get user of one project
        public function get_users_project($projet) {
            $query = 'SELECT
                projet_encadreur.id AS id,
                projet_encadreur.projet AS projet,
                projet_encadreur.encadreur AS encadreur,
                projet_encadreur.status AS status
            FROM
                projet_encadreur
            WHERE
                projet_encadreur.status = ? AND
                projet_encadreur.projet = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $projet
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // get student project
        public function get_student_project($id) {
            $query = 'SELECT
                projet.id AS projet,
                projet.dates AS date,
                projet.titre AS titre,
                projet.description AS description,
                projet.etudiant AS etudiant,
                projet.encadreur AS encadreur,
                projet.backgroud AS backgroud,
                projet.running AS running,
                projet.status AS status
            FROM
                projet
            WHERE
                projet.id = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function get_student_directeur($id) {
            $query = 'SELECT
                projet.id AS projet,
                projet.dates AS date,
                projet.titre AS titre,
                projet.description AS description,
                projet.etudiant AS etudiant,
                projet.encadreur AS encadreur,
                projet.backgroud AS backgroud,
                projet.running AS running,
                projet.status AS status
            FROM
                projet
            WHERE
                projet.encadreur = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function get_all($annee) {
            $query = "SELECT
                DISTINCT
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
                projet.status = ? AND
                inscription.annee = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $annee
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function get_by_id($id) {
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
                projet.status = ? AND
                projet.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $id
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function verify() {
            $query = 'SELECT * FROM projet WHERE etudiant = ? ';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->etudiant,
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function restaure($id) {
            $query = 'UPDATE projet SET status = ? WHERE id = ?';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $this->status,
                $id
            ]);
        }

    }