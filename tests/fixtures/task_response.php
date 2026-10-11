<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n: a store with an inbox, a tasks folder and a sent items
// folder lives in memory and every MAPI call is logged, so what
// processTaskResponse() does with a task request response can be compared
// between two trees call by call. Usage: task_response.php <tree> [log]
// prints the property tags on the first line and one scenario per line.
if (!function_exists('_')) {
	function _(string $text): string {
		return $text;
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

// named property ids are handed out per GUID and id, so the constructor of
// TaskRequest, getSubProperties() and the scenarios below agree on the tags
function mapi_getidsfromnames($store, array $ids, array $guids): array {
	$tags = [];
	foreach ($ids as $index => $id) {
		$key = bin2hex($guids[$index]) . ':' . $id;
		if (!isset($GLOBALS['named'][$key])) {
			$GLOBALS['named'][$key] = 0x8000 + count($GLOBALS['named']);
		}
		$tags[] = mapi_prop_tag(PT_UNSPECIFIED, $GLOBALS['named'][$key]);
	}

	return $tags;
}

function logCall(string $function, array $args): void {
	$GLOBALS['log'][] = [$function, normalize($args)];
}

function normalize(mixed $value): mixed {
	if (is_array($value)) {
		return array_map('normalize', $value);
	}
	if (is_string($value) && !preg_match('/^[\x20-\x7e]*$/', $value)) {
		return 'hex:' . bin2hex($value);
	}

	return $value;
}

function newObject(string $kind, array $props = []): int {
	$id = count($GLOBALS['objects']) + 1;
	$GLOBALS['objects'][$id] = [
		'kind' => $kind, 'props' => $props, 'attachments' => [], 'nextAttach' => 0,
		'messages' => [], 'embedded' => null,
	];

	return $id;
}

// stores, folders and messages can be opened by their entryid
function newEntry(string $kind, array $props = []): int {
	$id = newObject($kind, $props);
	$GLOBALS['objects'][$id]['props'][PR_ENTRYID] = 'entry' . $id;
	$GLOBALS['entries']['entry' . $id] = $id;

	return $id;
}

function newMessage(int $folder, array $props): int {
	$id = newEntry('message', $props);
	$GLOBALS['objects'][$folder]['messages'][] = $id;

	return $id;
}

function newAttachment(int $message, array $props, ?int $embedded = null): int {
	$num = $GLOBALS['objects'][$message]['nextAttach']++;
	$id = newObject('attach', [PR_ATTACH_NUM => $num] + $props);
	$GLOBALS['objects'][$id]['embedded'] = $embedded;
	$GLOBALS['objects'][$message]['attachments'][$num] = $id;

	return $id;
}

function copyMessage(int $source): int {
	$id = newObject('message', $GLOBALS['objects'][$source]['props']);
	foreach ($GLOBALS['objects'][$source]['attachments'] as $attach) {
		copyAttachment($attach, $id);
	}

	return $id;
}

function copyAttachment(int $source, int $message): void {
	$attach = $GLOBALS['objects'][$source];
	$embedded = $attach['embedded'] === null ? null : copyMessage($attach['embedded']);
	newAttachment($message, $attach['props'], $embedded);
}

function openEntry(?string $entryid): int {
	if ($entryid === null || !isset($GLOBALS['entries'][$entryid])) {
		throw new MAPIException('no such entry', MAPI_E_NOT_FOUND);
	}

	return $GLOBALS['entries'][$entryid];
}

function mapi_getprops($object, ?array $tags = null): array {
	logCall(__FUNCTION__, func_get_args());
	$props = $GLOBALS['objects'][$object]['props'];

	return $tags === null ? $props : array_intersect_key($props, array_flip($tags));
}

function mapi_setprops($object, array $props): bool {
	logCall(__FUNCTION__, func_get_args());
	foreach ($props as $tag => $value) {
		$GLOBALS['objects'][$object]['props'][$tag] = $value;
	}

	return true;
}

function mapi_deleteprops($object, array $tags): bool {
	logCall(__FUNCTION__, func_get_args());
	foreach ($tags as $tag) {
		unset($GLOBALS['objects'][$object]['props'][$tag]);
	}

	return true;
}

function mapi_savechanges($object, int $flags = 0): bool {
	logCall(__FUNCTION__, func_get_args());

	return true;
}

function mapi_copyto($source, array $excludeInterfaces, array $excludeProps, $destination, int $flags = 0): bool {
	logCall(__FUNCTION__, func_get_args());
	$error = array_shift($GLOBALS['copyErrors']);
	if ($error) {
		throw new MAPIException('copy refused', $error);
	}
	foreach ($GLOBALS['objects'][$source]['props'] as $tag => $value) {
		if ($tag !== PR_ENTRYID && !in_array($tag, $excludeProps, true)) {
			$GLOBALS['objects'][$destination]['props'][$tag] = $value;
		}
	}
	foreach ($GLOBALS['objects'][$source]['attachments'] as $attach) {
		copyAttachment($attach, $destination);
	}

	return true;
}

function mapi_message_getattachmenttable($message): array {
	logCall(__FUNCTION__, func_get_args());

	return ['object' => $message, 'rows' => 'attachments'];
}

function mapi_folder_getcontentstable($folder, int $flags = 0): array {
	logCall(__FUNCTION__, func_get_args());

	return ['object' => $folder, 'rows' => 'messages'];
}

function mapi_getmsgstorestable($session): array {
	logCall(__FUNCTION__, func_get_args());

	return ['object' => $session, 'rows' => 'stores'];
}

function restriction_matches(array $restriction, array $row): bool {
	[$type, $res] = $restriction;
	if ($type !== RES_PROPERTY) {
		throw new RuntimeException('restriction type ' . $type . ' is not modelled');
	}
	$equal = ($row[$res[ULPROPTAG]] ?? null) == $res[VALUE];

	return $res[RELOP] == RELOP_NE ? !$equal : $equal;
}

function mapi_table_queryallrows(array $table, ?array $tags = null, ?array $restriction = null): array {
	logCall(__FUNCTION__, func_get_args());
	$object = $GLOBALS['objects'][$table['object']];
	if ($table['rows'] === 'stores') {
		$rows = [[PR_DEFAULT_STORE => true, PR_ENTRYID => 'entry' . $object['store']]];
	}
	else {
		$rows = array_map(static fn ($id) => $GLOBALS['objects'][$id]['props'], array_values($object[$table['rows']]));
	}
	if ($restriction !== null) {
		$rows = array_values(array_filter($rows, static fn ($row) => restriction_matches($restriction, $row)));
	}
	if ($tags !== null) {
		$rows = array_map(static fn ($row) => array_intersect_key($row, array_flip($tags)), $rows);
	}

	return $rows;
}

function mapi_message_openattach($message, int $num): int {
	logCall(__FUNCTION__, func_get_args());

	return $GLOBALS['objects'][$message]['attachments'][$num] ?? throw new MAPIException('no such attachment', MAPI_E_NOT_FOUND);
}

function mapi_attach_openobj($attach, int $flags = 0): int {
	logCall(__FUNCTION__, func_get_args());

	return $GLOBALS['objects'][$attach]['embedded'] ?? throw new MAPIException('no embedded message', MAPI_E_NOT_FOUND);
}

function mapi_message_deleteattach($message, int $num, int $flags = 0): bool {
	logCall(__FUNCTION__, func_get_args());
	unset($GLOBALS['objects'][$message]['attachments'][$num]);

	return true;
}

function mapi_folder_createmessage($folder, int $flags = 0): int {
	logCall(__FUNCTION__, func_get_args());

	return newMessage($folder, []);
}

function mapi_msgstore_openentry($store, ?string $entryid = null, int $flags = 0): int {
	logCall(__FUNCTION__, func_get_args());

	return openEntry($entryid);
}

function mapi_msgstore_getreceivefolder($store): int {
	logCall(__FUNCTION__, func_get_args());

	return $GLOBALS['objects'][$store]['inbox'];
}

function mapi_openmsgstore($session, string $entryid): int {
	logCall(__FUNCTION__, func_get_args());

	return openEntry($entryid);
}

set_error_handler(static function (int $errno, string $errstr): bool {
	$GLOBALS['log'][] = ['warning', $errstr];

	return true;
});

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
	'class.baseexception', 'class.mapiexception', 'class.taskrequest',
] as $file) {
	require $argv[1] . '/' . $file . '.php';
}

