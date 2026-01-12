<?php
session_start();
session_destroy();
header("Location: /-e-commerce-dynamique/public/items.php");
exit;
