<?php
    class Functions {
        public static function generate_color() {
            $r = rand(0, 150);
            $g = rand(0, 150);
            $b = rand(0, 150);

            // Convertit en format hexadécimal
            return sprintf("#%02X%02X%02X", $r, $g, $b);
        }

        public static function date_format($date, $y = 2, $lg = 1, $showDay = false) {
            // Vérifie si la date est valide
            if (!strtotime($date)) {
                return "Date invalide";
            }

            $lgs = $lg == 1 ? 'fr_FR' : 'en_EN';

            // Crée un formateur pour les dates
            $formatter = new IntlDateFormatter(
                $lgs,
                IntlDateFormatter::FULL, // Inclut le jour si besoin
                IntlDateFormatter::NONE
            );

            $yeah = '';
            if ($y == 1) {
                $yeah = 'y';
            } elseif ($y == 2) {
                $yeah = 'yy';
            } elseif ($y == 3) {
                $yeah = 'yyy';
            } elseif ($y == 4) {
                $yeah = 'yyyy';
            }

            // Définition du format en fonction de `$showDay`
            if ($showDay) {
                $pattern = ($lg == 1) ? 'EEEE d MMMM ' . $yeah : 'EEEE, ' . $yeah . ' MMMM d';
            } else {
                $pattern = ($lg == 1) ? 'd MMMM ' . $yeah : $yeah . ' MMMM d';
            }

            $formatter->setPattern($pattern);

            // Convertit la date
            return $formatter->format(new DateTime($date));
        }

        public static function upload_file($inputName, $targetDir = "uploads/", $newFileName = null,  $allowedTypes = ["jpg", "jpeg", "png", "pdf"], $maxSize = 2 * 1024 * 1024) {
            if (empty($_FILES[$inputName]['name'])) {
                return ["success" => true, 'message' => ''];
            }
    
            if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
                return ["success" => false, "message" => "Aucun fichier ou erreur lors de l'upload."];
            }
    
            $file = $_FILES[$inputName];
            $fileName = basename($file["name"]);
            $fileSize = $file["size"];
            $fileTmpName = $file["tmp_name"];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
            // Si un nouveau nom de fichier est fourni, on l'utilise
            if ($newFileName) {
                $fileName = $newFileName . '.' . $fileExt;
            }
    
            $targetFilePath = $targetDir . $fileName;
    
            if (! in_array($fileExt, $allowedTypes)) {
                return ["success" => false, "message" => "Type de fichier non autorisé. Veuillez insérer un fichier au format : " . implode(", ", $allowedTypes) . "."];
            }
    
            if ($fileSize > $maxSize) {
                return ["success" => false, "message" => "Le fichier est trop volumineux. Limite: " . ($maxSize / 1024 / 1024) . " Mo."];
            }
    
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
    
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                return ["success" => true, "message" => $fileName];
            } else {
                return ["success" => false, "message" => "Erreur lors du déplacement du fichier."];
            }
        }

    }