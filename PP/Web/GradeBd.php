<?php
include('../Bd/pdo.php');
include('../Bd/brain.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jsonData = json_decode($_POST['jsonData'], true);
    try {
        global $connection;
        $gradesData = $jsonData;
        $subjectName = $gradesData['subject'];
        $subjectId = getIdSubject($subjectName);
            $studentId = getIdStudent($gradesData['name']);
            $key = $gradesData['day'];
            $grade = $gradesData['grade'];
                if(!checkRepeat($studentId, $subjectId, $key)) {
                    $query = $connection->prepare("INSERT INTO grade (grade, subject_id, student_id, lesson_num) VALUES ( :grade, :subject_id, :student_id, :lesson_num)");
                    $query->bindParam(":grade", $grade, PDO::PARAM_STR);
                    $query->bindParam(":subject_id", $subjectId, PDO::PARAM_INT);
                    $query->bindParam(":student_id", $studentId, PDO::PARAM_INT);
                    $query->bindParam(":lesson_num", $key, PDO::PARAM_INT);
                    $query->execute();
                } else {
                    $id = getIdRepeat($studentId, $subjectId, $key);
                    $query = $connection->prepare("UPDATE grade SET grade = :grade WHERE id = :id");
                    $query->bindParam(':grade', $grade);
                    $query->bindParam(':id', $id);
                    $query->execute();
                }

    } catch (PDOException $e) {
        $error = $e->getMessage();

        $response = [
            'data' => $gradesData,
            'test' => 1,
            'error' => $error,
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

} else {
    echo 'Неверный метод запроса';
}
?>