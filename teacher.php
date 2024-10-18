<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Teacher {
        function addTeacher($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "INSERT INTO tbl_teacher(teacher_fullname, teacher_collegeId, teacher_totalYears, 
            teacher_yearHired, teacher_yearReg, teacher_educAttain, teacher_profLicense, 
            teacher_empStatus, teacher_rank) ";
            $sql .= "VALUES(:teacher_fullname, :teacher_collegeId, :teacher_totalYears,
            :teacher_yearHired, :teacher_yearReg, :teacher_educAttain, :teacher_profLicense, 
            :teacher_empStatus, :teacher_rank )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":teacher_fullname", $json['teacher_fullname']);
            $stmt->bindParam(":teacher_collegeId", $json['teacher_collegeId']);
            $stmt->bindParam(":teacher_totalYears", $json['teacher_totalYears']);
            $stmt->bindParam(":teacher_yearHired", $json['teacher_yearHired']);
            $stmt->bindParam(":teacher_yearReg", $json['teacher_yearReg']);
            $stmt->bindParam(":teacher_educAttain", $json['teacher_educAttain']);
            $stmt->bindParam(":teacher_profLicense", $json['teacher_profLicense']);
            $stmt->bindParam(":teacher_empStatus", $json['teacher_empStatus']);
            $stmt->bindParam(":teacher_rank", $json['teacher_rank']);

            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

            return $returnValue;
        }

        function getTeacher(){
            include "connection.php";

            $sql = "SELECT tbl_teacher.teacher_id, tbl_teacher.teacher_fullname, tbl_college.college_name, tbl_teacher.teacher_empStatus
                    FROM tbl_teacher
                    JOIN tbl_college ON tbl_teacher.teacher_collegeId = tbl_college.college_id";
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