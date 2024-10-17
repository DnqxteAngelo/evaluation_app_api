<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Comment {
        function addComments($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "INSERT INTO tbl_comments(comment_evalId, comment_timeId, comment_text) ";
            $sql .= "VALUES(:comment_evalId, :comment_timeId, :comment_text)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":comment_evalId", $json['comment_evalId']);
            $stmt->bindParam(":comment_timeId", $json['comment_timeId']);
            $stmt->bindParam(":comment_text", $json['comment_text']);

            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

            return $returnValue;
        }

        function getComments(){
            include "connection.php";

            $sql = "SELECT tbl_evaluation.eval_subject, tbl_teacher.teacher_fullname, 
                    tbl_time.time_range, tbl_comments.comment_text
                    FROM tbl_comments
                    INNER JOIN tbl_evaluation ON tbl_evaluation.eval_id = tbl_comments.comment_evalId
                    INNER JOIN tbl_teacher ON tbl_teacher.teacher_id = tbl_evaluation.eval_teacherId
                    INNER JOIN tbl_time ON tbl_time.time_id = tbl_comments.comment_timeId";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }
    }

    $json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $comment = new Comment();
    switch ($operation){
        case "addComments":
            echo $comment->addComments($json);
            break;
        case "getComments":
            echo $comment->getComments();
            break;
    }

?>