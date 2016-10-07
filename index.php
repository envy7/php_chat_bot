<?php

//start_session();

$db=mysqli_connect("localhost","root","","chatbot") or die("Can not connect right now!");
//$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;


$access_token = "EAASK50x8ys8BALVU7K6HTevDYuwm6ZAazZCHlkiIC0JBjv8sAI6P7vGQRGjebLBZAr5oEszkp00ebtgkwzawG1hbOPr8EXS8aDOwDTc0iqDZCTTiMmcKhtFVmHqXv0NDNuJE5WRz49ZBqEXaZBTQLdlYFdkR0hT678BUaadcg5GAZDZD";

$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;

$flag=0;

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

if(checkschedule($message)){
        add_schedule_db($sender, $message,$url);
        $flag =1;
    }
checkupdatestatus($url);

//echo "sizeof message". $message. "end of message";

if($sender != 217358738681243 && isset($message)){

    $return_num_replies = checkfirsttime($sender);

//echo "replies = ".$return_num_replies."\n";

    if($return_num_replies <=3){

        switch ($return_num_replies) {
            case '1':
            firsttime($sender,$url);
            break;
            case '2':

            secondtime($sender,$url,$message);
            break;
            case '3':
            thirdtime($sender,$url,$message);
            break;
            break;
        }
    }

    else if ($flag!=1){


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


        save_message_and_response_api($sender,$message,$message_to_reply);

//echo sizeof($message_to_reply);
//echo $message_to_reply;
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

    }
}

