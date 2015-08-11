<?php
  session_start();
  $_SESSION['loggedIn']=False;
  session_destroy();
  header('location: homePage.php'); // redirct to home
?>