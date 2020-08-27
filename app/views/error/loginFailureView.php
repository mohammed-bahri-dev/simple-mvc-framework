<?php
extract($data);
?>
<h1>Erreur de Connection</h1>

<div class="alert alert-danger">
	<span class="fa fa-exclamation-triangle"></span> <?= $message ?>
</div>

<a href="<?= DIR ?>" class="btn btn-success">Retour Accueil</a>
