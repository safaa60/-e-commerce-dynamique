<?php
session_start();
session_destroy();
header("Location: /-e-commerce-dynamique/public/items.php");
exit;
//Redirige l’utilisateur vers la page des produits après la déconnexion.