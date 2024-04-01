<?php
session_start();
if(!isset($_SESSION['user_id']) || isset($_GET['logout'])){
    $_SESSION['user_id'] = null;
    header('Location: login.php');
    exit;
} else {
    include('../Bd/pdo.php');
    include('../Bd/brain.php');
    $role = checkRoleUser($_SESSION['user_id']);
    $_SESSION['role'] = $role;
}?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Style.css">
    <title>Главная</title>
</head>
<body>
    <style>
    .main_podval{
    display: flex;
    align-items: center;
    color: white;
    font-size: 24px;
    margin: 350px 48px;
    fill: white;
    width: 50px;
    padding: 5 10px;
    }
    </style>
    <div class="main-design container">
        <div class="left-part">
        <a href="Main.php"><div class="logo"><img src="../img/Group.svg" alt=""></div></a>
        <div class="main_menu">
            <!-- <a href="Main.php"><div><img src="../img/Домашняя.svg" alt="">Главная</div></a> -->
            <a href="Main.php"><div><img src="../img/Главная.svg" alt="">Главная</div></a>
            <a href="Group.php"><div><img src="../img/Группы.svg" alt="">Группы</div></a>
            <a href="Tables.php"><div><img src="../img/Журнал.svg" alt="">Журналы</div></a>
            <a href="Profile.php"><div><img src="../img/Настройки.svg" alt="">Профиль</div></a> </div>   
            <a href="?logout=1" class="logout"><div class="main_podval"><img src="../img/Выйти.svg" alt="">Выйти</div></a>
        
        </div>
        <div class="right-part">
            <div class="part_header">
                <span>Электронный журнал</span>
                <?php
                $info = getUserInfoById($_SESSION['user_id']);
                ?>
                <div class="logUserInformation">
                    <img src="../img/user_img.png" alt="" width="40">
                    <label><?php echo $info['lastname'].'. '.mb_substr($info['name'], 0, 1).'. '.mb_substr($info['surname'], 0, 1)?> </label>
                </div>
            </div>
            <div class="part_bottom">
                <div class="bottom_div">

                        
                    </div>
                    <h2 align=center>IT - Лицей Лидер</h2>
                    <div class="description center">
    
    <p>Это реальность, которую мы создаем в IT-Лицей Лидер . Мы верим, что каждый ребенок имеет право на качественное образование и возможность реализовать свой потенциал. Мы используем инновационные методики обучения, которые помогают детям развивать их навыки и интерес к технологиям. Выбирая IT-Лицей Лидер , вы принимаете решение о качественном образовании для вашего ребенка! Наш лицей - это место, где дети обучаются, развиваются и достигают успеха.
        <br><br>В этом электронном дневнике вы можете оценить успеваемость ваших деток.
</div>
        </div>
        
</body>
</html>