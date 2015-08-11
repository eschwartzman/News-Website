<!DOCTYPE html>
<html>
<head>
        <title>User Page</title>
        <link rel="stylesheet" href="homeStyle.css">
</head>

<body>
  <!-- Side menu -->
<ul class="navbar">
  <li><a href="homePage.php">Home page</a>
  <li><a href="logout.php">Log Out</a> 
</ul> 


<h1> Hello <?php session_start(); 
			echo $_SESSION['userAccount']; ?> </h1>
<p>Take a look and edit your stories, posts, and profile. You can also add more. </p>

<form method="post">
<input type="submit" name="editProfile" value="Edit Profile">  
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>

<?php

 if(isset($_POST['editProfile'])){
 	//check for CSRF
 		if($_SESSION['token'] !== $_POST['token']){
 		echo "error";
		die("Request forgery detected");
		}
	header( "Location: editProfile.php" );
	}
?>
		
<?php
	showProfile();
	postNews();
 ?>
 <?php 
 function showNewsWComments(){

	require 'database.php';
	
 	$name = $_SESSION['userAccount'];
	
	//show posted stories
	$stmt = $mysqli->prepare("SELECT * FROM news WHERE poster=?");
	if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
	}
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<table>
			<tr>
			<th>Previously Posted:</th>
			<th>Delete and Edit:</th>
			<th>News Links:</th>
			</tr>";
	while($row = $result->fetch_assoc()){
		$_SESSION['newsStory']=$row['newsstory'];
  		echo "<tr>";
  	  	echo "<td>" . htmlentities($row['title']) . "</td>";
  		echo "<td>" . htmlentities($row['newsstory']) . "</td>";
  		echo "<td>" . '<a href="'. "http://" .htmlentities($row['links']).'">'.htmlentities($row['links']).' </a>' . "</td>";
  		echo "<td>";
  		deleteStory($row['id']);
  		editStory($row['id']);
  		echo "</td>";
  		echo "</tr>";
  		
		}
	    $stmt->close();
	    echo "</table>";
	    showComments();	    
}
?>

<?php 
function showComments(){
	require 'database.php';

	//will show all of user's comments 
	$stmt = $mysqli->prepare("SELECT * FROM comments WHERE name=?"); 

	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;}
	$stmt->bind_param('s',$_SESSION['userAccount']);
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<table>
			<tr>
			<th>Comments:</th>
			<th>Delete and Edit:</th>
			</tr>";
	while($row = $result->fetch_assoc()){
	  	$_SESSION['comment']=$row['comment'];
	  	echo "<tr>";
  	  	echo "<td>" . htmlentities($row['name']) . " said: " .  htmlentities($row['comment']) ."</td>";
  		echo "<td>";
  		deleteComment($row['id']);
  		editComment($row['id']);
  		echo "</td>";
  		echo "</tr>";
  		
		}
	$stmt->close();
	echo "</table>";
}
?>

<?php 
function deleteStory($button_id){
	?>
	<form method="post">
	<input type="submit" name="<?php echo $button_id; ?>" value="Remove">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>
	
<?php

require 'database.php';
 	if(isset($_POST[$button_id])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
    	//delete comments for story
    	$stmt = $mysqli->prepare("delete from comments where news_id =?");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
        $stmt->bind_param('i', $button_id);
    	$stmt->execute();
    	$stmt->close();
    	//delete the news story itself
    	$stmt = $mysqli->prepare("delete from news where id = ?");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
         $stmt->bind_param('i', $button_id);
    	$stmt->execute();
    	$stmt->close();
    	header( "Location: userPage.php" );
			}	 
?>
<?php
}?>

<?php 
function deleteComment($button_id){
	$button_num = $button_id*2;
	?>
	<form method="post">
	<input type="submit" name="<?php echo $button_num; ?>" value="Remove Comment">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>
	
<?php

require 'database.php';
 	if(isset($_POST[$button_num])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
    	//delete comment
    	echo "in the query";
    	$stmt = $mysqli->prepare("delete from comments where id =?");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
        $stmt->bind_param('i', $button_id);
    	$stmt->execute();
    	$stmt->close();
    	header( "Location: userPage.php" );
    	}
?>
<?php
}?>

