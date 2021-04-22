<?php
$pwd = getcwd();
for($k=0; $k<10; ++$k){
	echo "Changing dir to bots/$k";
	chdir("bots/$k");
	echo "Starting child process";
	exec("./run-urls.php &");
	echo "Restoring old WD: $pwd";
	chdir($pwd);
}
