<?php

//start_session();

$db=mysqli_connect("localhost","root","","chatbot") or die("Can not connect right now!");
//$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;


$access_token = "EAAIpDWRp6RMBAHk8PKIbBuPocsbNmrszj6VrB1IDdse6JcaPvo9XZBqCP6jpz0C6Op72MmhrjxejeXDhLuVeyZAbIZBzNRQX9s2XEGIrfHWq1W7HXOLBqZCHa7tsHr1rKYJNiPMbC2iBvSr16wWG3ODF54lCLZBvWpZAErHqOnZBQZDZD";

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

if(!checkfirsttime($sender)){
    echo "success";
    firstchat($sender,$url);
}


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
        while(1){
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
}
?>