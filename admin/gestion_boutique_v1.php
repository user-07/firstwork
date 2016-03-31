<?php
//-------- configuration ----------- //
require_once '../inc/init.inc.php'; //-- j'appelle les require qui se trouvent dans init
if(isset($_POST['envoi'])) {
  foreach($_POST as $key => $value) {
    if($key !== 'envoi') {
      // on recupère les nom des indices du $_POST et on les transforme en variables
      // c'est comme si on faisait :
      // $reference = (!empty($_POST['reference'])) ? trim(strip_tags($_POST['reference'])) : ''; pour chaque clé / indice du $_POST
      ${$key} = (!empty($_POST[$key])) ? trim(strip_tags($_POST[$key])) : '';
    }
  }
  // regardons si la reference est déjà présent en BDD
  $check_reference = $ecommerce->prepare('SELECT reference FROM produit WHERE reference = :reference');
  $check_reference->bindValue(':reference', $reference, PDO::PARAM_STR);
  $check_reference->execute();
  if($check_reference->rowCount() > 0) {
    $msg = '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> La référence est déjà présente en BDD</p>';
  } else { // si la ref n'est PAS en bdd, je continue...
  // recuperation de la photo
  // 1 nous prenons le nom de la photo
    $photo = (!empty($_FILES['photo']['name'])) ? $_FILES['photo']['name'] : '';
    // 2 nous copions le fichier temporaire de la photo vers notre dossier d'images
    // echo __FILE__; // nous donne le chemin du fichier dans lequel on se trouve
    // echo dirname(__FILE__); // nous donne dossier parent du fichier dans lequel on se trouve
    echo dirname(dirname(__FILE__)); // nous donne dossier parent du dossier parent du fichier dans lequel on se trouve
    $source_photo = $_FILES['photo']['tmp_name'];
    $destination_photo = dirname(dirname(__FILE__)) . '/photos/' . $reference . '_' . $photo;
    copy($source_photo, $destination_photo); // je copie la photo temporaire de $_FILES dans mon dossier d'images
    // 1. requete d'insertion
    $insert_produit = $ecommerce->prepare('INSERT INTO produit(reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) VALUES(:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :photo, :prix, :stock)');
    foreach($_POST as $key => $value) {
      if($key !== 'envoi' && $key !== 'stock' && $key !== 'prix' ) {
        $insert_produit->bindValue(':'. $key, ${$key}, PDO::PARAM_STR);
      }
    }
    // comme photo appartient à $_FILES, nous le rentrons à la main
    $insert_produit->bindValue(':photo', $photo, PDO::PARAM_STR);
    $insert_produit->bindValue(':prix', $prix, PDO::PARAM_INT);
    $insert_produit->bindValue(':stock', $stock, PDO::PARAM_INT);
    $insert_produit->execute();
    $msg = '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Produit enregistré avec succès !</p>';
  } // fin du else qui check la reference
} // fin if envoi

//------- affichage ----------- //
include_once '../inc/header.inc.php';
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
                <input type="text" name="reference" id="reference" class="form-control input-sm" placeholder="Réference" value="">
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="categorie" id="categorie" class="form-control input-sm" placeholder="Catégorie" value="">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="titre" id="titre" class="form-control input-sm" placeholder="Titre" value="">
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="form-group">
                <input type="text" name="couleur" id="couleur" class="form-control input-sm" placeholder="Couleur" value="">
              </div>
            </div>
          </div>

          <div class="form-group">
            <textarea name="description" id="description" class="form-control input-sm" placeholder="Description"></textarea>
          </div>

          <div class="form-group">
            <input style="padding:5px;" type="file" name="photo" id="photo" class="form-control" placeholder="Photo" value="">
          </div>

           <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <label for="taille">taille</label>
                <select name="taille" id="taille">
                  <option value="xl">XL</option>
                  <option value="l">L</option>
                  <option value="m">M</option>
                </select>
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <input type="text" name="prix" id="prix" class="form-control input-sm" placeholder="Prix" value="">
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4">
              <div class="form-group">
                <input type="text" name="stock" id="stock" class="form-control input-sm" placeholder="Stock" value="">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="input-group">
                <span class="input-group-addon">
                  <input id="femme" type="radio" name="sexe" value="f">
                </span>
                <label for="femme" class="form-control">Femme</label>
              </div><!-- /input-group -->
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6">
              <div class="input-group">
                <span class="input-group-addon">
                  <input id="homme" type="radio" name="sexe" value="m">
                </span>
                <label class="form-control" for="homme" >Homme</label>
              </div>
            </div>
          </div>

          <button style="margin-top: 15px;" type="submit" name="envoi" class="btn btn-info btn-block">Ajout</button>

        </form>
      </div>
    </div>
  </div>
</div>

<?php
include_once '../inc/footer.inc.php';