<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Evaluation {
        function addEvaluation($json){
            include "connection.php";
        
            $json = json_decode($json, true);
        
            $sql = "INSERT INTO tbl_evaluation(eval_userId, eval_teacherId, eval_semesterId,
                    eval_subject, eval_date, eval_modality, eval_yearId) ";
            $sql .= "VALUES(:eval_userId, :eval_teacherId, :eval_semesterId,
            :eval_subject, :eval_date, :eval_modality, :eval_yearId)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":eval_userId", $json['eval_userId']);
            $stmt->bindParam(":eval_teacherId", $json['eval_teacherId']);
            $stmt->bindParam(":eval_semesterId", $json['eval_semesterId']);
            $stmt->bindParam(":eval_subject", $json['eval_subject']);
            $stmt->bindParam(":eval_date", $json['eval_date']);
            $stmt->bindParam(":eval_modality", $json['eval_modality']);
            $stmt->bindParam(":eval_yearId", $json['eval_yearId']);
        
            $stmt->execute();
            $evalId = $conn->lastInsertId(); // Get the last inserted ID
        
            return json_encode(['success' => $stmt->rowCount() > 0, 'evalId' => $evalId]);
        }
        

        function getPeriod(){
            include "connection.php";

            $sql = "SELECT * FROM tbl_period";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getSemester(){
            include "connection.php";

            $sql = "SELECT * FROM tbl_semester";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getSchoolYear(){
            include "connection.php";

            $sql = "SELECT * FROM tbl_schoolyear";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getYear(){
            include "connection.php";

            $sql = "SELECT * FROM tbl_year";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getEvaluation(){
            include "connection.php";

            $sql = "SELECT tbl_user.user_fullname, tbl_teacher.teacher_fullname, tbl_period.period_name, 
                    tbl_semester.sem_name, tbl_schoolyear.sy_name, tbl_evaluation.eval_subject, 
                    tbl_evaluation.eval_date, tbl_evaluation.eval_modality, tbl_year.year_level 
                    FROM tbl_evaluation
                    INNER JOIN tbl_user ON tbl_user.user_id = tbl_evaluation.eval_userId
                    INNER JOIN tbl_teacher ON tbl_teacher.teacher_id = tbl_evaluation.eval_teacherId
                    INNER JOIN tbl_period ON tbl_period.period_id = tbl_evaluation.eval_periodId
                    INNER JOIN tbl_semester ON tbl_semester.sem_id = tbl_evaluation.eval_semesterId
                    INNER JOIN tbl_schoolyear ON tbl_schoolyear.sy_id = tbl_evaluation.eval_schoolyearId
                    INNER JOIN tbl_year ON tbl_year.year_id = tbl_evaluation.eval_yearId";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }
    }

    $json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $evaluation = new Evaluation();
    switch ($operation){
        case "addEvaluation":
            echo $evaluation->addEvaluation($json);
            break;
        case "getPeriod":
            echo $evaluation->getPeriod();
            break;
        case "getSemester":
            echo $evaluation->getSemester();
            break;
        case "getSchoolYear":
            echo $evaluation->getSchoolYear();
            break;
        case "getYear":
            echo $evaluation->getYear();
            break;
        case "getEvaluation":
            echo $evaluation->getEvaluation();
            break;
    }

?>