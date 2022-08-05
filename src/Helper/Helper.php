<?php
function dd($debug){
	echo "<pre>";
	print_r($debug);
	echo "</pre>";
	die();
}

function pr($debug){
	echo "<pre>";
	print_r($debug);
	echo "</pre>";
}

function object2array($object)
{
    $array = json_decode(json_encode($object), true);
    return $array;
}