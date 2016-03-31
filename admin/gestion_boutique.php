<?php
//-------- configuration ----------- //
require_once '../inc/init.inc.php'; //-- j'appelle les require qui se trouvent dans init
if(!userAdmin()) {
  header('location:../connexion.php');
  exit();
}
// petit script pour remplir la boutique avec des produits :
if(!empty($_GET['action']) && $_GET['action'] == 'remplir') {
  $_GET['action'] = 'affichage';
  $ecommerce->exec("INSERT INTO `produit` (`id_produit`, `reference`, `categorie`, `titre`, `description`, `couleur`, `taille`, `sexe`, `photo`, `prix`, `stock`, `created_at`, `updated_at`) VALUES
(23, 'reference8781', 'categorie', 'titre', 'description', 'couleur', 'xl', 'm', 'reference8781_blue_tshirt.jpg', '0.00', 0, '2016-02-26 13:26:53', '2016-02-26 13:26:53'),
(24, 'XYGH546', 't shirt', 'super shirt', 'SUper t shirt blanc cool', 'blanc', 'l', 'm', 'xygh546_shir.jpg', '140.00', 10, '2016-02-26 15:07:09', '2016-02-26 15:07:09'),
(25, 'XY546', 't shirt', 'jean paul', 'Super jean paul Gautier', 'bleu', 'l', 'm', 'xy546_shir.jpg', '140.00', 10, '2016-02-26 15:08:34', '2016-02-26 15:08:34'),
(26, 'XY546889', 't shirt', 'redshirt', 'Super t shirt rouge redshot', 'rouge', 'xl', 'f', 'xy546889_shirt2.png', '140.00', 10, '2016-02-26 15:09:27', '2016-02-26 15:09:27'),
(27, 'XY54687', 'jean', 'bopant', 'Super t jeanbopant', 'bleu', 'm', 'f', 'xy54687_jean.jpg', '100.00', 10, '2016-02-26 15:10:38', '2016-02-26 15:10:38'),
(28, 'XY54678', 'pantalon', 'supant', 'Super pantalon noir', 'noir', 'l', 'm', 'xy54678_pant.jpg', '120.00', 52, '2016-02-26 15:17:13', '2016-02-26 15:17:13');
");
}
// initialisation des variables pour eviter les undefined
$reference = '';
$categorie = '';
$titre = '';
$description = '';
$couleur = '';
$taille = '';
$sexe = '';
$photo = '';
$prix = '';
$stock = '';

$pass = false; // variable de controle initialisée à true
if(isset($_POST['envoi'])) {
  $pass = true;
}
// je passe si j'ai cliqué sur le bouton
if($pass) {
  //debug($_POST);
  foreach($_POST as $key => $value) {
    if($key !== 'envoi') {
      // on recupère les nom des indices du $_POST et on les transforme en variables
      // c'est comme si on faisait :
      // $reference = trim(strip_tags($_POST['reference'])); pour chaque clé / indice du $_POST
      ${$key} = trim(strip_tags($_POST[$key]));
    }
  }
  if(empty($reference) || empty($titre) || empty($description) || empty($couleur) || empty($taille) || empty($stock) || empty($sexe) || empty($prix)) {
    // regardons si la reference est déjà présent en BDD
    $msg = '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Veuillez vérifier toutes vos saisies !</p>';
    $pass = false;
  }
}
// verification si la reference est dja presente en BDD
if($pass && !empty($_GET['action']) && $_GET['action'] == 'ajout') {
  $check_reference = $ecommerce->prepare('SELECT reference FROM produit WHERE reference = :reference');
    $check_reference->bindValue(':reference', $reference, PDO::PARAM_STR);
    $check_reference->execute();
    if($check_reference->rowCount() > 0) {
      $msg = '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> La référence est déjà présente en BDD</p>';
      $pass = false;
    }
}
// vérification de l'extension de la photo
if($pass) {
  if(!empty($_FILES['photo']['name'])) {
    if(!checkExtensionPhoto()) { // si l'extension de la photo n'est pas bonne, je pass à false
    $msg = '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Vérifiez l\'extension de la photo</p>';
      $pass = false;
    }
  }
}

if($pass) { // si $pass correspond toujours à true
  // recuperation de la photo
// 1 nous prenons le nom de la photo en minuscule
  if(!empty($_FILES['photo']['name'])) { // si y'a une photo (bouton parcourir), je la prend
    $photo = strToLower($reference . '_' . $_FILES['photo']['name']);
    // 2 nous copions le fichier temporaire de la photo vers notre dossier d'images
    // echo __FILE__; // nous donne le chemin du fichier dans lequel on se trouve
    // echo dirname(__FILE__); // nous donne dossier parent du fichier dans lequel on se trouve
    // echo dirname(dirname(__FILE__)); // nous donne dossier parent du dossier parent du fichier dans lequel on se trouve
    $source_photo = $_FILES['photo']['tmp_name'];
    $destination_photo = dirname(dirname(__FILE__)) . '/photos/' . $photo;
    copy($source_photo, $destination_photo); // je copie la photo temporaire de $_FILES dans mon dossier d'images
  } elseif (!empty($ancienne_photo)) { // sinon je prend la photo du cas de la modification, qui est déjà présente, pour la remettre dans la BDD
    $photo = $ancienne_photo;
  } else { // sinon je met rien
    $photo = '';
  }
  // 1. requete d'insertion
  $insert_produit = $ecommerce->prepare('REPLACE INTO produit(id_produit, reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) VALUES(:id_produit, :reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :photo, :prix, :stock)');
  foreach($_POST as $key => $value) {
    if($key !== 'envoi' && $key !== 'stock' && $key !== 'prix' && $key !== 'ancienne_photo' ) {
      $insert_produit->bindValue(':'. $key, ${$key}, PDO::PARAM_STR);
    }
  }
  // si je suis dans une modif je recupere l'id_produit
  $id_produit = (!empty($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])) ? $_GET['id_produit'] : '';
  $insert_produit->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
  // comme photo appartient à $_FILES, nous le rentrons à la main
  $insert_produit->bindValue(':photo', $photo, PDO::PARAM_STR);
  $insert_produit->bindValue(':prix', $prix, PDO::PARAM_INT);
  $insert_produit->bindValue(':stock', $stock, PDO::PARAM_INT);
  $insert_produit->execute();
  $msg = '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Produit enregistré avec succès !</p>';
}
if( (!empty($_GET['action']) && $_GET['action'] == 'suppression')
  && (!empty($_GET['id_produit']) && is_numeric($_GET['id_produit'])) ) {
$produit = recupInfoProduit($_GET['id_produit']);
// debug($produit);
 // je recupere le nom de la photo du produit
$suppresion_produit = $ecommerce->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
$suppresion_produit->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
$suppresion_produit->execute();
  if($suppresion_produit->rowCount() == 1){ // si le produit a bien été supprimé de la BDD
    $msg = '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Produit supprimé avec succès !</p>';
    if(!empty($produit['photo'])) {
    $chemin_photo_a_suppr = RACINE_SITE . '/photos/' . $produit['photo'];
    //debug($chemin_photo_a_suppr);
      if(file_exists($chemin_photo_a_suppr)) {
        unlink($chemin_photo_a_suppr); // unlink supprime un fichier
      } // fin if file_exists
    } // fin if !empty($produit['photo'])
  } // fin if $suppression_produit rowCount
} // fin if !empty suppression

