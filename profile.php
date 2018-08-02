<?php
include('./classes/DB.php');
include('./classes/login2.php');
include('./classes/post2.php');
include('./classes/Image.php');
include('./classes/Notify.php');

$username = "";
$isFollowing = False;
$verified = False;

$profimg = "";

if(isset($_GET['username'])){
	if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){

		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
		$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
		$verified =  DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
		$followerid = login2::isLoggedIn();

		$profimg = DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['profileimg'];

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

		//////////postimg///////////////
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
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body style="background-color:rgb(241,247,252);">
    <nav class="navbar navbar-light navbar-expand-md navigation-clean-search" style="background-color:rgb(255,255,255);">
        <div class="container"><a class="navbar-brand" href="#"><img src="assets/img/lolzcatz logo 2.png" style="width:200px;background-color:none;"></a><button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <form class="form-inline mr-auto" target="_self">

                    <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                        <input class="form-control sbox" type="text" placeholder="search posts...">
                        <ul class="list-group autocomplete" style="word-wrap:break-word;position:absolute;width:200px;z-index:100">
                            
                        </ul>
                    </div>

                </form>
                <ul class="nav navbar-nav">
                    <li class="nav-item" role="presentation"><a class="nav-link" href="index.html">Timeline</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#">Messages</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#">Notifications</a></li>
                    <li class="dropdown"><a class="dropdown-toggle nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">User</a>
                        <div class="dropdown-menu" role="menu">
                        	<a class="dropdown-item" role="presentation" drop-id="1" href="#">My profile</a>
                        	<a class="dropdown-item" role="presentation" drop-id="2" href="#">Change password</a>
                        	<a class="dropdown-item" role="presentation" drop-id="3" href="my-account.php">Upload profile pic</a>
                        	<a class="dropdown-item" role="presentation" drop-id="4" href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
        </div>
        </div>
    </nav>
    <div><h2 class="text-left" style="display: block;width: 60%;margin-right: auto;margin-left: auto;margin-top:15px;margin-bottom:15px;color: rgb(0, 127, 255);"><?php echo $username; ?>'s profile <?php if($verified) { echo ' - C00l';} ?>
    </h2>
    <!---------------------COLUMN STUFF-------------------->
   	<div>
   		<div class="container">
   			<div class="row">
   							
   				<div class="col-md-3 col-lg-2">
   					<div class="profpicdisplay">

   					</div>
   					
   					<!---------follow button-------------->
   					<div class="followbutton">
    		
    				</div>
    				<!-------------------------->
   					<h4 class="text-left" style="display: block; color: rgb(255,110,199);">About me</h4>
   					<blockquote class="">
    					<li class="list-group-item"><p class="mb-0">this is what i like and the shit i dont like lmao</p></li>
    				</blockquote>
    			</div>

   				
   					<div class="col-md-5 col-lg-8 col-xl-9 ">
   					
   						<div class="timelineposts">
   						<!-----------posts--------->
                        </div>
                    
              		</div>
              	<div class="newpost">
	               	<div class="col-md-4 col-lg-2 col-xl-1 ">
	                    <!----------new post button--------------->
	                    
	                </div>
	            </div>
            </div>
            </div>
        </div>
    </div>  	
    <!---------------MODAL FOR COMMENTS--------->
    <div class="modal fade" id="commentsmodal" role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Comments</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                    <p>The content of your modal.</p>
                </div>
                <div class="modal-footer"><button class="btn btn-light" type="button" data-dismiss="modal" style="display:block;background-color:rgb(255,110,199);color:rgb(255,255,255)">Close</button></div>
            </div>
        </div>
    </div>
    <!------------MODAL FOR NEW POST---------->
    <div class="modal fade" id="newpost" role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Post</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div style="max-height: 400px; overflow-y: auto">
                    <form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                  
						<textarea name="postbody" rows="8" cols="60" style="resize:none;display:block; margin-left: auto; margin-top:15px; margin-right: auto; margin-bottom: 5px;"></textarea>

						<div style="display:block; margin-left: 25px; margin-bottom: 10px;">
							Upload an image:
							<br />
							<!------<input type="file" name="postimg">
							<input type="submit" name="post" value="post">----->
							<input type="file" name="postimg" style="">
							
						</div>
					
	                </div>
	                <div class="modal-footer"><button class="btn btn-light" type="submit" name="post" value="post" style="display:block;background-color:rgb(0,127,255);color:rgb(255,255,255);margin-right:5px;">Post</button><button class="btn btn-light" type="button" data-dismiss="modal" style="display:block;background-color:rgb(255,110,199);color:rgb(255,255,255)">Close</button></div>
	                </form>
            </div>
        </div>
    </div>
    <!------------------------>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script type="text/javascript">

    function scrollToAnchor(aid){
    	var aTag = $(aid);
    	$('html,body').animate({scrollTop: aTag.offset().top}, 'slow');
    }
        $(document).ready(function() {

        	$('textarea').keypress(function(event) {

			    if (event.keyCode == 13) {
			        event.preventDefault();
			    }
			});

			

        	$('.sbox').keyup(function() {
                $('.autocomplete').html("")

                $.ajax({
                
                    type: "GET",
                    url: "api/search?query=" + $(this).val(),
                    processData: false,
                    contentType: "application/json",
                    data: '',
                    success: function(r) {
                        r = JSON.parse(r)

                        for(var i = 0; i < r.length; i++){
                            console.log(r[i].body)
                            $('.autocomplete').html(
                                $('.autocomplete').html() + 
                                '<a href="profile.php?username='+r[i].username+'#'+r[i].id+'"><li class="list-group-item"><span>'+r[i].body+'</span></li></a>'
                            )
                        }
                    },
                    error: function(r){
                        console.log(r)
                    }
                })
            })

            /////////////////////MY STUFF//////////////////
        
	        $.ajax({
	            type: "GET",
	            url: "api/users",
	            processData: false,
	            contentType: "application/json",
	            data: '',
	            success: function(r) {
	                
	                $('[drop-id]').click(function(){
	                    if($(this).attr('drop-id') == "1"){
	                        console.log("success1")
	                        window.open("profile.php?username="+r, "_self");
	                    }else if($(this).attr('drop-id') == "2"){
	                        console.log("success2")
	                    }else if($(this).attr('drop-id') == "3"){
	                        console.log("success3")
	                    }else if($(this).attr('drop-id') == "4"){
	                        console.log("success4")
	                    }
	                                
	                })
	                ///////NEW POST OPENS ONLY FOR LOGGED IN SELF USER//////////////
			        if(r == '<?php echo $username; ?>'){
				        $('.newpost').html(
					        $('.newpost').html() +
					            '<button class="btn btn-primary" type="button" style="display:block;margin-left:10px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%;" onclick="showNewPostModal()">New post</button>'
				        )
				    ////////////following stuff///////////////////
			        }else{
			        	$.ajax({
	            			type: "GET",
	            			url: "api/follow?username=<?php echo $username; ?>",
	            			processData: false,
	            			contentType: "application/json",
	            			data: '',
	            			success: function(r) {
	            				console.log(r);

	            				if(r == "0"){
							        $('.followbutton').html(
								        $('.followbutton').html() +
								        '<button class="btn btn-primary follow" follow-id="'+r+'" type="submit" style="display:block;margin-bottom:15px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%">follow</button>'
								    )
						    	}else if(r == "1"){
						    		$('.followbutton').html(
								        $('.followbutton').html() +
								        '<button class="btn btn-primary follow" follow-id="'+r+'" type="submit" style="display:block;margin-bottom:15px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%">unfollow</button>'
								    )
						    	}
						       												
						        $('[follow-id]').click(function(){
						        	var followid = $(this).attr('follow-id');
						        	
								    $.ajax({
						                type: "POST",
						                url: "api/follow?username=<?php echo $username; ?>",
						                processData: false,
						                contentType: "application/json",
						                data: '',
						                success: function(r){
						                    console.log(r);
						                    if(r == "1"){
						                    	$("[follow-id='"+followid+"']").html('unfollow</button>');
						                    	//console.log('followed')	
						                    }else if (r == "0"){
						                    	$("[follow-id='"+followid+"']").html('follow</button>');
						                    	//console.log('unfollowed')	
						                    }	                        
						                }
						            })
							    	

					     		})
			     			}
			     		})
			        	
			        }
		            
	              	///////////PROFILE PIC/////////////////////
	              	if('<?php echo $profimg; ?>'){
					    $('.profpicdisplay').html(
					        $('.profpicdisplay').html() +
					        	'<img src="" data-tempsrc="<?php echo $profimg; ?>" class="postimg" style="display:block;width:100%;">'
					    )
					}else{
						$('.profpicdisplay').html(
					        $('.profpicdisplay').html() +
					        	'<img src="" data-tempsrc="https://i.imgur.com/ml86Eqw.jpg" class="postimg" style="display:block;width:100%;">'
					    )
					}
				    $('.postimg').each(function(){
					    this.src=$(this).attr('data-tempsrc')
					    this.onload = function(){
							this.style.opacity = '1';
						}
					})
					//////////////////////////////

	            }
	        })

            $.ajax({
                type: "GET",
                url: "api/profileposts?username=<?php echo $username; ?>",
                processData: false,
                contentType: "application/json",
                data: '',
                success: function(r) {
                    var posts = JSON.parse(r)
                    
                    $.each(posts, function(index){

                    	if(posts[index].PostImage == ""){
	                        $('.timelineposts').html(
	                            $('.timelineposts').html() + 
	                            '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote class="blockquote" style="word-wrap: break-word; background-color:rgb(255,255,255); display: block;width: 60%;"><p>'+posts[index].PostBody+'</p><footer class="blockquote-footer">'+posts[index].PostedBy+', '+posts[index].PostDate+'<button class="btn btn-primary btn-sm" data-id="'+posts[index].PostId+'" type="button" style="margin-left:30px;background-color:rgb(0,127,255);">'+posts[index].Likes+''+posts[index].isLiked+'</button><button class="btn btn-primary btn-sm" data-postid="'+posts[index].PostId+'" type="button" style="margin-left:5px;background-color:rgb(0,127,255);">Comment</button>&nbsp;&nbsp;</footer></blockquote></li>'
                        	)
                       	}else{
                       		$('.timelineposts').html(
	                            $('.timelineposts').html() + 
	                            '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote class="blockquote" style="word-wrap: break-word; background-color:rgb(255,255,255); display: block;width: 60%;"><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'"><footer class="blockquote-footer">'+posts[index].PostedBy+', '+posts[index].PostDate+'<button class="btn btn-primary btn-sm" data-id="'+posts[index].PostId+'" type="button" style="margin-left:30px;background-color:rgb(0,127,255);">'+posts[index].Likes+''+posts[index].isLiked+'</button><button class="btn btn-primary btn-sm" data-postid="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);margin-left:5px;">Comment</button>&nbsp;&nbsp;</footer></blockquote></li>'
	                        )
                       	}
                            //'<blockquote class="blockquote" style="background-color:rgb(255,255,255); display: block;padding-left: 30px;width: 60%;margin-right: auto;margin-left: auto;">'+posts[index].PostBody+'</p><footer class="blockquote-footer">'+posts[index].PostedBy+', '+posts[index].PostDate+' &nbsp;&nbsp;<button class="btn btn-primary btn-sm" data-id="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);">'+posts[index].Likes+' Likes</button><button class="btn btn-primary btn-sm" data-postid="'+posts[index].PostId+'" type="button" style="background-color:rgb(0,127,255);margin:5px;">Comment</button>&nbsp;&nbsp;</footer></blockquote>'
                        

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
                                    console.log(res.isLiked);
                                    if(res.isLiked == "0"){
                                    	$("[data-id='"+buttonid+"']").html(''+res.Likes+' Likes</button>');
                                    	console.log(r);
                                    }else if(res.isLiked == "1"){
                                    	$("[data-id='"+buttonid+"']").html(''+res.Likes+' Unlike</button>');
                                    	console.log(r);
                                    }
                                },
                                error: function(r){
                                    console.log(r);
                                }
                            });
                        })
                       
                    })

					$('.postimg').each(function(){
						this.src=$(this).attr('data-tempsrc')
						this.onload = function(){
							this.style.opacity = '1';
						}
					})

                    scrollToAnchor(location.hash)
                },
                error: function(r) {
                    console.log(r)
                }
            });
        });

		function showNewPostModal(){
			$('#newpost').modal('show')
		}

        function showCommentsModal(res){
            $('#commentsmodal').modal('show')
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

