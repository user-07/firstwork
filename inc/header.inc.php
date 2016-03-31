<!DOCTYPE html>
<html>
<head>
	<title>E COMMERCE</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= URL ?>/styles/style.css">
</head>
<body>
<header class="container">
	<div class="row">
		<?php
			include_once 'menu.inc.php';
		?>
	</div>
</header>
<div class="content container">
<?= (!empty($msg)) ? $msg : '' ?>