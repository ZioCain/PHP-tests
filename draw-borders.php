<?php
$argc = count($argv);
if($argc!==3){
	echo "Usage: ".$argv[0]." input.jpg output.jpg\n\n";
	exit();
}
function imageCreateFromAny($filepath, &$outType) {
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) return false;
	$outType = $type;
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
        break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
        break;
        case 3 :
            $im = imageCreateFromPng($filepath);
        break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
        break;
    }
    return $im;
}
function getColorAt($image,$x,$y){
	return imagecolorsforindex($image, imagecolorat($image, $x, $y));
}
function diffColors($col1, $col2){
	if($col1===null || $col2===null) return 0;
	return ($col1["red"]*65536+$col1["green"]*256+$col1["blue"]) - ($col2["red"]*65536+$col2["green"]*256+$col2["blue"]);
}
define("THRESHOLD", 1048576);
$type = null;
$input = imageCreateFromAny($argv[1], $type);
define("BORDER_COLOR", imagecolorallocate($input, 0, 128, 255));
define("KEEP_INPUT", false);
$width = imagesx($input);
$height = imagesy($input);
$output = imagecreatetruecolor($width,$height);
if(!$output) die("Cannot create output image.");
$x = 0;
$y = 0;
echo "THRESHOLD: ".THRESHOLD."\nSIZE: $width x $height\nKEEP INPUT: ".(KEEP_INPUT?"yes":"no")."\n\nProcessing...\n";
for($y = 0; $y<$height; ++$y){
	for($x = 0; $x<$width; ++$x){
		echo "\r$x , $y";
		$col = getColorAt($input, $x, $y);
		$col_left = null; if($x>0) $col_left = getColorAt($input, $x-1, $y);
		$col_right = null; if($x<$width-1) $col_right = getColorAt($input, $x+1, $y);
		$col_up = null; if($y>0) $col_up = getColorAt($input, $x, $y-1);
		$col_down = null; if($y<$height-1) $col_down = getColorAt($input, $x, $y+1);
		$inserted = false;
		if( diffColors($col, $col_left)>THRESHOLD ){$inserted=true;imagesetpixel($output, $x-1, $y, BORDER_COLOR);}
		if( diffColors($col, $col_right)>THRESHOLD ){$inserted=true;imagesetpixel($output, $x+1, $y, BORDER_COLOR);}
		if( diffColors($col, $col_up)>THRESHOLD ){$inserted=true;imagesetpixel($output, $x, $y-1, BORDER_COLOR);}
		if( diffColors($col, $col_down)>THRESHOLD ){$inserted=true;imagesetpixel($output, $x, $y+1, BORDER_COLOR);}
		if(KEEP_INPUT && !$inserted) imagesetpixel($output,$x,$y,imagecolorallocate($input, $col["red"], $col["green"], $col["blue"]));
	}
}
switch($type){
	case 1 :
		imagegif($output, $argv[2]);
	break;
	case 2 :
		imagejpeg($output, $argv[2]);
	break;
	case 3 :
		imagepng($output, $argv[2]);
	break;
}
echo "\nProcess completed\n\n";
