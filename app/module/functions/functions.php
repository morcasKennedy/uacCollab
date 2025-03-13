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
    }