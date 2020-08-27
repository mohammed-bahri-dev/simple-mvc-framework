<?php

use Helpers\Assets;
use Helpers\Url;

?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<title><?php echo $data['title'].' - '.SITETITLE;?></title>

	<!-- CSS -->
	<?php
	Assets::css([
		'//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
		Url::templatePath() . 'css/style.css',
		Url::templatePath() . 'font-awesome/css/font-awesome.min.css',
	]);
	?>

</head>
<body>

<div class="container">

	<?php //var_dump($_SESSION); ?>

	<?php // Flash Errror Message
		if ( isset($data['error_msg']) ) : ?>
     	<div class="alert alert-danger mt20">
    		<span class="fa fa-exclamation-triangle"></span> <?= $data['error_msg'] ?>
    	</div>
    <?php endif; ?>
