<?php
if(count($argv)!=2){
	echo "usage: php ".$argv[0]." <glob pattern>\nGlob pattern:\tmust be something like folder/*\n\n";
	exit();
}
$fls=glob($argv[1]);
$del=0;
$failed=0;
$skip=0;
$total=count($fls);
foreach($fls as $f){
	echo $f.": ";
	if(!is_dir($f) && filesize($f)==0){
		if(unlink($f)){
			echo "DELETED";
			$del++;
		}else{
			echo "FAILED";
			$failed++;
		}
	}else{
		echo "not empty";
		$skip++;
	}
	echo "\n";
}
echo "FILES: $total\nDELETED: $del\nFAILED: $failed\nSKIPPED/not empty: $skip\n\n";
?>
