# lolzcatz

Summer 2018 Project 

A social network site utilizing Bootstrap, PHP, PDO queries, sql tables, Cloudflare's ajax requests, javascript, phpmailer, and Imgur's image uploading API. 

#### Update
The classes folder has been deprecated and all of the API's for database access is now found under api/index.php. All displayed functions in the readme are currently operational besides notifications, commenting, messaging, and user searching. The operational pages are the following: 

## navbar
* links all pages together
* search box for all posts (but will make this possible for all users as well)
## profile.php
* A functional follow/unfollow button
* A posting box
  * image upload button (that uploads to Imgur)
* a list of posts in most recent first order
  * posts with like/unlike button
  * comment box
* post delete button
#### Your account page
![alt text](https://i.imgur.com/GKnHBi0.png)
#### Friend's page 
![alt text](https://i.imgur.com/pB0YQs0.png)
## login.html
* login tokens expires in 7 days 
## create-account.html
* welcome email is sent to user
* password is encrypted using MD5 hash generator 
_
![alt text](https://i.imgur.com/YozT9g2.png)
## my-account.php
* able to change user's profile picture in the database 
## index.html
* posts of user's followings 
#### Timeline page 
![alt text](https://i.imgur.com/vKedUIV.png)
## forgot-password.html 
* ability to change password in the database 
## databse info
* used xampp server
* SQL tables for comments, followers, login_tokens, messages, notifications, password_tokents, posts, post_likes, and users
_
![alt text](https://i.imgur.com/i5myKyh.png)






