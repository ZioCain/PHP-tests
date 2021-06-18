<?php
  function microtime_float()
  {
      list($usec, $sec) = explode(" ", microtime());
      return ((float)$usec + (float)$sec);
  }

  define('ITERATIONS', 2000000);
  header('Content-Type: text/plain');

  printf("Starting benchmark, over %d iterations:\r\n\r\n", ITERATIONS);

  print("Imploding...");

  $list = Array();
  for ($_ = 0; $_ < ITERATIONS; $_++)
  	$list[] = 'a';

  $start = microtime_float();
  
  $result = implode(',',$list);

  $end = microtime_float() - $start;
  printf("%0.3f seconds\r\n", $end);

  print("Concatenating...");
  $start = microtime_float();

  $result = '';
  for ($_ = 0; $_ < ITERATIONS; $_++)
    $result .= ','.$list[$_];

  $end = microtime_float() - $start;
  printf("%0.3f seconds\r\n", $end);
?>