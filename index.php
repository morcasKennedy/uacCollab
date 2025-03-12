<?php
    // Récupérer l'URL ou définir une valeur par défaut
    $url = $_GET['url'] ?? '';

    // Liste des routes définies
    require_once './app/rooter.php';

    $parts = explode('-', $url, 2);  // Diviser l'URL en deux parties
    $type = $parts[0];  // Le type (avant le '-')


    // Vérifier et traiter l'URL
    if (empty($url)) {
        require_once './views/views-home.php';  // Page d'accueil si l'URL est vide
    } elseif (array_key_exists($url, $routes)) {
        // Vérifier si l'URL demandée existe dans les routes
        require_once './views/views-'. $routes[$url] . '.php';  // Inclure la page correspondant à la route
    } elseif (array_key_exists($type, $routes_get)) {
        $id = isset($parts[1]) ? $parts[1] : null;
        // Inclure la page correspondant au type trouvé
        require_once './views/views-' . $routes_get[$type] . '.php';
        // Vous pouvez utiliser $id si nécessaire dans la page incluse
    } else {
        // Si l'URL ne correspond à aucune route définie, afficher la page 404
        $page = $url;
        require_once './views/views-404.php';
    }