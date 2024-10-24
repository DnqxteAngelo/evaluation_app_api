<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Evaluation {
        function addEvaluation($json){
            include "connection.php";
        
            $json = json_decode($json, true);
        
            $sql = "INSERT INTO tbl_evaluation(eval_userId, eval_teacherId, eval_semesterId, eval_schoolyearId,
                    eval_periodId, eval_subject, eval_date, eval_modality, eval_yearId) ";
            $sql .= "VALUES(:eval_userId, :eval_teacherId, :eval_semesterId, :eval_schoolyearId, 
            :eval_periodId, :eval_subject, :eval_date, :eval_modality, :eval_yearId)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":eval_userId", $json['eval_userId']);
            $stmt->bindParam(":eval_teacherId", $json['eval_teacherId']);
            $stmt->bindParam(":eval_semesterId", $json['eval_semesterId']);
            $stmt->bindParam(":eval_schoolyearId", $json['eval_schoolyearId']);
            $stmt->bindParam(":eval_periodId", $json['eval_periodId']);
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

        function getEvaluation($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "SELECT tbl_evaluation.eval_id, tbl_user.user_fullname, tbl_teacher.teacher_fullname, tbl_period.period_name, 
                    tbl_semester.sem_name, tbl_schoolyear.sy_name, tbl_evaluation.eval_subject, 
                    tbl_evaluation.eval_date, tbl_evaluation.eval_modality, tbl_year.year_level 
                    FROM tbl_evaluation
                    INNER JOIN tbl_user ON tbl_user.user_id = tbl_evaluation.eval_userId
                    INNER JOIN tbl_teacher ON tbl_teacher.teacher_id = tbl_evaluation.eval_teacherId
                    INNER JOIN tbl_period ON tbl_period.period_id = tbl_evaluation.eval_periodId
                    INNER JOIN tbl_semester ON tbl_semester.sem_id = tbl_evaluation.eval_semesterId
                    INNER JOIN tbl_schoolyear ON tbl_schoolyear.sy_id = tbl_evaluation.eval_schoolyearId
                    INNER JOIN tbl_year ON tbl_year.year_id = tbl_evaluation.eval_yearId
                    WHERE tbl_evaluation.eval_id = :eval_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':eval_id', $json['eval_id']);
            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getEvaluationRecords($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "SELECT 
                        a.act_id AS trans_actId, 
                        a.act_name, 
                        a.act_code, 
                        a.act_person, 
                        COALESCE(SUM(
                            CASE 
                                WHEN e.eval_periodId = :eval_periodId 
                                AND e.eval_teacherId = :eval_teacherId 
                                AND e.eval_semesterId = :eval_semesterId  
                                AND e.eval_schoolyearId = :eval_schoolyearId 
                                THEN 1 
                                ELSE 0 
                            END
                        ), 0) AS tally
                    FROM 
                        tbl_activities a
                    LEFT JOIN 
                        tbl_transaction t ON a.act_id = t.trans_actId
                    LEFT JOIN 
                        tbl_evaluation e ON t.trans_evalId = e.eval_id
                    GROUP BY 
                        a.act_id, a.act_name, a.act_code, a.act_person;
                ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':eval_periodId', $json['eval_periodId']);
            $stmt->bindParam(':eval_teacherId', $json['eval_teacherId']);
            $stmt->bindParam(':eval_semesterId', $json['eval_semesterId']);
            $stmt->bindParam(':eval_schoolyearId', $json['eval_schoolyearId']);
            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }

        function getEvaluationDetails($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "SELECT  
                    te.teacher_fullname, 
                    e.eval_subject, 
                    e.eval_date
                FROM 
                    tbl_evaluation e
                LEFT JOIN 
                    tbl_teacher te ON e.eval_teacherId = te.teacher_id
                WHERE 
                    e.eval_periodId = :eval_periodId
                    AND e.eval_teacherId = :eval_teacherId
                    AND e.eval_semesterId = :eval_semesterId
                    AND e.eval_schoolyearId = :eval_schoolyearId
                ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':eval_periodId', $json['eval_periodId']);
            $stmt->bindParam(':eval_teacherId', $json['eval_teacherId']);
            $stmt->bindParam(':eval_semesterId', $json['eval_semesterId']);
            $stmt->bindParam(':eval_schoolyearId', $json['eval_schoolyearId']);
            $stmt->execute();
            $returnValue = $stmt->fetch(PDO::FETCH_ASSOC);
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
            echo $evaluation->getEvaluation($json);
            break;
        case "getEvaluationRecords":
            echo $evaluation->getEvaluationRecords($json);
            break;
        case "getEvaluationDetails":
            echo $evaluation->getEvaluationDetails($json);
            break;
    }

?>