<?php


class cyberpanelapicall {
	function verifyConnection($user,$pass){
		$builder = new requestBuilder();
		$builder->sendApiRequest();
	}
}