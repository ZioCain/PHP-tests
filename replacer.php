<?php
$argc = count($argv);
if($argc!=4){
	echo "\n\tFILE REPLACER\n\n\tusage: php ".$argv[0]." FILE <pattern> <replace_with>\n\nThis tool uses preg_replace to apply changes, so check out how it works online: https://www.php.net/manual/en/function.preg-replace.php\nIf not provided, slashes will be added at the beginning and at the end of it\n\n";
	exit();
}
$file = $argv[1];
if (($handle = fopen($file, "r")) === FALSE) {
	echo "Could not open source file.\nStop.\n";
	exit();
}
fclose($handle);
$regex = $argv[2];
if($regex[0]!='/'){
	$regex = '/'.$regex.'/gm';
}
echo preg_replace($regex, $argv[3], file_get_contents($file));
