<?php
session_start();
if(isset($_SESSION['user_id'])){
    header('Location: main.php');
    exit;
}

include('../Bd/pdo.php');
include('../Bd/brain.php');

if (isset($_POST['register'])) {
    if ($_POST['captcha'] !== $_SESSION['captcha_result']) {
        echo '<script>alert("Неправильная капча!");</script>';
        generateCaptcha();
    } else {
        $username = $_POST['username'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'];
        $password = $_POST['password'];
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Validate username (at least 4 characters)
        if (!preg_match('/^.{4,}$/u', $username)) {
            echo '<script>alert("Имя пользователя должно содержать не менее 4 символов!");</script>';
            generateCaptcha();
            exit;
        }

        // Validate name, lastname, and surname (only Russian characters)
        if (!preg_match('/^[а-яА-ЯёЁ\s]+$/u', $name) ||
            !preg_match('/^[а-яА-ЯёЁ\s]+$/u', $lastname) ||
            !preg_match('/^[а-яА-ЯёЁ\s]+$/u', $surname)) {
            echo '<script>alert("Имя, фамилия и отчество должны содержать только кириллицу!");</script>';
            generateCaptcha();
            exit;
        }

        // Validate password (at least 4 characters)
        if (!preg_match('/^.{4,}$/u', $password)) {
            echo '<script>alert("Пароль должен содержать не менее 4 символов!");</script>';
            generateCaptcha();
            exit;
        }

        // Rest of your code...

        // Check if the username already exists
        $query = $connection->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            echo '<script>alert("Этот адрес уже зарегистрирован!");</script>';
            generateCaptcha();
            exit;
        }

        // If the username is unique, insert the new user
        $query = $connection->prepare("INSERT INTO users(username, name, lastname, surname, password) VALUES (:username, :name, :lastname, :surname, :password_hash)");
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->bindParam("name", $name, PDO::PARAM_STR);
        $query->bindParam("lastname", $lastname, PDO::PARAM_STR);
        $query->bindParam("surname", $surname, PDO::PARAM_STR);
        $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
        $result = $query->execute();

        if ($result) {
            header('Location: LogIn.php');
            exit;
        }
    }
} else {
    generateCaptcha();
}

function generateRandomString($length = 6)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function generateCaptcha()
{
    $captcha_string = generateRandomString(6);
    $_SESSION['captcha_result'] = $captcha_string;
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
    <title>Регистрация</title>
</head>
<body>
<div class="log_background">
    <div>
        <form action="" method="post" class="log_form">
            <h2>Регистрация</h2>
            <div class="log_input">
                <input type="text" name="username" placeholder="Введите логин" required>
                <input type="text" name="name" placeholder="Введите имя" required>
                <input type="text" name="lastname" placeholder="Введите фамилию" required>
                <input type="text" name="surname" placeholder="Введите отчество" required>
                <input type="password" name="password" placeholder="Введите пароль" required>
                <input type="password" name="verify_password" placeholder="Подтвердите пароль" required>
                <div class="log_input">
                    <label for="captcha" class="captcha_text"><?php echo $_SESSION['captcha_result']; ?></label>
                    <input type="text" name="captcha" id="captcha" placeholder="Капча" required>
                </div>
                <div>
                    <button type="submit" name="register">Зарегистрироваться</button>
                </div>
            </div>
            <div><a href="Login.php">Есть аккаунт? Войти</a></div>
        </form>
    </div>
</div>
</body>
</html>