<?php 

namespace QPStorage\Base;

use QPStorage\Exceptions\NotEnoughSpaceException;
use QPStorage\StorageInterface;
use \ArrayObject as ArrayObject;
use \Swoole\Table as Table;
use \Iterator as Iterator;

class Storage extends StorageIterator implements StorageInterface {

	protected Table|null $table = null;

	public function __construct() {
		if ($this->table === null) {
			$this->createTable();
		}
	}

	public function createTable() {
		$this->table = new Table($this->size);
		foreach($this->definitions as $column => $options) {
			$this->table->column($column, ...$options);
		}
		$this->table->create(); 
	}

	public function put(string $key, array $data) : void {
		$data = $this->wrapp($data, "SET");

		if((@$this->table->set($key, $data)) === false) {
			throw new NotEnoughSpaceException("Cannot allocate more memory, please add more size to the table");
		}			
	}

	public function get(string $key) : object {
		$data = $this->wrapp($this->table->get($key), "GET");
		return (object)(($data === false) ? [] : $this->createModel($data));
	}

	public function has(string $key) : bool {
		return $this->table->exist($key);
	}

	public function delete(string $key) : void {
		$this->table->del($key);
	}

	public function wrapp(array $data, string $action) : array {
		if (!property_exists($this, "casts")) {
			return $data;
		}
		foreach($this->casts as $column => $caster) {
			if (array_key_exists($column, $data)) {
				if (array_key_exists($column, $this->casts)) {
					if ($action === "SET") {
						if (is_object($caster)) {
							$data[$column] = $caster->set($data[$column]);	
						}else {						
							$data[$column] = (new $caster)->set($data[$column]);
						}
					}else {
						if (is_object($caster)) {
							$data[$column] = $caster->get($data[$column]);	
						}else {						
							$data[$column] = (new $caster)->get($data[$column]);
						}
					}
				}
			}
		}
		return $data;
	}

	public function createModel(array $data) : object {
		foreach($this->definitions as $columnName => $options) {
			$this->{$columnName} = $data[$columnName];
		}
		return $this;
	}

	public function count() : int {
		return $this->table->count();
	}

}