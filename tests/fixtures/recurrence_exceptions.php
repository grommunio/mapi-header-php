<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n: the recurring message, its exception attachments and their
// embedded messages live in memory, and every MAPI call that createException()
// and modifyException() make is written down with its arguments.
if (!extension_loaded('iconv')) {
	dl('iconv.' . PHP_SHLIB_SUFFIX);
}

if (!function_exists('_')) {
	function _(string $text): string {
		return $text;
	}
}

if (!function_exists('ngettext')) {
	function ngettext(string $singular, string $plural, int $count): string {
		return $count == 1 ? $singular : $plural;
	}
}

function mapi_load_mapidefs($flags): void {}

function mapi_prop_tag(int $type, int $id): int {
	return ($id << 16) | $type;
}

function mapi_prop_id(int $tag): int {
	return $tag >> 16;
}

function mapi_prop_type(int $tag): int {
	return $tag & 0xFFFF;
}

function mapi_getidsfromnames($store, array $ids, array $guids): array {
	return array_map(static fn ($index) => mapi_prop_tag(PT_UNSPECIFIED, 0x8000 + $index), array_keys($ids));
}

function mapi_createoneoff(string $name, string $type, string $address, int $flags = 0): string {
	return "{$type}:{$address}";
}

// Objects are addressed by stream resources, as the library checks
// is_resource() on the message it copies attachments from.
function object_create(string $label, array $extra = []): mixed {
	$GLOBALS['objects'][] = $extra + [
		'label' => $label, 'props' => [], 'recipients' => [], 'attachments' => [], 'next' => 0,
	];
	$handle = fopen('php://memory', 'r');
	$GLOBALS['handles'][(int) $handle] = array_key_last($GLOBALS['objects']);
	$GLOBALS['keep'][] = $handle;

	return $handle;
}

function object_id($handle): int {
	return $GLOBALS['handles'][(int) $handle];
}

function &object_of($handle): array {
	return $GLOBALS['objects'][object_id($handle)];
}

function object_open(int $id): mixed {
	$handle = fopen('php://memory', 'r');
	$GLOBALS['handles'][(int) $handle] = $id;
	$GLOBALS['keep'][] = $handle;

	return $handle;
}

function label($handle): string {
	return $handle === null ? 'store' : object_of($handle)['label'];
}

function trace(string $call): void {
	$GLOBALS['trace'][] = $call;
}

function render(mixed $value): string {
	if (is_array($value)) {
		$parts = [];
		foreach ($value as $key => $item) {
			$parts[] = (is_int($key) && array_is_list($value) ? '' : (is_int($key) ? prop_name($key) : $key) . ': ') . render($item);
		}

		return (array_is_list($value) ? '[' : '{') . implode(', ', $parts) . (array_is_list($value) ? ']' : '}');
	}
	if (is_string($value) && (preg_match('//u', $value) !== 1 || preg_match('/[\x00-\x08\x0e-\x1f]/', $value))) {
		return strlen($value) > 32 ? 'binary:' . strlen($value) . ':' . md5($value) : 'binary:' . bin2hex($value);
	}

	return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}

function prop_name(int $tag): string {
	return $GLOBALS['names'][$tag] ?? sprintf('0x%08X', $tag);
}

function render_props(?array $props): string {
	if ($props === null) {
		return 'all';
	}
	ksort($props);

	return render($props);
}

function render_tags(?array $tags): string {
	return $tags === null ? 'all' : '[' . implode(', ', array_map('prop_name', $tags)) . ']';
}

function render_recipients(array $rows): string {
	$short = [];
	foreach ($rows as $row) {
		$short[] = array_intersect_key($row, array_flip([PR_ROWID, PR_EMAIL_ADDRESS, PR_RECIPIENT_TYPE, PR_RECIPIENT_FLAGS, PR_RECIPIENT_TRACKSTATUS]));
	}

	return render($short);
}

function mapi_getprops($object, ?array $tags = null): array {
	trace('mapi_getprops(' . label($object) . ', ' . render_tags($tags) . ')');
	if ($object === null) {
		return [];
	}
	$props = object_of($object)['props'];

	return $tags === null ? $props : array_intersect_key($props, array_flip($tags));
}

