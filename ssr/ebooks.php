<?php

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
session_start();
    if(!isset($_SESSION['username'])){
        header('Location: login.php');
    }
    require_once('../app/db_connect.php');
    require_once('../app/models/EbookList.php');
    require_once('../app/models/CategoryList.php');
    require_once('../app/models/PropertyList.php');
    $el=new EbookList($conn);
	if(!isset($_GET['search'])){
        $el->getAllFromDatabase();
    } else{
        $el->getAllFromDatabaseBySearchCriteria($_GET['search']);
    }

    $pl=new PropertyList($conn);
	$pl->getAllFromDatabase();
    $cl=new CategoryList($conn);
	$cl->getAllFromDatabase();
    if(isset($_POST['action']) && $_POST['action']=='delete'){
        $el->deleteFromDatabase($_POST['id']);
        $el=new EbookList($conn);
	    $el->getAllFromDatabase();
    }
    
    if(isset($_POST['brand'])){
        $ebookId=$el->insertIntoDatabase($_POST['brand'],$_POST['model'],$_POST['category']);       
        $propsArray=$pl->getDataAsArray();
        for($i=0;$i<count($propsArray);$i++){
            $el->addEbookProperty($ebookId,$propsArray[$i]['id'],$_POST['prop_'.$propsArray[$i]['id']]);
        }
        $el=new EbookList($conn);
	    $el->getAllFromDatabase();
    }
?>
<html>
    <head>
        <title>Ebooks List</title>
        <link href="../assets/style.css" rel="stylesheet" />
    </head>
    <body>
        <div class='container'>
            <div class='navigation'>
            <form>
                    <input type="text" name="search" id="searchInput" required/>
                    <button type="submit">Пошук</i></button>
                </form>
                <ul>
                    <li><a href="ebooks.php">Електроні книги</a></li>
                    <li><a href="categories.php">Категорії</a></li>
                    <li><a href="properties.php">Властивості</a></li>
                    <li><a href="logout.php">Вийти</a></li>
                </ul>
            </div>
            <div class='table-content'>
                    <h1 style="text-align: center;">Електроні книги</h1>
                    <table id="dataTable">
                        <thead>
                            <th>Бренд(Виробник)</th>
                            <th>Модель</th>
                            <th>Категорія</th>
                            <th>Характеристики</th>
                            <th>Дії</th>
                        </thead>
                        <tbody>
                            <?php echo $el->getTable();?>
                        </tbody>
                    </table>
            </div>
            <div class='form-content'>
                <form method="POST">
                    <p>Бренд</p>
                    <p><input type="text" placeholder="Бренд(Виробник)" name="brand" required/></p>
                    <p>Модель</p>
                    <p><input type="text" placeholder="Модель" name="model" required/></p>
                    <p>Категорія</p>
                    <p><?php echo $cl->getDataAsSelect(); ?></p>
                    <?php echo $pl->getDataAsInputBlock(); ?>
                    <div style="text-align: center;">
                        <p><button type="submit">Зберегти</button></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>