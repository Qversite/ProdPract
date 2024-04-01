<?php
include('../Bd/pdo.php');
include('../Bd/brain.php');
$group = $_GET['group'];
$subject = $_GET['subject'];
$subjectId = getIdSubject($subject);
$query = $connection->prepare("SELECT id FROM groups WHERE name = :group");
$query->bindParam(":group", $group, PDO::PARAM_STR);
$query->execute();
$groupId = $query->fetchColumn();

$query = $connection->prepare("SELECT * FROM student WHERE group_id = :groupId");
$query -> bindParam(":groupId", $groupId, PDO::PARAM_STR);
$query -> execute();
$data = $query -> fetchAll(PDO::FETCH_ASSOC);
session_start();
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../css/Style.css">
    <title><?php echo $group?></title>
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
        <div class="logo"><img src="../img/Group.svg" alt=""></div>
        <div class="main_menu">
            <!-- <a href="Main.php"><div><img src="../data/Домашняя.svg" alt="">Главная</div></a> -->
            <a href="Main.php"><div><img src="../img/Главная.svg" alt="">Главная</div></a>
            <a href="Group.php"><div><img src="../img/Группы.svg" alt="">Группы</div></a>
            <a href="Tables.php"><div><img src="../img/Журнал.svg" alt="">Журналы</div></a>
            <a href="Profile.php"><div><img src="../img/Настройки.svg" alt="">Профиль</div></a>  </div>
            <a href="?logout=1" class="logout"><div class="main_podval"><img src="../img/Выйти.svg" alt="">Выйти</div></a>
        
    </div>
    <div class="right-part">
        <div class="part_header">
            <?php
            $info = getUserInfoById($_SESSION['user_id']);
            ?>
            <span>Группа : <?php echo $group?></span>
            <span>Предмет : <?php echo $subject?></span>
            <div class="logUserInformation">
                <img src="../img/user_img.png" alt="" width="40">
                <label><?php echo $info['lastname'].'. '.mb_substr($info['name'], 0, 1).'. '.mb_substr($info['surname'], 0, 1)?> </label>
            </div>
        </div>
        <div class="part_bottom">
<form id="gradeForm" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
    <table border="1">
        <thead>
        <tr>
            <th>ФИО</th>
            <?php for ($day = 1; $day <= 31; $day++): ?>
                <th><?= $day ?></th>
            <?php endfor; ?>
            <th>Средний бал</th> 
        </tr>
        </thead>
        <tbody>
            
        <?php
       foreach ($data as $name) {
        echo '<tr> <td>'.$name['lastname'].'</td>';
        $gradeData = getGradesByStudentIdAndSubjectId($name['id'], $subjectId);
        $totalGrades = 0;
        $numGrades = 0;
        for ($day = 1; $day <= 31; $day++) {
            $found = false;
            foreach ($gradeData as $grade) {
                if ($grade['lesson_num'] == $day) {
                    if($role){
                        echo '<td contenteditable="true" class="editable-cell" data-original-value="" oninput="limitInput(this, 1)">' . $grade['grade'] . '</td>';
                    } else {
                        echo '<td data-original-value="">' . $grade['grade'] . '</td>';
                    }
                    $totalGrades += intval($grade['grade']);
                    $numGrades++;
                    $found = true;
                    break;
                }
            }
    
            if (!$found) {
                if($role){
                    echo '<td contenteditable="true" class="editable-cell" data-original-value="" oninput="limitInput(this)"></td>';
                } else {
                    echo '<td data-original-value=""></td>';
                }
            }
        }
    
        $averageGrade = $numGrades > 0 ? round($totalGrades / $numGrades, 2) : 0;
        echo '<td>'.$averageGrade.'</td>';
        echo '</tr>';
    }
        ?>
        
        </tbody>
    </table>
</form>
    </div>
</div>
<script>
    function limitInput(element) {
        var allowedCharactersRegex = /[2345НнуУ]/g;

        var sanitizedText = element.textContent.match(allowedCharactersRegex);

        if (sanitizedText !== null) {
            element.textContent = sanitizedText.join('');
        } else {
            element.textContent = '';
        }

        var maxLength = 1;
        if (element.textContent.length > maxLength) {
            element.textContent = element.textContent.substring(0, maxLength);
        }
    }
    $(document).ready(function () {
        // Initialize original values
        $('.editable-cell').each(function () {
            $(this).attr('data-original-value', $(this).text());
        });

        function sendData(element) {
            var cell = $(element);
            var originalValue = cell.attr('data-original-value');
            var currentValue = cell.text();
            subjectValue = <?php echo '"'.$_GET['subject'].'"'?>;
            console.log(subjectValue);
            if (originalValue !== currentValue) {
                var studentDataObject = {
                    'name': cell.closest('tr').find('td:first-child').text(),
                    'day': cell.index(),
                    'grade': currentValue,
                    'subject': subjectValue
                };

                var jsonData = JSON.stringify(studentDataObject);

                $.ajax({
                    type: 'POST',
                    url: 'GradeBd.php',
                    data: {jsonData: jsonData},
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (error) {
                        console.error('Error:', error);
                    }
                });

                // Update original value after sending
                cell.attr('data-original-value', currentValue);
            }
        }

        $('.editable-cell').on('input', function () {
            sendData(this);
        });
    });
</script>

</body>
</html>