<?php

require_once('TwitterAPIExchange.php');

$consumerKey = 'b1sPC3txH9d3XLP8XKicveCjk';
$consumerKeySecret = 'eysV4xqcrF6QzaBt2T99ntfWDDJom0bMtHxMKC31SQsdWb2QdF';
$accessToken = '140559619-fkhpAAKBc1MrQ82cWKFEvTvoBL3aHWMv1Hia4Kfg';
$accessTokenSecret = 'D5NfMbSKONfCabQIPzLMaEBRT0VM88gdr2TZXQlovgGcT';
$settings = array(
    'oauth_access_token' => $accessToken,
    'oauth_access_token_secret' => $accessTokenSecret,
    'consumer_key' => $consumerKey,
    'consumer_secret' => $consumerKeySecret
    );
$name = "epointsUK";
$names = "bigdealslocal";
$i = 0;
$j = 0;
$cursor = -1;
$cursors = -1;

do {
$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?cursor=' . $cursor . '&screen_name=' . $name . '';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
$response = json_decode($response, true);

if(isset($respose["errors"]))
{
$errors = $response["errors"];
}

if (!empty($errors)) {

foreach($errors as $error){
$code = $error['code'];
$msg = $error['message'];
echo "<br><br>Error " . $code . ": " . $msg;
}

$cursor = 0;
}

else {
$users = $response['ids'];

foreach($users as $user){
$i++;
}

$cursor = $response["next_cursor"];
}

}
while ( $cursor != 0 );

do {
$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?cursor=' . $cursors . '&screen_name=' . $names . '';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
$response = json_decode($response, true);

if(isset($respose["errors"]))
{
$errors = $response["errors"];
}

if (!empty($errors)) {

foreach($errors as $error){
$code = $error['code'];
$msg = $error['message'];
echo "<br><br>Error " . $code . ": " . $msg;
}

$cursor = 0;
}

else {
$users = $response['ids'];

foreach($users as $user){
$j++;
}

$cursor = $response["next_cursor"];
}

}
while ( $cursor != 0 );

if (!empty($users)) {
echo '<br><br>ePointsUK Twitter Followers: ' . $i;
echo '<br><br>BigDL Twitter Followers: ' . $j;
}
?>
