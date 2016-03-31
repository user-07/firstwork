<a href="<?= URL ?>"><img src="<?= URL ?>/assets/images/logo.png" alt=""></a>
<nav class="collapse navbar-collapse col-md-10">
<ul class="nav navbar-nav navbar-inverse">
<?php if(empty($_SESSION['utilisateur'])) :  ?>
<li><a href="<?= URL ?>/inscription.php">inscription</a></li>
<li><a href="<?= URL ?>/connexion.php">connexion</a></li>
<?php endif; ?>

<li><a href="<?= URL ?>/boutique.php">accès boutique</a></li>
<li><a href="<?= URL ?>/panier.php">panier</a></li>

<?php if(userConnected()) : ?>
<li><a href="<?= URL ?>/profil.php">profil</a></li>
<li><a href="<?= URL ?>/connexion.php?action=deconnexion">deconnexion</a></li>
<?php endif; ?>

<?php if(userAdmin()) : ?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <span class="caret"></span></a>
	<ul class="dropdown-menu" role="menu">
		<li><a href="<?= URL ?>/admin/gestion_membres.php">Gestion des membres</a></li>
		<li><a href="<?= URL ?>/admin/gestion_commandes.php">Gestion des commandes</a></li>
		<li><a href="<?= URL ?>/admin/gestion_boutique.php?action=affichage">Gestion de la boutique</a></li>
	</ul>
</li>
<?php endif; ?>
</ul>
</nav>