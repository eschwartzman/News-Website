<!DOCTYPE html>
<html>
<head>
        <title>Sports Newz</title>
        <link rel="stylesheet" href="homeStyle.css">
</head>

<body>
  <!-- Side menu -->
<ul class="navbar">
  <li><a href="homePage.php">Home page</a>
  <li><a href="signUp.php">Sign Up</a>
  <li><a href="login.php">Log In Now</a>
 <?php session_start();
  if( isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] ):?>
  <li><a href="logout.php">Log Out</a> 
  <li><a href="userPage.php">User Page</a> 
  <?php endif; ?>
</ul>      

<!-- Main Area -->
<h1>Welcome to Sports Newz</h1>

<p>Check out the user submitted stories or ESPN below</p>
<p>Log in or sign up to add your own news or comments</p>


 <iframe src="http://espn.com" sandbox="allow-same-origin allow-forms allow-scripts"></iframe> 

<!--display the news/comments and allow user to comment if logged in-->
 <?php 
 	//query to find number of stories in database
 	require 'database.php';
 	
	$stmt = $mysqli->prepare("select id from news");
	if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
	}
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc()){
			$ids[] = $row['id'];
			}
			if(!empty($ids)){
				for($i = 0; $i < sizeof($ids); $i++)
				{
				showNewsWComments($ids[$i]);
				}
			}
			else{
			echo "try adding some news in the 'User Page' tab";
			}
	$stmt->close();
 
 	?>  	
 
<?php 
function showComments($id){
	require 'database.php';

	$stmt = $mysqli->prepare("SELECT * FROM comments WHERE news_id=?"); 

	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;}
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
//comments table 
	echo "<table>
			<tr>
			<th>Story Comments:</th>
			</tr>";
	while($row = $result->fetch_assoc()){
	  	echo "<tr>";
  	  	echo "<td>" . htmlentities($row['name']) . " said: " .  htmlentities($row['comment']) ."</td>";
  		echo "</tr>";
		}
		
	$stmt->close();
	
	echo "</table>";
}

?>


<?php
function showNewsWComments($id){
	require 'database.php';
	$stmt = $mysqli->prepare("SELECT * FROM news where id=?"); 

	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;}
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	echo "<table>
			<tr>
			<th>News:</th>
			<th>Link:</th>
			<th>Comment:</th>
			</tr>";
	while($row = $result->fetch_assoc()){
	  	echo "<tr>";
  	  	echo "<td>" . htmlentities($row['title']) . "</td>";
  		echo "</tr>";
  		echo "<tr>";
  		echo "<td>" . htmlentities($row['newsstory']) . "</td>";
  		echo "<td>" . '<a href="'. "http://" .htmlentities($row['links']).'">'.htmlentities($row['links']).' </a>' . "</td>";
  		echo "<td>";
  		if( isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] ){ 
  				commentPost($_SESSION['userAccount'], $id);
  				}
  				else{ 
  				echo "Please log in to gain comment abilities"; 
  					}
  		echo"</td>";
  		echo "</tr>";
  		
		}
	$stmt->close();
	echo "</table>";
	showComments($id);
	
}
?>

<?php 
function commentPost($user, $id){
	?>
	<form method="post">
<!--  	<textarea name="comment" rows="3" cols="20"></textarea> -->
 	<input type="text" name="comment">
	<input type="submit" name="<?php echo $id; ?>" value="Post">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>
	
<?php

require 'database.php';
 	if(isset($_POST[$id])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
    	$comment = $mysqli->real_escape_string($_POST['comment']);
    	//adds comment to database	
    	$userName= $_SESSION['userAccount'];
    	$stmt = $mysqli->prepare("insert into comments (news_id, name, comment) values (?, ?, ?)");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
    	$stmt->bind_param('iss',  $id, $userName, $comment);
    	$stmt->execute();
    	$stmt->close();
    	header( "Location: homePage.php" );
			}	 
?>
<?php
}?>





</body>
</html>