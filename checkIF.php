<?php
$str = "asdf";
echo str_replace("--","-",preg_replace("[^a-zA-Z0-9]", "-", $str));
