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
        // Fetch both student and teacher activities in one query
        $sql = "SELECT trans_actId, COUNT(trans_actId) AS tally FROM tbl_transaction WHERE trans_evalId = :trans_evalId GROUP BY trans_actId";
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
