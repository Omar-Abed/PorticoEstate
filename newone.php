<?php


$txt = '<!DOCTYPE html>
<html>
<body>

<h2>Local Links</h2>

<p><a href="html_images.asp">HTML Images</a> is a link to a page on this website.</p> if you have asømsc

<p><a href="https://www.w3.org/">https://www.w3.org/</a> is a link to a website on the World Wide Web.</p>

</body>
</html><php>  something ';

$text_message  = array('text' => $txt );
function sms ($newArray,$fields)
    {

$newArray = array_map(function($v){
    return trim(strip_tags($v));
}, $text_message);

$fields = array(
'sender' => '56375000',
'receiver' => '4740584817',
'message' => $text_message['text'],
'username' => 'ak4631',
'password' => 'rGo#!=FrA2487522344GgWwE'

);

echo $text_message['text'];
$url = 'http://smsalert.no/gui/ws/integration.asmx/SendSMS';
$fields_string = http_build_query($fields);
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

$result = curl_exec($ch);
curl_close($ch);
}
?>
