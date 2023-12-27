<?php
require_once('../app/db_connect.php');
    session_start();
    if(isset($_SESSION['username'])){
        header('Location: ebooks.php');
    }
    $loginError='';
    if(isset($_POST['login'])){
        $sql = "SELECT * FROM `user` WHERE `login`='".$_POST['login']."' AND `password`='".md5($_POST['password'])."'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $_SESSION['username']='admin';
                header('Location: ebooks.php');
            } else {
                $loginError='Неправильний логін або пароль';
            }
        
    }
?>
<html>
    <head>
        <title>Login</title>
        <link href="../assets/style.css" rel="stylesheet" />
    </head>
    <body>
        <div class='container login-container'>
            <div class='form-content'>
                <h1 style="text-align: center;">Вхід</h1>
                <form method="POST">
                <div style="text-align: center;">
                        <p><input type="text" placeholder="Логін" name="login" required/></p>
                        <p><input type="password" placeholder="Пароль" name="password" required/></p>
                        <p><button type="submit">Увійти</button></p>
                        <p><?php echo $loginError; ?></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>