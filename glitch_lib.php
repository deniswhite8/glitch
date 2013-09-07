<?php

function glitch($filename) {
	$ex = substr($filename, -4);
	$gif = false;
	if($ex == ".gif") $gif = true;

	$img = null;
	if($gif) $img = imagecreatefromgif($filename);
	else $img = imagecreatefromjpeg($filename);

	$w = imagesx($img);
	$h = imagesy($img);

	$count = rand(3, 4);
	$d = round($h / $count);
	$mas = array();

	$mas[0] = 0;
	for($i = 1; $i < $count - 1; $i++) {
		$mas[$i] = $mas[$i - 1] + $d + rand(-$d/2, $d/2);
	}
	$mas[$count-1] = $h;

	for($i = 0; $i < $count - 1; $i++) {
		rgbOffset($img, $mas[$i], $mas[$i + 1], rand(-$d/2, $d/2), rand(-$d/2, $d/2), rand(-$d/2, $d/2));
	}

	$min_img = imagecreatetruecolor(150, 150);
	imagecopyresampled($min_img, $img, 0,0,0,0, 150, 150, $w, $h);


	if($gif) imagegif($min_img, $filename);
	else imagejpeg($min_img, $filename);

	imagedestroy($img);
	return $min_img;
}

function tile($img_mas, $size, $n, $margin, $filename) {
	$s = $size*$n + $margin*($n+1);
	$res = imagecreatetruecolor($s, $s);

	//imagealphablending($res, true);
	//imagefill($res, 0,0,hexdec("FFFFFF"));

	$x = 0;
	$y = 0;
	foreach ($img_mas as $img) {
		imagecopyresized($res, $img, $margin + $x*($size+$margin), $margin + $y*($size+$margin), 0,0, $s,$s,$s,$s);
		imagedestroy($img);
		$x++;
		if($x >= $n) {
			$x = 0;
			$y++;
		}
	}
	imagejpeg($res, $filename);
	imagedestroy($res);
}


function filter($img, $y0, $y1, $v, $red, $green, $blue) {
	$w = imagesx($img);
	$mask = hexdec(($red?"FF":"00").($green?"FF":"00").($blue?"FF":"00"));
	for($y = $y0; $y <= $y1; $y++) {
		$mas = array();
		for($x = 0; $x < $w; $x++) {
			$i = 0;
			if($v > 0) {
				if($x+$v < $w) $i = $x+$v;
				else $i = $x+$v-$w;
			} else {
				if($x+$v >= 0) $i = $x+$v;
				else $i = $w+$x+$v;
			}

			if($mask) $mas[$i] = imagecolorat($img, $x, $y) & $mask;
			else $mas[$i] = hexdec("FFFFFF") - imagecolorat($img, $x, $y);
		}
		for($x = 0; $x < $w; $x++)
			imagesetpixel($img, $x, $y, $mas[$x]);
	}
}

function offset($v, $x, $w) {
	$i = 0;
	if($v > 0) {
		if($x+$v < $w) $i = $x+$v;
		else $i = $x+$v-$w;
	} else {
		if($x+$v >= 0) $i = $x+$v;
		else $i = $w+$x+$v;
	}
	return $i;
}

function rgbOffset($img, $y0, $y1, $vr, $vg, $vb) {
	$w = imagesx($img);
	for($y = $y0; $y <= $y1; $y++) {
		$masr = array();
		$masg = array();
		$masb = array();
		for($x = 0; $x < $w; $x++) {
			$masr[offset($vr, $x, $w)] = imagecolorat($img, $x, $y) & hexdec("FF0000");
			$masg[offset($vg, $x, $w)] = imagecolorat($img, $x, $y) & hexdec("00FF00");
			$masb[offset($vb, $x, $w)] = imagecolorat($img, $x, $y) & hexdec("0000FF");
		}
		for($x = 0; $x < $w; $x++)
			imagesetpixel($img, $x, $y, $masr[$x] + $masg[$x] + $masb[$x]);
	}
}