function firsttime($sender,$url){

    $db = $GLOBALS['db'];
    $num = 1;
    $sql = "INSERT INTO `clients`(`id`,`replies`) VALUES ('$sender', '$num')";
    $result = mysqli_query($db,$sql);

    $messages= "Hello User. We welcome you to Chat_bot. Please help us know you better. Please enter Following Details \\n1. Name \\n2. Interests";
    echo $sender;
    $temp = $messages;

    $ch = curl_init($url);
    $jsonData = '{
        "recipient":{
            "id":"'."$sender".'"
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

}

function secondtime($sender,$url,$message){
    $db = $GLOBALS['db'];
    $num = 2;
    $sql = "UPDATE `clients` SET `replies`='$num' WHERE `id` = '$sender'";
    $result = mysqli_query($db,$sql);
    $sql1 = "INSERT INTO `user_record`(`id`,`name`) VALUES ('$sender', '$message')";
    $result1 = mysqli_query($db,$sql1);

    $send_message = "Please Give your comma separated Interests";

    $ch = curl_init($url);
    $jsonData = '{
        "recipient":{
            "id":"'."$sender".'"
        },
        "message":{
            "text":"'."$send_message".'"
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


}

function thirdtime($sender,$url,$message){
    $db = $GLOBALS['db'];
    $num = 3;
    $sql = "UPDATE `clients` SET `replies`='$num' WHERE `id` = '$sender'";
    $result = mysqli_query($db,$sql);
    $sql1 = "UPDATE `user_record` SET `interests`= '$message' WHERE `id` = '$sender'";
    $result1 = mysqli_query($db,$sql1);

    $send_message = "Thank You for ur Response \\n I can help u with many things like \\n1.News \\n2.Scheduling \\n 3.OCR \\n4. Image/gif search";

    $ch = curl_init($url);
    $jsonData = '{
        "recipient":{
            "id":"'."$sender".'"
        },
        "message":{
            "text":"'."$send_message".'"
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
}





function checkfirsttime($sender){
    $db = $GLOBALS['db'];

    $sql = "SELECT `id`, `replies` FROM `clients` WHERE `id`= '$sender' ";
    $result = mysqli_query($db,$sql);
    $num_query = mysqli_num_rows($result);
    if($num_query == 0) return 1;
    else {
        //echo $num_query;
        $row = mysqli_fetch_row($result);
        //echo "size = ".sizeof($row);
        $num_replies = $row[1];
        switch ($num_replies) {
            case '1':
            return 2;
            break;
            case '2':
            return 3;
            break;
            case '3':
            return 4;
            break;
        }


    }
}

function save_message_and_response_api($sender,$message,$message_to_reply){
    $db = $GLOBALS['db'];
    $sql = "INSERT INTO `chat_history`(`id`, `user_message`, `reply`) VALUES  ('$sender','$message','$message_to_reply')";
    $result = mysqli_query($db,$sql);
}


function checkschedule($message){

        $pattern = "/((?<!\S)@\w+(?!\S))/";

        preg_match($pattern, $message, $matches);
        if(isset($matches[0])){
        $string = $matches[0];
        $maintag = substr($string, 1);
        //echo $maintag;
        if( strcasecmp($maintag,"schedule")==0){
            return true;
        }
        else{
            return false;
        }
    }
    else{
        return false;

    }
}

function add_schedule_db($sender, $message,$url){
    $db = $GLOBALS['db'];
    $pattern = "/\B#[^\B]+/";
    preg_match($pattern, $message, $matches);
    if(isset($matches)){
    $string = $matches[0];

    $temp = strrpos($string,"on");
    $date = substr($string, ($temp+3));
    $title = substr($string, 1, ($temp-2));
   // echo $date;
    $date_curr = date("Y-m-d");
    if($date > $date_curr){
   // echo $title;
    $sql = "INSERT INTO `scheduler`(`id`,`title`,`date`) VALUES ('$sender','$title','$date') ";
    $result = mysqli_query($db,$sql);

    $sql_update_meta_data1 = "UPDATE `status_table_metadata` SET `updated_on` = '$date_curr' AND `is_updated`= 'N'";
    mysqli_query($db,$sql_update_meta_data1);

    }
    else {
         $ch = curl_init($url);
            $error_message = "The date is not in correct. Please enter valid future date in correct format";
                $jsonData = '{
                    "recipient":{
                        "id":"'."$sender".'"
                    },
                    "message":{
                        "text":"'."$error_message".'"
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
    }
   
}
}

function checkupdatestatus($url){
    $db = $GLOBALS['db'];
    //$date = date("Y-m-d");
    $date = date("Y-m-d");
    $sql = "SELECT `updated_on`, `is_updated` FROM  `status_table_metadata`";
    $result_meta_data = mysqli_query($db,$sql);
    echo mysqli_num_rows($result_meta_data);
    $row_meta_data = mysqli_fetch_row($result_meta_data); 
    if(!(($row_meta_data[0] == $date) && ($row_meta_data[1] == "Y"))){
        $time = 12;
        if($time >= 9){

            $sql = "SELECT `id`, `interests` FROM  `user_record` WHERE `updated` = 'N'";
            $result = mysqli_query($db,$sql);
            $num_query = mysqli_num_rows($result);
            echo  $num_query;
        //start of new func
            for($i=0;$i<$num_query;$i++){

                $row=mysqli_fetch_row($result);
                $ch = curl_init($url);
                $tmp1 = $row[0];
                $tmp2 = $row[1];
                $jsonData = '{
                    "recipient":{
                        "id":"'."$tmp1".'"
                    },
                    "message":{
                        "text":"'."$tmp2".'"
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
            }

            $sql_update_status = "UPDATE `user_record` SET `updated` = 'Y'";
            mysqli_query($db,$sql_update_status);

        //end of new func
        echo $date;
            $sql1 = "SELECT `id`, `title` FROM `scheduler` WHERE `date` = '$date' AND `updated` = 'N'";
            $result1 = mysqli_query($db,$sql1);
            $num_query =  mysqli_num_rows($result1);
            echo "number 2 = ".$num_query;
            for($i=0;$i<$num_query;$i++){

                $row=mysqli_fetch_row($result1);
                $ch = curl_init($url);
                $tmp1 = $row[0];
                $tmp2 = $row[1]." today";
                $jsonData = '{
                    "recipient":{
                        "id":"'."$tmp1".'"
                    },
                    "message":{
                        "text":"'."$tmp2".'"
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
            }


            $sql_update_status1 = "UPDATE `scheduler` SET `updated` = 'Y' WHERE `date` <= '$date'";
            mysqli_query($db,$sql_update_status1);

            echo $date;
            $sql_update_meta_data = "UPDATE `status_table_metadata` SET `updated_on` = '$date' AND `is_updated`= 'Y'";
            mysqli_query($db,$sql_update_meta_data);

        }
    }
}


?>