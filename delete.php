<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Content-Type: application/json ; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        // header('HTTP/1.1 204 No Content');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        
        $id = $_GET['id'];
        
        echo 'id:'. $id;

        require_once('conn.php');

        $sql = "DELETE FROM basicdata WHERE id = $id";
        $result = $conn -> query($sql);
        if(!$result){
            die($conn -> error);
        }

        $response = array('message' => 'Data deleted successfully');
        echo json_encode($response);
    } else {
        
        header('HTTP/1.1 405 Method Not Allowed');
        echo 'Method Not Allowed';
    }

?>