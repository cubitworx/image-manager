<?php

namespace App\Console\Commands;

$links = [
	'public/storage' => 'storage/app/public',
];

foreach ($links as $link => $target) {
	if (file_exists($link)) {
		echo "Link `$link` already exists\n";
	} else {
		make_symlink($target, $link);
		echo "Link created `$link`:`$target`.\n";
	}
}

echo "Links have been created.\n";

function make_symlink($target, $link) {
	if (PHP_OS !== 'WINNT')
		return symlink($target, $link);

	$mode = is_dir($target) ? 'J' : 'H';

	exec("mklink /{$mode} " . escapeshellarg($link) . ' ' . escapeshellarg($target));
}
