<?php


$db=mysqli_connect("localhost","root","","chatbot") or die("Can not connect right now!");

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

$sender = $input['entry'][0]['messaging'][0]['sender']['id'];

$message = $input['entry'][0]['messaging'][0]['message']['text'];


checkupdatestatus($url);


if($sender != 217358738681243 && isset($message)){

    $return_num_replies = checkfirsttime($sender);

	//echo $return_num_replies."\n";
    if($return_num_replies <=3){

        switch ($return_num_replies) {
            case '1':
            firsttime($sender,$url);
            $flag = 1;

            break;
            case '2':
            secondtime($sender,$url,$message);
            $flag = 1;

            break;
            case '3':
            thirdtime($sender,$url,$message);
            $flag = 1;

            break;
        }
    }

    else if (isMainTag($message)){
	echo "inside tags\n";
        $maintag = getMainTags($message);

        switch ($maintag) {
          case 'schedule':
                add_schedule_db($sender, $message,$url);
                $flag = 1;
               break;
        case 'places':
                getPlaces($message,$url,$sender);
                $flag = 1;
               break;
        case 'news':
                getNews($message,$url,$sender);
                $flag = 1;
               break;
        case 'weather':
                getWeather($message,$url,$sender);
                $flag = 1;
               break;
          break;
        }

    }
    else{

echo "inside API Call\n";
        
        $message_to_reply = api_ai_call($message,$sender);

        save_message_and_response_api($sender,$message,$message_to_reply);

        send_simple_message_messenger($sender, $message_to_reply,$url);
    }
}
function firsttime($sender,$url){

    $db = $GLOBALS['db'];
    $num = 1;
    $sql = "INSERT INTO `clients`(`id`,`replies`) VALUES ('$sender', '$num')";
    mysqli_query($db,$sql);

    $messages= "Hello User. We welcome you to Chat_bot. Please help us know you better. Please enter Following Details \\n1. Name \\n2. Interests";

    send_simple_message_messenger($sender, $messages ,$url);
}

function secondtime($sender,$url,$message){
    $db = $GLOBALS['db'];
    $num = 2;
    $sql = "UPDATE `clients` SET `replies`='$num' WHERE `id` = '$sender'";
    mysqli_query($db,$sql);
    $sql1 = "INSERT INTO `user_record`(`id`,`name`) VALUES ('$sender', '$message')";
    mysqli_query($db,$sql1);

    $send_message = "Please Give your comma separated Interests";

    send_simple_message_messenger($sender,$send_message,$url);
}

function thirdtime($sender,$url,$message){
    $db = $GLOBALS['db'];
    $num = 3;
    $sql = "UPDATE `clients` SET `replies`='$num' WHERE `id` = '$sender'";
    mysqli_query($db,$sql);
    $sql1 = "UPDATE `user_record` SET `interests`= '$message' WHERE `id` = '$sender'";
    mysqli_query($db,$sql1);

    $send_message = "Thank You for ur Response \\n I can help u with many things like \\n1.News \\n2.Scheduling \\n 3.OCR \\n4. Image/gif search";

    send_simple_message_messenger($sender,$send_message,$url);

}

function checkfirsttime($sender){
    $db = $GLOBALS['db'];

    $sql = "SELECT `id`, `replies` FROM `clients` WHERE `id`= '$sender' ";
    $result = mysqli_query($db,$sql);
    $num_query = mysqli_num_rows($result);
    if($num_query == 0) return 1;
    else {
        $row = mysqli_fetch_row($result);
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
        $date_curr = date("Y-m-d");
        if($date > $date_curr){
            $sql = "INSERT INTO `scheduler`(`id`,`title`,`date`) VALUES ('$sender','$title','$date') ";
            $result = mysqli_query($db,$sql);
            echo "current date = ".$date_curr;
            //$sql_update_meta_data1 = "UPDATE `status_table_metadata` SET `updated_on` = '".date("Y-m-d")."' AND `is_updated`= 'N' WHERE `serial`= '1'";
            //echo mysqli_query($db,$sql_update_meta_data1);

        }
        else {
                $error_message = "The date is not in correct. Please enter valid future date in correct format";
                send_simple_message_messenger($sender,$error_message,$url);
    }

}
}