function mapi_setprops($object, array $props): bool {
	trace('mapi_setprops(' . label($object) . ', ' . render_props($props) . ')');
	$target = &object_of($object);
	foreach ($props as $tag => $value) {
		$target['props'][$tag] = $value;
	}

	return true;
}

function mapi_deleteprops($object, array $tags): bool {
	trace('mapi_deleteprops(' . label($object) . ', ' . render_tags($tags) . ')');
	$target = &object_of($object);
	foreach ($tags as $tag) {
		unset($target['props'][$tag]);
	}

	return true;
}

function mapi_savechanges($object): bool {
	trace('mapi_savechanges(' . label($object) . ')');

	return true;
}

function mapi_message_getattachmenttable($message): array {
	trace('mapi_message_getattachmenttable(' . label($message) . ')');

	return ['object' => $message, 'rows' => 'attachments'];
}

function mapi_message_getrecipienttable($message): array {
	trace('mapi_message_getrecipienttable(' . label($message) . ')');

	return ['object' => $message, 'rows' => 'recipients'];
}

function mapi_table_queryallrows(array $table, ?array $tags = null, ?array $restriction = null): array {
	$filter = 'all';
	if ($restriction !== null) {
		if ($restriction[0] !== RES_PROPERTY || $restriction[1][RELOP] !== RELOP_EQ) {
			throw new RuntimeException('restriction type ' . $restriction[0] . ' is not modelled');
		}
		$tag = $restriction[1][ULPROPTAG];
		$filter = prop_name($tag) . ' == ' . render($restriction[1][VALUE][$tag]);
	}
	trace('mapi_table_queryallrows(' . label($table['object']) . '.' . $table['rows'] . ', ' . render_tags($tags) . ', ' . $filter . ')');
	$object = object_of($table['object']);
	if ($table['rows'] === 'attachments') {
		$rows = [];
		foreach ($object['attachments'] as $id) {
			$rows[] = $GLOBALS['objects'][$id]['props'];
		}
	}
	else {
		$rows = $object['recipients'];
	}
	if ($restriction !== null) {
		$rows = array_values(array_filter($rows, static fn ($row) => ($row[$tag] ?? null) == $restriction[1][VALUE][$tag]));
	}
	if ($tags !== null) {
		$rows = array_map(static fn ($row) => array_intersect_key($row, array_flip($tags)), $rows);
	}

	return $rows;
}

function mapi_message_createattach($message, ?int $flags = 0): mixed {
	$parent = &object_of($message);
	$num = $parent['next']++;
	$attachment = object_create(label($message) . '/attach' . $num, ['embedded' => null]);
	object_of($attachment)['props'][PR_ATTACH_NUM] = $num;
	$parent['attachments'][$num] = object_id($attachment);
	trace('mapi_message_createattach(' . label($message) . ') = ' . label($attachment));

	return $attachment;
}

function mapi_message_openattach($message, int $num): mixed {
	trace('mapi_message_openattach(' . label($message) . ', ' . $num . ')');
	$id = object_of($message)['attachments'][$num] ?? null;

	return $id === null ? false : object_open($id);
}

function mapi_message_deleteattach($message, int $num, ?int $flags = 0): bool {
	trace('mapi_message_deleteattach(' . label($message) . ', ' . $num . ')');
	$parent = &object_of($message);
	unset($parent['attachments'][$num]);

	return true;
}

function mapi_attach_openobj($attachment, ?int $flags = 0): mixed {
	trace('mapi_attach_openobj(' . label($attachment) . ', ' . $flags . ')');
	$attach = &object_of($attachment);
	if ($attach['embedded'] === null) {
		if (!($flags & MAPI_CREATE)) {
			return false;
		}
		$attach['embedded'] = object_id(object_create($attach['label'] . '/message'));
	}

	return object_open($attach['embedded']);
}

function mapi_copyto($source, array $excludeIids, array $excludeTags, $target, ?int $flags = 0): bool {
	trace('mapi_copyto(' . label($source) . ', ' . render_tags($excludeTags) . ', ' . label($target) . ', ' . $flags . ')');
	$to = &object_of($target);
	foreach (object_of($source)['props'] as $tag => $value) {
		if (!in_array($tag, $excludeTags, true) && $tag !== PR_ATTACH_NUM) {
			$to['props'][$tag] = $value;
		}
	}

	return true;
}

