<!DOCTYPE html>
<html>
<head>
        <title>Login</title>
        <link rel="stylesheet" href="homeStyle.css">
</head>

<body>
  <!-- Side menu -->
<ul class="navbar">
  <li><a href="homePage.php">Home page</a>
  <li><a href="login.php">Login</a>
</ul>  
<form method="post">
 New User Name:<br>
<input type="text" name="username"><br><br>
New User Password:<br>
<input type="text" name="password"><br><br>
<input type="submit" name="submit" value="Sign Up">
</form>

<?php
require 'database.php';
session_start();
if(isset($_POST['submit'])){
       $userName = $mysqli->real_escape_string($_POST['username']);
       $password = $mysqli->real_escape_string($_POST['password']);
       $nameUsed = False;
       if((!preg_match('/^[\w_\-]+$/',$userName)) || (!preg_match('/^[\w_\-]+$/',$password))){
        echo "Invalid Username or Password";
        exit;
        }
        //check if username already exists
		$stmt = $mysqli->prepare("select username from users where username=?");
		if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
		}
		
		$stmt->bind_param('s', $userName);
 		$stmt->execute();		
        //if already in table prompt for a new one
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
				if (sizeof($row["username"])>0){ 
				$nameUsed= True;
				echo "Username Taken Please Enter a New One";}
		}
		//allow the name/password to be added to table
        if(!$nameUsed){
        	echo "Thanks for signing up!";
        	$crypt_pass = crypt($password);
        	$add_name = $mysqli->prepare("insert into users (username, password)
        	values('$userName', '$crypt_pass')");
			if(!$add_name){
				printf("Query Failed: %s\n", $mysqli->error);
				exit;
				}
			$add_name->execute();
			$add_name->close();
        }
        
        $stmt->close();
    }
       





?>







</body>




</html>