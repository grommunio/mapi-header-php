<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n: a store, a folder and a recurring task live in memory and
// every MAPI call is recorded with its arguments, so the test can check what
// regenerateTask() gives the new task and what it does to the recurring one.
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

function object_create(string $kind, array $props = [], array $extra = []): int {
	$GLOBALS['objects'][] = $extra + ['kind' => $kind, 'props' => $props, 'recipients' => [], 'attachments' => [], 'large' => []];

	return array_key_last($GLOBALS['objects']);
}

function record(string $function, array $args): void {
	$GLOBALS['calls'][] = array_merge([$function], printable($args));
}

// an interface id is binary, the log has to be JSON
function printable(mixed $value): mixed {
	if (is_array($value)) {
		return array_map('printable', $value);
	}

	return is_string($value) && !preg_match('//u', $value) ? 'hex:' . bin2hex($value) : $value;
}

function mapi_getprops($object, ?array $tags = null): array {
	record(__FUNCTION__, func_get_args());
	$props = $GLOBALS['objects'][$object]['props'];
	$result = [];
	foreach ($tags ?? array_keys($props) as $tag) {
		if (isset($GLOBALS['objects'][$object]['large'][$tag])) {
			// gromox answers with an error for a value too large to come back inline
			$result[mapi_prop_tag(PT_ERROR, mapi_prop_id($tag))] = MAPI_E_NOT_ENOUGH_MEMORY;
		}
		elseif (isset($props[$tag])) {
			$result[$tag] = $props[$tag];
		}
	}

	return $result;
}

function mapi_setprops($object, array $props): bool {
	record(__FUNCTION__, func_get_args());
	foreach ($props as $tag => $value) {
		// the identity of a message is not for the client to set
		if (!in_array($tag, [PR_ENTRYID, PR_PARENT_ENTRYID, PR_STORE_ENTRYID], true)) {
			$GLOBALS['objects'][$object]['props'][$tag] = $value;
		}
	}

	return true;
}

function mapi_savechanges($object): bool {
	record(__FUNCTION__, func_get_args());
	if ($GLOBALS['objects'][$object]['kind'] === 'message') {
		$GLOBALS['objects'][$object]['props'][PR_ENTRYID] ??= 'message-' . $object;
	}

	return true;
}

function mapi_msgstore_openentry($store, ?string $entryid = null, ?int $flags = 0): int {
	record(__FUNCTION__, func_get_args());
	foreach ($GLOBALS['objects'] as $id => $object) {
		if ($object['kind'] !== 'store' && ($object['props'][PR_ENTRYID] ?? null) === $entryid) {
			return $id;
		}
	}

	throw new RuntimeException('nothing has the entryid ' . $entryid);
}

function mapi_folder_createmessage($folder, ?int $flags = 0): int {
	record(__FUNCTION__, func_get_args());
	$message = object_create('message', [
		PR_PARENT_ENTRYID => $GLOBALS['objects'][$folder]['props'][PR_ENTRYID],
		PR_STORE_ENTRYID => $GLOBALS['objects'][$folder]['props'][PR_STORE_ENTRYID],
	]);
	// rows a new message is born with, so that the loop removing them runs
	$GLOBALS['objects'][$message]['recipients'] = $GLOBALS['bornWith'];

	return $message;
}

function mapi_message_getrecipienttable($message): int {
	record(__FUNCTION__, func_get_args());

	return object_create('table', [], ['of' => $message, 'rows' => 'recipients']);
}

function mapi_message_getattachmenttable($message): false|int {
	record(__FUNCTION__, func_get_args());
	if ($GLOBALS['objects'][$message]['attachments'] === false) {
		return false;
	}

	return object_create('table', [], ['of' => $message, 'rows' => 'attachments']);
}

function mapi_table_queryallrows($table, ?array $tags = null, ?array $restriction = null): array {
	record(__FUNCTION__, func_get_args());
	['of' => $of, 'rows' => $kind] = $GLOBALS['objects'][$table];
	$rows = $GLOBALS['objects'][$of][$kind];
	if ($kind === 'attachments') {
		$rows = array_map(static fn ($attach) => $GLOBALS['objects'][$attach]['props'], $rows);
	}
	if ($tags !== null) {
		$rows = array_map(static fn ($row) => array_intersect_key($row, array_flip($tags)), $rows);
	}

	return array_values($rows);
}