function checkupdatestatus($url){
    $db = $GLOBALS['db'];
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

            for($i=0;$i<$num_query;$i++){

                $row=mysqli_fetch_row($result);
                $tmp1 = $row[0];
                $tmp2 = $row[1];
                send_simple_message_messenger($tmp1,$tmp2,$url);

            }

            $sql_update_status = "UPDATE `user_record` SET `updated` = 'Y'";
            mysqli_query($db,$sql_update_status);

            $sql1 = "SELECT `id`, `title` FROM `scheduler` WHERE `date` = '$date' AND `updated` = 'N'";
            $result1 = mysqli_query($db,$sql1);
            $num_query =  mysqli_num_rows($result1);
            echo "number 2 = ".$num_query;
            for($i=0;$i<$num_query;$i++){

                $row=mysqli_fetch_row($result1);
                 $tmp1 = $row[0];
                $tmp2 = $row[1]." today";
                send_simple_message_messenger($tmp1,$tmp2,$url);
            }

            $sql_update_status1 = "UPDATE `scheduler` SET `updated` = 'Y' WHERE `date` <= '$date'";
            mysqli_query($db,$sql_update_status1);

            $date = date("Y-m-d");
            $sql_update_meta_data = "UPDATE `status_table_metadata` SET `updated_on` = '$date' AND `is_updated`= 'Y' WHERE `serial` = '1'";
            mysqli_query($db,$sql_update_meta_data);
        }
    }
}

function getHashTags($string){
    $pattern = "/\B#[^\B]+/";
    preg_match($pattern, $string, $matches);
    $returntag= substr($matches[0], 1);
    return $returntag;
}

function getMainTags($string){
    $pattern = "/((?<!\S)@\w+(?!\S))/";
    preg_match($pattern, $string, $matches);
    $returntag= substr($matches[0], 1);
    return $returntag;
}

function isMaintag($string){
 $pattern = "/((?<!\S)@\w+(?!\S))/";
    preg_match($pattern, $string, $matches);
if(isset($matches[0])){
return true;
}
else {
return false;
}
}
function getNews($message,$url,$sender)
    {
        //news api - a3f365280e404a49b0595f6c1d8cec05
        $hashtag = getHashTags($message);
        $chnews = curl_init();
        curl_setopt_array($chnews, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://newsapi.org/v1/articles?source=".$hashtag."&apiKey=a3f365280e404a49b0595f6c1d8cec05"
        ));
        $resp = curl_exec($chnews);
        curl_close($chnews);
        $resp_news = json_decode($resp, true);
        $news_length= sizeof($resp_news['articles']);
        $array_of_news = [];
        for($i = 0; $i < $news_length; $i++){
            $news_title[$i] = $resp_news['articles'][$i]['title'];
            $news_url[$i] = $resp_news['articles'][$i]['url'];
            $news_image_url[$i] = $resp_news['articles'][$i]['urlToImage'];
            $news_desc[$i] = $resp_news['articles'][$i]['description'];     
        }
        
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
        //if(!empty($input['entry'][0]['messaging'][0]['message']['text'])){
            curl_exec($ch);
            curl_close($ch);
        //}
    
    }

function getWeather($message,$url,$sender){
        //apikey = a3e33f871698f4ec
    $hashtag = getHashTags($message);
        $chweather = curl_init();
        curl_setopt_array($chweather, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "http://api.wunderground.com/api/a3e33f871698f4ec/geolookup/conditions/forecast/q/".$hashtag.".json"
        ));
        $resp = curl_exec($chweather);
        curl_close($chweather);
        $resp_weather = json_decode($resp, true);
        //get humidity, temperature, wind, weather, icon_url
        $humidity = "Humidity is ".$resp_weather['current_observation']['relative_humidity'].". ";
        $temperature = "Temperature is ".$resp_weather['current_observation']['temperature_string'].". ";
        $wind = "Wind is ".$resp_weather['current_observation']['wind_string'].". ";
        $weather = "Weather at ".$hashtag." is ".$resp_weather['current_observation']['weather'];
        $weather_img_url = $resp_weather['current_observation']['icon_url'];
        $weather_desc = $temperature.$wind.$humidity;

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
                                "title" : "'."$weather".'",
                                "image_url" : "'."$weather_img_url".'",
                                "subtitle" : "'."$weather_desc".'",
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
        //if(!empty($input['entry'][0]['messaging'][0]['message']['text'])){
            curl_exec($ch);
            curl_close($ch);
        //}
    }

