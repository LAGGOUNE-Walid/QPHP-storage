<?php 

use PHPUnit\Framework\TestCase;
use QPStorage\Base\Storage;
use QPStorage\Casts\JsonCast;
use QPStorage\Exceptions\NotEnoughSpaceException;

class TableTest extends TestCase {


	public function testSetDataToTheTable() {
		$storage = $this->createStorage("users", 1024, [
			"id" 		=> 		[Swoole\Table::TYPE_INT],
			"name" 		=> 		[Swoole\Table::TYPE_STRING, 64],
			"options" 		=> 		[Swoole\Table::TYPE_STRING, 64]
		]);
		$storage->put("foo", ["id" => 1, "name" => "foo", "options" => ["age" => 22]]);
		$this->assertEquals($storage->get("foo")->id, 1);
	}

	public function testNotNotEnoughSpaceExceptionIsThrown() {
		$storage = $this->createStorage("tiny_users", 10, [
			"id" 		=> 		[Swoole\Table::TYPE_INT],
			"name" 		=> 		[Swoole\Table::TYPE_STRING, 64],
			"options" 		=> 		[Swoole\Table::TYPE_STRING, 64]
		]);
		$this->expectException(NotEnoughSpaceException::class);
		for ($i=0; $i <= 100 ; $i++) { 
			$storage->put("foo-$i", ["id" => 1, "name" => "foo", "options" => ["age" => 22]]);
		}

	}

	public function testThatCasterIsCalled() {
		$jsonCastMock = $this->createMock(JsonCast::class);
		$jsonCastMock->expects($this->once())->method("set");
		$jsonCastMock->expects($this->once())->method("get");
		$storage = $this->createStorage(
			"usersWithCast", 
			1024,
			[
				"id" 		=> 		[Swoole\Table::TYPE_INT],
				"name" 		=> 		[Swoole\Table::TYPE_STRING, 64],
				"options" 		=> 		[Swoole\Table::TYPE_STRING, 64]
			], 
			[
				"options" => $jsonCastMock
			]
		);
		$storage->put("foo", ["id" => 1, "name" => "foo", "options" => ["age" => 22]]);
		$storage->get("foo");
	}

	public function createStorage(string $name, int $size, array $definitions, array $casts = []) {
		$storage = $this->getMockBuilder(Storage::class)->onlyMethods(["__construct"])->disableOriginalConstructor()->getMock();
		$storage->name = $name;
		$storage->size 			= 	$size;
		$storage->definitions 	= $definitions;
		if ($casts !== []) {
			$storage->casts = $casts;
		}
		$storage->createTable();
		return $storage;
	}

}