<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header('Location: login.php');
    }
    require_once('../app/db_connect.php');
    require_once('../app/models/CategoryList.php');
    $cl=new CategoryList($conn);
	if(!isset($_GET['search'])){
        $cl->getAllFromDatabase();
    } else{
        $cl->getAllFromDatabaseBySearchCriteria($_GET['search']);
    }

    if(isset($_POST['action']) && $_POST['action']=='delete'){
        $cl->deleteFromDatabase($_POST['id']);
        $cl=new CategoryList($conn);
	    $cl->getAllFromDatabase();
    }
    if(isset($_POST['name'])){
        $cl->insertIntoDatabase($_POST['name']);
        $cl=new CategoryList($conn);
	    $cl->getAllFromDatabase();
    }
?>
<html>
    <head>
        <title>Categories List</title>
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
                    <h1 style="text-align: center;">Категорії</h1>
                    <table id="dataTable">
                        <thead>
                            <th>Назва</th>
                            <th>Дії</th>
                        </thead>
                        <tbody>
                            <?php echo $cl->getTable();?>
                        </tbody>
                    </table>
            </div>
            <div class='form-content'>
                <form method="POST">
                    <p>Назва категорії</p>
                    <p><input type="text" placeholder="Назва" name="name" required/></p>
                    <div style="text-align: center;">
                        <p><button type="submit">Зберегти</button></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>