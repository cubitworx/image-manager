<?php

namespace App\Http\Controllers;

use App\Support\Exif;
use App\Support\Image;
use App\Support\Iptc;
use Illuminate\Http\Request;

class MetadataController extends Controller {

	protected $_location = 'C:/Users/charl/Downloads/photos';

	public function index(Request $request) {
		return response()->json($this->_loadMetadata());
	}

	public function save(Request $request) {
		$image = new Image();

		foreach ($request->json()->all() as $metadata) {
			$image->saveMetadata("{$this->_location}/{$metadata['filename']}", $metadata, [
				'iptc' => new Iptc(),
			]);
			break;
		}
	}

	protected function _loadMetadata(): array {
		$exif = new Exif();
		$iptc = new Iptc();

		$editor = [
			'keywords' => [],
		];
		$files = [];

		foreach (glob("{$this->_location}/*.{gif,jpeg,jpg,png}", GLOB_BRACE) as $file) {
			$exifData = $exif->getData($file, ['FileDateTime', 'ISOSpeedRatings', 'MimeType']);
			$iptcData = $iptc->getData($file, ['2#025', '2#080', '2#085', '2#120']);
			$editor = [
				'keywords' => array_merge($editor['keywords'], $iptcData['2#025'] ?? []),
			];

			$files[] = [
				'exif' => $exifData,
				'filename' => basename($file),
				'group_uid' => md5(dirname($file)),
				'iptc' => $iptcData,
				'iptc_human' => $iptc->convertIptcTagCodes($iptcData),
				'uid' => md5($file),
			];
		}

		$editor = [
			'keywords' => array_unique($editor['keywords']),
		];

		return [
			'editor' => $editor,
			'files' => $files,
		];
	}

}
