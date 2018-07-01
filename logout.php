<?php
include('./classes/DB.php');
include('./classes/login2.php');

if(!login2::isLoggedIn()){
	die("Not logged in");
}

if(isset($_POST['confirm'])){
	if(isset($_POST['alldevices'])){
		DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>login2::isLoggedIn()));
	}else{
		if(isset($_COOKIE['LOLID'])){
			DB::query('DELETE FROM login_tokens WHERE token=:token', array('token'=>sha1($_COOKIE['LOLID'])));
		}
		setcookie('LOLID', '1', time()-3600);
		setcookie('LOLID_', '1', time()-3600);
	}
}
?>

<h1>Logout of your Account?</h1>
<p>Are you sure you'd like to logout?</p>
<form class="logout.php" method="post">
	<input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
	<input type="submit" name="confirm" value="Confirm">
</form>