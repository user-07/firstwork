<?php
echo '<h3>A l\'ancienne : </h3>';
$md5_1 = md5('soleil75');
$md5_2 = md5('soleil75');
echo '<p>Avec MD5 : ' . $md5_1 . '</p>';
echo '<p>Avec MD5 2 : ' . $md5_2 . '</p>';

echo '<h6>' . md5('soleil75') . '</h6>';

if(md5('soleil75') == $md5_1) {
	echo '<p>Le 1er mot de passe avec MD5 est BON</p>';
} else {
	echo '<p>Le 1er mot de passe avec MD5 est MAUVAIS</p>';
}
if(md5('soleil75') == $md5_2) {
	echo '<p>Le 1er mot de passe avec MD5 est BON</p>';
} else {
	echo '<p>Le 1er mot de passe avec MD5 est MAUVAIS</p>';
}

echo '<hr><hr>';

echo '<h3>New generation :</h3>';

$hash1 = password_hash('soleil75', PASSWORD_DEFAULT);
$hash2 = password_hash('soleil75', PASSWORD_DEFAULT);
echo '<p>Avec password_hash : ' . $hash1 . '</p>';
echo '<p>Avec password_hash 2 : ' . $hash2 . '</p>';

echo password_verify('soleil75', $hash1);
if(password_verify('soleil75', $hash1)) {
	echo '<p>Le 1er mot de passe avec password_hash est BON</p>';
} else {
	echo '<p>Le 1er mot de passe avec password_hash est MAUVAIS</p>';
}

if(password_verify('soleil75', $hash2)) {
	echo '<p>Le 2nd mot de passe avec password_hash est BON</p>';
} else {
	echo '<p>Le 2nd mot de passe avec password_hash est MAUVAIS</p>';
}

echo '<hr><hr>';
echo '<h3>Si on fait un password verify sur un md5 ca marche pas :</h3>';

if(password_verify('soleil75', $md5_1)) {
	echo '<p>Le 1er mot de passe avec MD5 est BON</p>';
} else {
	echo '<p>Le 1er mot de passe avec MD5 est MAUVAIS</p>';
}

if(password_verify('soleil75', $md5_2)) {
	echo '<p>Le 2nd mot de passe avec MD5 est BON</p>';
} else {
	echo '<p>Le 2nd mot de passe avec MD5 est MAUVAIS</p>';
}

echo '<hr>';

