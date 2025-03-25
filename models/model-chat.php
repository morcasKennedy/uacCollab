<?php
    class Message{
        private $date;
        private $db;
        private $contenu;
        private $fichier;
        private $projet;
        private $auteur;
        private $message_id;
        private $admin;
        private $status = 0;
        private $role;

        public function __construct($db) {
            $this->db = $db;
            $this->date = date('Y-m-d H:i:s');
        }

        public function Message($contenu = null, $fichier = null, $projet = null, $auteur = null, $role = null, $admin = null) {
            $this->contenu = $contenu;
            $this->fichier = $fichier;
            $this->projet = $projet;
            $this->auteur = $auteur;
            $this->admin = $admin;
            $this->role = $role;
        }

        // Insert message
        public function insert() {
            $query = 'INSERT INTO message VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $this->date,
                $this->contenu,
                $this->fichier,
                $this->projet,
                $this->auteur,
                $this->role,
                $this->admin,
            ]);
        }

        // Get aull message
        public function get_all($projet) {
            $query = "SELECT
                message.id AS id,
                message.dates AS date,
                message.contenu AS contenu,
                message.fichier AS fichier,
                message.projet AS projet,
                message.auteur AS auteur,
                message.role AS role,
                message.admin AS admin
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

        // get message for group
        public function get_group($group, $project) {
            $query = "SELECT
                message.id AS id,
                message.dates AS date,
                message.contenu AS contenu,
                message.fichier AS fichier,
                message.projet AS projet,
                message.auteur AS auteur,
                message.role AS role,
                message.admin AS admin
            FROM
                message
            WHERE
                message.admin = ? AND
                message.projet = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $project,
                $group,
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Get all conversation
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

        // Count chat
        public function count($projet, $auteur, $role) {
            $query = "SELECT
                COUNT(*) AS nb
            FROM
                suivi_message
            WHERE
                project = ? AND
                status = ? AND
                auteur = ? AND
                role = ?;
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
               $projet,
               0,
               $auteur,
               $role
            ]);

            $result = 0;
            while($row = $stmt->fetch()) {
                $result = $row->nb;
            }
            return $result;
        }

        // Count coversation
        public function count_conversation($auteur, $role) {
            $query = "SELECT
                    COUNT(*) as nb
                FROM
                    suivi_message
                WHERE
                    status = ? AND
                    auteur = ? AND
                    role = ?
                GROUP BY
                    project";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
               0,
               $auteur,
               $role
            ]);

            $result = [];
            while($row = $stmt->fetch()) {
                $result[] = $row;
            }
            return $result;
        }

        // Compter les message non lu
        public function message_read($message_id) {
            $query = "SELECT
                COUNT(*) AS nb
            FROM
                suivi_message
            WHERE
                message = ? AND
                status = ?
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
               $message_id,
               0
            ]);

            $result = '';
            while($row = $stmt->fetch()) {
                $result = $row->nb;
            }
            return $result > 0 ? '' : '-all';
        }

        public function set_status($projet, $auteur, $role) {
            $query = 'UPDATE
                suivi_message
            SET
                status = ?
            WHERE
                project = ? AND
                auteur = ? AND
                role = ?
            ';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                1,
                $projet,
                $auteur,
                $role
            ]);
        }



        public function suivi_message($message_id = null, $projet = null, $auteur = null, $role = null) {
            $this->message_id = $message_id;
            $this->projet = $projet;
            $this->auteur = $auteur;
            $this->role = $role;

        }

        public function insert_suivi() {
            $query = 'INSERT INTO suivi_message VALUES (?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                null,
                $this->message_id,
                $this->projet,
                $this->auteur,
                $this->role,
                $this->status
            ]);
        }



    }