//------- affichage ----------- //
if(!empty($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) {
  $ajoutActif = 'active';
  $affichageActif = '';
} else {
  $ajoutActif = '';
  $affichageActif = 'active';
}
include_once '../inc/header.inc.php';
?>
<ul style="margin: 10px 0;" class="nav nav-tabs">
  <li role="presentation" class="<?= $affichageActif ?>"><a href="gestion_boutique.php?action=affichage">Afficher les produits</a></li>
  <li role="presentation" class="<?= $ajoutActif ?>"><a href="gestion_boutique.php?action=ajout">Ajouter un produit</a></li>
</ul>
<?php
$bouton = (!empty($_GET['action']) && $_GET['action'] == 'modification') ? 'modifier' : 'ajouter';
if(!empty($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification') ) :

  if(!empty($_GET['id_produit']) && is_numeric($_GET['id_produit']) ) {
    $produit = recupInfoProduit($_GET['id_produit']); // me donne le produit en BDD en me basant sur son id
    //debug($produit,2);
    foreach($produit as $key => $value) {
      // $reference = $produit['reference'];
      ${$key} = $produit[$key];
    }
  }
?>
<div class="row centered-form">
  <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Ajouter un produit</h3>
      </div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data" role="form">
          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="reference" id="reference" class="form-control input-sm" placeholder="Réference" value="<?= $reference ?>">
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="categorie" id="categorie" class="form-control input-sm" placeholder="Catégorie" value="<?= $categorie ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="titre" id="titre" class="form-control input-sm" placeholder="Titre" value="<?= $titre ?>">
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="couleur" id="couleur" class="form-control input-sm" placeholder="Couleur" value="<?= $couleur ?>">
              </div>
            </div>
          </div>

          <div class="form-group">
            <textarea name="description" id="description" class="form-control input-sm" placeholder="Description"><?= $description ?></textarea>
          </div>

          <div class="form-group">
            <input style="padding:5px;" type="file" name="photo" id="photo" class="form-control" placeholder="Photo">
          </div>
          <?php if(!empty($_GET['action']) && $_GET['action'] == 'modification') : ?>
          <div class="form-group">
            <img src="<?= URL . '/photos/' . $photo ?>">
            <input type="hidden" name="ancienne_photo" value="<?= $photo ?>">
          </div>
          <?php endif; ?>
           <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <label for="taille">taille</label>
                <select name="taille" id="taille">
                  <option value="xl" <?= ($taille == 'xl') ? 'selected' : '' ?> >XL</option>
                  <option value="l" <?= ($taille == 'l') ? 'selected' : '' ?> >L</option>
                  <option value="m" <?= ($taille == 'm') ? 'selected' : '' ?> >M</option>
                </select>
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <input type="text" name="prix" id="prix" class="form-control input-sm" placeholder="Prix" value="<?= $prix ?>">
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <input type="text" name="stock" id="stock" class="form-control input-sm" placeholder="Stock" value="<?= $stock ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="input-group">
                <span class="input-group-addon">
                  <input id="femme" type="radio" name="sexe" value="f" <?= ($sexe == 'f') ? 'checked' : '' ?> >
                </span>
                <label for="femme" class="form-control">Femme</label>
              </div><!-- /input-group -->
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="input-group">
                <span class="input-group-addon">
                  <input id="homme" type="radio" name="sexe" value="m" <?= ($sexe == 'm') ? 'checked' : '' ?>>
                </span>
                <label class="form-control" for="homme" >Homme</label>
              </div>
            </div>
          </div>

          <button style="margin-top: 15px;" type="submit" name="envoi" class="btn btn-info btn-block"><?= ucfirst($bouton) ?></button>

        </form>
      </div>
    </div>
  </div>
</div>
<?php
endif;
// Affichage des produits :
if(!empty($_GET['action']) && $_GET['action'] == 'affichage') :
$recup_produits = $ecommerce->query("SELECT id_produit, reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock, DATE_FORMAT(created_at, '%d/%m/%y %H:%i') AS created_at, DATE_FORMAT(updated_at, '%d/%m/%y %H:%i') AS updated_at FROM produit");
$produits = $recup_produits->fetchAll(PDO::FETCH_ASSOC);
$nbre_produits = count($produits);
// debug($produits);
?>
<h1>Vous possedez <?= $nbre_produits ?> produits</h1>
<table class="table table-hover">
    <thead>
      <tr>
       <?php
       foreach($produits[0] as $key => $value) {
        switch($key) {
          case 'id_produit' :
            echo '<th>ID</th>';
            break;
          case 'created_at' :
            echo '<th>date de création</th>';
            break;
          case 'updated_at' :
            echo '<th>date de modification</th>';
            break;
          default :
            echo '<th>'.$key.'</th>';
            break;
          }
       }
        ?>
        <!-- je rajoute 2 TH à la fin (en sortie de boucle) -->
        <th>mod</th>
        <th>suppr</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($produits as $key => $value) : ?>
          <tr>
            <?php
              foreach($value as $k => $v) {
                if($k == 'photo') {
                  echo '<td><img src="' . URL . '/photos/' . $v . '"></td>';
                } else {
                  echo '<td>' . $v . '</td>';
                }
              }
              echo '<td><a href="?action=modification&id_produit='.$value['id_produit'].'">M</a></td>';
              echo '<td><a href="?action=suppression&id_produit='.$value['id_produit'].'">X</a></td>';
             ?>
          </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <form>
  <button name="action" value="remplir">Remplir la boutique</button>
  </form>
<?php
endif;



include_once '../inc/footer.inc.php';