<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/post2.php');
include('./classes/Image.php');

$username = "";
$isFollowing = False;
$verified = False;

if(isset($_GET['username'])){
	if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){

		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		$verified =  DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
		$followerid = login2::isLoggedIn();

		if(isset($_POST['follow'])){

			if($userid != $followerid){
	
				if(!DB::query('SELECT follower_id from followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid))){

					if($followerid == 13){
						DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
					}

					DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array('userid'=>$userid, ':followerid'=>$followerid));
				}else{
					echo 'Already following';
				}
				$isFollowing = True;
			}
		}

		if(isset($_POST['unfollow'])){

			if($userid != $followerid){
				if(DB::query('SELECT follower_id from followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid))){

					if($followerid == 13){
						DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
					}

					DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid));
				}
				$isFollowing = False;
			}
		}

		if(DB::query('SELECT follower_id from followers WHERE user_id=:userid', array(':userid'=>$userid))){
			//echo 'Already following';
			$isFollowing = True;
		}

		//////////posting///////////////
		if(isset($_POST['post'])){
			if($_FILES['postimg']['size'] == 0){
				post2::createPost($_POST['postbody'], login2::isLoggedIn(), $userid);
			}else{
				$postid = post2::createImgPost($_POST['postbody'], login2::isLoggedIn(), $userid);
				Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
			}
		}
		///////////////likes//////////
		if(isset($_GET['postid'])){
			post2::likePost($_GET['postid'], $followerid);
		}

		$posts = post2::displayPosts($userid, $username, $followerid);
		///////////////////////////////
	}else{
		die('User not found');
	}
}
?>

<h1><?php echo $username; ?>'s profile<?php if($verified) {echo ' - verified';} ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<?php
	if($userid != $followerid){
		if($isFollowing){
			echo '<input type="submit" name="unfollow" value="Unfollow">';
		}else{
			echo '<input type="submit" name="follow" value="Follow">';
		}
	}
	?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
	<textarea name="postbody" rows="8" cols="80"></textarea>
	<br />Upload an image:
	<input type="file" name="postimg">
	<input type="submit" name="post" value="post">
</form>


<div class="posts">
	<?php echo $posts; ?>  
</div>