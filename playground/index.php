<?php
$url = "http://wp-rest-api.test/wp-json/myplugin/v1/author/1?&price=50";
//Cette reqûete est côté serveur, on n'est pas soumis au SOP ou au CORS
$data = file_get_contents($url);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playground</title>
</head>

<body>

    <h1><i>Origine</i> du projet Front</h1>

    <h2>Objectifs</h2>

    <p>Simule l'origin du projet front pour tester la consommation de l'API et la CORS policy définie du côté de l'API.</p>

    <h2>Requêtes tests</h2>

    <p>Aller voir dans la console les résultats des reqûetes AJAX</p>

    <p>
        On voudrait mettre en place une CORS Policy du côté du serveur exposant l'API REST. Et n'autoriser que l'origin de ce serveur à la consommer
    </p>

    <p>Résutlat de la requête <?php echo $url ?> GET depuis le serveur : <?php print_r($data); ?> </p>

    <script>
        const credentials = {
            username: "paul",
            password: "paul"
        }
        if (window.fetch) {

            //Reqûetes envoyées depuis le navigateur, soumises à la SOP et CORS

            fetch('http://wp-rest-api.test/wp-json/wp/v2').then((response) => response.json()).then(($data) => console.log('GET d\'une route wordpress :', $data))

            fetch('http://wp-rest-api.test/wp-json/myplugin/v1/author/1?&price=50').then((response) => response.json()).then(($data) => console.log('GET d\'une route custom :', $data))

            fetch('http://wp-rest-api.test/wp-json/jwt-auth/v1/token', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "*/*",
                },
                body: JSON.stringify(credentials)
            }).then((response) => response.json()).then(($data) => console.log('Récupération du token : ', $data))
        }
    </script>

</body>

</html>