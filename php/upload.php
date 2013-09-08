<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$upload_url = $_POST["upload_url"];
$image = $_POST["image"];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $upload_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, array("photo"=>"@".__DIR__.$image));
$out = curl_exec($curl);

echo $out;
curl_close($curl);
