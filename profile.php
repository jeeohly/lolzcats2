<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/post2.php');
include('./classes/Image.php');
include('./classes/Notify.php');

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
		//////////deleting posts
		if(isset($_POST['deletepost'])){
			if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                echo 'Post deleted';
            }
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
		if(isset($_GET['postid']) && !isset($_POST['deletepost'])){
			post2::likePost($_GET['postid'], $followerid);
		}

		$posts = post2::displayPosts($userid, $username, $followerid);
		///////////////////////////////
	}else{
		die('User not found');
	}
}
?>

<!--<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
	<textarea name="postbody" rows="8" cols="80"></textarea>
	<br />Upload an image:
	<input type="file" name="postimg">
	<input type="submit" name="post" value="post">
</form>-->

<!---------------------->

<!DOCTYPE html>
<html style="background-color:rgb(241,247,252);">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lolzcatz</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Dark.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Search.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body style="background-color:rgb(241,247,252);">
    <nav class="navbar navbar-light navbar-expand-md navigation-clean-search" style="background-color:rgb(255,255,255);">
        <div class="container"><a class="navbar-brand" href="#"><img src="assets/img/lolzcatz logo 2.png" style="width:200px;background-color:none;"></a><button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <form class="form-inline mr-auto" target="_self">
                    <div class="form-group"><label for="search-field"><i class="fa fa-search"></i></label><input class="form-control search-field" type="search" name="search" id="search-field" style="width:200px;"></div>
                </form>
                <ul class="nav navbar-nav">
                    <li class="nav-item" role="presentation"><a class="nav-link active" href="#">Timeline</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#">Messages</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#">Notifications</a></li>
                    <li class="dropdown"><a class="dropdown-toggle nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">User</a>
                        <div class="dropdown-menu" role="menu"><a class="dropdown-item" role="presentation" href="#">First Item</a><a class="dropdown-item" role="presentation" href="#">Second Item</a><a class="dropdown-item" role="presentation" href="#">Third Item</a></div>
                    </li>
                </ul>
        </div>
        </div>
    </nav>
    <div><h2 class="text-left" style="display: block; padding-left: 30px; width: 60%;margin-right: auto;margin-left: auto;margin-top:15px;margin-bottom:15px;color: rgb(0, 127, 255);"><?php echo $username; ?>'s profile <?php if($verified) { echo ' - C00l';} ?></h2>
    <!---------------------COLUMN STUFF-------------------->
   	<div>
   		<div class="container">
   			<div class="row">			
   				<div class="col-md-3 col-lg-2" style=""><h4 class="text-left" style="display: block; color: rgb(255,110,199); margin: 5px">About me</h4><blockquote class="blockquote">
    				<p class="mb-0">this is what i like and the shit i dont like lmao</p>
				</blockquote></div>	

   				
   					<div class="col-md-4 col-lg-8 col-xl-9 offset-lg-0 offset-xl-0">
   					
   						<div class="timelineposts">

                        </div>
                    
              		</div>

               	<div class="col-md-4 col-lg-2 col-xl-1 offset-lg-0">
                    <button class="btn btn-primary" type="button" style="margin:0px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width: 120;width: 120px;">New post</button>
                    
                </div>
            </div>
            </div>
        </div>
    </div>  	
    <!---------------MODAL--------->
    <div class="modal fade" role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Comments</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                    <p>The content of your modal.</p>
                </div>
                <div class="modal-footer"><button class="btn btn-light" type="button" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <!------------------------>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                type: "GET",
                url: "api/profileposts?username=<?php echo $username; ?>",
                processData: false,
                contentType: "application/json",
                data: '',
                success: function(r) {
                    var posts = JSON.parse(r)
                    $.each(posts, function(index){
                        $('.timelineposts').html(
                            $('.timelineposts').html() + 

                            '<blockquote class="blockquote" style="background-color:rgb(255,255,255); display: block;padding-left: 30px;">'+posts[index].PostBody+'</p><footer class="blockquote-footer">'+posts[index].PostedBy+', '+posts[index].PostDate+' &nbsp;&nbsp;<button class="btn btn-primary btn-sm" data-id="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);">'+posts[index].Likes+' Likes</button><button class="btn btn-primary btn-sm" data-postid="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);margin:5px;">Comment</button>&nbsp;&nbsp;</footer></blockquote>'



                            //'<blockquote class="blockquote" style="background-color:rgb(255,255,255); display: block;padding-left: 30px;width: 60%;margin-right: auto;margin-left: auto;">'+posts[index].PostBody+'</p><footer class="blockquote-footer">'+posts[index].PostedBy+', '+posts[index].PostDate+' &nbsp;&nbsp;<button class="btn btn-primary btn-sm" data-id="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);">'+posts[index].Likes+' Likes</button><button class="btn btn-primary btn-sm" data-postid="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);margin:5px;">Comment</button>&nbsp;&nbsp;</footer></blockquote>'
                        )

                        $('[data-postid]').click(function(){
                            var buttonid = $(this).attr('data-postid');
                             $.ajax({
                                type: "GET",
                                url: "api/comments?postid=" + $(this).attr('data-postid'),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r){
                                    var res = JSON.parse(r)
                                    showCommentsModal(res);
                                },
                                error: function(r){
                                    console.log(r);
                                }
                            });
                        });

                        $('[data-id]').click(function(){
                            var buttonid = $(this).attr('data-id');
                            $.ajax({
                                type: "POST",
                                url: "api/likes?id=" + $(this).attr('data-id'),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r){
                                    var res = JSON.parse(r)
                                    $("[data-id='"+buttonid+"']").html(''+res.Likes+' Likes</button>');
                                    console.log(r);
                                },
                                error: function(r){
                                    console.log(r);
                                }
                            });
                        })
                    })
                },
                error: function(r) {
                    console.log(r)
                }
            });
        });

        function showCommentsModal(res){
            $('.modal').modal('show')
            var output = "";
            for(var i = 0; i < res.length; i++){
                output += res[i].Comment;
                output += " - ";
                output += res[i].CommentedBy;
                output += "<hr />";
            }
            $('.modal-body').html(output)
        }
    </script>
</body>

</html>

