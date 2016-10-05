<?php

//start_session();

$db=mysqli_connect("localhost","root","","chatbot") or die("Can not connect right now!");
//$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;


$access_token = "EAAIpDWRp6RMBANKpTJbAQShvLINRcsBP6Fon0qyBxxb2ZCOQfOp24EzIpc59ZB2wSQchpzgheZA5G1XGvynX1QKLPh5bf7M3GlOli3guqB5jCOz5IZClilKI7c4rFaDss2bijXUvAhiSqMViDu3KGuq0ymsz3qies50jEZA1gZBQZDZD";

$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;


$verify_token = "php_chat_bot";
$hub_verify_token = null;
 
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
 
 
if ($hub_verify_token === $verify_token) {
    echo $challenge;
}


//$input = json_decode($variable, true, 512, JSON_BIGINT_AS_STRING);
$input = json_decode(file_get_contents('php://input'), true);
//echo sizeof($input);
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];

$message = $input['entry'][0]['messaging'][0]['message']['text'];


//using regex to extract @ and #
function getHashTags($string){
	$pattern = "/\B#[^\B]+/";
	preg_match($pattern, $string, $matches);
	return $matches[0];
}

function getMainTags($string){
	$pattern = "/((?<!\S)@\w+(?!\S))/";
	preg_match($pattern, $string, $matches);
	return $matches[0];
}

$maintag = substr(getMainTags($message), 1);
$hashtag = substr(getHashTags($message), 1);

$message_to_reply = $maintag.$hashtag;
//set flag to not trigger the api.ai api
if(strlen($maintag) == 0 || strlen($hashtag) == 0){
	$useapiai = true;
}
else{
	$useapiai = false;
}

if(!$useapiai){
	
	if($maintag === "news"){
		//news api - a3f365280e404a49b0595f6c1d8cec05
		//$message_to_reply = "entered inside";
		$chnews = curl_init();
		curl_setopt_array($chnews, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => "https://newsapi.org/v1/articles?source=".$hashtag."&apiKey=a3f365280e404a49b0595f6c1d8cec05"
		));
		$resp = curl_exec($chnews);
		$resp_news = json_decode($resp, true);
		$news_length= sizeof($resp_news['articles']);
		$array_of_news = [];
		for($i = 0; $i < $news_length; $i++){
			$news_title[$i] = $resp_news['articles'][$i]['title'];
			$news_url[$i] = $resp_news['articles'][$i]['url'];
			$news_image_url[$i] = $resp_news['articles'][$i]['urlToImage'];
			$news_desc[$i] = $resp_news['articles'][$i]['description'];		
		}

		curl_close($chnews);
		
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;

		//Initiate cURL.
		$ch = curl_init($url);
		//The JSON data.
		$jsonData1 = '{
		    "recipient":{
		        "id":"'."$sender".'"
		    },
		    "message":{
		        "attachment" : {
		        	"type" : "template",
		        	"payload": {
		        		"template_type" : "generic",
		        		"elements" : [
		        			{
		        				"title" : "'."$news_title[0]".'",
		        				"item_url" : "'."$news_url[0]".'",
		        				"image_url" : "'."$news_image_url[0]".'",
		        				"subtitle" : "'."$news_desc[0]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[1]".'",
		        				"item_url" : "'."$news_url[1]".'",
		        				"image_url" : "'."$news_image_url[1]".'",
		        				"subtitle" : "'."$news_desc[1]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[2]".'",
		        				"item_url" : "'."$news_url[2]".'",
		        				"image_url" : "'."$news_image_url[2]".'",
		        				"subtitle" : "'."$news_desc[2]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[3]".'",
		        				"item_url" : "'."$news_url[3]".'",
		        				"image_url" : "'."$news_image_url[3]".'",
		        				"subtitle" : "'."$news_desc[3]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[4]".'",
		        				"item_url" : "'."$news_url[4]".'",
		        				"image_url" : "'."$news_image_url[4]".'",
		        				"subtitle" : "'."$news_desc[4]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			}
		        		]
		        	}
		        }
		    }
		}';

		//Encode the array into JSON.
		$jsonDataEncoded = $jsonData1;
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		//Execute the request
		if(!empty($input['entry'][0]['messaging'][0]['message']['text'])){
		    curl_exec($ch);
		    curl_close($ch);
		}
	
	}
}


