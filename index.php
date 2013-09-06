<?php


$code = $_GET["code"];
$appId = 3863760;
$appSecret = "IxjyxOcG0IOdbYnVMX4v";
$siteUrl = "http://glitch.loc";

if($code) {
	$token = VKauth($appId, $appSecret, $code, $siteUrl);
	$data = getData(request("https://api.vk.com/method/friends.get?order=random&count=5&fields=photo_100&v=5.0&access_token=".$token));
} else {
	readfile("main.html");
}


function request($url) {
	$cr = curl_init();
	curl_setopt($cr, CURLOPT_URL, $url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($cr, CURLOPT_HEADER, 0);
	$response = curl_exec($cr);
	curl_close($cr);
	return json_decode($response);
}

function VKauth($appId, $appSecret, $code, $siteUrl) {
	$u = "https://oauth.vk.com/access_token?client_id=".$appId."&client_secret=".$appSecret."&code=".$code."&redirect_uri=".$siteUrl;
	$response = request($u);
	return $response->{"access_token"};
}

function getData($obj) {
	$ret = array();

	$arr = $obj->{"response"}->{"items"};
	foreach ($arr as $val) {
		$ret[] = array("id" => $val->{"id"}, "photo" => $val->{"photo_100"});
		download($val->{"photo_100"}, $val->{"id"});
	}

	return $ret;
}

function download($url, $file) {
	$dest_file = @fopen(__DIR__."/tmp/".$file, "w");
	$resource = curl_init();
	curl_setopt($resource, CURLOPT_URL, $url);
	curl_setopt($resource, CURLOPT_FILE, $dest_file);
	curl_setopt($resource, CURLOPT_HEADER, 0);
	curl_exec($resource);
	curl_close($resource);
	fclose($dest_file);
}