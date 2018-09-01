<?php
require_once("DB.php");
class post2{
	public static function createImgPost($postbody, $loggedInUserId){
        $db = new DB("127.0.0.1", "lolzcatz", "root", "");
		$db->query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$loggedInUserId));
        $postid = $db->query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
        return $postid;
	}

}
?>