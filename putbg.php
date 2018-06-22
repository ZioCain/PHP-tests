<?php
$fs=glob("*.png");
foreach($fs as $f){
	$dimensions = getimagesize($f);
	$x = $dimensions[0];
	$y = $dimensions[1];
	$im = imagecreatetruecolor($x,$y);
	$src_ = imagecreatefrompng($f);
	// Prepare alpha channel for transparent background
	$alpha_channel = imagecolorallocatealpha($im, 0, 0, 0, 255);
	imagecolortransparent($im, $alpha_channel);
	// Fill image
	imagefill($im, 0, 0, $alpha_channel);
	// Copy from other
	imagecopy($im,$src_, 0, 0, 0, 0, $x, $y);
	// Save transparency
	imagesavealpha($im,true);
	// Save PNG
	imagepng($im,$f,9);
	imagedestroy($im);
}
?>