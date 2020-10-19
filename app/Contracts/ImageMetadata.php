<?php

namespace App\Contracts;

interface ImageMetadata {

	public function embed(string $file, array $metadata): string;

}
