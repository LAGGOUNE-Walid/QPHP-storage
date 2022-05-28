## QPHP-storage
A simple storage library uses swoole table. <br/>
This library is built to work with queue php swoole server. but it is available as component to use it whenever you want.

## Requirements
- PHP > 8.0.2
- swoole > 4.8
## Install
```
composer require qphpstorage/qphpstorage
```
## Example
```php
<?php
require "vendor/autoload.php";

use QPStorage\Base\Storage;
use QPStorage\Casts\JsonCast;

class UserStorage extends Storage {

	// Required
	public string $name 		= 	"users";
	// Required
	public int $size 			= 	1024;
	// Required
	public array $definitions 	= [
		"id" 		=> 		[Swoole\Table::TYPE_INT],
		"name" 		=> 		[Swoole\Table::TYPE_STRING, 64],
		"options" 		=> 		[Swoole\Table::TYPE_STRING, 64]
	];
	// Optional , if you do not need it , remove the property
	public array $casts 	= [
		// You are free to use your own casters , but you should implement QPStorage\Casts\CastInterface
		"options" => JsonCast::class
	];

}
```

Get the results

```php
<?php
$users = new UserStorage;
$users->put("foo", ["id" => 1, "name" => "foo", "options" => ["age" => 22]]);
$users->put("bar", ["id" => 2, "name" => "bar", "options" => ["age" => 24]]);

echo $users->get("foo")->options["age"];
// Loop over users

$users->rewind();
while($users->valid()) {
	$user = $users->current();
	echo $user->name."\n";
	$users->next();
}

```

## Custom casts
```php
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
```