function mapi_message_modifyrecipients($message, int $flags, array $rows): bool {
	trace('mapi_message_modifyrecipients(' . label($message) . ', ' . $flags . ', ' . render_recipients($rows) . ')');
	$target = &object_of($message);
	foreach ($rows as $row) {
		$rowid = $row[PR_ROWID] ?? null;
		$found = null;
		foreach ($target['recipients'] as $index => $existing) {
			if (($rowid !== null && $existing[PR_ROWID] === $rowid) ||
				($rowid === null && isset($row[PR_ENTRYID]) && $existing[PR_ENTRYID] === $row[PR_ENTRYID])) {
				$found = $index;
				break;
			}
		}
		if ($flags === MODRECIP_REMOVE) {
			if ($found !== null) {
				unset($target['recipients'][$found]);
				$target['recipients'] = array_values($target['recipients']);
			}
		}
		elseif ($flags === MODRECIP_MODIFY && $found !== null) {
			$target['recipients'][$found] = $row;
		}
		else {
			$row[PR_ROWID] = count($target['recipients']) + 1;
			$target['recipients'][] = $row;
		}
	}

	return true;
}

// the property tags are defined by php-mapi at load time; the stub lists them
foreach (file($argv[1] . '/dev/php-mapi-stub.php') as $line) {
	if (preg_match("/^\tdefine\\('(\\w+)', (0x[0-9A-Fa-f]+|-?\\d+|'[^']*')\\);$/", $line, $match) && !defined($match[1])) {
		$value = $match[2];
		define($match[1], match (true) {
			str_starts_with($value, '0x') => hexdec(substr($value, 2)),
			str_starts_with($value, "'") => substr($value, 1, -1),
			default => (int) $value,
		});
		if (str_starts_with($match[1], 'PR_') && is_int(constant($match[1]))) {
			$GLOBALS['names'][constant($match[1])] ??= $match[1];
		}
	}
}

foreach ([
	'gettext', 'mapi.util', 'mapidefs', 'mapitags', 'mapiguid',
	'class.baseexception', 'class.mapiexception', 'class.recurrenceexception',
	'class.baserecurrence', 'class.recurrence',
] as $file) {
	require $argv[1] . '/' . $file . '.php';
}

const DAY = 86400;

// Berlin: GMT+1, DST from the last Sunday of March 02:00 to the last Sunday of October 03:00
const BERLIN = [
	'timezone' => -60, 'timezonedst' => -60,
	'dststartmonth' => 3, 'dststartweek' => 5, 'dststartday' => 0, 'dststarthour' => 2,
	'dstendmonth' => 10, 'dstendweek' => 5, 'dstendday' => 0, 'dstendhour' => 3,
];

function recipient(string $address, int $flags = recipSendable): array {
	return [
		PR_ENTRYID => mapi_createoneoff($address, 'SMTP', $address),
		PR_DISPLAY_NAME => $address,
		PR_EMAIL_ADDRESS => $address,
		PR_ADDRTYPE => 'SMTP',
		PR_RECIPIENT_TYPE => MAPI_TO,
		PR_RECIPIENT_FLAGS => $flags,
	];
}

function recipients(mixed $spec): array {
	if ($spec === null) {
		return [];
	}
	if (array_is_list($spec)) {
		return array_map('recipient', $spec);
	}
	$delta = [];
	foreach ($spec as $change => $addresses) {
		$delta[$change] = array_map('recipient', $addresses);
	}

	return $delta;
}

// Property names are the keys of Recurrence::$proptags or PR_ constants;
// a time is given as [day, minute] of the series in local time.
function props(Recurrence $recurrence, int $start, array $spec): array {
	$props = [];
	foreach ($spec as $name => $value) {
		$tag = $recurrence->proptags[$name] ?? constant($name);
		if (is_array($value) && isset($value['t'])) {
			$value = $recurrence->toGMT($recurrence->tz, $start + $value['t'][0] * DAY + $value['t'][1] * 60);
		}
		$props[$tag] = $value;
	}

	return $props;
}

