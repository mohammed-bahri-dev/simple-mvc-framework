<?php
extract($data);
?>

<h1>Bienvenue sur votre espace Collaborateur</h1>
<hr>
<div class="jumbotron">
    <p>Code Authorisation reçu : <?= $code ?></p>
</div>

<div class="jumbotron">
    <div class="titre"><span class="badge titre">3</span> 
        <span class="bold">Récupérer les tokens : Access Token, Refresh Token et Id Token, </span>
    </div>
    <div class="mt20">
        <a class="btn btn-success" href="token">Requêter Authent Collab (AUC9)</a>
    </div>
    <div class="mt20">
        <div class="bold">options curl pour récupérer les token</div>
        <pre class="pre-wrap"><?php print_r($options) ?></pre>
    </div>
</div>
