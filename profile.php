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

		if(DB::query('SELECT follower_id from followers WHERE user_id=:userid', array(':userid'=>$userid))){
			//echo 'Already following';
			$isFollowing = True;
		}
	}else{
		die('User not found');
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
}
?>

<!DOCTYPE html>
<html style="background-color:rgb(245,246,250);">

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

<body style="background-color:#f1f7fc">
    <nav class="navbar navbar-light navbar-expand-md navigation-clean-search" style="background-color:rgb(255,255,255);">
        <div class="container"><a class="navbar-brand" href="#"><img src="assets/img/lolzcatz logo 1.png" style="width:50px;background-color:none;"></a><button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <form class="form-inline mr-auto" target="_self">

                    <div class="searchbox">
                        <input class="form-control sbox" type="text" placeholder="search posts..." style="width:400px;">
                        <ul class="list-group autocomplete" style="word-wrap:break-word;position:absolute;width:400px;z-index:100">
                            
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
    <div>
    <!---------------------COLUMN STUFF-------------------->
   		<div class="container" style="margin-top:10px;">
   			<div class="row">
   							
   				<div class="col-xl-3">
   					<div class="profpicdisplay">

   					</div>

   					<h2 style="text-align:center;margin-bottom:15px;color:rgb(0, 127, 255);"><?php echo $username; ?> <?php if($verified) { echo ' <img src="assets/img/verified marker.png" style="width:25px;background-color:none;">';} ?>
    				</h2>
   					
   					<!---------follow button-------------->
   					<div class="followbutton">
    					
    				</div>

    				<!--------------------stats------>
	    			<div class="lolzbox2" style="height:55px">
		    			<div class="stat"><p align="center" class="mb-0" style="font-size:12px;">Posts</p>
		    				<div class="postcount" style="color:rgb(255,110,199);"></div>
		    			</div>
		    			<div class="stat"><p align="center" class="mb-0" style="font-size:12px;">Followers</p>
		    				<div class="followercount" style="color:rgb(255,110,199);"></div>
		    			</div>
		    			<div class="stat"><p align="center" class="mb-0" style="font-size:12px;">Following</p>
		    				<div class="followingcount" style="color:rgb(255,110,199);"></div>
		    			</div>
	   				</div>


    				<div class="lolzbox"><div class="subheader">About me</div><p class="mb-0">i like to go skii. last winter i went to aspen and it was gnarly! XD Anchorage, Ak resident</p></div>

    			</div>

   				
   					<div class="col-xl-6">
   						<div class="composepost">
   						</div>
   						<div class="newtimelineposts">
   						<!-----------posts--------->
                        </div>
   						<div class="timelineposts">
   						<!-----------posts--------->
                        </div>
              		</div>
              	<div class="col-xl-3">
	               	   	<div class="newpost">
	                    <!----------new post button--------------->
	                    
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
                    <h4 class="modal-title">New Post</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;"><span aria-hidden="true">×</span></button></div>
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
	                <div class="modal-footer">
	                	<button type="submit" name="post" value="post" style="display:inline-block;background-color:rgb(0,127,255);color:rgb(255,255,255);width:50%;">Post</button>
	                	<button type="button" data-dismiss="modal" style="display:inline-block;background-color:rgb(255,110,199);color:rgb(255,255,255);width:50%;">Close</button>
	                </div>
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
        	/////////disabling enter button////
        	$('textarea').keypress(function(event) {

			    if (event.keyCode == 13) {
			        event.preventDefault();
			    }
			});
        	///////////////////////search////////////////////
        	$('.sbox').keyup(function() {
                $('.autocomplete').html('');
                $.ajax({
                    type: "GET",
                    url: "api/search?query=" + $(this).val(),
                    processData: false,
                    contentType: "application/json",
                    data: '',
                    success: function(r) {
                        r = JSON.parse(r)
						for(var i = 0; i < r.length; i++){
	                        //console.log(r[i].body)
	                        $('.autocomplete').html(
	                            $('.autocomplete').html() + 
	                            '<a href="profile.php?username='+r[i].username+'#'+r[i].id+'"><div class="lolzsearch"><div class="searchtext"><span>'+r[i].body+'</span></div></div></a>'
	                        )
	                    }
                    },
                    error: function(r){
                        console.log(r)
                    }
                })
            })
        	$(document).click(function(event){
        		$('.autocomplete').html('')
        	})
            /////////////////////MY STUFF//////////////////

            ///////////////////stats////////////////
			$.ajax({
				type: "GET",
			    url: "api/stats?username=<?php echo $username; ?>",
			    processData: false,
			    contentType: "application/json",
			    data: '',
			    success:function(r){
					var res = JSON.parse(r);
					$('.postcount').html(
					    $('.postcount').html() + '<p align="center" class="mb-0" stat-id="1" style="font-size:18px;">'+res.postcount+'</p>'
					)
					$('.followercount').html(
					    $('.followercount').html() + '<p align="center" class="mb-0" stat-id="2" style="font-size:18px;">'+res.followercount+'</p>'
					)
					$('.followingcount').html(
					    $('.followingcount').html() + '<p align="center" class="mb-0" stat-id="3" style="font-size:18px;">'+res.followingcount+'</p>'
					)
			    }
					                
			})
			////////////////////////////////
        
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
					            '<button class="btn btn-primary" type="button" style="margin-bottom:10px;display:block;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%;" onclick="showNewPostModal()">New post</button>'
				        )
				        $('.composepost').html(
				        	$('.composepost').html() + 
				        		'<div style="margin-bottom:10px;"><div class="postheader"><textarea name="lolzpostinput" id="postinput" class="postinput" rows="1" data-min-rows="1" style="padding-bottom:10px;" placeholder="What u wanna say? XD"></textarea></div><div class="picfile"></div><input type="file" id="my_file" style="display:none;" class="my_file" name="my_file"><button class="uploadbutton" type="submit" id="filedisplay"><img src="assets/img/image.png" class="icon"><div class="buttontext2">Upload pic</div></button><button name="postbutton" class="postbutton" post-id="1" type="button"><img src="assets/img/post.png" class="icon"><div class="buttontext">Post</div></button></div>'
				        )
				        document.getElementById('filedisplay').onclick = function() {
						    document.getElementById('my_file').click(); 
						};
						//////FILE DISPLAY///////////
						$('#my_file').change(function(){
							var filename = document.getElementById('my_file').value;
							var filename = filename.replace(/^.*[\\\/]/, '');
							$('.picfile').html('<div class="fileback"><button class="reportdelete" style="margin-right:5px;" delete-pic="0"><img src="assets/img/x.png" class="icon3"></button>'+filename+'</div>')

							$('[delete-pic]').click(function(){
								document.getElementById('my_file').value = '';
								$('.picfile').html('')
								console.log($('.my_file').val());
							})
							console.log($('.my_file').val());
						})
						/////////////////////////
    					$(document)
						    .one('focus.postinput', 'textarea.postinput', function(){
						        var savedValue = document.getElementById('postinput').value;
						        document.getElementById('postinput').value = '';
						        document.getElementById('postinput').baseScrollHeight = document.getElementById('postinput').scrollHeight;
						        document.getElementById('postinput').value = savedValue;
						    })
						    .on('input.postinput', 'textarea.postinput', function(){
						        var minRows = document.getElementById('postinput').getAttribute('data-min-rows')|0, rows;
						        document.getElementById('postinput').rows = minRows;
						        rows = Math.ceil((document.getElementById('postinput').scrollHeight - document.getElementById('postinput').baseScrollHeight) / 24);
						        document.getElementById('postinput').rows = minRows + rows;
					    	});
					         //////////////compose post/////////////////////
						$('[post-id]').click(function(){
							var body = $('.postinput').val()
							body = body.replace(/(["])/g, "\\\"")
							body = body.replace(/(['])/g, "\'")
							body = body.replace(/\\/g, "\\\\")
							var img = $('.my_file').val()
							img = img.replace(/\\/g, "\\\\")
							$.ajax({
				                type: "POST",
				                url: "api/post?body="+ img + " " + body,
				                processData: false,
				                contentType: "application/json",
				                data: '',
				                success: function(r) {
				                	console.log(r);
				                	var posts2 = JSON.parse(r)
				                	window.open("profile.php?username=<?php echo $username; ?>", "_self");

				                	//$('.newtimelineposts').html(
	                            		//$('.newtimelineposts').html() + '<div style="margin-bottom:10px;"><div class="postheader"><p class="mb-0" style="width:70%;display:inline-block;"><a href="profile.php?username='+posts2.PostedBy+'" style="font-weight:500;"><img src="'+posts2.Profpic+'" data-tempsrc="'+posts2.Profpic+'" class="postprofile" id="img'+posts2.postId+'">'+posts2.PostedBy+'</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'+posts2.PostDate+'</p><span style="float:right;margin-top:5px;"><button class="reportdelete" delete-id="'+posts2.PostId+'"><img src="assets/img/'+posts2.Deletereport+'.png" class="icon2"></button></span></div><div class="postbody"><p class="mb-0">'+posts2.PostBody+'</p></div><div class="postfooter"><button class="likebutton" type="button" data-id="'+posts2.PostId+'"><img src="assets/img/thumbs.png" class="icon"><div class="buttonnumber">'+posts2.Likes+'</div><div class="buttontext">'+posts2.isLiked+'</div></button><button class="commentbutton" data-postid="'+posts2.PostId+'" type="button"><img src="assets/img/comment2.png" class="icon"><div class="buttonnumber">'+posts2.commentcount+'</div><div class="buttontext"> Comment</div></button></div><div class="commentbox" comment-id="'+posts2.PostId+'"><img src="'+posts2.Userpic+'" class="userpic"><div class="commentbubble"><textarea class="commentinput" id="'+posts2.PostId+'" rows="1" data-min-rows="1"  placeholder="comment..."></textarea></div></div></div>'
	                            		//)

				                }

							})
						})	
					
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
								        '<button class="btn btn-primary follow" follow-id="'+r+'" type="submit" style="display:block;margin-bottom:10px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%">follow</button>'
								    )
						    	}else if(r == "1"){
						    		$('.followbutton').html(
								        $('.followbutton').html() +
								        '<button class="btn btn-primary follow" follow-id="'+r+'" type="submit" style="display:block;margin-bottom:10px;background-color:#ffffff;color:rgb(33,37,41); padding:5px;width:100%">unfollow</button>'
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
						                	var res = JSON.parse(r);
						                    console.log(res.isfollowing);
						                    if(res.isfollowing == "1"){
						                    	$("[follow-id='"+followid+"']").html('unfollow</button>');
						                    	//console.log('followed')	
						                    }else if (res.isfollowing == "0"){
						                    	$("[follow-id='"+followid+"']").html('follow</button>');
						                    	//console.log('unfollowed')	
						                    }
						                   	$("[stat-id=2]").html(''+res.followercount+'</p>');                     
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
					        	'<img src="" data-tempsrc="<?php echo $profimg; ?>" class="postimg" style="border:1px solid #ccc;border-radius: 50%;width:100%;display:block;margin-right:auto;margin-left:auto;border-radius: 50%;object-fit:cover;width:150px;height:150px;margin-bottom:5px">'
					    )
					}else{
						$('.profpicdisplay').html(
					        $('.profpicdisplay').html() +
					        	'<img src="" data-tempsrc="https://i.imgur.com/ml86Eqw.jpg" class="postimg" style="border:1px solid #ccc;border-radius: 50%;width:100%;display:block;margin-right:auto;margin-left:auto;border-radius: 50%;object-fit:cover;width:150px;height:150px;margin-bottom:5px">'
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
	                            '<div style="margin-bottom:10px;"><div class="postheader"><p class="mb-0" style="width:70%;display:inline-block;"><a href="profile.php?username='+posts[index].PostedBy+'" style="font-weight:500;"><img src="" data-tempsrc="'+posts[index].Profpic+'" class="postprofile" id="img'+posts[index].postId+'">'+posts[index].PostedBy+'</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'+posts[index].PostDate+'</p><span style="float:right;margin-top:5px;"><button class="reportdelete" delete-id="'+posts[index].PostId+'"><img src="assets/img/'+posts[index].Deletereport+'.png" class="icon2"></button></span></div><div class="postbody"><p class="mb-0">'+posts[index].PostBody+'</p></div><div class="postfooter"><button class="likebutton" type="button" data-id="'+posts[index].PostId+'"><img src="assets/img/thumbs.png" class="icon"><div class="buttonnumber">'+posts[index].Likes+'</div><div class="buttontext">'+posts[index].isLiked+'</div></button><button class="commentbutton" data-postid="'+posts[index].PostId+'" type="button"><img src="assets/img/comment2.png" class="icon"><div class="buttonnumber">'+posts[index].commentcount+'</div><div class="buttontext"> Comment</div></button></div><div class="commentbox" comment-id="'+posts[index].PostId+'"><img src="'+posts[index].Userpic+'" class="userpic"><div class="commentbubble"><textarea class="commentinput" id="'+posts[index].PostId+'" rows="1" data-min-rows="1"  placeholder="comment..."></textarea></div></div></div>'
                        	)
                       	}else{
                       		$('.timelineposts').html(
	                            $('.timelineposts').html() + 
	                            '<div style="margin-bottom:10px;"><div class="postheader"><p class="mb-0" style="width:70%;display:inline-block;"><a href="profile.php?username='+posts[index].PostedBy+'" style="font-weight:500;"><img src="" data-tempsrc="'+posts[index].Profpic+'" class="postprofile" id="img'+posts[index].postId+'">'+posts[index].PostedBy+'</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'+posts[index].PostDate+'</p><span style="float:right;margin-top:5px;"><button class="reportdelete" delete-id="'+posts[index].PostId+'"><img src="assets/img/'+posts[index].Deletereport+'.png" class="icon2"></button></span></div><div class="postbody"><p class="mb-0">'+posts[index].PostBody+'</p></div><div style="background-color:rgb(255,255,255)"><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postpic" id="img'+posts[index].postId+'"></div><div class="postfooter"><button class="likebutton" type="button" data-id="'+posts[index].PostId+'"><img src="assets/img/thumbs.png" class="icon"><div class="buttonnumber">'+posts[index].Likes+'</div><div class="buttontext">'+posts[index].isLiked+'</div></button><button class="commentbutton" data-postid="'+posts[index].PostId+'" type="button"><img src="assets/img/comment2.png" class="icon"><div class="buttonnumber">'+posts[index].commentcount+'</div><div class="buttontext"> Comment</div></button></div><div class="commentbox" comment-id="'+posts[index].PostId+'"><img src="'+posts[index].Userpic+'" class="userpic"><div class="commentbubble"><textarea class="commentinput" id="'+posts[index].PostId+'" rows="1" data-min-rows="1"  placeholder="comment..."></textarea></div></div></div>'
	                        )

                       	}

                       	if(posts[index].commentcount != 0){
	                       	$.ajax({
	                			type: "GET",
	                			url: "api/comments?id="+posts[index].PostId+"",
				                processData: false,
				                contentType: "application/json",
				                data: '',
				                success: function(r) {
				                    var comments = JSON.parse(r)
				                    $.each(comments, function(index2){
				                    	$("[comment-id='"+posts[index].PostId+"']").html(
		                            		$("[comment-id='"+posts[index].PostId+"']").html() + '<a href="profile.php?username='+comments[index2].commentBy+'" style="font-weight:500;"><img src="'+comments[index2].commentprofpic+'" class="userpic"><div class="commentbubbleposts"><div class="commenttext">'+comments[index2].commentBy+'</a> '+comments[index2].commentbody+'</div></div><br>'
		                            	)
				                    })
				                }
				            })
                        }
                        //////TEXT AREA EXPAND////////////

    					$(document)
						    .one('focus.commentinput', 'textarea.commentinput', function(){
						        var savedValue = document.getElementById(posts[index].PostId).value;
						        document.getElementById(posts[index].PostId).value = '';
						        document.getElementById(posts[index].PostId).baseScrollHeight = document.getElementById(posts[index].PostId).scrollHeight;
						        document.getElementById(posts[index].PostId).value = savedValue;
						    })
						    .on('input.commentinput', 'textarea.commentinput', function(){
						        var minRows = document.getElementById(posts[index].PostId).getAttribute('data-min-rows')|0, rows;
						        document.getElementById(posts[index].PostId).rows = minRows;
						        rows = Math.ceil((document.getElementById(posts[index].PostId).scrollHeight - document.getElementById(posts[index].PostId).baseScrollHeight) / 22);
						        document.getElementById(posts[index].PostId).rows = minRows + rows;
					    	});		
					    /////////////////COMMENT INPUT//////////////////
                     $('[data-postid]').click(function(){
                         var buttonid = $(this).attr('data-postid');
                         document.getElementById(buttonid).focus();
                     });
                     $('[id]').keypress(function(event) {
								if (event.keyCode == 13) {
								    event.preventDefault();
								}
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
                                    	$("[data-id='"+buttonid+"']").html('<img src="assets/img/thumbs.png" class="icon"><div class="buttonnumber">'+res.Likes+'</div><div class="buttontext"> Like</div>');
                                    	console.log(r);
                                    }else if(res.isLiked == "1"){
                                    	$("[data-id='"+buttonid+"']").html('<img src="assets/img/thumbs.png" class="icon"><div class="buttonnumber">'+res.Likes+'</div><div class="buttontext"> Unlike</div>');
                                    	console.log(r);
                                    }
                                },
                                error: function(r){
                                    console.log(r);
                                }
                            });
                        })

                        $('[delete-id]').click(function(){
                        	var buttonid = $(this).attr('delete-id');
                        	$.ajax({
                        		type: "DELETE",
                                url: "api/postdelete?id=" + $(this).attr('delete-id'),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r){
                                	console.log("<?php echo $username; ?>");
                                	console.log(r);
                                	if(r == "1"){
                                		window.open("profile.php?username=<?php echo $username; ?>", "_self");

                                	}
                                }
                        	})
                       	})
                    })

					$('.postprofile').each(function(){
						this.src=$(this).attr('data-tempsrc')
						this.onload = function(){
							this.style.opacity = '1';
						}
					})

					$('.postpic').each(function(){
						this.src=$(this).attr('data-tempsrc')
						this.onload = function(){
							this.style.opacity = '1';
						}
					})

					scrollToAnchor(location.hash);

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

