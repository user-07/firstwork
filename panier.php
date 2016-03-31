<?php
// inclure les fichiers de configuration
require_once 'inc/init.inc.php';
creationPanier(); // je créé un panier
// si je clic sur le bouton vider le panier, je le vide...
if(!empty($_GET['action']) && $_GET['action'] == 'vider_panier') {
	unset($_SESSION['panier']);
}

// si je clic sur "payer"
if(!empty($_GET['action']) && $_GET['action'] == 'payer') {
	// insertion dans la table commande
	// INSERT INTO commande(id_membre,montant,date) VALUES(:id_membre,:montant,NOW())
	$insert_commande = $ecommerce->prepare("INSERT INTO commande(id_membre,montant,date) VALUES(:id_membre,:montant,NOW())");
	$insert_commande->bindValue(':id_membre', $_SESSION['utilisateur']['id_membre'], PDO::PARAM_INT);
	$insert_commande->bindValue(':montant', calculMontantTotal() * 1.2, PDO::PARAM_INT);
	$insert_commande->execute();
	// ensuite, j'insère chaque produit dans la table detail_commande
	// INSERT INTO detail_commande(id_commande,id_produit,quantite,prix) VALUES(:id_de_la_commande_qui_vient_detre_inseree, :id_article, :quantite, :prix)
	// Toutes les infos se trouve dans la $_SESSION['panier'] SAUF l'id_commande nouvellement créé, qui sera récupéré grace à lastInsertId() de PDO
	$id_commande = $ecommerce->lastInsertId();
	// debug($id_commande);
	$insert_detail_commande = $ecommerce->prepare("INSERT INTO detail_commande(id_commande,id_produit,quantite,prix) VALUES(:id_de_la_commande_qui_vient_detre_inseree, :id_article, :quantite, :prix)");
	$nbre_produits = count($_SESSION['panier']['id_article']);
	for($i=0;$i<$nbre_produits;$i++) { // j'éxecute la requete pour chaque produit
	$insert_detail_commande->bindValue(':id_de_la_commande_qui_vient_detre_inseree', $id_commande, PDO::PARAM_INT);
	$insert_detail_commande->bindValue(':id_article',$_SESSION['panier']['id_article'][$i], PDO::PARAM_INT);
	$insert_detail_commande->bindValue(':quantite',$_SESSION['panier']['quantite'][$i], PDO::PARAM_INT);
	$insert_detail_commande->bindValue(':prix',$_SESSION['panier']['prix'][$i], PDO::PARAM_INT);
	$insert_detail_commande->execute();
	}
}
// message de confirmation avec suppression du bouton payer et vider le contenu du panier 
unset($_SESSION['payer']);
	$msg = '<div class="Votre commande a bien été pris en compte elle sera traité sous peu">';


// si je desire supprimer un article du panier
if(!empty($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_article_panier']) && is_numeric($_GET['id_article_panier'])) {
	retirerArticleDuPanier($_GET['id_article_panier']);
}

debug($_SESSION);

if(isset($_POST['ajout_panier'])) {
	$produitAjoute = recupInfoProduit($_POST['id_article']);
	if(!$produitAjoute) { // si le produit n'existe pas c'est qu'il y a eu triche
		header('location:boutique.php');
		exit();
	} else {
		ajouterArticleDansPanier($produitAjoute['titre'],$produitAjoute['id_produit'],$_POST['quantite'],$produitAjoute['prix'], $produitAjoute['photo']);
		header('location:panier.php'); // je rafraichi la page pour supprimer le renvoi des posts, dans le cas ou l'internaute appuie sur F5 (actualisation de page)
	}
}
// inclure les fichiers d'affichage
include_once 'inc/header.inc.php';
?>
<h1>Panier</h1>
<table class="table table-hover">
    <thead>
      <tr>
        <th>titre</th>
        <th>quantité</th>
        <th>prix unitaire</th>
        <th>photo</th>
        <th>supprimer</th>
      </tr>
    </thead>
    <tbody>
    <?php if(!empty($_SESSION['panier']['id_article'])) : ?>
    <?php $nbre_produits = count($_SESSION['panier']['id_article']); ?>
      <?php for($i=0;$i<$nbre_produits;$i++) : ?>
      <tr>
      	<td><?= $_SESSION['panier']['titre'][$i] ?></td>

      	<td><?= $_SESSION['panier']['quantite'][$i] ?></td>

      	<td><?= $_SESSION['panier']['prix'][$i] ?></td>

		<!-- On refait un lien vers la fiche_article du produit -->
      	<td><a href="fiche_article.php?id_article=<?= $_SESSION['panier']['id_article'][$i] ?>"><img src="<?= URL ?>/photos/<?= $_SESSION['panier']['photo'][$i] ?>"></a></td>

      	<td><a href="?action=suppression&id_article_panier=<?= $_SESSION['panier']['id_article'][$i] ?>">X</a></td>
      </tr>
      <?php endfor; ?>
      <tr colspan="4">
      	<td>Total HT : <?= calculMontantTotal() ?>€</td>
      </tr><tr colspan="4">
      	<td>Total TTC : <?= calculMontantTotal() * 1.2 ?>€</td>
      </tr>
  <?php else : ?>
  	<tr col="4">
  		<td>Votre panier est vide</td>
  	</tr>
  <?php endif; ?>
    </tbody>
  </table>
  <form>
  <?php if(!empty($_SESSION['panier']['id_article'])) : ?>
  <button name="action" value="vider_panier">Vider le panier</button>
  <button name="action" value="payer">Payer</button>
  </form>

<?php
include_once 'inc/footer.inc.php';


/* SELECT c.id_commande, c.date, m.prenom, m.no m, p.titre, p.categorie, p.photo, dc.quantite, dc.prix
FROM membre AS m
		INNER JOIN commande AS c
			ON m.id_membre = c.id_membre
		INNER JOIN detail_commande AS dc
			ON c.id_commande = dc.id_commande
		INNER JOIN produit AS p
			ON p.id_produit = dc.id_produit;

SELECT c.id_commande, c.date, m.prenom, m.nom, p.titre, p.categorie, p.photo, dc.quantite, dc.prix
FROM membre m, commande c, detail_commande dc, produit p
WHERE m.id_membre = c.id_membre
AND c.id_commande = dc.id_commande
AND p.id_produit = dc.id_produit */
