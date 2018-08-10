<?php
require_once("DB.php");
require_once("Mail.php");

$db = new DB("127.0.0.1", "lolzcatz", "root", "");
if ($_SERVER['REQUEST_METHOD'] == "GET") {

        if ($_GET['url'] == "auth") {

        } else if ($_GET['url'] == "search") {

                $tosearch = explode(" ", $_GET['query']);
                if(count($tosearch) == 1){
                        $tosearch = str_split($tosearch[0], 2);
                }

                $whereclause = "";
                $paramsarray = array(':body'=>'%'.$_GET['query'].'%');
                for ($i = 0; $i < count($tosearch); $i++) {
                        if ($i % 2) {
                                $whereclause .= " OR body LIKE :p$i ";
                                $paramsarray[":p$i"] = $tosearch[$i];
                        }
                }
                $posts = $db->query('SELECT posts.id, posts.body, users.username, posts.posted_at FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body '.$whereclause.' LIMIT 10', $paramsarray);

                //echo "<pre>";
                echo json_encode($posts);

        ///////TO GET USERNAME
        } else if ($_GET['url'] == "users") {
                
                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['LOLID'])))[0]['user_id'];
                $username = $db->query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
                echo $username;

        ///////////////////follow//////////////
        } else if ($_GET['url'] == "follow") {
                $isFollowing = "0"; 
                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $followerid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['LOLID'])))[0]['user_id'];

                if($db->query('SELECT follower_id from followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))){
                        $isFollowing = "1";
                }

                echo $isFollowing;

        } else if ($_GET['url'] == "comments" && isset($_GET['postid'])) {
                $output = "";
                $comments = $db->query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id=users.id', array(':postid'=>$_GET['postid']));
                $output .= "[";
                foreach($comments as $comment){
                        $output .= "{";
                                $output .= '"Comment": "'.$comment['comment'].'",';
                                $output .= '"CommentedBy": "'.$comment['username'].'"';
                        $output .= "},";
                        //echo $comment['comment']." ~ ".$comment['username']."<hr />";
                }
                $output = substr($output, 0, strlen($output)-1);
                $output .= "]";
                echo $output;

        } else if ($_GET['url'] == "posts") {

                $token = $_COOKIE['LOLID'];

                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                $followingposts = $db->query('SELECT posts.user_id, posts.id, posts.body, posts.postimg, posts.posted_at, posts.likes, users.`username` FROM users, posts, followers
                WHERE posts.user_id = followers.user_id
                AND users.id = posts.user_id
                AND follower_id = :userid
                ORDER BY posts.posted_at DESC;', array(':userid'=>$userid));
                ///////////////
                $isliked = " Unlike";
                $token = $_COOKIE['LOLID'];
                $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                //////////////////////////////
                $profpic = "";
                $deletereport = "report";

                $response = "[";
                foreach($followingposts as $post){
                        /////////STUFF TO GET UNLIKE DISPLAY//////////////////
                        $postId = $post['id'];
                        if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                                $isliked = " Likes";
                        }else{
                                $isliked = " Unlike";
                        }
                        if($db->query('SELECT user_id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))){
                                $deletereport = "delete";
                        }
                        ////////////////////////////PROF PIC////////////////
                        $profpic = $db->query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$post['user_id']))[0]['profileimg'];
                        //////////////////////////////////////
                        $response .= "{";

                                $response .= '"PostId": '.$post['id'].',';
                                $response .= '"PostBody": "'.$post['body'].'",';
                                $response .= '"PostedBy": "'.$post['username'].'",';
                                $response .= '"PostDate": "'.$post['posted_at'].'",';
                                $response .= '"PostImage": "'.$post['postimg'].'",';
                                $response .= '"isLiked": "'.$isliked.'",';
                                $response .= '"Profpic": "'.$profpic.'",';
                                $response .= '"Deletereport": "'.$deletereport.'",';
                                $response .= '"Likes": '.$post['likes'].'';
                        $response .= "},";

                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";
                
                http_response_code(200);
                echo $response;
             
        }else if ($_GET['url'] == "profileposts") {

                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

                $followingposts = $db->query('SELECT posts.user_id, posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, users.`username` FROM users, posts
                WHERE users.id = posts.user_id
                AND users.id = :userid
                ORDER BY posts.posted_at DESC;', array(':userid'=>$userid));
                ///////////////
                $isliked = " Unlike";
                $token = $_COOKIE['LOLID'];
                $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

                $userpic = $db->query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$likerId))[0]['profileimg'];
                //////////////////////////////
                $profpic = "";
                $deletereport = "report";
                $commentcount = "";

                $response = "[";
                foreach($followingposts as $post){
                        /////////STUFF TO GET UNLIKE DISPLAY//////////////////
                        $postId = $post['id'];
                        if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                                $isliked = " Likes";
                        }else{
                                $isliked = " Unlike";
                        }
                        if($db->query('SELECT user_id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))){
                                $deletereport = "delete";
                        }
                        ////////////////////////////PROF PIC////////////////
                        $profpic = $db->query('SELECT profileimg FROM users WHERE id=:userid', array(':userid'=>$post['user_id']))[0]['profileimg'];
                        ///////////////////////////////////////////////////
                        $commentcount = sizeof($db->query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id=users.id', array(':postid'=>$post['id'])));

                        $response .= "{";
                                $response .= '"PostId": '.$post['id'].',';
                                $response .= '"PostBody": "'.$post['body'].'",';
                                $response .= '"PostedBy": "'.$post['username'].'",';
                                $response .= '"PostDate": "'.$post['posted_at'].'",';
                                $response .= '"PostImage": "'.$post['postimg'].'",';
                                $response .= '"isLiked": "'.$isliked.'",';
                                $response .= '"Profpic": "'.$profpic.'",';
                                $response .= '"Deletereport": "'.$deletereport.'",';
                                $response .= '"Userpic": "'.$userpic.'",';
                                $response .= '"commentcount": '.$commentcount.',';
                                $response .= '"Likes": '.$post['likes'].'';
                        $response .= "},";

                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";
                
                http_response_code(200);
                echo $response;
        ///////////////STATS////////////////////////
        }else if ($_GET['url'] == "stats") {
                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

                $postcount = $db->query('SELECT id FROM posts WHERE user_id=:userid', array(':userid'=>$userid));
                $followercount = $db->query('SELECT id FROM followers WHERE user_id=:userid', array(':userid'=>$userid));
                $followingcount = $db->query('SELECT id FROM followers WHERE follower_id=:userid', array(':userid'=>$userid));

                echo "{";
                echo '"postcount":';
                echo(sizeof($postcount)).',';
                echo '"followercount":';
                echo(sizeof($followercount)).',';
                echo '"followingcount":';
                echo(sizeof($followingcount));
                echo "}";
        }else if ($_GET['url'] == "comments") {
        //////comments///////////////////
                $postId = $_GET['id'];
                $comments = $db->query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id=users.id ORDER BY comments.posted_at DESC;', array(':postid'=>$postId));

                $response = "[";
                foreach($comments as $comment){
                        $response .= "{";
                                $response .= '"commentbody": "'.$comment['comment'].'",';    
                                $response .= '"commentBy": "'.$comment['username'].'"';
                        $response .= "},";
                }
                $response = substr($response, 0, strlen($response)-1);
                $response .= "]";
                echo $response;
        }
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if ($_GET['url'] == "users") {
                $postBody = file_get_contents("php://input");
                $postBody = json_decode($postBody);

                $username = $postBody->username;
                $email = $postBody->email;
                $password = $postBody->password;

                if(!$db->query('SELECT username from users WHERE username=:username', array(':username'=>$username))){

                        if(strlen($username) >= 3 && strlen($username) <= 32){
                                if(preg_match('/[a-zA-z0-9_]+/', $username)){
                                        if(strlen($password) >= 6 && strlen($password) <= 60){
                                                if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                                                        if(!$db->query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))){
                                                                $db->query('INSERT INTO users VALUES(\'\', :username, :password, :email, \'0\', "https://i.imgur.com/ml86Eqw.jpg")', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
                                                                echo '{ "Success": "User Created" }';
                                                                http_response_code(200);
                                                                ///email
                                                                Mail::sendMail('Welcome to LOLZCATZ U WONT REGRET IT', 'Your account has been created homeslice', $email);
                                                                $cstrong = True;
                                                                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                                                                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
                                                                $db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
                                                                echo '{ "Token": "'.$token.'" }';
                                                                setcookie("LOLID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                                                                setcookie("LOLID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

                                                        }else{
                                                                echo '{ "Error": "Email in use" }';
                                                                http_response_code(409);

                                                        }
                                                }else{
                                                        echo '{ "Error": "Invalid email" }';
                                                        http_response_code(409);

                                                }
                                        }else{
                                                echo '{ "Error": "Invalid password" }';
                                                http_response_code(409);

                                        }
                                }else{
                                        echo '{ "Error": "Invalid username" }';
                                        http_response_code(409);
                                }
                        }else{
                                echo '{ "Error": "Invalid username" }';
                                http_response_code(409);
                        }
                }else{
                        echo '{ "Error": "User exists" }';
                        http_response_code(409);
                }        
        }


        if ($_GET['url'] == "auth") {
                $postBody = file_get_contents("php://input");
                $postBody = json_decode($postBody);
                $username = $postBody->username;
                $password = $postBody->password;
                if ($db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
                        if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
                                $cstrong = True;
                                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
                                $db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
                                echo '{ "Token": "'.$token.'" }';
                                setcookie("LOLID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                                setcookie("LOLID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
                        } else {
                                echo '{ "Error": "Invalid username or password!" }';
                                http_response_code(401);
                        }
                } else {
                        echo '{ "Error": "Invalid username or password!" }';
                        http_response_code(401);
                }
        ////////LIKES//////////
        } else if ($_GET['url'] == "likes"){

                $isliked = "0";
                $postId = $_GET['id'];
                $token = $_COOKIE['LOLID'];

                $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];


                if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
                        $db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
                        //Notify::createNotify("", $postId);
                        $isliked = "1";
                } else {
                        $db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
                        $db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
                        $isliked = "0";
                }

                echo "{";
                echo '"Likes":';
                echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'].',';
                echo '"isLiked":';
                echo $isliked;
                echo "}";
        //////////////FOLLOW//////////////////////////
        } else if ($_GET['url'] == "follow"){
                $isFollowing = "0"; 
                $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $followerid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['LOLID'])))[0]['user_id'];

                if($userid != $followerid){
                        if(!$db->query('SELECT follower_id from followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid))){

                                if($followerid == 13){
                                        $db->query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                                }

                                $db->query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array('userid'=>$userid, ':followerid'=>$followerid));
                                $isFollowing = "1";
                        }else{
                                if($db->query('SELECT follower_id from followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid))){

                                        if($followerid == 13){
                                                $db->query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));

                                        }

                                        $db->query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array('userid'=>$userid, ':followerid'=>$followerid));
                                        $isFollowing = "0";
                                }
                                
                        }
                        
                }

                $followercount = $db->query('SELECT id FROM followers WHERE user_id=:userid', array(':userid'=>$userid));

                echo "{";
                echo '"followercount":';
                echo(sizeof($followercount)).',';
                echo '"isfollowing":';
                echo $isFollowing;
                echo "}";
        } 
        ////////////////
}  else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        if ($_GET['url'] == "auth") {
                if (isset($_GET['token'])) {
                        if ($db->query("SELECT token FROM login_tokens WHERE token=:token", array(':token'=>sha1($_GET['token'])))) {
                                $db->query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_GET['token'])));
                                echo '{ "Status": "Success" }';
                                http_response_code(200);
                        } else {
                                echo '{ "Error": "Invalid token" }';
                                http_response_code(400);
                        }
                } else {
                        echo '{ "Error": "Malformed request" }';
                        http_response_code(400);
                }
        ///////////////deleting posts////////////////////////
        }else if ($_GET['url'] == "postdelete") {
                $candelete = "0";

                $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['LOLID'])))[0]['user_id'];

                if ($db->query('SELECT id FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['id'], ':userid'=>$userid))) {
                        $db->query('DELETE FROM posts WHERE id=:postid', array(':postid'=>$_GET['id']));
                        $db->query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['id']));
                        $db->query('DELETE FROM comments WHERE post_id=:postid', array(':postid'=>$_GET['id']));
                        $candelete = "1";
                }
                echo $candelete;
        }
} else {
        http_response_code(405);
}
?>