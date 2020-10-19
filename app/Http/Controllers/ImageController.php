<?php

namespace App\Http\Controllers;

use App\Support\Jpeg;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller {

	protected $_location = 'C:/Users/charl/Downloads/photos';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function generateThumbnail(Request $request, string $groupUid, string $filename) {
		if ($groupUid !== md5($this->_location))
			throw new Exception("Source image folder does not match requested thumbnail: {$this->_location}");

		$destination = "app/public/images/thumbnails/$groupUid";
		$target = storage_path($destination . "/{$filename}");

		if (!file_exists(storage_path($destination)))
			mkdir(storage_path($destination), 0755, true);

		(new Jpeg())->generateThumbnail("{$this->_location}/$filename", $target);

		if (!file_exists($target))
			throw new Exception("Failed to create thumbnail: $target");

		return redirect("/storage/images/thumbnails/{$groupUid}/{$filename}");
	}

}
