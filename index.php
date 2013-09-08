<?php

include "glitch_lib.php";

$code = $_GET["code"];
$appId = 3863760;
$appSecret = "IxjyxOcG0IOdbYnVMX4v";
$siteUrl = "http://glitch.loc";

if($code) {
	$token = VKauth($appId, $appSecret, $code, $siteUrl);
	$uid = request("https://api.vk.com/method/users.get?v=5.0&access_token=".$token)->{"response"}[0]->{"id"};
	$data = getData($uid, request("https://api.vk.com/method/friends.get?order=random&count=4&fields=photo_max&v=5.0&access_token=".$token));
	$wall = "";
	$arr = array();

	foreach ($data as $i) {
		$arr[] = glitch(__DIR__."/tmp/".$i["name"]);
		$wall .= "<a href=\"http://vk.com/id".$i["id"]."\"><img src=\"/tmp/".$i["name"]."\" class=\"wall-img\"></a>";
	}
	tile($arr, 150, 2, 5, __DIR__."/tmp/".$uid."-wall.jpg");
	include "wall.html";

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

function getData($uid, $obj) {
	$ret = array();

	$arr = $obj->{"response"}->{"items"};
	foreach ($arr as $val) {
		$name = $uid."-".$val->{"id"}.substr($val->{"photo_max"}, -4);
		$ret[] = array("id" => $val->{"id"}, "name" => $name);
		download($val->{"photo_max"}, $name);
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