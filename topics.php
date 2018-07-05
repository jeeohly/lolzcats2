<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/post2.php');
include('./classes/Image.php');

if(isset($_GET['topic'])){
	if(DB::query("SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']))){

		$posts = DB::query("SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']));

		foreach($posts as $post){
			echo $post['body']."<br />";
		}
	}
}

?>