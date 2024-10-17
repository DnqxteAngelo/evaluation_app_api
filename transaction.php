<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

class Transaction {
    function addTransaction($json){
        include "connection.php";

        $json = json_decode($json, true);

        $sql = "INSERT INTO tbl_transaction(trans_evalId, trans_timeId, trans_actId) ";
        $sql .= "VALUES(:trans_evalId, :trans_timeId, :trans_actId)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":trans_evalId", $json['trans_evalId']);
        $stmt->bindParam(":trans_timeId", $json['trans_timeId']);
        $stmt->bindParam(":trans_actId", $json['trans_actId']);

        $stmt->execute();
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

        return $returnValue;
    }

    function getActivities() {
        include "connection.php";

        // Fetch both student and teacher activities in one query
        $sql = "SELECT act_id, act_code, act_name, act_person FROM tbl_activities WHERE act_person IN ('S', 'T')";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = null; $stmt = null;

        return json_encode($returnValue);
    }

    function getTimeRange() {
        include "connection.php";

        // Fetch both student and teacher activities in one query
        $sql = "SELECT time_id, time_range FROM tbl_time";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = null; $stmt = null;

        return json_encode($returnValue);
    }

    function countActivityTally($json) {
        include "connection.php";
        $json = json_decode($json, true);
    
        // SQL query with COALESCE to handle NULL values
        $sql = "SELECT 
                    a.act_id AS trans_actId, 
                    a.act_name, 
                    a.act_code, 
                    a.act_person, 
                    COALESCE(COUNT(t.trans_actId), 0) AS tally
                FROM tbl_activities a
                LEFT JOIN tbl_transaction t 
                    ON a.act_id = t.trans_actId 
                    AND t.trans_evalId = :trans_evalId
                GROUP BY a.act_id, a.act_name, a.act_code, a.act_person";
    
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':trans_evalId', $json['trans_evalId']);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = null; $stmt = null;
    
        return json_encode($returnValue);
    }
    
}

$json = isset($_POST['json']) ? $_POST['json'] : "";
$operation = $_POST['operation'];

$transaction = new Transaction();
switch ($operation) {
    case "addTransaction":
        echo $transaction->addTransaction($json);
        break;
    case "getActivities":
        echo $transaction->getActivities();
        break;
    case "getTimeRange":
        echo $transaction->getTimeRange();
        break;
    case "countActivityTally":
        echo $transaction->countActivityTally($json);
        break;
}
?>
