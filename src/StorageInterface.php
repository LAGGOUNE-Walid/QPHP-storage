<?php 

namespace QPStorage;

use \ArrayObject as ArrayObject;

interface StorageInterface {

	/**
	 * @method put: Put data to the storage table
	 * @param string key
	 * @param array value
	 * @throws QPStorage\Exceptions\NotEnoughSpaceException
	 * @return void
	 */
    public function put(string $key, array $value) : void;

    /**
	 * @method get: Get data from storage table
	 * @param string key
	 * @return Object
	 */
    public function get(string $key) : object;

    /**
	 * @method has: Check if key exists in the table
	 * @param string key
	 * @return boolean
	 */
    public function has(string $key) : bool;

    /**
	 * @method delete: Delete data from table
	 * @param string key
	 * @return void
	 */
    public function delete(string $key) : void;

    /**
     * @method wrapp: Execute caster.
     * @param array data
     * @param string actions : "SET" or "GET"
     * @return array : wrapped data 
     */
    public function wrapp(array $data, string $action) : array;

}