$withLog = ($argv[2] ?? '') === 'log';
$goid = hex2bin('0123456789abcdef0123456789abcdef');
$otherGoid = hex2bin('fedcba9876543210fedcba9876543210');
// what mapi_copyto() throws, one entry per call
$copyFailures = ['ok' => [], 'retry' => [MAPI_E_INVALID_PARAMETER], 'fail' => [MAPI_E_NO_ACCESS]];

function buildScenario(array $case): array {
	global $goid, $otherGoid, $copyFailures;
	$GLOBALS['objects'] = [];
	$GLOBALS['entries'] = [];
	$GLOBALS['named'] = [];
	$GLOBALS['log'] = [];
	$GLOBALS['copyErrors'] = $copyFailures[$case['copy']];

	$session = newObject('session');
	$store = newEntry('store');
	$inbox = newEntry('folder');
	$tasks = newEntry('folder');
	$sent = newEntry('folder');
	$GLOBALS['objects'][$session]['store'] = $store;
	$GLOBALS['objects'][$store]['inbox'] = $inbox;
	$GLOBALS['objects'][$store]['props'][PR_IPM_SENTMAIL_ENTRYID] = 'entry' . $sent;
	$GLOBALS['objects'][$inbox]['props'][PR_IPM_TASK_ENTRYID] = 'entry' . $tasks;

	$tags = getPropIdsFromStrings($store, [
		'task_goid' => 'PT_BINARY:PSETID_Common:0x8519',
		'taskupdates' => 'PT_BOOLEAN:PSETID_Task:0x811b',
		'updatecount' => 'PT_LONG:PSETID_Task:0x8112',
		'taskstate' => 'PT_LONG:PSETID_Task:0x8113',
		'taskhistory' => 'PT_LONG:PSETID_Task:0x811a',
		'taskmode' => 'PT_LONG:PSETID_Common:0x8518',
		'taskfcreator' => 'PT_BOOLEAN:PSETID_Task:0x811e',
		'task_acceptance_state' => 'PT_LONG:PSETID_Task:0x812a',
		'ownership' => 'PT_LONG:PSETID_Task:0x8129',
		'owner' => 'PT_STRING8:PSETID_Task:0x811f',
		'status' => 'PT_LONG:PSETID_Task:' . PidLidTaskStatus,
		'percent_complete' => 'PT_DOUBLE:PSETID_Task:' . PidLidPercentComplete,
		'complete' => 'PT_BOOLEAN:PSETID_Task:' . PidLidTaskComplete,
		'categories' => 'PT_MV_STRING8:PS_PUBLIC_STRINGS:Keywords',
	]);

	$props = [
		PR_MESSAGE_CLASS => 'IPM.TaskRequest.' . $case['class'],
		PR_SUBJECT => 'Task Accepted: Write the report',
		PR_SENDER_NAME => 'Assignee',
		PR_SENDER_EMAIL_ADDRESS => 'assignee@example.invalid',
		PR_SENDER_ENTRYID => "\x00\x00\x00\x00assignee",
		PR_SENDER_ADDRTYPE => 'SMTP',
		PR_SENDER_SEARCH_KEY => "SMTP:ASSIGNEE@EXAMPLE.INVALID\0",
		PR_SENT_REPRESENTING_NAME => 'Assignee',
		PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'assignee@example.invalid',
		PR_SENT_REPRESENTING_ENTRYID => "\x00\x00\x00\x00assignee",
		PR_SENT_REPRESENTING_ADDRTYPE => 'SMTP',
		PR_SENT_REPRESENTING_SEARCH_KEY => "SMTP:ASSIGNEE@EXAMPLE.INVALID\0",
		PR_MESSAGE_DELIVERY_TIME => 1700000000,
		$tags['task_goid'] => $goid,
	];
	if ($case['toMe'] !== 'absent') {
		$props[PR_MESSAGE_TO_ME] = $case['toMe'];
	}
	if ($case['processed'] !== 'absent') {
		$props[PR_PROCESSED] = $case['processed'];
	}
	if ($case['msgCount'] !== 'absent') {
		$props[$tags['updatecount']] = $case['msgCount'];
	}
	$message = newMessage($inbox, $props);

	$task = [
		PR_MESSAGE_CLASS => 'IPM.Task',
		PR_SUBJECT => 'Write the report',
		PR_ICON_INDEX => ICON_TASK_ASSIGNEE,
		$tags['task_goid'] => $goid,
		$tags['taskstate'] => tdsOWN,
		$tags['taskhistory'] => thAccepted,
		$tags['taskmode'] => tdmtTaskAcc,
		$tags['taskfcreator'] => false,
		$tags['owner'] => 'Assignee',
		$tags['status'] => 1,
		$tags['percent_complete'] => 0.25,
		$tags['complete'] => false,
		$tags['categories'] => ['Blue category'],
		$tags['updatecount'] => 1,
	];
	if ($case['sub'] !== 'absent') {
		$task[$tags['taskupdates']] = $case['sub'];
	}
	$embedded = [
		'none' => [],
		'match' => [$task],
		'other' => [[$tags['task_goid'] => $otherGoid] + $task],
		'two-other' => [[$tags['task_goid'] => $otherGoid] + $task, [$tags['task_goid'] => $otherGoid] + $task],
		'broken-then-match' => [null, $task],
	];
	if ($case['embedded'] !== 'none') {
		newAttachment($message, [PR_ATTACH_METHOD => ATTACH_BY_VALUE, PR_ATTACH_FILENAME => 'notes.txt']);
	}
	foreach ($embedded[$case['embedded']] as $subProps) {
		$sub = null;
		if ($subProps !== null) {
			$sub = newObject('message', $subProps);
			newAttachment($sub, [PR_ATTACH_METHOD => ATTACH_BY_VALUE, PR_ATTACH_FILENAME => 'draft.txt']);
		}
		newAttachment($message, [PR_ATTACH_METHOD => ATTACH_EMBEDDED_MSG], $sub);
	}

	$assigned = [
		PR_MESSAGE_CLASS => 'IPM.Task',
		PR_SUBJECT => 'Write the report',
		PR_ICON_INDEX => ICON_TASK_ASSIGNER,
		PR_SENDER_NAME => 'Assigner',
		$tags['task_goid'] => $goid,
		$tags['taskstate'] => tdsACC,
		$tags['taskhistory'] => thAssigned,
		$tags['taskmode'] => tdmtNothing,
		$tags['taskfcreator'] => true,
		$tags['task_acceptance_state'] => olTaskDelegationUnknown,
		$tags['ownership'] => olDelegatedTask,
		$tags['owner'] => 'Assignee',
		$tags['categories'] => ['Assigner category'],
		$tags['updatecount'] => 1,
	];
	newMessage($tasks, [$tags['task_goid'] => $otherGoid] + $assigned);
	$existing = [
		'none' => [],
		'exists' => [$assigned],
		'newer' => [[$tags['updatecount'] => 2] + $assigned],
		'two' => [$assigned, [PR_SUBJECT => 'Second copy'] + $assigned],
	];
	foreach ($existing[$case['task']] as $taskProps) {
		$id = newMessage($tasks, $taskProps);
		newAttachment($id, [PR_ATTACH_METHOD => ATTACH_BY_VALUE, PR_ATTACH_FILENAME => 'old1.txt']);
		newAttachment($id, [PR_ATTACH_METHOD => ATTACH_BY_VALUE, PR_ATTACH_FILENAME => 'old2.txt']);
	}

	$request = [
		PR_MESSAGE_CLASS => 'IPM.TaskRequest',
		PR_SUBJECT => 'Task Request: Write the report',
		$tags['task_goid'] => $goid,
		$tags['taskstate'] => tdsACC,
		$tags['taskhistory'] => thAssigned,
	];
	newMessage($sent, [$tags['task_goid'] => $otherGoid] + $request);
	for ($i = 0; $i < ['none' => 0, 'one' => 1, 'two' => 2][$case['sent']]; ++$i) {
		newMessage($sent, $request);
	}

	return [
		'request' => new TaskRequest($store, $message, $session),
		'tags' => $tags, 'message' => $message, 'tasks' => $tasks, 'sent' => $sent,
	];
}

