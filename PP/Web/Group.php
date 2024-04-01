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
    <div class="left-part" id="left_part">
    <a href="Main.php"><div class="logo"><img src="../img/Group.svg" alt=""></div></a>
        <div class="main_menu">
            <!-- <a href="Main.php"><div><img src="../data/Домашняя.svg" alt="">Главная</div></a> -->
            <a href="Main.php"><div><img src="../img/Главная.svg" alt="">Главная</div></a>
            <a href="Group.php"><div><img src="../img/Группы.svg" alt="">Группы</div></a>
            <a href="Tables.php"><div><img src="../img/Журнал.svg" alt="">Журналы</div></a>
            <a href="Profile.php"><div><img src="../img/Настройки.svg" alt="">Профиль</div></a></div>
            <a href="?logout=1" class="logout"><div class="main_podval"><img src="../img/Выйти.svg" alt="">Выйти</div></a>
        
    </div>
    <div class="right-part" id="right_part">
        <div class="part_header">
            <?php
            global $connection;
            $info = getUserInfoById($_SESSION['user_id']);
            ?>
            <span>Список групп</span>
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
                $studentData = getAllStudentByGroup($group['name']);
                foreach ($studentData as $student){
                    echo "<li class='delete-student' title='Удалить' data-student-id=".$student['id'].">".$student['lastname']." ".$student['name']." ".$student['surname']."</li>";
                }
                    if($role) {
                        echo "<a onclick='toggleAddStudentBlock()' class='add_things'>Добавить студента</a>";
                        echo "<a onclick='toggleDeleteGroup()' class='add_things'>Удалить Группу</a>";
                    }
                    echo "</div></details>";
                }
                if($role){
                    echo '<a onclick="toggleAddGroupBlock()" class="groupAdd">Добавить группу</a>';
                }
            ?>
            </aside>
        </div>
        </div>

    <div id="addGroupBlock" class="add-block">
        <form action="functions.php" method="post" class="form-add">
            <div class="form-group">
                <label for="groupName">Название группы:</label>
                <input type="text" id="groupName" name="group_name" required>
            </div>
            <div>
            <button type="submit" name="submit_group">Добавить группу</button>
            <button onclick="toggleAddGroupBlock()">Отмена</button>
            </div>
        </form>
    </div>
    <!-- Добавление студентов -->
    <div id="addStudentBlock">
        <form action="functions.php" method="post" class="form-add">
            <div class="form-group">
                <label for="firstName">Имя:</label>
                <input type="text" id="firstName" name="name" required>
            </div>

            <div class="form-group">
                <label for="lastName">Фамилия:</label>
                <input type="text" id="lastName" name="lastname" required>
            </div>

            <div class="form-group">
                <label for="middleName">Отчество:</label>
                <input type="text" id="middleName" name="surname" required>
            </div>

            <div class="form-group">
                <label for="group">Группа:</label>
                <select id="group" name="group" required>
                    <?php
                    // Пример массива групп
                    $groups = getAllGroup();

                    // Заполнение выпадающего списка групп
                    foreach ($groups as $group) {
                        echo '<option value="' . $group['name'] . '">' . $group['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="submit_student">Добавить</button>
            <button onclick="toggleAddStudentBlock()">Отмена</button>
        </form>
    </div>
</div>

<script>
    function confirmDeleteStudent(link) {
        var studentId = link.dataset.studentId;

        var confirmDelete = confirm('Вы уверены, что хотите удалить студента? ');
        if (confirmDelete) {
            deleteStudent(studentId);
        } else {
        }
    }

    function deleteStudent(studentId) {
        // Отправьте POST-запрос на сервер с использованием fetch или другого метода
        fetch('functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'student_id=' + encodeURIComponent(studentId),
        })
            .then(response => response.json())
            .then(data => {
                // Обработка ответа от сервера (если необходимо)
                alert(data.message);
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        setTimeout(function() {
            window.location.href = 'functions.php';
        }, 500);
    }
<?php
    if($role){
    echo "document.addEventListener('DOMContentLoaded', function () {
        var deleteLinks = document.querySelectorAll('.delete-student');

        deleteLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                confirmDeleteStudent(link);
            });
        });
    });";}
?>
</script>
<script>
    var detailsElements = document.querySelectorAll('.group-details');
    var selected = document.getElementById('group');
    var group;
    detailsElements.forEach(function(detailsElement) {
        var summaryText = detailsElement.querySelector('.group-summary');

        detailsElement.addEventListener('toggle', function() {
            if (detailsElement.open) {
                var text = summaryText.textContent;
                group = text;
                console.log(group);
                selected.value = group;
            }
        });
    });

    function toggleAddStudentBlock() {
        var addStudentBlock = document.getElementById("addStudentBlock");
        var blur1 = document.getElementById("left_part");
        var blur2 = document.getElementById("right_part");

        // Изменяем стиль фильтра для эффекта размытия
        blur1.style.filter = (blur1.style.filter === "blur(3px)") ? "none" : "blur(3px)";
        blur2.style.filter = (blur2.style.filter === "blur(3px)") ? "none" : "blur(3px)";

        // Изменяем видимость блока
        addStudentBlock.style.display = (addStudentBlock.style.display === "block") ? "none" : "block";
    }

    function toggleAddGroupBlock() {
        var addGroupBlock = document.getElementById("addGroupBlock");
        var blur1 = document.getElementById("left_part");
        var blur2 = document.getElementById("right_part");

        // Изменяем стиль фильтра для эффекта размытия
        blur1.style.filter = (blur1.style.filter === "blur(3px)") ? "none" : "blur(3px)";
        blur2.style.filter = (blur2.style.filter === "blur(3px)") ? "none" : "blur(3px)";

        // Изменяем видимость блока
        addGroupBlock.style.display = (addGroupBlock.style.display === "block") ? "none" : "block";
    }
    function toggleDeleteGroup(){
        var confirmDelete = confirm('Вы уверены, что хотите удалить группу?' + ' ' + group + '?');
        if (confirmDelete) {
            fetch('functions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'group=' + group,
            });
            setTimeout(function() {
                window.location.href = 'functions.php';
            }, 500);
        }
    }
</script>
</body>
</html>