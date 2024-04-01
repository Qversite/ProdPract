<?php
session_start();
if(!isset($_SESSION['user_id']) || isset($_GET['logout'])){
    $_SESSION['user_id'] = null;
    header('Location: login.php');
    exit;
} else {
    include('../Bd/pdo.php');
    include('../Bd/brain.php');
$role = $_SESSION['role'];
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/Style.css">
    <title>Таблицы</title>
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
<div class="main-design container" id="mainDesign">
    <div class="left-part">
    <a href="Main.php"><div class="logo"><img src="../img/Group.svg" alt=""></div></a>
        <div class="main_menu">
            <!-- <a href="Main.php"><div><img src="../img/Домашняя.svg" alt="">Главная</div></a> -->
            <a href="Main.php"><div><img src="../img/Главная.svg" alt="">Главная</div></a>
            <a href="Group.php"><div><img src="../img/Группы.svg" alt="">Группы</div></a>
            <a href="Tables.php"><div><img src="../img/Журнал.svg" alt="">Журналы</div></a>
            <a href="Profile.php"><div><img src="../img/Настройки.svg" alt="">Профиль</div></a>  </div>
            <a href="?logout=1" class="logout"><div class="main_podval"><img src="../img/Выйти.svg" alt="">Выйти</div></a>
    
    </div>
    <div class="right-part">
        <div class="part_header">
            <?php
            global $connection;
            $info = getUserInfoById($_SESSION['user_id']);
            ?>
            <span>Журналы</span>
            <div class="logUserInformation">
                <img src="../img/user_img.png" alt="" width="40">
                <label><?php echo $info['lastname'].'. '.mb_substr($info['name'], 0, 1).'. '.mb_substr($info['surname'], 0, 1)?> </label>
            </div>
        </div>
        <div class="part_bottom">
            <aside>
                <?php
                $groupData = getAllGroup();
                foreach ($groupData as $group){
                    echo "
                            <details class='group-details'>
                            <summary class='group-summary'>".$group['name']."</summary>";
                    echo "<div class=details_list>";
                    $subjectData = getAllSubjectsByGroup($group['name']);
                    foreach ($subjectData as $student){
                        echo "<a href='Table.php?subject=".$student['name']."&group=".$group['name']."'><li>".$student['name']."</li></a>";
                    }
                    if($role) {
                        echo "<a onclick='toggleAddStudentBlock()' class='add_things'>Добавить дисциплину</a>";
                    }
                    echo "</div></details>";
                }
                ?>
            </aside>
        </div>
    </div>
</div>

<!-- Добавление дисциплины -->
<div id="addStudentBlock">
    <form action="functions.php" method="post" class="form-add">
        <div class="form-group">
            <label for="firstName">Название:</label>
            <input type="text" id="firstName" name="name" required>
        </div>

        <div class="form-group">
            <label for="group">Группа:</label>
            <select id="group" name="group" required>
                <?php
                // Пример массива групп
                $groups = getAllGroup();

                // Заполнение выпадающего списка групп
                foreach ($groups as $group) {
                    echo '<option value="' . $group['name'] . '">' . $group['name'] . '</a></option>';
                }
                ?>
            </select>
        </div>

        <button type="submit" name="submit_subject">Добавить</button>
        <button onclick="toggleAddStudentBlock()">Отмена</button>
    </form>
</div>

<script>

    var detailsElements = document.querySelectorAll('.group-details');
    var selected = document.getElementById('group');

    detailsElements.forEach(function(detailsElement) {
        var summaryText = detailsElement.querySelector('.group-summary');

        detailsElement.addEventListener('toggle', function() {
            if (detailsElement.open) {
                var text = summaryText.textContent;
                console.log(text);
                selected.value = text;
            }
        });
    });

    function toggleAddStudentBlock() {
        var addStudentBlock = document.getElementById("addStudentBlock");
        var body = document.getElementById("mainDesign");

        // Изменяем стиль фильтра для эффекта размытия
        body.style.filter = (body.style.filter === "blur(3px)") ? "none" : "blur(3px)";

        // Изменяем видимость блока
        addStudentBlock.style.display = (addStudentBlock.style.display === "block") ? "none" : "block";
    }
</script>
</body>
</html>