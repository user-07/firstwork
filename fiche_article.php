<?php
// inclure les fichiers de configuration
require_once 'inc/init.inc.php';
// SELECT * FROM produit WHERE id_produit = :id_produit correspond $_GET['id_article']
$id_produit = !empty($_GET['id_article']) && is_numeric($_GET['id_article']) ?  trim(strip_tags($_GET['id_article'])) : ''; // je recupere l'id qui se trouve dans l'url dans ?id_article=
$article = recupInfoProduit($id_produit);
if(!$article) { // si je ne trouve pas de resultat alors je redirige vers la boutique
	header('location:boutique.php');
	exit;
}

// inclure les fichiers d'affichage
include_once 'inc/header.inc.php';
?>

<div class="row">
	<div class="col-md-7">
		<h1><?= $article['titre'] ?></h1>
		<div class="thumbnail">
			<img src="<?= URL ?>/photos/<?= $article['photo'] ?>" alt="">
			<div class="caption">
				<p class="text-center"></p>
				<p class="lead text-center"></p>
				<form method="post" action="panier.php">
				<p class="text-left">
					Taille : <?= strtoUpper($article['taille']) ?><br>
					Prix : <?= $article['prix'] ?> €
				</p>
				<select name="quantite">
					<?php  if($article['stock'] > 10) $article['stock'] = 10; // si on a plus de 10 produits en stock, on limite à 10 pour éviter les erreurs d'achat ?>
					<?php for($i=1;$i<=$article['stock'];$i++) : ?>
					<option value="<?= $i ?>"><?= $i ?></option>
				<?php endfor; ?>
				</select> <br>
				Couleur : <?= $article['couleur'] ?> <br>
				Reference : <?= $article['reference'] ?> <br>
				Description :
				<p class="text-center">
					<?= $article['description'] ?>
				</p>
				<input type="hidden" name="id_article" value="<?= $article['id_produit'] ?>">
				<p class="text-right">
					<button type="submit" name="ajout_panier" class="btn btn-default">Ajouter au panier</button>
				</p>
				</form>
			</div>
		</div>
	</div>
</div>



<?php
include_once 'inc/footer.inc.php';