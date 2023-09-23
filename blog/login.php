<?php

    header('Access-Control-Allow-Origin: http://192.168.0.88:8080');
    header('Access-Control-Allow-Headers: Origin, Content-Type');
    header('Content-Type: application/json ; charset=utf-8');
    header('Access-Control-Allow-Method: POST,OPTIONS,GET');

    date_default_timezone_set('Asia/Taipei');

    require_once("conn.php");

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents("php://input"),true);
        $account = $data['account'];
        $password = $data['password'];

        //SQL injection
        $sql = "SELECT * FROM userdata WHERE account = ? LIMIT 1";
        $stmt = $conn -> prepare($sql);
        
        //use bind_param method to bind value that can know value's type
        $stmt -> bind_param('s' , $account);
        
        //run search
        $stmt -> execute();

        //search result
        $result = $stmt -> get_result();

        if($result -> num_rows === 0){
            echo json_encode(['state' => 2, 'message' => 'Account is not defined or Error']);
        }

        if(!isLocked($account,$conn)){
            return;
        }

        verifyLogin($result,$password,$account,$conn);
    }
    
    // if($_SERVER['REQUEST_METHOD'] === 'GET'){
    //     //check login status
    //     // session_start();
    //     echo 'account: ' . $_SESSION['account'] . '---id:' . session_id();
    //     if (isset($_SESSION['account'])) {
    //         // echo json_encode(['loggedIn' => true,"username" => $_SESSION['account']]);
    //         echo 'Session account exists: ' . $_SESSION['account'];
    //     } else { 
    //         // echo json_encode(['loggedIn' => false,"username" => $_SESSION['account']]);
    //         echo 'Session account exists: ' . $_SESSION['account'];
    //     }
    // }

    function isLocked($account,$conn){
        $query = "SELECT locked_until FROM userdata WHERE account = ?";
        $stmt = $conn -> prepare($query);
        if($stmt === false){
            die("Search locked_until is failed.." . $conn -> error);
        }

        $stmt -> bind_param("s", $account);
    
        if($stmt->execute()){
            $result = $stmt -> get_result();
            $row = $result -> fetch_assoc();
            if($row){
                $current_time = time();
                if (strtotime($row['locked_until']) < $current_time) {
                    return true;
                }else{
                    echo json_encode(['state' => 4, 'message' => 'Account is locked,need to wait on 1 minutes and then try again!']);
                    return false;
                }
            }
        }else{
            echo "Search failed: " . $conn->error;
        }
        
    }

    function increaseLoginAttempts($account,$conn) {
        
        $query = "UPDATE userdata SET login_attempts = login_attempts + 1 WHERE account = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $account);
    
        if ($stmt->execute()) {
           
            $maxAttempts = 3; 
            $query = "SELECT login_attempts FROM userdata WHERE account = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $account);
            $stmt->execute();
    
            $result = $stmt->get_result(); 
            $row = $result->fetch_assoc();  
            $times = $row['login_attempts'];

            if ($row['login_attempts'] >= $maxAttempts) {
                handleAccountLocking($account,$conn);
            }else{
                echo json_encode(['state' => 3, 'message' => 'password failed!', 'times' => $times]);
            }
        }
    }
    
    function handleAccountLocking($account,$conn) {
        $lockDuration = 1; 
        
        $endTime = date('Y-m-d H:i:s', strtotime("+{$lockDuration} minutes"));
        $query = "UPDATE userdata SET locked_until = ? WHERE account = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $endTime, $account);
        $stmt->execute();
        echo json_encode(['state' => 4, 'message' => 'Account is locked']);
        resetLoginAttempts($account,$conn);
    }

    function resetLoginAttempts($account,$conn){
        $query = "UPDATE userdata SET login_attempts = 0 WHERE account = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $account);
        $stmt->execute();
    }

    function verifyLogin($result,$password,$account,$conn){
        if($result -> num_rows === 1){
            $row = $result -> fetch_assoc();
            //check the same password as MySQL's password what is used password_verifty method
            if(password_verify($password, $row['password'])){
                echo json_encode(['state' => 1, 'message' => 'log in successfully!']);
                
                resetLoginAttempts($account,$conn);
            }
            else{
                increaseLoginAttempts($account,$conn);
                //echo json_encode(['state' => 2, 'message' => 'password failed!']);
            }
        }
    }

    // $hashed_pwd = password_hash($row['password'], PASSWORD_DEFAULT);
    // $conn -> query("UPDATE `userdata` SET `password` = $hashed_pwd WHERE `userdata`.`account_id` = 1");
    // $stmt = $conn->prepare("UPDATE userdata SET password = ? WHERE account_id = 1");
    // $stmt->bind_param("s", $hashed_pwd);
    // $stmt->execute();
    // print_r($hashed_pwd);
?>