<?php

    require("./Includes/dbh.php");

    session_start();

    if (isset($_SESSION["username"])) {
        header("Location: index.php");
        die();
    }

    if (isset($_POST["btnLogin"])) {
        if (isset($_POST["username"]) && isset($_POST["pass"])) {
            if (!empty($_POST["username"]) && !empty($_POST["pass"])) {
                
                $username = $_POST["username"];
                $password = $_POST["pass"];

                $stmt = $conn->prepare("select * from login where username = ?");
        
                $stmt->bind_param("s", $username);
        
                $stmt->execute();
        
                $result = $stmt-> get_result();
                
                $row = $result->fetch_assoc();

                $passwordCheck = password_verify($password, $row["password"]);

                if($passwordCheck) {
                    
                    $stmt->close();
                    
                    $conn->close();

                    $_SESSION["username"] = $username;

                    header("Location: index.php");
                    die();
                }

                $stmt->close();
        
                $conn->close();
            }
        }
    }


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Assets/css/login.css">
</head>
<body>
    <form id="msform" method="POST">
    <fieldset>
        <h2 class="fs-title">Login</h2>
        <input type="text" name="username" placeholder="Username" autocomplete="off" required/>
        <input type="password" name="pass" placeholder="Password" autocomplete="off" required/>
        <button type="submit" name="btnLogin" class="next action-button">Login</button>
    </fieldset>
    </form>
</body>
</html>