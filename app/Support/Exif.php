<?php

namespace App\Support;

use App\Contracts\ImageMetadata;

class Exif implements ImageMetadata {

	public function embed(string $file, array $metadata): string {
		throw new Exception('Not yet implemented');
	}

	public function getData(string $file, array $tags = null): array {
		$data = [];
		try {
			$data = exif_read_data($file) ?? [];
		} catch (Exception $e) {}

		return $tags ? array_intersect_key($data, array_flip($tags)) : $data;
	}

}
