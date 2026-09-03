<?php
$c = file_get_contents('compiled.php');
$tokens = token_get_all($c);
$if = 0; $endif = 0;
foreach($tokens as $t) {
  if (is_array($t)) {
    if ($t[0] == T_IF) $if++;
    if ($t[0] == T_ENDIF) $endif++;
  }
}
echo 'if: ' . $if . PHP_EOL;
echo 'endif: ' . $endif . PHP_EOL;
