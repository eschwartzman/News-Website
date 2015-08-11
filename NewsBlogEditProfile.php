<!DOCTYPE html>
<html>
<head>
        <title>Sports Newz</title>
        <link rel="stylesheet" href="homeStyle.css">
</head>

<body>

<?php
session_start();
addInfo();
?>

<?php
function addInfo(){
     ?>
<form method="post">
<p>Try Adding info About Yourself:</p>
Name: <input type="text" name="myName"><br>
About Me: <textarea name="about"></textarea><br> 
<input type="submit" name="set_about" value="Update Profile">  
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
</form>

<?php
require 'database.php';

 if(isset($_POST['set_about'])){
 	//check for CSRF
 	if($_SESSION['token'] !== $_POST['token']){
 	echo "error";
	die("Request forgery detected");
		}
    $name = $mysqli->real_escape_string($_POST['myName']); 
 	$about = $mysqli->real_escape_string($_POST['about']); 
 	$id = $_SESSION['userAccount'];
 echo "got past the stuff";
 $stmt = $mysqli->prepare("update users set name=?, about=? where username=?;");
    
     if(!$stmt){
         printf("Query Prep Failed: %s\n", $mysqli->error);
         exit;}
         
    $stmt->bind_param('sss', $name, $about, $id);
    $stmt->execute();
    $stmt->close();
 	header( "Location: userPage.php" );
	}
?>
<?php
}?>



</body>
</html>