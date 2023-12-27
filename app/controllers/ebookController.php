<?php
    session_start();
    if(!isset($_SESSION['username'])){
        echo '{"error":"Unauthorized"}';
        die();
    }
    require_once('../db_connect.php');
    require_once('../models/EbookList.php');
    require_once('../models/PropertyList.php');
    $pl=new PropertyList($conn);
    $pl->getAllFromDatabase();
    $el=new EbookList($conn);
    if($_SERVER['REQUEST_METHOD']=="GET"&&!isset($_GET['id'])){
        if(!isset($_GET['search'])){
            $el->getAllFromDatabase();
        } else{
            $el->getAllFromDatabaseBySearchCriteria($_GET['search']);
        }
        echo $el->convertToJSON();
    }

    if($_SERVER['REQUEST_METHOD']=="GET"&&isset($_GET['id'])){
        $record=$el->getFromDatabaseById($_GET['id']);
        $record['properties']=$el->getEbookPropertiesById($_GET['id']);
        echo json_encode($record,JSON_UNESCAPED_UNICODE);
    }
    
   if($_SERVER['REQUEST_METHOD']=="POST"){
        $data = json_decode( file_get_contents('php://input') );
        $ebookId=$el->insertIntoDatabase($data->brand,$data->model,$data->category);   
        //echo json_encode($propsArray);
        for($i=0;$i<count($propsArray);$i++){
            $el->addEbookProperty($ebookId,$propsArray[$i]['id'],$data->{'prop_'.$propsArray[$i]['id']});
        }
    }
    if($_SERVER['REQUEST_METHOD']=="DELETE"){
        $el->deleteFromDatabase($_REQUEST['id']);
        echo '{"status":"success"}';
    }
    if($_SERVER['REQUEST_METHOD']=="PUT"){
        $data = json_decode( file_get_contents('php://input') );
        $el->updateDatabaseById($data->id,$data->brand,$data->model,$data->category);
        $propsArray=$pl->getDataAsArray();
        for ($i=0;$i<count($propsArray);$i++){
            $el->refreshEbookProperty($data->id,$propsArray[$i]['id'],$data->{'prop_'.$propsArray[$i]['id']});
        }
        echo json_encode($propsArray);
        echo '{"status":"success"}';
    }
    
?>