function mapi_message_modifyrecipients($message, int $flags, array $rows): bool {
	record(__FUNCTION__, func_get_args());
	foreach ($rows as $row) {
		if ($flags === MODRECIP_REMOVE) {
			$GLOBALS['objects'][$message]['recipients'] = array_values(array_filter(
				$GLOBALS['objects'][$message]['recipients'],
				static fn ($existing) => $existing[PR_ROWID] !== $row[PR_ROWID]
			));
		}
		else {
			$GLOBALS['objects'][$message]['recipients'][] = $row;
		}
	}

	return true;
}

function mapi_message_openattach($message, int $num): int {
	record(__FUNCTION__, func_get_args());
	foreach ($GLOBALS['objects'][$message]['attachments'] as $attach) {
		if ($GLOBALS['objects'][$attach]['props'][PR_ATTACH_NUM] === $num) {
			return $attach;
		}
	}

	throw new RuntimeException('there is no attachment ' . $num);
}

function mapi_message_createattach($message, ?int $flags = 0): int {
	record(__FUNCTION__, func_get_args());
	$attach = object_create('attach', [PR_ATTACH_NUM => count($GLOBALS['objects'][$message]['attachments'])]);
	$GLOBALS['objects'][$message]['attachments'][] = $attach;

	return $attach;
}

function mapi_copyto($source, array $excludeInterfaces, array $excludeProps, $destination, ?int $flags = 0): bool {
	record(__FUNCTION__, func_get_args());
	foreach ($GLOBALS['objects'][$source]['props'] as $tag => $value) {
		if ($tag !== PR_ATTACH_NUM && !in_array($tag, $excludeProps, true)) {
			$GLOBALS['objects'][$destination]['props'][$tag] = $value;
		}
	}

	return true;
}

function mapi_openproperty($object, int $tag, ?string $iid = null, ?int $interfaceflags = 0, ?int $flags = 0): int {
	record(__FUNCTION__, func_get_args());

	return object_create('stream', [], [
		'of' => $object, 'tag' => $tag, 'position' => 0,
		'data' => $GLOBALS['objects'][$object]['props'][$tag] ?? '',
	]);
}

function mapi_stream_stat($stream): array {
	record(__FUNCTION__, func_get_args());

	return ['cb' => strlen($GLOBALS['objects'][$stream]['data'])];
}

function mapi_stream_seek($stream, int $offset, ?int $whence = 0): bool {
	record(__FUNCTION__, func_get_args());
	$GLOBALS['objects'][$stream]['position'] = $offset;

	return true;
}

function mapi_stream_read($stream, int $length): string {
	record(__FUNCTION__, func_get_args());
	$chunk = substr($GLOBALS['objects'][$stream]['data'], $GLOBALS['objects'][$stream]['position'], $length);
	$GLOBALS['objects'][$stream]['position'] += strlen($chunk);

	return $chunk;
}

function mapi_stream_setsize($stream, int $size): bool {
	record(__FUNCTION__, func_get_args());
	$GLOBALS['objects'][$stream]['data'] = str_pad(substr($GLOBALS['objects'][$stream]['data'], 0, $size), $size, "\0");

	return true;
}

function mapi_stream_write($stream, string $data): int {
	record(__FUNCTION__, func_get_args());
	$position = $GLOBALS['objects'][$stream]['position'];
	$GLOBALS['objects'][$stream]['data'] = substr_replace($GLOBALS['objects'][$stream]['data'], $data, $position, strlen($data));
	$GLOBALS['objects'][$stream]['position'] = $position + strlen($data);

	return strlen($data);
}

function mapi_stream_commit($stream): bool {
	record(__FUNCTION__, func_get_args());
	['of' => $of, 'tag' => $tag, 'data' => $data] = $GLOBALS['objects'][$stream];
	$GLOBALS['objects'][$of]['props'][$tag] = $data;

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
	}
}