$cases = [];
foreach (['Accept', 'Decline', 'Update', 'Complete', 'Unknown'] as $class) {
	foreach ([true, false, 'absent'] as $toMe) {
		foreach (['absent', false, true] as $processed) {
			foreach (['match', 'other', 'broken-then-match', 'none', 'two-other'] as $embedded) {
				foreach ([true, false, 'absent'] as $sub) {
					foreach (['none', 'exists', 'newer', 'two'] as $task) {
						foreach (['one', 'none', 'two'] as $sent) {
							foreach ([1, 'absent'] as $msgCount) {
								foreach (['ok', 'retry', 'fail'] as $copy) {
									$case = compact('class', 'toMe', 'processed', 'embedded', 'sub', 'task', 'sent', 'msgCount', 'copy');
									// the dimensions a path does not reach stay at their first value
									$rest = $processed === true || in_array($embedded, ['none', 'two-other'], true);
									if ($rest && ($sub !== true || $task !== 'none' || $sent !== 'one' || $msgCount !== 1 || $copy !== 'ok')) {
										continue;
									}
									if ($toMe !== true && $sent !== 'one') {
										continue;
									}
									if ($task === 'none' && $msgCount !== 1) {
										continue;
									}
									$cases[] = $case;
								}
							}
						}
					}
				}
			}
		}
	}
}

