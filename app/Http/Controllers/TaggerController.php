<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaggerController extends Controller {
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __invoke(Request $request) {
		return view('layouts.tagger');
	}

}
