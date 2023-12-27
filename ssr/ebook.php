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
	$ebook=$el->getFromDatabaseById($_GET['id']);
    $pl=new PropertyList($conn);
	$pl->getAllFromDatabase();
    $cl=new CategoryList($conn);
	$cl->getAllFromDatabase();
    $ebookProps=$el->getEbookPropertiesById($_GET['id']);
    //print_r($ebookProps);
    if(isset($_POST['brand'])){
        $el->updateDatabaseById($_POST['id'],$_POST['brand'],$_POST['model'],$_POST['category']);
        $propsArray=$pl->getDataAsArray();
        for ($i=0;$i<count($propsArray);$i++){
            $el->refreshEbookProperty($_POST['id'],$propsArray[$i]['id'],$_POST['prop_'.$propsArray[$i]['id']]);
        }
        header('Location:ebooks.php');
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
                <ul>
                    <li><a href="ebooks.php">Електронні книги</a></li>
                    <li><a href="categories.php">Категорії</a></li>
                    <li><a href="properties.php">Властивості</a></li>
                    <li><a href="logout.php">Вийти</a></li>
                </ul>
            </div>
            
            <div class='form-content'>
                <form method="POST">
                    <p>Бренд</p>
                    <p><input value="<?php echo $ebook['brand'];?>" type="text" placeholder="Модель" name="brand" required/></p>
                    <p>Модель</p>
                    <p><input value="<?php echo $ebook['model'];?>" type="text" placeholder="Виробник" name="model" required/></p>
                    <p>Категорія</p>
                    <p><?php echo $cl->getDataAsSelectWithSelectedOption($ebook['category_id']); ?></p>
                    <?php echo $pl->getDataAsInputBlockWithValues($ebookProps); ?>
                    <p><input value="<?php echo $ebook['id'];?>" type="hidden" name="id" required/></p>
                    <div style="text-align: center;">
                        <p><button type="submit">Зберегти</button></p>
                    </div>
                </form>
            </div>
            <div></div>
        </div>
    </body>
</html>