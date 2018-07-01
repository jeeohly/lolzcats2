<?php
class post2{

	public static function createPost($postbody, $loggedInUserId, $profileUserId){

			if(strlen($postbody) > 160 || strlen($postbody) < 1){
				die('Incorrect length');
			}

			if($loggedInUserId == $profileUserId){
				DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody'=>$postbody, ':userid'=>$profileUserId));
			}else{
				die('Incorrect user');
			}
	}

	public static function likePost($postid, $likerId){
		if(!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$likerId))){
				DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
				DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array('postid'=>$postid, ':userid'=>$likerId));
			}else{
				DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postid));
				DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$likerId));
			}
	}

	public static function displayPosts($userid, $username, $loggedInUserId){
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
		$posts = "";
		foreach($dbposts as $p){

			if(!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))){

				$posts .= htmlspecialchars($p['body'])."
				<form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
					<input type='submit' name='like' value='like'>
					<span>".$p['likes']." likes</span>
				</form>
				<hr /></br />";
			}else{
				$posts .= htmlspecialchars($p['body'])."
				<form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
					<input type='submit' name='unlike' value='unlike'>
					<span>".$p['likes']." likes</span>
				</form>
				<hr /></br />";
			}
		}
		return $posts;
	}
}
?>