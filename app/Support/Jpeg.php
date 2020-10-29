<?php

namespace App\Support;

use Intervention\Image\Image;

class Jpeg {

	public function generateThumbnail(string $source, string $target, array $options = []) {
		$exif = new Exif();
		$options = $options + [
			'aspect' => true,
			'height' => 600,
			'quality' => 85,
			'width' => 600,
		];
		$exifData = $exif->getData($source, ['Orientation']);

		$targetHeight = $options['height'];
		$targetWidth = $options['width'];

		$sourceImage = imagecreatefromjpeg($source);
		$sourceWidth = imagesx($sourceImage);
		$sourceHeight = imagesy($sourceImage);

		if ($options['aspect']) {
			if ($sourceWidth > $sourceHeight)
				$targetHeight = (int)floor($sourceHeight * ($targetWidth / $sourceWidth));
			else
				$targetWidth = (int)floor($sourceWidth * ($targetHeight / $sourceHeight));
		}

		$image = imagecreatetruecolor($targetWidth, $targetHeight);

		// Resize
		imagecopyresampled($image, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

		if (isset($exifData['Orientation'])) {
			// Rotate as needed
			static $rotations = [3 => 180, 4 => 180, 5 => 270, 6 => 270, 7 => 90, 8 => 90];
			if (isset($rotations[$exifData['Orientation']]))
				$image = imagerotate($image, $rotations[$exifData['Orientation']], 0);

			// Flip as needed
			static $flips = [2 => IMG_FLIP_HORIZONTAL, 4 => IMG_FLIP_HORIZONTAL, 5 => IMG_FLIP_HORIZONTAL, 7 => IMG_FLIP_HORIZONTAL];
			if (isset($flips[$exifData['Orientation']]))
				imageflip($image, $flips[$exifData['Orientation']]);
		}

		imagejpeg($image, $target, $options['quality']);
	}

	public function storeMetadata(string $file, array $metadata, array $drivers = []) {
		// TODO Not sure how this is going to work with EXIF data since IPTC embed function returns whole file
		$content = '';
		foreach ($drivers as $tag => $driver)
			$content = $driver->embed($file, $metadata[$tag]);

		if (file_put_contents(str_replace('.jpg', '-new.jpg', $file), $content) === false)
			throw new Exception('Error saving image');
	}

}