foreach ([
	'gettext', 'mapi.util', 'mapidefs', 'mapitags', 'mapiguid',
	'class.baseexception', 'class.mapiexception', 'class.recurrenceexception',
	'class.baserecurrence', 'class.taskrecurrence',
] as $file) {
	require $argv[1] . '/' . $file . '.php';
}

date_default_timezone_set('UTC');

class RegenerateTaskRecurrence extends TaskRecurrence {
	// $action is private to TaskRecurrence
	public function setAction(array $action): void {
		Closure::bind(function () use ($action) { $this->action = $action; }, $this, TaskRecurrence::class)();
	}

	public function getAction(): array {
		return Closure::bind(fn () => $this->action, $this, TaskRecurrence::class)();
	}
}

$bodies = [
	'absent' => null,
	'empty' => '',
	'padding only' => "\0\0",
	'plain' => "Notes\r\n",
	'separated' => "------------\r\nNotes\r\n",
	'large' => str_repeat("Notes\r\n", 400),
	'large separated' => "------------\r\n" . str_repeat("Notes\r\n", 400),
];

// a store with a folder holding a recurring task as the case describes it,
// returns the task and its recurrence object
function recurringTask(array $case, array $bodies): array {
	$GLOBALS['objects'] = [];
	$GLOBALS['calls'] = [];
	$GLOBALS['bornWith'] = [];
	$store = object_create('store', [PR_ENTRYID => 'store']);
	object_create('folder', [PR_ENTRYID => 'tasks', PR_STORE_ENTRYID => 'store']);
	$message = object_create('message', [
		PR_ENTRYID => 'task',
		PR_PARENT_ENTRYID => 'tasks',
		PR_STORE_ENTRYID => 'store',
		PR_MESSAGE_CLASS => 'IPM.Task',
		PR_SUBJECT => 'Recurring task',
		PR_IMPORTANCE => IMPORTANCE_NORMAL,
	]);
	$tags = (new TaskRecurrence($store, $message))->proptags;
	$props = [
		$tags['status'] => olTaskNotStarted,
		$tags['percent_complete'] => 0.0,
		$tags['complete'] => false,
		$tags['startdate'] => gmmktime(0, 0, 0, 4, 13, 2026),
		$tags['commonstart'] => gmmktime(0, 0, 0, 4, 13, 2026),
		$tags['duedate'] => gmmktime(0, 0, 0, 4, 15, 2026),
		$tags['commonend'] => gmmktime(0, 0, 0, 4, 15, 2026),
		$tags['reminder'] => true,
		$tags['reminder_time'] => gmmktime(9, 0, 0, 4, 15, 2026),
		$tags['recurring'] => true,
		$tags['owner'] => 'Owner',
	];
	if ($bodies[$case['body']] !== null) {
		$props[PR_BODY] = $bodies[$case['body']];
	}
	if (str_starts_with($case['body'], 'large')) {
		$GLOBALS['objects'][$message]['large'][PR_BODY] = true;
	}
	mapi_setprops($message, $props);
	for ($i = 1; $i <= $case['recipients']; ++$i) {
		$GLOBALS['objects'][$message]['recipients'][] = [
			PR_ROWID => $i,
			PR_ENTRYID => 'recipient-' . $i,
			PR_DISPLAY_NAME => 'Recipient ' . $i,
			PR_EMAIL_ADDRESS => 'recipient' . $i . '@example.invalid',
			PR_SMTP_ADDRESS => 'recipient' . $i . '@example.invalid',
			PR_ADDRTYPE => 'SMTP',
			PR_RECIPIENT_TYPE => MAPI_TO,
			PR_RECIPIENT_FLAGS => recipSendable,
		];
	}
	for ($i = 1; $i <= $case['bornWith']; ++$i) {
		$GLOBALS['bornWith'][] = [PR_ROWID => 100 + $i, PR_DISPLAY_NAME => 'Born with ' . $i];
	}
	if ($case['attachments'] === 'no table') {
		$GLOBALS['objects'][$message]['attachments'] = false;
	}
	for ($i = 0; $i < (int) $case['attachments']; ++$i) {
		$GLOBALS['objects'][$message]['attachments'][] = object_create('attach', [
			PR_ATTACH_NUM => $i,
			PR_ATTACH_SIZE => 100 * ($i + 1),
			PR_ATTACH_LONG_FILENAME => 'file' . $i . '.txt',
			PR_ATTACHMENT_HIDDEN => false,
			PR_DISPLAY_NAME => 'file' . $i . '.txt',
			PR_ATTACH_METHOD => ATTACH_BY_VALUE,
			PR_ATTACH_DATA_BIN => 'contents of file ' . $i,
		]);
	}
	$recurrence = new RegenerateTaskRecurrence($store, $message);
	$GLOBALS['calls'] = [];

	return [$message, $recurrence];
}

