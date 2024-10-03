<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class College {
        function addCollege($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "INSERT INTO tbl_college(college_deptId, college_name) ";
            $sql .= "VALUES(:college_deptId, :college_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":college_deptId", $json['college_deptId']);
            $stmt->bindParam(":college_name", $json['college_name']);

            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

            return $returnValue;
        }

        function getCollege(){
            include "connection.php";

            $sql = "SELECT college_id, college_name FROM tbl_college";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }
    }

    $json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $college = new College();
    switch ($operation){
        case "addCollege":
            echo $college->addCollege($json);
            break;
        case "getCollege":
            echo $college->getCollege();
            break;
    }

?>