else{
	
	$apiaiurl = "https://api.api.ai/v1/query?v=20150910";   

	$ch1 = curl_init($apiaiurl);

	$jsonData1 = '{
	    "query":[
	        "'."$message".'"
	    ],
	    "lang": "en",
	    "sessionId": "'."$sender".'"
	    }';

	$jsonDataEncoded1 = $jsonData1;
	curl_setopt($ch1, CURLOPT_POST, 1);
	curl_setopt($ch1, CURLOPT_POSTFIELDS, $jsonDataEncoded1);
	curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Authorization: Bearer 0f4915a28b964721860f3b7c5db16eea', 'Content-Type: application/json' ));
	//curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);

	$airesult = curl_exec($ch1);
	curl_close($ch1);

	$response = json_decode($airesult, true);
	    
	if(sizeof($response)){
	    $message_to_reply = $response['result']['fulfillment']['speech'];
	}
	else{
	    $message_to_reply = "Sorry, I didnt understand that.";
	}

	//echo sizeof($message_to_reply);
	echo $message_to_reply;
	//print $message_to_reply;
	
	$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;

	//Initiate cURL.
	$ch = curl_init($url);
	//The JSON data.
	$jsonData = '{
	    "recipient":{
	        "id":"'."$sender".'"
	    },
	    "message":{
	        "text":"'."$message_to_reply".'"
	    }
	}';

	$jsonData1 = '{
		    "recipient":{
		        "id":"'."$sender".'"
		    },
		    "message":{
		        "attachment" : {
		        	"type" : "template",
		        	"payload": {
		        		"template_type" : "generic",
		        		"elements" : [
		        			{
		        				"title" : "'."$news_title[0]".'",
		        				"item_url" : "'."$news_url[0]".'",
		        				"image_url" : "'."$news_image_url[0]".'",
		        				"subtitle" : "'."$news_desc[0]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[1]".'",
		        				"item_url" : "'."$news_url[1]".'",
		        				"image_url" : "'."$news_image_url[1]".'",
		        				"subtitle" : "'."$news_desc[1]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[2]".'",
		        				"item_url" : "'."$news_url[2]".'",
		        				"image_url" : "'."$news_image_url[2]".'",
		        				"subtitle" : "'."$news_desc[2]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[3]".'",
		        				"item_url" : "'."$news_url[3]".'",
		        				"image_url" : "'."$news_image_url[3]".'",
		        				"subtitle" : "'."$news_desc[3]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			},
		        			{
		        				"title" : "'."$news_title[4]".'",
		        				"item_url" : "'."$news_url[4]".'",
		        				"image_url" : "'."$news_image_url[4]".'",
		        				"subtitle" : "'."$news_desc[4]".'",
		        				"buttons":[
							      {
							        "type":"element_share"
							      }              
							    ]
		        			}
		        		]
		        	}
		        }
		    }
		}';

	//Encode the array into JSON.
	$jsonDataEncoded = $jsonData;
	//Tell cURL that we want to send a POST request.
	curl_setopt($ch, CURLOPT_POST, 1);
	//Attach our encoded JSON string to the POST fields.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	//Set the content type to application/json
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	//Execute the request
	if(!empty($input['entry'][0]['messaging'][0]['message']['text'])){
	    curl_exec($ch);
	    curl_close($ch);
	}


	/*
	function firstchat($id,$url){

	    $messages= array("Enter Your Name",
	      "Give us your Choices",
	      "At what time u want updates");


	    for($i =0; $i<3;$i++){
	        echo  $messages[$i];
	        $temp = $messages[$i];

	        $ch = curl_init($url);
	       $jsonData = '{
	    "recipient":{
	        "id":"'."$id".'"
	    },
	    "message":{
	        "text":"'."$temp".'"
	    }
	}';

	//Encode the array into JSON.
	        $jsonDataEncoded = $jsonData;
	//Tell cURL that we want to send a POST request.
	        curl_setopt($ch, CURLOPT_POST, 1);
	//Attach our encoded JSON string to the POST fields.
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	//Set the content type to application/json
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	//Execute the request
	        if(!empty($jsonData)){
	            curl_exec($ch);
	            curl_close($ch);
	        }
	        while(){
	        $input = json_decode(file_get_contents('php://input'), true);
	        }
	        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];

	        $message = $input['entry'][0]['messaging'][0]['message']['text'];

	        echo $message;

	    }


	}

	function checkfirsttime($sender){
	    $db = $GLOBALS['db'];

	    $sql = "SELECT `id` FROM `user_record` WHERE `id`= '$sender' ";
	    $result = mysqli_query($db,$sql);
	    $num_query = mysqli_num_rows($result);
	    if($num_query >0) return true;
	}*/
	}





?>