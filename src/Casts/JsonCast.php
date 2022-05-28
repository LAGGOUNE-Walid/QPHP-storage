<?php 

namespace QPStorage\Casts;

class JsonCast implements CastInterface {

	public function set($data) {
		$data = json_encode($data);
		return (JSON_ERROR_NONE !== json_last_error()) ? "" : $data;
	}

	public function get($data) {
		 $data = json_decode($data, true);
		return (JSON_ERROR_NONE !== json_last_error()) ? [] : $data;
	}

}