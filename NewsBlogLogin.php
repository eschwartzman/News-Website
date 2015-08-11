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
  <li><a href="signUp.php">Sign Up</a>
</ul>  

<form method="post">
 Login Name:<br>
<input type="text" name="username"><br><br>
User Password:<br>
<input type="text" name="password"><br><br>
<input type="submit" name="submit" value="Login">
</form>

<?php
require 'database.php';
session_start();


if(isset($_POST['submit'])){
       $userName = $mysqli->real_escape_string($_POST['username']);
       $password = $mysqli->real_escape_string($_POST['password']);
       if((!preg_match('/^[\w_\-]+$/',$userName)) || (!preg_match('/^[\w_\-]+$/',$password))){
        echo "Invalid Username or Password";
        exit;
        }
		// Use a prepared statement
		$stmt = $mysqli->prepare("SELECT COUNT(*), id, password FROM users WHERE username=?");
 
		// Bind the parameter
		$stmt->bind_param('s', $userName);
		$user = $_POST['username'];
		$stmt->execute();
 
		// Bind the results
		$stmt->bind_result($cnt, $user_id, $pwd_hash);
		$stmt->fetch();
 
		// Compare the submitted password to the actual password hash
		if( $cnt == 1 && crypt($password, $pwd_hash)==$pwd_hash){
		$_SESSION['loggedIn']= True;
		$_SESSION['user_id'] = $user_id;
		$_SESSION['userAccount']= $userName;
		//create unique id for security
		$_SESSION['token'] = substr(md5(rand()), 0, 10);
		header( "Location: homePage.php" );
		}else{
		echo "Please try again";
		}    
        
    }
       




?>



</body>




</html>