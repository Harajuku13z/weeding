<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichier trop volumineux</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 520px; margin: 80px auto; padding: 24px; text-align: center; }
        h1 { font-size: 1.25rem; color: #333; }
        p { color: #666; line-height: 1.6; }
        code { background: #f0f0f0; padding: 2px 8px; border-radius: 4px; }
        a { color: #0d6efd; }
    </style>
</head>
<body>
    <h1>Fichier trop volumineux</h1>
    <p>La requête dépasse la limite autorisée par le serveur (PHP <code>post_max_size</code>).</p>
    <p>Pour les uploads galerie ou thème, lancez le serveur avec les bonnes limites&nbsp;:</p>
    <p><code>./serve</code></p>
    <p>ou&nbsp;: <code>php -c php.ini artisan serve</code></p>
    <p><a href="{{ url()->previous() }}">Retour</a></p>
</body>
</html>
