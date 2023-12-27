<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
    session_start();
    if(!isset($_SESSION['username'])){
        header('Location: login.php');
    }
    require_once('../app/db_connect.php');
    require_once('../app/models/PropertyList.php');
    $pl=new PropertyList($conn);
	$property=$pl->getFromDatabaseById($_GET['id']);
    
    if(isset($_POST['name'])){
        $pl->updateDatabaseById($_POST['id'],$_POST['name'],$_POST['units']);
        header('Location:properties.php');
    }
?>
<html>
    <head>
        <title>Properties List</title>
        <link href="../assets/style.css" rel="stylesheet" />
    </head>
    <body>
        <div class='container'>
            <div class='navigation'>
                <ul>
                    <li><a href="ebooks.php">Електроні книги</a></li>
                    <li><a href="categories.php">Категорії</a></li>
                    <li><a href="properties.php">Властивості</a></li>
                    <li><a href="logout.php">Вийти</a></li>
                </ul>
            </div>
            
            <div class='form-content'>
                <form method="POST">
                    <p>Назва:</p>
                    <p><input value="<?php echo $property['name'];?>" type="text" placeholder="Назва" name="name" required/></p>
                    <p>Одиниця вимірювання</p>
                    <p><input value="<?php echo $property['units'];?>" type="text" placeholder="Одиниці вимірювання" name="units" required/></p>
                    <p><input value="<?php echo $property['id'];?>" type="hidden" name="id" required/></p>
                    <div style="text-align: center;">
                        <p><button type="submit">Зберегти</button></p>
                    </div>
                    <p id="propertyErrorText"></p>
                    <p id="propertyDeleteErrorText"></p>
                </form>
            </div>
            <div></div>
        </div>
    </body>
</html>