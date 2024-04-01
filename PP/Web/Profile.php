<?php
session_start();

if (!isset($_SESSION['user_id']) || isset($_GET['logout'])) {
    $_SESSION['user_id'] = null;
    header('Location: login.php');
    exit;
} else {
    include('../Bd/pdo.php');
    include('../Bd/brain.php');
    function clearSessionMessages() {
        unset($_SESSION['error_message']);
        unset($_SESSION['success_message']);
    }
    
    $role = checkRoleUser($_SESSION['user_id']);
    $_SESSION['role'] = $role;
    $info = getUserInfoById($_SESSION['user_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       // Изменение пароля
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = 'Все поля должны быть заполнены';
        header('Location: profile.php');
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = 'Новый пароль и подтверждение нового пароля не совпадают';
        header('Location: profile.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $user_info = getUserInfoById($user_id); // Предполагается, что эта функция возвращает массив с данными пользователя, включая пароль

    if (!password_verify($old_password, $user_info['password'])) {
        $_SESSION['error_message'] = 'Старый пароль неверен';
        header('Location: profile.php');
        exit;
    }

    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password = :new_password WHERE id = :user_id');
    $stmt->execute(['new_password' => $new_password_hash, 'user_id' => $user_id]);

    $_SESSION['success_message'] = 'Пароль успешно изменен';
    header('Location: profile.php');
    exit;
}

        // Изменение имени, фамилии и отчества
        if (isset($_POST['change_info'])) {
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $lastname = $_POST['lastname'];

            $stmt = $pdo->prepare('UPDATE users SET name = :name, surname = :surname, lastname = :lastname WHERE id = :user_id');
            $stmt->execute(['name' => $name, 'surname' => $surname, 'lastname' => $lastname, 'user_id' => $_SESSION['user_id']]);
            
            $_SESSION['success_message'] = 'Информация о пользователе успешно обновлена';
            header('Location: profile.php');
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<style>
    body {
    background-image: url('img/background.jpg');
    background-size: cover; /* Покрывает весь экран, сохраняя пропорции */
    background-position: center; /* Центрирует изображение */
    background-repeat: no-repeat; /* Предотвращает повторение изображения */
}


           .form-control {
        display: block;
        margin: 0 auto;
        width: 250px;    
    }

    .profile-container {
        margin-top: 50px;
        text-align: center;
    }

    .profile-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        margin: 0 auto;
    }

    .profile-info {
        margin-top: 20px;
    }

    .profile-info h2 {
        font-size: 24px;
    }

    .profile-info p {
        font-size: 16px;
    }

    .alert {
        margin-top: 10px;
        padding: 10px;
        border: 1px solid transparent;
        border-radius: 5px;
        width: 300px;
        margin: 0 auto;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    a:hover {
        color: blue;
    }
    .close {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
</style>

    </style>
<body>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; ?>
        <span class="close">&times;</span>
    </div>
    <?php clearSessionMessages(); ?>
<?php endif; ?>
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success" id="successAlert">
        <?php echo $_SESSION['success_message']; ?>
        <span class="close">&times;</span>
    </div>
    <?php clearSessionMessages(); ?>
<?php endif; ?>

<div class="container profile-container">
        <img class="profile-image" src="../img/user_img.png" alt="Profile Image">
        <div class="profile-info">
            <h2><?php echo $info['name'] . ' ' . $info['surname']; ?></h2>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; ?>
                    <span class="close">&times;</span>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success" id="successAlert">
                    <?php echo $_SESSION['success_message']; ?>
                    <span class="close">&times;</span>
                </div>
            <?php endif; ?>
            <form method="post" action="profile.php">
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $info['name']; ?>">
                </div>
                <div class="form-group">
                    <label for="surname">Фамилия</label>
                    <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $info['surname']; ?>">
                </div>
                <div class="form-group">
                    <label for="lastname">Отчество</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $info['lastname']; ?>">
                </div>
                
                <button type="submit" class="btn btn-primary" name="change_info">Изменить информацию</button>
        </form>
        <br>
        <form method="post" action="profile.php">
            <div class="form-group">
                <label for="old_password">Старый пароль</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Новый пароль</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Подтверждение нового пароля</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="change_password">Сменить пароль</button>
        </form><br>
        <a href="main.php"><div>Вернуться</div></a>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.alert .close').click(function() {
                $(this).closest('.alert').hide();
            });
        });
    </script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>





</body>
</html>