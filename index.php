<?php
	echo "Heheh";
	$config_str = file_get_contents("app/config.json");

	echo "<br>$config_str<br>";

	$config = json_decode($config_str,true);

	var_dump($config);
?>