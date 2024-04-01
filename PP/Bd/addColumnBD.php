<?php
include('../Bd/pdo.php');
include('../Bd/module_global.php');
global $connection;
$sql = '
CREATE DATABASE IF NOT EXISTS ваша_база_данных CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Создание таблицы "users" с полем "name"
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Создание таблицы "groups"
CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Создание таблицы "subject"
CREATE TABLE subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    group_id INT,
    FOREIGN KEY (group_id) REFERENCES groups(id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Создание таблицы "student"
CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    group_id INT,
    FOREIGN KEY (group_id) REFERENCES groups(id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Создание таблицы "grade"
CREATE TABLE grade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade VARCHAR(255) NOT NULL,
    subject_id INT,
    student_id INT,
    lesson_num INT,
    FOREIGN KEY (subject_id) REFERENCES subject(id),
    FOREIGN KEY (student_id) REFERENCES student(id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;
';
$stmt = $connection->prepare($sql);
$stmt->execute();

$sql = "
INSERT INTO users (username, name, lastname, surname, password, role)
VALUES ('admin', 'Admin', 'Admin', 'Admin', :password, 'admin');";
$stmt = $connection->prepare($sql);
$password_hash = password_hash('123', PASSWORD_BCRYPT);
$stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);
$stmt->execute();

$sql = "
INSERT INTO users (username, name, lastname, surname, password, role)
VALUES ('user', 'User', 'User', 'User', :password, 'user');";
$stmt = $connection->prepare($sql);
$stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);
$stmt->execute();

$group = 'Ис-4';
$sql = "INSERT INTO groups (name) VALUES (:group)";
$stmt = $connection->prepare($sql);
$stmt->bindParam(":group", $group, PDO::PARAM_STR);
$stmt->execute();



$id = getIdGroup($group);
echo $id;
$sql = "
INSERT INTO student (name, lastname, surname, group_id) VALUES
('Виктор', 'Перл', 'Сергеевич', :id);";
$stmt = $connection->prepare($sql);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: ../Web/Login.php');