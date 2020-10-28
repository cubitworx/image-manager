<?php

namespace App\Support;

class MetaStore {

	public function loadLocation(string $location) {
		if (!file_exists("$location/.imagedata"))
			return [];

		$files = [];
		foreach (json_decode(file_get_contents("$location/.imagedata"), true) as $file)
			$files[$file['uid']] = $file;

		return $files;
	}

	public function storeLocation(string $location, array $files) {
		if (file_put_contents("$location/.imagedata", json_encode($files, JSON_PRETTY_PRINT)) === false)
			throw new Exception('Error saving image metadata');
	}

}