<?php
function editComment($id){
		$butt_on=$id*3;
		$butt_on2=$id*4;
	?>
	<form method="post">
	<input type="submit" name="<?php echo $butt_on; ?>" value="Edit Comment">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>
	
	<?php
	require 'database.php';
 	if(isset($_POST[$butt_on])){
 	?>
 	<form method="post">
 	<textarea name="commentE" placeholder="<?php echo $_SESSION['comment'] ?>" rows="2" cols="40"></textarea> 
	<input type="submit" name="<?php echo $butt_on2; ?>" value="Submit Edit">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>
	
	<?php
 	}
	if(isset($_POST[$butt_on2])){
	//check for CSRF
		if($_SESSION['token'] !== $_POST['token']){
 		echo "error";
		die("Request forgery detected");
			}

		$edit = $mysqli->real_escape_string($_POST['commentE']);
    	$stmt = $mysqli->prepare("update comments set comment=? where id=$id");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
    	$stmt->bind_param('s', $edit);
    	$stmt->execute();
    	$stmt->close();
    	header( "Location: userPage.php" );
	}
?>
<?php
}?>

<?php 
//gets passed id of the news item
function editStory($id){
	$button_id=$id*5;
	$button_id2=$id*7
	?>
	<form method="post">
	<input type="submit" name="<?php echo $button_id; ?>" value="Edit">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>  <!--test-->
<?php

require 'database.php';
 	if(isset($_POST[$button_id])){
 	
 	?>
 	<form method="post">
	<textarea name="comment" placeholder="<?php echo $_SESSION['newsStory'] ?>" rows="2" cols="40"></textarea> 
	<input type="text" name="newLink" placeholder="post edited link">
	<input type="submit" name="<?php echo $button_id2; ?>" value="Submit Change">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</form>	
		<?php
 	}
 	if(isset($_POST[$button_id2])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
		
    	$comment = $mysqli->real_escape_string($_POST['comment']);
    	$newLink = $mysqli->real_escape_string($_POST['newLink']);
    	//$stmt = $mysqli->prepare("update news set newsstory=? where id=$id");
    	$stmt = $mysqli->prepare("update news set newsstory=?, links=? where id=$id");
     	if(!$stmt){
        	 printf("Query Prep Failed: %s\n", $mysqli->error);
         	exit;}
    	$stmt->bind_param('ss', $comment, $newLink);
    	$stmt->execute();
    	$stmt->close();
    	header( "Location: userPage.php" );
			} 
?>
<?php
}?>

<?php
function postNews(){
     ?>
<form method="post">
<p>Try Adding a News Story:</p>
News Title: <input type="text" name="postTitle"><br>
News Story: <input type="text" name="postText"><br>
Link: <textarea name="postLink"></textarea>(Please enter link in the format "adress.com")<br> 
<input type="submit" name="post_story" value="Post News">  
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>

<?php
require 'database.php';

 if(isset($_POST['post_story'])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
    $newstory = $mysqli->real_escape_string($_POST['postText']); 
 	$title = $mysqli->real_escape_string($_POST['postTitle']); 
 	$name = $_SESSION['userAccount'];
 	$link = $mysqli->real_escape_string($_POST['postLink']);
 
 $stmt = $mysqli->prepare("insert into news(title, poster, newsstory, links) values (?,?,?,?)");
    
     if(!$stmt){
         printf("Query Prep Failed: %s\n", $mysqli->error);
         exit;}
         
    $stmt->bind_param('ssss', $title, $name, $newstory, $link);
    $stmt->execute();
    $stmt->close();
 	header( "Location: userPage.php" );
	}
?>
<?php
}?>

<?php
	showNewsWComments();
	?>

<?php 
function showProfile(){
	require 'database.php';

	//will show all of user's comments 
	$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=?"); 

	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;}
	$stmt->bind_param('s',$_SESSION['userAccount']);
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<table>
			<tr>
			<th>Name:</th>
			<th>About:</th>
			</tr>";
	while($row = $result->fetch_assoc()){
	  	echo "<tr>";
  	  	echo "<td>" . htmlentities($row['name']) . "</td>";
  	  	echo "<td>" . htmlentities($row['about']) . "</td>";
  		echo "</tr>";
  		
  		
		}
	$stmt->close();
	echo "</table>";
}
?>








</body>
</html>