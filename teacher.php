<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Teacher {
        function addTeacher($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "INSERT INTO tbl_teacher(teacher_fullname, teacher_deptId) ";
            $sql .= "VALUES(:teacher_fullname, :teacher_deptId)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":teacher_fullname", $json['teacher_fullname']);
            $stmt->bindParam(":teacher_deptId", $json['teacher_deptId']);

            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

            return $returnValue;
        }

        function getTeacher(){
            include "connection.php";

            $sql = "SELECT tbl_department.dept_name, tbl_teacher.teacher_fullname FROM tbl_teacher
                    INNER JOIN tbl_department ON tbl_department.dept_id = tbl_teacher.teacher_deptId";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }
    }

    $json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $teacher = new Teacher();
    switch ($operation){
        case "addTeacher":
            echo $teacher->addTeacher($json);
            break;
        case "getTeacher":
            echo $teacher->getTeacher();
            break;
    }

?>