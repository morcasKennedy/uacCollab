<?php
    class Affectation{
        private $date;
        private $db;
        private $etudiant;
        private $encadreur;
        private $annee;
        private $promotion;
        private $status = 1;

        public function __construct($db) {
            $this->db = $db;
            $this->date = date('Y-m-d H:i:s');
        }

        public function affectation($etudiant = null, $encadreur = null, $annee = null, $promotion = null) {
            $this->etudiant = $etudiant;
            $this->encadreur = $encadreur;
            $this->annee = $annee;
            $this->promotion = $promotion;
        }

        public function insert() {
            $query = 'INSERT INTO affectation VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $this->date,
                $this->etudiant,
                $this->encadreur,
                $this->annee,
                $this->promotion,
                $this->status,
            ]);
        }

        public function update($id) {
            $query = 'UPDATE affectation SET etudiant = ?, encadreur = ?, annee = ?, promotion = ? WHERE id = ?';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $this->etudiant,
                $this->encadreur,
                $this->annee,
                $this->promotion,
                $id
            ]);
        }

        public function delete($id) {
            $query = 'UPDATE affectation SET status = ? WHERE id = ?';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                0,
                $id
            ]);
        }

        public function restaure($id) {
            $query = 'UPDATE affectation SET status = ? WHERE id = ?';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $this->status,
                $id
            ]);
        }

        public function get_all($annee, $promotion) {
            $query = "SELECT
                affectation.id AS id,
                affectation.dates AS date,
                affectation.etudiant AS etudiant,
                affectation.encadreur AS encadreur,
                affectation.annee AS annee,
                affectation.promotion AS promotion,
                affectation.annee AS annee,
                CONCAT(etudiant.nom, ' ', etudiant.postnom, ' ', etudiant.prenom) AS etudiant_noms,
                CONCAT(encadreur.nom, ' ', encadreur.postnom, ' ', encadreur.prenom) AS encadreur_noms,
                CONCAT(promotion.description, ' ', departement.description) AS promotion_description
            FROM
                affectation, etudiant, encadreur, departement, promotion
            WHERE
                etudiant.id = affectation.etudiant AND
                encadreur.id = affectation.encadreur AND
                promotion.id = affectation.promotion AND
                departement.id = promotion.departement AND
                affectation.status = ? AND
                affectation.annee = ? AND
                affectation.promotion = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->status,
                $annee,
                $promotion
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function verify() {
            $query = 'SELECT * FROM affectation WHERE etudiant = ? ';
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

        public function verify_update($id) {
            $query = 'SELECT * FROM affectation WHERE etudiant = ? AND id != ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $this->etudiant,
                $id
            ]);

            $result = [];
            if($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }
    }