<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php73\Rector\ConstFetch\SensitiveConstantNameRector;
// import the correct class
use Rector\Set\ValueObject\LevelSetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
	$rectorConfig->phpVersion(PhpVersion::PHP_82);
	$rectorConfig->sets([
		LevelSetList::UP_TO_PHP_81,
		LevelSetList::UP_TO_PHP_82,
	]);

	$rectorConfig->skip([
		// your directories
		__DIR__ . '/plugins/files/php/vendor',
		__DIR__ . '/plugins/files/php/Files/Backend/Webdav/sabredav/vendor',
		__DIR__ . '/plugins/kendox/php/vendor',

		// skip the rule by its correct class name
		SensitiveConstantNameRector::class,
	]);
};
