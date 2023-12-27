<?php
$route=explode("/",$_SERVER['REQUEST_URI']);
$endpoint=explode("?",$route[4])[0];
if($endpoint=="categories"){
    require_once('../controllers/categoryController.php');
} else if($endpoint=="properties"){
    require_once('../controllers/propertyController.php');
} else if($endpoint=="ebooks"){
    require_once('../controllers/ebookController.php');
} else if($endpoint=="login"){
    require_once('../controllers/loginController.php');
} 
?>