<?php

namespace App\Http\Controllers;

use App\Support\Meta;
use Illuminate\Http\Request;

class MetadataController extends Controller {

	protected $_location = 'C:/Users/charl/Downloads/photos';

	public function index(Request $request) {
		return response()->json((new Meta())->loadLocation($this->_location));
	}

	public function store(Request $request) {
		(new Meta())->storeLocation($this->_location, $request->json('files'));

		response()->json('Success');
	}

}
