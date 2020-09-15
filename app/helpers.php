<?php
function pr($arr, $is_die = null) {

	echo '<pre>';
	print_r($arr);
	echo '</pre>';

	if($is_die) {
		die();
	}
}

function get_token() {

	try{

		$filename = 'token.txt';

		$file = file_get_contents($filename);
		$obj = unserialize($file);
		//$data = Storage::disk('local')->get($filename);
		//$obj = unserialize($data);

		return $obj;


	}
	catch(\Exception $e){
	   // do task when error
	   echo $e->getMessage();   // insert query
	   return false;
	}
}

?>
