<?php
$words = preg_replace("#\\\\#", "", $_GET['words']);
$words = urlencode($words);
header("Content-Type:audio/mpeg");
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, 'http://translate.google.com/translate_tts?ie=UTF-8&tl=en&q='.$words); 
curl_setopt($curl, CURLOPT_HEADER,0);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_exec($curl); 
curl_close($curl);
?>