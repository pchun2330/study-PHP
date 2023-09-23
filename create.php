<?php

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, Content-Type');
	header('Content-Type: application/json ; charset=utf-8');

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		$data = json_decode(file_get_contents("php://input"),true);
		
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	
		echo "Username: " . $data['username'] . "<br>";
		echo "Age: " . $data['age'] . "<br>";
		echo "Phone: " . $data['phone'] . "<br>";
	} else {
		echo "No POST data received." . "<br>";
	}


	require_once('conn.php');
	
	if(empty($data['username']) || empty($data['age']) || empty($data['phone'])){
		die('資料不能空白');
	};

	$name = $data['username'];
	$age = $data['age'];
	$phone = $data['phone'];
	
	$sql = sprintf(
		"INSERT INTO basicdata(name,age,phone) VALUES ('%s', '%d', '%s')",
		$name,$age,$phone
	);

	$result = $conn -> query($sql);
	if(!$result){
		die($conn -> error);
	}
	
	//header("Location: index.php");
?>