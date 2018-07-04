<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/Image.php');

if(login2::isLoggedIn()){
	$userid = login2::isLoggedIn();
}else{
	die('Not logged in');
}


if(isset($_POST['uploadprofileimg'])){

	Image::uploadImage('profileimg', "UPDATE users SET profileimg=:profileimg WHERE id=:userid", array(':userid' =>$userid));
}
?>


<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
	Upload a profile image:
	<input type="file" name="profileimg">
	<input type="submit" name="uploadprofileimg" value="Upload image">
</form>