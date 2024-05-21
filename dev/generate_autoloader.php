<?php

// This script generates autoloader.php
if (extension_loaded('mapi')) {
	mapi_load_mapidefs(1);
	$constants = get_defined_constants(true);

	// Filter the relevant constants
	$relevant_prefixes = ['PR_', 'PidLid', 'MAPI', 'ec', 'RPC_', 'SYNC_'];
	$relevant_constants = array_filter($constants['Core'], function ($key) use ($relevant_prefixes) {
		foreach ($relevant_prefixes as $prefix) {
			if (strpos($key, $prefix) === 0) {
				return true;
			}
		}

		return false;
	}, ARRAY_FILTER_USE_KEY);

	$autoloader_content = "<?php\n";
	foreach ($relevant_constants as $name => $value) {
		$autoloader_content .= "if (!defined('{$name}')) {\n";
		$autoloader_content .= "\tdefine('{$name}', {$value});\n";
		$autoloader_content .= "}\n";
	}
	$autoloader_content .= "?>";

	file_put_contents('autoloader.php', $autoloader_content);

	echo "autoloader.php has been generated successfully.\n";
}
else {
	echo "The mapi module is not loaded.\n";
}
