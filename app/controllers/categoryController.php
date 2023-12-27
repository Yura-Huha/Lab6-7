<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    session_start();
    require_once('../db_connect.php');
    require_once('../models/CategoryList.php');
    $cl=new CategoryList($conn);
	
    if($_SERVER['REQUEST_METHOD']=="GET"&&!isset($_GET['id'])){
        if(!isset($_GET['search'])){
            $cl->getAllFromDatabase();
        } else{
            $cl->getAllFromDatabaseBySearchCriteria($_GET['search']);
        }
        echo $cl->convertToJSON();
    }
    if($_SERVER['REQUEST_METHOD']=="GET"&&isset($_GET['id'])){
        $record=$cl->getFromDatabaseById($_GET['id']);
        echo json_encode($record,JSON_UNESCAPED_UNICODE);
    }
    /*if($_SERVER['REQUEST_METHOD']=="POST"){
        $data = json_decode( file_get_contents('php://input') );
        $cl->insertIntoDatabase($data->name);
        echo '{"status":"success"}';
    }*/
    if (isset($_POST['name'])) {
        $result = $cl->insertIntoDatabase($_POST['name']);
        if ($result == "exists") {
            echo '{"status":"error", "message":"Категорія вже існує"}';
        } else {
            echo '{"status":"success"}';
        }
    }
    if($_SERVER['REQUEST_METHOD']=="DELETE"){
        $cl->deleteFromDatabase($_REQUEST['id']); 
        echo $response;
    }
    if($_SERVER['REQUEST_METHOD']=="PUT"){
        $data = json_decode( file_get_contents('php://input') );
        $cl->updateDatabaseById($data->id,$data->name);
        echo '{"status":"success"}';
    }
    
?>