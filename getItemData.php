<?php

    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json ; charset=utf-8');

    require_once('conn.php');

    $id = $_GET['id'];

    $sql = "SELECT * FROM basicdata WHERE id = $id";

    $result = $conn -> query($sql);

    if (!$result) {
        die($conn->error);
    }

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
?>