<?php 

namespace QPStorage\Base;

use \Iterator as Iterator;

class StorageIterator implements Iterator {

	public function rewind() {
		$this->table->rewind();
	}

	public function valid() {
		return $this->table->valid();
	}

	public function current() {
		$data = $this->wrapp($this->table->current(), "GET");
		return (object)(($data === false) ? [] : $this->createModel($data));
	}

	public function key() {
		return $this->table->key();
	}

	public function next() {
		$this->table->next();
	}

}