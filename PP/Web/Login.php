<?php
session_start();
if(isset($_SESSION['user_id'])){
header('Location: Group.php');
exit;
}?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Style.css">
    <title>Авторизация</title>
    <?php
        if(isset($_SESSION["error"])){
            echo "<style>.log_input input{border-bottom:2px solid red;}</style>";
        }
    ?>
</head>
<body>
<div class="log_background">
    <div>
        <form action="" method="post" class="log_form">
            <div><h2>Авторизация</h2></div>
            <div class="log_input">
                <input type="text" name="username" placeholder="Введите логин" required>
                <input type="password" name="password" placeholder="Введите пароль" required>
            </div>
            <div>
                <button type="submit" name="login">Войти</button>
            </div>
            <div><a href="registration.php">Нету аккаунта? Зарегестрироваться</a></div>
        </form>
    </div>
    <?php
    include('../Bd/pdo.php');
    include('../Bd/brain.php');
    if (isset($_POST['login'])) {
        $error = null;
        $username = $_POST['username'];
        $password = $_POST['password'];
        $query = $connection->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (password_verify($password, $result['password'])) {
                $_SESSION['user_id'] = $result['id'];
                header('Location: Group.php');
            }else{
                    $error = "password_error";
                    $_SESSION['error'] = $error;
            }
        }
    }
    ?>
    
</div>
</body>
</html>