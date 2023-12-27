<?php
    session_start();
    require_once('../db_connect.php');
    if(isset($_POST['login'])){
        $sql = "SELECT * FROM `user` WHERE `login`='".$_POST['login']."' AND `password`='".md5($_POST['password'])."'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $_SESSION['username']='admin';
                echo '{"userlogin":"admin","error":""}';
            } else {
                echo '{"error":"Неправильний логін або пароль"}';
            }
        
    } else if(isset($_GET['action']) && $_GET['action']=='logout'){
        session_destroy();
        echo '{"userlogin":"'.(isset($_SESSION['username'])?$_SESSION['username']:"").'"}';
    } else {
        echo '{"userlogin":"'.(isset($_SESSION['username'])?$_SESSION['username']:"").'"}';
    }
?>