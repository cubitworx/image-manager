<?php

namespace App\Support;

class Jpeg {

	public function generateThumbnail(string $source, string $target, array $options = []) {
		$options = $options + [
			'aspect' => true,
			'height' => 400,
			'quality' => 85,
			'width' => 400,
		];

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
		imagecopyresampled($image, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

		imagejpeg($image, $target, $options['quality']);
	}

	public function saveMetadata(string $file, array $metadata, array $drivers = []) {
		// TODO Not sure how this is going to work with EXIF data since IPTC embed function returns whole file
		$content = '';
		foreach ($drivers as $tag => $driver)
			$content = $driver->embed($file, $metadata[$tag]);

		if (file_put_contents(str_replace('.jpg', '-new.jpg', $file), $content) === false)
			throw new Exception('Error saving image');
	}

}
