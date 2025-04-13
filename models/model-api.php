<?php
    class Api {
        private $db;
        private $status = 1;

        public function __construct($db) {
            $this->db = $db;
        }

        // Get annee academique from database
        public  function get_annee() {
            $query = 'SELECT
                annee.id AS id,
                annee.description AS description
            FROM
                annee
            WHERE
                status = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get promotion from database
        public  function get_promotion() {
            $query = 'SELECT
                promotion.id AS id,
                promotion.description AS description,
                departement.description AS description_departement
            FROM
                promotion, departement
            WHERE
                departement.id = promotion.departement AND
                promotion.status = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get encadreur from database
        public  function get_encadreur() {
            $query = 'SELECT
                encadreur.id AS id,
                encadreur.nom AS nom,
                encadreur.postnom AS postnom,
                encadreur.prenom AS prenom,
                encadreur.telephone AS telephone,
                encadreur.adresse AS adresse,
                encadreur.email AS email
            FROM
                encadreur
            WHERE
                encadreur.status = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get encadreur from database by id
        public  function get_encadreur_id($id) {
            $query = 'SELECT
                encadreur.id AS id,
                encadreur.nom AS nom,
                encadreur.postnom AS postnom,
                encadreur.prenom AS prenom,
                encadreur.telephone AS telephone,
                encadreur.adresse AS adresse,
                encadreur.email AS email
            FROM
                encadreur
            WHERE
                encadreur.id = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id
            ]);

            $result = '';
            while($row = $stmt->fetch()) {
                $result = $row->nom . ' ' . $row->prenom;
            }
            return $result;
        }

        // Get etudiant from database
        public  function get_etudiant($an, $prom) {
            $query = 'SELECT
                etudiant.id AS id,
                etudiant.nom AS nom,
                etudiant.postnom AS postnom,
                etudiant.prenom AS prenom,
                etudiant.telephone AS telephone,
                etudiant.adresse AS adresse,
                etudiant.email AS email
            FROM
                etudiant, inscription
            WHERE
                inscription.etudiant = etudiant.id AND
                etudiant.status = ? AND
                inscription.annee = ? AND
                inscription.promotion = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $an,
                $prom
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get etudiant by id
        public  function get_etudiant_id($id) {
            $query = 'SELECT
                etudiant.id AS id,
                etudiant.nom AS nom,
                etudiant.postnom AS postnom,
                etudiant.prenom AS prenom,
                etudiant.telephone AS telephone,
                etudiant.adresse AS adresse,
                etudiant.email AS email
            FROM
                etudiant, inscription
            WHERE
                inscription.etudiant = etudiant.id AND
                inscription.id = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id,
            ]);

            $result = '';
            while($row = $stmt->fetch()) {
                $result = $row->nom . ' ' . $row->prenom;
            }
            return $result;
        }

        // Get etudiant by id
        public  function get_etudiant_email($id) {
            $query = 'SELECT
                etudiant.id AS id,
                etudiant.nom AS nom,
                etudiant.postnom AS postnom,
                etudiant.prenom AS prenom,
                etudiant.telephone AS telephone,
                etudiant.adresse AS adresse,
                etudiant.email AS email
            FROM
                etudiant, inscription
            WHERE
                inscription.etudiant = etudiant.id AND
                inscription.id = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id,
            ]);

            $result = '';
            while($row = $stmt->fetch()) {
                $result = $row->email;
            }
            return $result;
        }


        // Log all users
        public function log_users($email) {
            $query = 'SELECT *
            FROM
                encadreur
            WHERE
                (telephone = ? OR email = ?) AND
                status = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $email,
                $email,
                $this->status
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get admin for project
        public function get_admin($encadreur, $last_year) {
            $query = 'SELECT
                COUNT(*) AS nb
            FROM
                projet_encadreur, projet, inscription
            WHERE
                inscription.id = projet.etudiant AND
                projet.id = projet_encadreur.projet AND
                projet_encadreur.encadreur = ? AND
                projet_encadreur.admin = ? AND
                inscription.annee = ? AND
                projet_encadreur.status = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $encadreur,
                $this->status,
                $last_year,
                $this->status
            ]);

            $result = 0;
            if($row = $stmt->fetch()) {
                $result = $row->nb;
            }
            return $result > 0 ? true : false;
        }

        public function get_admin_by_project($project) {
            $query = 'SELECT
               *
            FROM
                projet_encadreur
            WHERE
                projet = ? AND
                admin = ?
                ';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $project,
                $this->status
            ]);

            $result = [];
            if($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function log_etudiants($email) {
            $query = 'SELECT
                inscription.id AS id,
                inscription.dates AS date,
                inscription.description AS description,
                inscription.etudiant AS etudiant,
                inscription.promotion AS promotion,
                inscription.annee AS annee,
                inscription.status AS status,
                etudiant.id AS id_etudiant,
                etudiant.matricule AS matricule,
                etudiant.nom AS nom,
                etudiant.postnom AS postnom,
                etudiant.prenom AS prenom,
                etudiant.genre AS genre,
                etudiant.date_naissance AS date_naissance,
                etudiant.adresse AS adresse,
                etudiant.image AS image,
                etudiant.telephone AS telephone,
                etudiant.email AS email,
                etudiant.mot_de_passe AS mot_de_passe,
                etudiant.status AS status_etudiant
            FROM
                etudiant, inscription
            WHERE
                etudiant.id = inscription.etudiant AND
                (etudiant.telephone = ? OR etudiant.email = ?) AND
                etudiant.status = ?
            ORDER BY
                inscription.id DESC
            LIMIT
                1';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $email,
                $email,
                $this->status
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get last annee academique
        public function get_last_year() {
            $query = 'SELECT annee.id as id
            FROM
                annee, affectation
            WHERE
                affectation.annee = annee.id  AND
                affectation.status = ?
            ORDER BY
                affectation.id DESC
            LIMIT
                1';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status
            ]);

            $result = 0;
            while($row = $stmt->fetch()) {
                $result = $row->id;
            }
            return $result;
        }

        // Get the students affected for a project associated with a supervisor and an academic year.
        public  function get_etudiant_by_year($encadreur, $annee) {
            $query = 'SELECT
                inscription.id AS id,
                etudiant.nom AS nom,
                etudiant.postnom AS postnom,
                etudiant.prenom AS prenom,
                etudiant.telephone AS telephone,
                etudiant.adresse AS adresse,
                etudiant.email AS email,
                CONCAT(promotion.description, " ", departement.description ) AS promotion
            FROM
                etudiant, inscription, affectation, promotion, departement
            WHERE
                inscription.etudiant = etudiant.id AND
                affectation.etudiant = inscription.id AND
                promotion.id = inscription.promotion AND
                departement.id = promotion.departement AND
                affectation.status = ? AND
                affectation.encadreur = ? AND
                affectation.annee = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $encadreur,
                $annee
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function get_send_email_encadreur($projet){
            $query = "SELECT encadreur.nom, encadreur.postnom, encadreur.prenom, encadreur.email FROM encadreur, projet_encadreur WHERE encadreur.id=projet_encadreur.encadreur AND projet_encadreur.projet = ?  AND projet_encadreur.status = ?";
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

    }