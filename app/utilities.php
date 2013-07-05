<?php

function simple_format($str) {
	$str = "<p>".preg_replace("/\n+/s", "</p><p>", $str)."</p>";
	return $str;
}

?>