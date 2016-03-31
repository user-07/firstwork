<?php
$DSN = 'mysql:host=localhost;dbname=ecommerce';
$user = 'root';
$mdp = '';
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
];
$ecommerce = new PDO($DSN,$user,$mdp,$options);


