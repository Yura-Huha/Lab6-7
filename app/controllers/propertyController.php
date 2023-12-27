<?php
  ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
  session_start();
    if(!isset($_SESSION['username'])){
        echo '{"error":"Unauthorized"}';
        die();
    }
    require_once('../db_connect.php');
    require_once('../models/PropertyList.php');
    $pl=new PropertyList($conn);
    if($_SERVER['REQUEST_METHOD']=="GET"&&!isset($_GET['id'])){
        if(!isset($_GET['search'])){
            $pl->getAllFromDatabase();
        } else{
            $pl->getAllFromDatabaseBySearchCriteria($_GET['search']);
        }
        echo $pl->convertToJSON();
    }
    if($_SERVER['REQUEST_METHOD']=="GET"&&isset($_GET['id'])){
        $record=$pl->getFromDatabaseById($_GET['id']);
        echo json_encode($record,JSON_UNESCAPED_UNICODE);
    }
    /*if($_SERVER['REQUEST_METHOD']=="POST"){
        $data = json_decode( file_get_contents('php://input') );
        $pl->insertIntoDatabase($data->name,$data->units);
        echo '{"status":"success"}';
    }*/
    if(isset ($_POST['name'])){
        $result = $pl->insertIntoDatabase($_POST['name'],$_POST['units']);
        if($result == "exists"){
            echo '{"status":"error", "message":"Характеристика вже існує"}';
        } else {
            echo '{"status":"success"}';
        }
    }

    if($_SERVER['REQUEST_METHOD']=="DELETE"){
        $pl->deleteFromDatabase($_REQUEST['id']);
        echo $response;
    }
    if($_SERVER['REQUEST_METHOD']=="PUT"){
        $data = json_decode( file_get_contents('php://input') );
        $pl->updateDatabaseById($data->id,$data->name,$data->units);
        echo '{"status":"success"}';
    }
?>