<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

class Auth {
    function login($json){
        include 'connection.php';

        $json = json_decode($json, true);

        $sql = "SELECT * FROM tbl_user WHERE user_username = :username ";
        $sql .= "AND user_password = :password ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $json['username']);
        $stmt->bindParam(':password', $json['password']);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($returnValue);
    }

    function getUser($json){
        include "connection.php";

        $json = json_decode($json, true);

        $sql = "SELECT tbl_user.user_fullname, tbl_department.dept_name, tbl_userrole.role_name
                FROM tbl_user
                INNER JOIN tbl_department ON tbl_department.dept_id = tbl_user.user_deptId
                INNER JOIN tbl_userrole ON tbl_userrole.role_id = tbl_user.user_roleId
                WHERE tbl_user.user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $json['user_id']);

        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = null; $stmt = null;

        return json_encode($returnValue);
    }
}

$json = isset($_POST['json']) ? $_POST['json'] : "";
    $operation = $_POST['operation'];

    $auth = new Auth();
    switch ($operation){
        case "login":
            echo $auth->login($json);
            break;
        case "getUser($json)":
            echo $auth->getUser($json);
            break;    
    }