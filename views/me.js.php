var ejangi = {};
    ejangi.designs = <?php if(isset($designs) && is_array($designs) && count($designs) > 0) { 
							echo "['{$base}/designs/".implode("', '{$base}/designs/", $designs)."'];\n";
						 } else { echo "null;"; } ?>
    ejangi.me = <?php if(isset($me)) { echo $me; } else { echo '{}'; } ?>;