function objectState(int $object): array {
	$state = $GLOBALS['objects'][$object];

	return [
		'props' => $state['props'],
		'recipients' => $state['recipients'],
		'attachments' => $state['attachments'] === false ? false :
			array_map(static fn ($attach) => $GLOBALS['objects'][$attach]['props'], $state['attachments']),
	];
}

function newMessage(int $task): int {
	foreach ($GLOBALS['objects'] as $id => $object) {
		if ($object['kind'] === 'message' && $id !== $task) {
			return $id;
		}
	}

	throw new RuntimeException('no task was created');
}

$cases = [];
foreach ([true, false] as $markComplete) {
	foreach ([[], ['subject' => 'Edited subject']] as $subject) {
		foreach ([[], ['importance' => IMPORTANCE_HIGH]] as $importance) {
			foreach ([[], ['startdate' => gmmktime(0, 0, 0, 4, 14, 2026)]] as $startdate) {
				foreach ([[], ['duedate' => gmmktime(0, 0, 0, 4, 16, 2026)]] as $duedate) {
					foreach ([0, 2] as $recipients) {
						foreach ([0, 2] as $bornWith) {
							foreach (['no table', '0', '2'] as $attachments) {
								foreach (array_keys($bodies) as $body) {
									$cases[] = [
										'markComplete' => $markComplete,
										'action' => $subject + $importance + $startdate + $duedate,
										'recipients' => $recipients,
										'bornWith' => $bornWith,
										'attachments' => $attachments,
										'body' => $body,
									];
								}
							}
						}
					}
				}
			}
		}
	}
}

$results = [];
foreach ($cases as $case) {
	[$task, $recurrence] = recurringTask($case, $bodies);
	$action = $case['action'] + ['complete' => $case['markComplete'] ? 1 : 0];
	if ($case['markComplete']) {
		$action['date_completed'] = gmmktime(14, 0, 0, 4, 14, 2026);
	}
	$recurrence->setAction($action);
	$result = $recurrence->regenerateTask($case['markComplete']);
	$results[] = [
		'case' => $case,
		'calls' => $GLOBALS['calls'],
		'result' => $result,
		'new' => objectState(newMessage($task)),
		'task' => objectState($task),
		'action' => $recurrence->getAction(),
	];
}

// completing an occurrence of a weekly regenerating task through the public entry point
[$task, $recurrence] = recurringTask([
	'body' => 'plain', 'recipients' => 1, 'bornWith' => 0, 'attachments' => '1',
], $bodies);
$recurrence->recur = [
	'type' => 11, 'subtype' => 1, 'everyn' => 1, 'regen' => 1, 'weekdays' => 0, 'term' => 0x23,
	'start' => gmmktime(0, 0, 0, 4, 13, 2026), 'end' => 0x7FFFFFFF,
	'startocc' => 0, 'endocc' => 0, 'changed_occurrences' => [], 'deleted_occurrences' => [],
];
$recur = ['complete' => 1, 'date_completed' => gmmktime(14, 0, 0, 4, 14, 2026), 'subject' => 'Completed occurrence'];
$result = $recurrence->markOccurrenceComplete($recur);
$series = [
	'calls' => $GLOBALS['calls'],
	'result' => $result,
	'new' => objectState(newMessage($task)),
	'task' => objectState($task),
	'recur' => $recur,
];

echo json_encode(['tags' => $recurrence->proptags, 'cases' => $results, 'series' => $series], JSON_THROW_ON_ERROR);
