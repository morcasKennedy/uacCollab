<?php
    class Message{
        private $date;
        private $db;
        private $contenu;
        private $fichier;
        private $projet;
        private $auteur;
        private $role;

        public function __construct($db) {
            $this->db = $db;
            $this->date = date('Y-m-d H:i:s');
        }

        public function Message($contenu = null, $fichier = null, $projet = null, $auteur = null, $role = null) {
            $this->contenu = $contenu;
            $this->fichier = $fichier;
            $this->projet = $projet;
            $this->auteur = $auteur;
            $this->role = $role;
        }

        public function insert() {
            $query = 'INSERT INTO message VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $this->date,
                $this->contenu,
                $this->fichier,
                $this->projet,
                $this->auteur,
                $this->role,
            ]);
        }

        public function get_all($projet) {
            $query = "SELECT
                message.id AS id,
                message.dates AS date,
                message.contenu AS contenu,
                message.fichier AS fichier,
                message.projet AS projet,
                message.auteur AS auteur,
                message.role AS role
            FROM
                message
            WHERE
                message.projet = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
               $projet
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        public function get_last_conversation($projet) {
            $query = "SELECT
                message.id AS id,
                message.dates AS date,
                message.contenu AS contenu,
                message.fichier AS fichier,
                message.projet AS projet,
                message.auteur AS auteur,
                message.role AS role
            FROM
                message
            WHERE
                message.projet = ?
            ORDER BY
                message.id DESC
            LIMIT 1
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
               $projet
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }
    }