function getPlaces($message,$url,$sender){

         $hashtag = getHashTags($message);
        $placesquery = urlencode($hashtag);
        $chplaces = curl_init();
        curl_setopt_array($chplaces, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".$placesquery."&key=AIzaSyBtsnxKAwolqIfQF8lXw6s_MnWtGbH4DtI"
            ));
        $resp = curl_exec($chplaces);
        curl_close($chplaces);
        $resp_places = json_decode($resp, true); 
        $places_length= sizeof($resp_places['results']);
        for($i = 0; $i < 5; $i++){
            $place_name[$i] = $resp_places['results'][$i]['name'];
            $place_address[$i] = $resp_places['results'][$i]['formatted_address'];
            $place_rating[$i] = $resp_places['results'][$i]['rating'];
            $place_icon[$i] = $resp_places['results'][$i]['icon'];
        }

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
                            "title" : "'."$place_name[0]".'",
                            "image_url" : "'."$place_icon[0]".'",
                            "subtitle" : "'."$place_address[0]".'",
                            "buttons":[
                            {
                                "type":"element_share"
                            }              
                            ]
                        },
                        {
                            "title" : "'."$place_name[1]".'",
                            "image_url" : "'."$place_icon[1]".'",
                            "subtitle" : "'."$place_address[1]".'",
                            "buttons":[
                            {
                                "type":"element_share"
                            }              
                            ]
                        },
                        {
                            "title" : "'."$place_name[2]".'",
                            "image_url" : "'."$place_icon[2]".'",
                            "subtitle" : "'."$place_address[2]".'",
                            "buttons":[
                            {
                                "type":"element_share"
                            }              
                            ]
                        },
                        {
                            "title" : "'."$place_name[3]".'",
                            "image_url" : "'."$place_icon[3]".'",
                            "subtitle" : "'."$place_address[3]".'",
                            "buttons":[
                            {
                                "type":"element_share"
                            }              
                            ]
                        },
                        {
                            "title" : "'."$place_name[4]".'",
                            "image_url" : "'."$place_icon[4]".'",
                            "subtitle" : "'."$place_address[4]".'",
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
        //if(!empty($input['entry'][0]['messaging'][0]['message']['text'])){
        curl_exec($ch);
        curl_close($ch);
        //}
    }

function searchNews($message,$url,$sender){
    //api key   2e7172da1af241579fd822ee581a1535

    $placesquery = urlencode($hashtag);
        $chplaces = curl_init();
        curl_setopt_array($chplaces, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".$placesquery."&key=AIzaSyBtsnxKAwolqIfQF8lXw6s_MnWtGbH4DtI"
            ));
        $resp = curl_exec($chplaces);
        curl_close($chplaces);
        $resp_places = json_decode($resp, true); 
        //Just make the necessary calls with interest array and custom search query 
    //api key   2e7172da1af241579fd822ee581a1535
        //request : https://api.cognitive.microsoft.com/bing/v5.0/news/search?cricket&5&en-IN
        //Refernce : https://dev.cognitive.microsoft.com/docs/services/56b43f72cf5ff8098cef380a/operations/56b449fbcf5ff81038d15cdf
        //
}

function send_simple_message_messenger($sender, $message, $url){
//Initiate cURL.
        $ch = curl_init($url);
//The JSON data.
        $jsonData = '{
            "recipient":{
                "id":"'."$sender".'"
            },
            "message":{
                "text":"'."$message".'"
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

function api_ai_call($message,$sender){
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

        return $message_to_reply;
}

?>
