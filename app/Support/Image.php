<?php

namespace App\Support;

class Image {

	public function loadLocation(string $location): array {
		$exif = new Exif();
		$iptc = new Iptc();

		$files = [];
		foreach (glob("{$location}/*.{gif,jpeg,jpg,png}", GLOB_BRACE) as $file) {
			$exifData = $exif->getData($file, ['FileDateTime', 'ISOSpeedRatings', 'MimeType']);
			$iptcData = $iptc->getData($file, ['2#025', '2#080', '2#085', '2#120']);

			$uid = md5($file);
			$files[$uid] = [
				'exif' => $exifData,
				'filename' => basename($file),
				'group_uid' => md5(dirname($file)),
				'iptc' => $iptcData,
				'iptc_human' => $iptc->convertIptcTagCodes($iptcData),
				'uid' => $uid,
			];
		}

		return $files;
	}

	public function storeLocation(string $location, array $files) {
		dd('Not yet implemented');

		$jpeg = new Jpeg();

		foreach ($files as $metadata) {
			$jpeg->storeMetadata("{$location}/{$metadata['filename']}", $metadata, [
				'iptc' => new Iptc(),
			]);
			break;
		}
	}

}
