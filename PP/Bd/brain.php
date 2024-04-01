<?php
function getUserInfoById($id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Выполнение запроса
    $stmt->execute();

    // Получение результата
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
function checkRepeat($studentId, $subjectId, $key){
    global $connection;
    $query = $connection->prepare("SELECT id FROM grade WHERE subject_id = :subjectId AND student_id = :studentId AND lesson_num = :lesson_num");
    $query->bindParam(":subjectId", $subjectId, PDO::PARAM_INT);
    $query->bindParam(":studentId", $studentId, PDO::PARAM_INT);
    $query->bindParam(":lesson_num", $key, PDO::PARAM_INT);
    $query->execute();
    if(empty($query -> fetchColumn())){
        return false;
    } else {
        return true;
    }
}
function getIdRepeat($studentId, $subjectId, $key){
    global $connection;
    $query = $connection->prepare("SELECT id FROM grade WHERE subject_id = :subjectId AND student_id = :studentId AND lesson_num = :lesson_num");
    $query->bindParam(":subjectId", $subjectId, PDO::PARAM_INT);
    $query->bindParam(":studentId", $studentId, PDO::PARAM_INT);
    $query->bindParam(":lesson_num", $key, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchColumn();
}
function getIdSubject(string $subject)
{
    global $connection; // Предполагается, что $connection - это глобальная переменная или передана как аргумент
    $query = $connection->prepare("SELECT id FROM subject WHERE name = :subject");
    $query->bindParam(":subject", $subject, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchColumn();
}

function getIdStudent(string $student)
{
    global $connection; // Предполагается, что $connection - это глобальная переменная или передана как аргумент
    $query = $connection->prepare("SELECT id FROM student WHERE lastname = :student");
    $query->bindParam(":student", $student, PDO::PARAM_STR);
    $query->execute();
    $id = $query->fetchColumn();
    return $id;
}
function getIdGroup(string $groupName){
    global $connection;
    $query = $connection->prepare("SELECT id FROM groups WHERE name = :groupName");
    $query->bindParam(":groupName", $groupName, PDO::PARAM_STR);
    $query->execute();
    $id = $query->fetchColumn();
    return $id;
}
function getGradesByStudentIdAndSubjectId($studentId, $subjectId){
    global $connection;
    $query = $connection->prepare("SELECT * FROM grade WHERE student_id = :studentId AND subject_id = :subjectId ORDER BY lesson_num ASC");
    $query->bindParam(":studentId", $studentId, PDO::PARAM_STR);
    $query->bindParam(":subjectId", $subjectId, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll();
}
function getAllGroup(){
    global $connection;
    $query = $connection->prepare("SELECT * FROM groups");
    $query->execute();
    return $query->fetchAll();
}
function getAllStudentByGroup($groupName){
    global $connection;
    $GroupId = getIdGroup($groupName);
    $query = $connection->prepare("SELECT * FROM student WHERE group_id = :groupId");
    $query->bindParam(":groupId", $GroupId, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll();
}
function getAllSubjectsByGroup($groupName){
    global $connection;
    $GroupId = getIdGroup($groupName);
    $query = $connection->prepare("SELECT * FROM subject WHERE group_id = :groupId");
    $query->bindParam(":groupId", $GroupId, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchAll();
}
function checkRoleUser($userId){
    $user = getUserInfoById($userId);
    if($user['role'] == 'admin'){
        return true;
    } else {
        return false;
    }
}