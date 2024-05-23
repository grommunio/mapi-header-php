<?php

function fetchAndGenerateStubs(string $url): string {
	// Fetch the remote file
	$stubFileContents = file_get_contents($url);
	if ($stubFileContents === false) {
		throw new Exception("Unable to fetch the file from {$url}");
	}

	// Correcting the stub file contents for the specific error
	$stubFileContents = str_replace('res ', 'resource ', $stubFileContents);

	// Extract function definitions
	preg_match_all('/function\s+(\w+)\s*\(([^)]*)\)\s*:\s*([\w|?\\\\]+)\s*{}/', $stubFileContents, $matches, PREG_SET_ORDER);

	$stubFunctions = "class resource {}\n\n";

	foreach ($matches as $match) {
		$functionName = $match[1];
		$parameters = str_replace('resource', 'resource', $match[2]);
		$returnType = str_replace('resource', 'resource', $match[3]);

		// Generate the return value based on the return type
		$returnValue = generateReturnValue($returnType);

		// Generate PHPDoc for function
		$phpDoc = generatePHPDoc($parameters, $returnType);

		$stubFunctions .= "{$phpDoc}\n";
		$stubFunctions .= "function {$functionName}({$parameters}): {$returnType} {\n";
		$stubFunctions .= "\treturn {$returnValue};\n";
		$stubFunctions .= "}\n\n";
	}

	return $stubFunctions;
}

function generateReturnValue(string $returnType) {
	// Determine the return value based on the type hint
	switch ($returnType) {
		case 'void':
			return '';

		case 'int':
		case 'int|false':
			return '0';

		case 'bool':
		case 'bool|false':
			return 'false';

		case 'string':
		case 'string|false':
			return "''";

		case 'array':
		case 'array|false':
			return '[]';

		case 'mixed':
			return 'null';

		case 'resource':
		case 'resource|false':
			return 'fopen("php://memory", "r")';

		default:
			if (strpos($returnType, '|') !== false) {
				$types = explode('|', $returnType);

				return generateReturnValue($types[0]);
			}

			return 'null';
	}
}

function generatePHPDoc(string $parameters, string $returnType): string {
	$paramDocs = [];
	if ($parameters) {
		$params = explode(',', $parameters);
		foreach ($params as $param) {
			$param = trim($param);
			if (strpos($param, ' ') !== false) {
				[$type, $name] = explode(' ', $param);
				$type = str_replace('resource', 'resource', $type); // Ensure type remains resource
				$paramDocs[] = " * @param {$type} {$name}";
			}
			else {
				$paramDocs[] = " * @param mixed {$param}";
			}
		}
	}

	$paramDocs[] = " * @return {$returnType}";

	return "/**\n" . implode("\n", $paramDocs) . "\n */";
}

// URL of the mapi.stub.php file
$url = 'https://raw.githubusercontent.com/grommunio/gromox/master/php_mapi/mapi.stub.php';

try {
	$stubFunctions = fetchAndGenerateStubs($url);

	// Generate autoloader.php content
	$autoloaderContent = "<?php\n\n";
	$autoloaderContent .= $stubFunctions;

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

		foreach ($relevant_constants as $name => $value) {
			$autoloaderContent .= "if (!defined('{$name}')) {\n";
			if (is_numeric($value)) {
				$value = sprintf("0x%08X", $value);
			}
			$autoloaderContent .= "\tdefine('{$name}', {$value});\n";
			$autoloaderContent .= "}\n";
		}
	}
	else {
		echo "The mapi module is not loaded.\n";
	}

	$autoloaderContent .= "?>";

	file_put_contents('autoloader.php', $autoloaderContent);

	echo "autoloader.php has been generated successfully.\n";
}
catch (Exception $e) {
	echo "Error: " . $e->getMessage() . "\n";
}
