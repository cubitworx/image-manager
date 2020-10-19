<?php

if (! function_exists('public_path')) {
	/**
	 * Get the path to the public folder.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function public_path($path = '') {
		return realpath(__DIR__ . '/../public/') . $path;
	}
}

if (! function_exists('storage_path')) {
	/**
	 * Get the path to the storage folder.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function storage_path($path = '') {
		return realpath(__DIR__ . '/../storage/') . ltrim($path, '\\/');
	}
}
