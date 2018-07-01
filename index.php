<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/post2.php');

$showTimeline = False;
if(login2::isLoggedIn()){
	$userid = login2::isLoggedIn();
	$showTimeline = True;
}else{
	echo 'Not logged in';
}

if(isset($_GET['postid'])){
	post2::likePost($_GET['postid'], $userid);
}

$followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = 13
ORDER BY posts.likes DESC;');

foreach($followingposts as $post){
	echo $post['body']." ~ ".$post['username'];
    echo "<form action='index.php?postid=".$post['id']."' method='post'>";
    
    if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
        echo "<input type='submit' name='like' value='Like'>";
    }else{
        echo "<input type='submit' name='unlike' value='Unlike'>";
    }
    echo "<span>".$post['likes']." likes</span>
    </form>
    <hr /></br />";
    //test
}
?>