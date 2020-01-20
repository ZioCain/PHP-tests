<?php
$array=[0,1,2,3,4,5,6];
print_r($array);
$len = count($array);
$last = $array[$len-1];
for($k=$len-1; $k>0; --$k){
	$array[$k] = $array[$k-1];
}
$array[0] = $last;
print_r($array);
