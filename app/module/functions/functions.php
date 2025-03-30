<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/../../../libraries/vendor/autoload.php';
 // Assurez-vous que PHPMailer est installé via Composer
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

        public static function first_capital_letter($str) {
            if(empty($str)) {
                return '';
            }
            $str = strtolower($str);
            $str = ucfirst($str);
            return $str;
        }

        public static function send_mail($email_to, $user_name, $subject, $body) {
            $mail = new PHPMailer(true);
            try {
                // Configurer le serveur SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
                $mail->SMTPAuth   = true;
                $mail->Username   = 'uaccollab@gmail.com'; // Remplacez par votre e-mail
                $mail->Password   = 'sypm hwpw kjto ixpf'; // Utilisez un mot de passe d'application si besoin
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // Destinataires
                $mail->setFrom('uaccollab@gmail.com', 'UAC Collab'); // Expediteur
                $mail->addAddress($email_to,$user_name);// A qui destinateur

                // Contenu de l'e-mail
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->AltBody = strip_tags($body);

                // Envoi du mail
                return $mail->send() ? true : false;
            } catch (Exception $e) {
                return ["success" => false, "message" => "Erreur: " . $mail->ErrorInfo];
            }
        }

        public static function local_time($stored_date, $user_timezone = 'UTC') {
            // Vérifier si le fuseau horaire est valide
            if (!in_array($user_timezone, timezone_identifiers_list(), true)) {
                $user_timezone = 'UTC'; // Si invalide, utiliser UTC par défaut
            }

            try {
                // Créer un nouvel objet DateTimeImmutable pour chaque appel
                $date = new DateTimeImmutable($stored_date, new DateTimeZone('UTC'));

                // Changer de fuseau horaire sans affecter d'autres conversions
                $converted_date = $date->setTimezone(new DateTimeZone($user_timezone));

                // Retourner l'heure locale au format H:i
                return $converted_date->format('H:i');
            } catch (Exception $e) {
                return null; // En cas d'erreur, retourner '00:00' ou null
            }
        }


    }