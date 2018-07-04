<?php
class Image{

	public static function uploadImage($formname, $query, $params){
		$image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));

		$options = array('http'=>array(
			'method'=>"POST",
			'header'=>"Authorization: Bearer 1d96f37d7cd0a2175585ece9cfc4b753f25a8256\n".
			"Content-Type: application/x-www-form-urlencoded", 
			'content'=>$image
		));

		$context = stream_context_create($options);

		$imgurURL = "https://api.imgur.com/3/image";

		if($_FILES[$formname]['size'] > 10240000){
			die('Image too big, must be 10MB or less');
		}

		$response = file_get_contents($imgurURL, false, $context);
		$response = json_decode($response);

		$preparams = array($formname=>$response->data->link);

		$params = $preparams + $params;

		DB::query($query, $params);
	}

}
?>