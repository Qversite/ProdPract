<?php
include('../Bd/pdo.php');
include('../Bd/brain.php');
    if(isset($_POST['submit_student'])){
        global $connection;
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'];
        $group_id = getIdGroup($_POST['group']);
        $query = $connection->prepare("INSERT INTO student (name, lastname, surname, group_id) VALUES ( :name, :lastname, :surname, :groupId)");
        $query->bindParam(":name", $name, PDO::PARAM_STR);
        $query->bindParam(":lastname", $lastname, PDO::PARAM_STR);
        $query->bindParam(":surname", $surname, PDO::PARAM_STR);
        $query->bindParam(":groupId", $group_id, PDO::PARAM_STR);
        $query->execute();
        header("Location: Group.php");
        exit;
    } else if(isset($_POST['submit_group'])){
        global $connection;
        $groupName = $_POST['group_name'];
        $query = $connection->prepare("INSERT INTO groups (name) VALUES ( :name )");
        $query->bindParam(":name", $groupName, PDO::PARAM_STR);
        $query->execute();
        header("Location: Group.php");
        exit;
    } else if(isset($_POST['submit_subject'])) {
        global $connection;
        $name = $_POST['name'];
        $group_id = getIdGroup($_POST['group']);
        $query = $connection->prepare("INSERT INTO subject (name, group_id) VALUES ( :name, :group)");
        $query->bindParam(":name", $name, PDO::PARAM_STR);
        $query->bindParam(":group", $group_id, PDO::PARAM_INT);
        $query->execute();
        header("Location: Tables.php");
        exit;
    } else if(isset($_POST['student_id'])){
            global $connection;
            $id = $_POST['student_id'];
            try{
            $sql = "DELETE FROM grade WHERE student_id = :studentId;";
            $query = $connection->prepare($sql);
            $query->bindParam(":studentId", $id, PDO::PARAM_INT);
            $query->execute();

            $sql = "DELETE FROM student WHERE id = :studentId;";
            $query = $connection->prepare($sql);
            $query->bindParam(":studentId", $id, PDO::PARAM_INT);
            $query->execute();
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['message' => $e->getMessage()]);
            }
            exit;
    } else if(isset($_POST['group'])) {
        global $connection;
        $id = getIdGroup($_POST['group']);
            try {
                $sql = "DELETE FROM grade WHERE student_id IN (SELECT student_id FROM student WHERE group_id = :group_id)";
                $query = $connection->prepare($sql);
                $query->bindParam(":group_id", $id, PDO::PARAM_INT);
                $query->execute();

                $sql = "DELETE FROM student WHERE group_id = :group_id";
                $query = $connection->prepare($sql);
                $query->bindParam(":group_id", $id, PDO::PARAM_INT);
                $query->execute();

                $sql = "DELETE FROM subject WHERE group_id = :group_id";
                $query = $connection->prepare($sql);
                $query->bindParam(":group_id", $id, PDO::PARAM_INT);
                $query->execute();

                $sql = "DELETE FROM groups WHERE id = :group_id";
                $query = $connection->prepare($sql);
                $query->bindParam(":group_id", $id, PDO::PARAM_INT);
                $query->execute();
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['message' => $e->getMessage()]);
            }
        exit;
    } else {
        echo '<script type="text/javascript">window.location.href = document.referrer;</script>';
    }
