<?php
// inclure les fichiers de configuration
require_once 'inc/init.inc.php';
// traitement pour récupérer les catégories
// recuete pour recupérer mes catégories (toujours la même requete)
$recup_categories = $ecommerce->query("SELECT DISTINCT categorie FROM produit");
// je transforme mon resultat SQL en tableau PHP
$categories = $recup_categories->fetchAll(PDO::FETCH_ASSOC);
// je check mon tableau PHP
/*debug($categories);
echo '<hr>';*/
/*echo $categories[0]['categorie'] . '<br>'; // t shirt
echo $categories[1]['categorie'] . '<br>'; // t shirt
echo $categories[2]['categorie'] . '<br>'; // t shirt
echo $categories[3]['categorie'] . '<br>'; // t shirt*/
$nbre_categories = count($categories); // me donne le nombre de clés dans mon tableau $categories

// Recuperation des produits de chaque catégorie au moment où on clique dessus
// 1. je prepare ma requete
// SELECT * FROM produit WHERE categorie = :categorie correspond à $_GET['categorie']
$recup_produits = $ecommerce->prepare("SELECT * FROM produit WHERE categorie = :categorie");
$nom_categorie = (!empty($_GET['categorie'])) ? trim(strip_tags($_GET['categorie'])) : '';
// 2. je bind mon :categorie avec $_GET['categorie']
$recup_produits->bindValue(':categorie',$nom_categorie, PDO::PARAM_STR);

// 3. j'éxécute la requete
$recup_produits->execute();

// 4. je recupere les résultats grace à fetchAll
$produits = $recup_produits->fetchAll(PDO::FETCH_ASSOC);
// debug($produits);
// 5. je dispatch les "titres", "description", "prix" etc... dans la div
$nbre_produits = count($produits);
// inclure les fichiers d'affichage
include_once 'inc/header.inc.php';
// affichage des catégories sous forme de liens en UL LI
?>
<h1>Boutique</h1>
<div class="dropdown clearfix">
	<button id="drop" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Catégories <span class="caret"></span></button>
	<ul class="dropdown-menu" role="menu" aria-labbelledby="drop">
	<?php for($i=0;$i<$nbre_categories;$i++) :  ?>
		<li role="presentation"><a role="menuitem" href="?categorie=<?php echo $categories[$i]['categorie'] ?>"><?php echo $categories[$i]['categorie'] ?></a></li>
		<?php endfor; ?>
	</ul>
</div>
<div class="row">
<?php for($i=0;$i<$nbre_produits;$i++) : ?>
	<div class="col-xs-2">
		<h3 class="text-center"><?= $produits[$i]['titre'] ?></h3>
		<p class="text-center">
			<a href="fiche_article.php?id_article=<?= $produits[$i]['id_produit'] ?>"><img src="<?= URL ?>/photos/<?= $produits[$i]['photo'] ?>" ></a>
			<p class="lead text-center">Prix : <?= $produits[$i]['prix'] ?> €</p>
		</p>
	</div>
<?php endfor; ?>

</div>

<?php
include_once 'inc/footer.inc.php';