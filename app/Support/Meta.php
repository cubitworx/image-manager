<?php

namespace App\Support;

class Meta {

	public function loadLocation(string $location): array {
		$exif = new Exif();
		$iptc = new Iptc();

		$files = array_merge(
			(new Image())->loadLocation($location),
			(new MetaStore())->loadLocation($location)
		);

		$keywords = array_map(function ($item) {
			return $item['iptc']['2#025'] ?? [];
		}, $files);

		return [
			'editor' => [
				// flatmap keywords
				'keywords' => array_unique(array_merge(...array_values($keywords))),
			],
			'files' => array_values($files),
		];
	}

	public function storeLocation(string $location, array $files) {
		(new MetaStore())->storeLocation($location, $files);
	}

}
