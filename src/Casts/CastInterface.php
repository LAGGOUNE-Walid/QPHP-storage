<?php 

namespace QPStorage\Casts;

interface CastInterface {

	public function set($data);

	public function get($data);

}