<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

    class Department {
        function addDepartment($json){
            include "connection.php";

            $json = json_decode($json, true);

            $sql = "INSERT INTO tbl_department(dept_name) ";
            $sql .= "VALUES(:dept_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":dept_name", $json['dept_name']);

            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;

            return $returnValue;
        }

        function getDepartment(){
            include "connection.php";

            $sql = "SELECT * FROM tbl_department ";
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null; $stmt = null;

            return json_encode($returnValue);
        }
    }

    $json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $dept = new Department();
    switch ($operation){
        case "addDepartment":
            echo $dept->addDepartment($json);
            break;
        case "getDepartment":
            echo $dept->getDepartment();
            break;
    }

?>