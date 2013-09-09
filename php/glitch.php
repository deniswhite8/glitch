<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

	include "glitch_lib.php";

	$data = $_POST["data"];
	$tmp = "/../tmp_img/";
	$img_tmp = __DIR__.$tmp;
	$img_arr = array();
	$new_size = 150;
	$ret = array();
	$ret["mas"] = array();

	foreach($data["mas"] as $i) {
		$ex = substr($i["photo"], -4);
		$name = $data["id"]."_".$i["id"].$ex;

		download($i["photo"], $img_tmp.$name);
		$img_arr[] = glitch($img_tmp.$name, $new_size);
		$ret["mas"][] = array("photo"=>$tmp.$name, "id"=>$i["id"]);
	}
	tile($img_arr, $new_size, ceil(sqrt(count($data["mas"]))), 5, $img_tmp.$data["id"]."_wall.jpg");
	$ret["wall"] = $tmp.$data["id"]."_wall.jpg";

	function download($url, $file) {
		$dest_file = @fopen($file, "w");
		$resource = curl_init();
		curl_setopt($resource, CURLOPT_URL, $url);
		curl_setopt($resource, CURLOPT_FILE, $dest_file);
		curl_setopt($resource, CURLOPT_HEADER, 0);
		curl_exec($resource);
		curl_close($resource);
		fclose($dest_file);
	}

	echo json_encode($ret);