// A notice and an exception are part of what a case does, so they are
// written down with the calls; the file and line would move with every edit.
set_error_handler(static function (int $severity, string $message): bool {
	trace('php: ' . $message);

	return true;
});

$first = true;
foreach (json_decode(file_get_contents('php://stdin'), true, 512, JSON_THROW_ON_ERROR) as $case) {
	$GLOBALS['objects'] = [];
	$GLOBALS['handles'] = [];
	$GLOBALS['trace'] = [];
	$tz = ($case['zone'] ?? null) === 'Europe/Berlin' ? BERLIN : null;
	$start = $case['start'] ?? 1901232000; // Monday 2030-04-01, still ahead when the reminder is looked up
	$message = object_create('message');
	$tags = (new Recurrence(null, $message))->proptags;
	$GLOBALS['names'] = array_flip($tags) + $GLOBALS['names'];
	mapi_setprops($message, [PR_MESSAGE_CLASS => 'IPM.Appointment']);
	$recurrence = new Recurrence(null, $message);
	$recurrence->tz = $tz;
	mapi_setprops($message, props($recurrence, $start, $case['series'] ?? []));
	mapi_message_modifyrecipients($message, MODRECIP_ADD, recipients($case['recipients'] ?? null));
	$recurrence = new Recurrence(null, $message);
	$recurrence->setRecurrence($tz, ($case['recur'] ?? []) + [
		'type' => 10, 'subtype' => 0, 'everyn' => 1440, 'regen' => 0, 'term' => 0x22, 'numoccur' => 10,
		'start' => $start, 'end' => $start + 9 * DAY, 'startocc' => 600, 'endocc' => 660,
	]);
	foreach ($case['existing'] ?? [] as $existing) {
		$day = $start + $existing['day'] * DAY;
		$recurrence->createException(props($recurrence, $start, $existing['props'] ?? []), $day, $existing['delete'] ?? false, recipients($existing['recips'] ?? null));
		// an exception whose attachment went missing, as modifyException() restores it
		$attachment = empty($existing['orphan']) ? false : $recurrence->getExceptionAttachment($day);
		if ($attachment !== false) {
			mapi_message_deleteattach($message, mapi_getprops($attachment, [PR_ATTACH_NUM])[PR_ATTACH_NUM]);
		}
	}
	$source = false;
	if (!empty($case['copyFrom'])) {
		$source = object_create('source');
		mapi_setprops($source, [PR_SUBJECT => 'Occurrence', $tags['location'] => 'Source location']);
		foreach (['file.txt' => ATTACH_BY_VALUE, 'hidden' => ATTACH_EMBEDDED_MSG] as $name => $method) {
			$attachment = mapi_message_createattach($source);
			mapi_setprops($attachment, [PR_ATTACH_METHOD => $method, PR_DISPLAY_NAME => $name, PR_ATTACH_LONG_FILENAME => $name, PR_ATTACH_SIZE => 10]);
		}
	}
	$props = props($recurrence, $start, $case['props'] ?? []);
	$day = $start + $case['day'] * DAY + ($case['minute'] ?? 0) * 60;
	$GLOBALS['trace'] = [];

	try {
		if ($case['call'] === 'create') {
			$return = $recurrence->createException($props, $day, $case['delete'] ?? false, recipients($case['recips'] ?? null), $source);
		}
		else {
			$return = $recurrence->modifyException($props, $day, recipients($case['recips'] ?? null), $source);
		}
	}
	catch (Throwable $e) {
		$return = get_class($e) . ': ' . preg_replace('/, called in .*/s', '', $e->getMessage());
	}
	$recur = $recurrence->recur;
	foreach ($recur['changed_occurrences'] as &$changed) {
		ksort($changed);
	}
	unset($changed);
	echo $first ? '' : "\n", '== ', $case['name'], "\n";
	$first = false;
	echo 'return ', is_bool($return) ? json_encode($return) : $return, "\n";
	echo implode("\n", $GLOBALS['trace']), $GLOBALS['trace'] === [] ? '' : "\n";
	echo 'changed ', render($recur['changed_occurrences']), "\n";
	echo 'deleted ', render($recur['deleted_occurrences']), "\n";
}