// the properties the test looks at, with the attachment count
function dumpMessage(int $message): array {
	global $tags;
	$object = $GLOBALS['objects'][$message];
	$wanted = [
		PR_PROCESSED, PR_MESSAGE_CLASS, PR_SUBJECT, PR_SENDER_NAME, PR_MESSAGE_DELIVERY_TIME, PR_ICON_INDEX,
		$tags['task_goid'], $tags['taskhistory'], $tags['taskstate'], $tags['task_acceptance_state'],
		$tags['ownership'], $tags['taskmode'], $tags['taskfcreator'], $tags['status'],
		$tags['percent_complete'], $tags['categories'],
	];

	return [
		'props' => normalize(array_intersect_key($object['props'], array_flip($wanted))),
		'attachments' => count($object['attachments']),
	];
}

$tags = null;
foreach ($cases as $case) {
	$scenario = buildScenario($case);
	if ($tags === null) {
		$tags = $scenario['tags'];
		echo json_encode(['tags' => $tags], JSON_THROW_ON_ERROR), "\n";
	}
	$GLOBALS['log'] = [];

	try {
		$result = $scenario['request']->processTaskResponse();
		$exception = null;
	}
	catch (MAPIException $e) {
		$result = null;
		$exception = [$e::class, $e->getCode()];
	}
	$attachments = [];
	foreach ($GLOBALS['objects'][$scenario['message']]['attachments'] as $id) {
		$attach = $GLOBALS['objects'][$id];
		$attachments[] = [
			'hidden' => $attach['props'][PR_ATTACHMENT_HIDDEN] ?? null,
			'task' => $attach['embedded'] === null ? null :
				normalize($GLOBALS['objects'][$attach['embedded']]['props'][$tags['task_goid']]),
		];
	}
	echo json_encode([
		'case' => $case,
		'result' => $result,
		'exception' => $exception,
		'calls' => array_count_values(array_column($GLOBALS['log'], 0)),
		'log' => $withLog ? $GLOBALS['log'] : null,
		'message' => dumpMessage($scenario['message']),
		'attachments' => $attachments,
		'tasks' => array_map('dumpMessage', $GLOBALS['objects'][$scenario['tasks']]['messages']),
		'sent' => array_map('dumpMessage', $GLOBALS['objects'][$scenario['sent']]['messages']),
	], JSON_THROW_ON_ERROR), "\n";
}
