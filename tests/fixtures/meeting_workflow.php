<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n: a mailbox in memory with the store, folders, address book
// and attachments the attendee and organizer paths of Meetingrequest touch.
// Every MAPI call is recorded in order with its arguments, and every PHP
// warning along the way, so two trees can be compared call by call.
// Arguments: the library directory and the group of cases to run
// (response, accept, forward or notification). Prints one JSON array.
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

class MapiObject {
	public function __construct(public string $type, public int $id) {}
}

function model_reset(): void {
	$GLOBALS['model'] = ['objects' => [], 'entryids' => [], 'names' => [], 'users' => [], 'calls' => []];
}

function &model_object(MapiObject $object): array {
	return $GLOBALS['model']['objects'][$object->id];
}

function model_create(string $type, array $props = []): MapiObject {
	$id = count($GLOBALS['model']['objects']) + 1;
	$object = new MapiObject($type, $id);
	if (in_array($type, ['message', 'folder', 'store'], true) && !isset($props[PR_ENTRYID])) {
		$props[PR_ENTRYID] = "EID:{$type}:{$id}";
	}
	$GLOBALS['model']['objects'][$id] = [
		'type' => $type,
		'props' => $props,
		'recipients' => [],
		'attachments' => [],
		'messages' => [],
	];
	if (isset($props[PR_ENTRYID])) {
		$GLOBALS['model']['entryids'][$props[PR_ENTRYID]] = $id;
	}

	return $object;
}

/**
 * A property value as the record shows it: binary as hex, and a time taken
 * while running as "now", so that two runs can be compared.
 */
function model_value(mixed $value): mixed {
	if (is_int($value) && abs($value - $_SERVER['REQUEST_TIME']) < 600) {
		return 'now';
	}
	if (is_string($value) && (!preg_match('//u', $value) || preg_match('/[\x00-\x08\x0E-\x1F]/', $value))) {
		return 'bin:' . bin2hex($value);
	}

	return $value;
}

function model_digest(mixed $value): string {
	if ($value instanceof MapiObject) {
		return $value->type . '#' . $value->id;
	}
	if (is_array($value)) {
		$parts = [];
		foreach ($value as $key => $item) {
			$parts[] = (is_int($key) && $key > 0xFFFF ? sprintf('0x%08X', $key) : $key) . '=>' . model_digest($item);
		}

		return '[' . implode(', ', $parts) . ']';
	}
	$value = model_value($value);

	return is_string($value) ? json_encode($value, JSON_THROW_ON_ERROR) : var_export($value, true);
}

function model_record(string $name, array $args): void {
	$GLOBALS['model']['calls'][] = $name . '(' . implode(', ', array_map('model_digest', $args)) . ')';
}

function mapi_load_mapidefs($flags = null): void {}

function mapi_prop_tag(int $type, int $id): int {
	return ($id << 16) | $type;
}

function mapi_prop_id(int $tag): int {
	return $tag >> 16;
}

function mapi_prop_type(int $tag): int {
	return $tag & 0xFFFF;
}

function mapi_last_hresult(): int {
	return 0;
}

function mapi_is_error(int $code): bool {
	return ($code & 0x80000000) != 0;
}

function mapi_ianatz_to_tzdef(string $timezone): false {
	return false;
}

function mapi_getidsfromnames($store, array $ids, array $guids): array {
	model_record(__FUNCTION__, [$store, $ids]);
	$tags = [];
	foreach ($ids as $index => $id) {
		$key = bin2hex($guids[$index]) . ':' . $id;
		if (!isset($GLOBALS['model']['names'][$key])) {
			$GLOBALS['model']['names'][$key] = 0x8000 + count($GLOBALS['model']['names']);
		}
		$tags[] = mapi_prop_tag(PT_UNSPECIFIED, $GLOBALS['model']['names'][$key]);
	}

	return $tags;
}

function mapi_createoneoff(string $name, string $type, string $address, int $flags = 0): string {
	return "ONEOFF:{$type}:{$address}:{$name}";
}

function mapi_getprops(MapiObject $object, ?array $tags = null): array {
	model_record(__FUNCTION__, [$object, $tags]);
	$props = model_object($object)['props'];
	if ($tags === null) {
		return $props;
	}
	$result = [];
	foreach ($tags as $tag) {
		if (isset($props[$tag])) {
			$result[$tag] = $props[$tag];
		}
	}

	return $result;
}

function mapi_setprops(MapiObject $object, array $props): bool {
	model_record(__FUNCTION__, [$object, $props]);
	$data = &model_object($object);
	foreach ($props as $tag => $value) {
		$data['props'][$tag] = $value;
	}

	return true;
}

function mapi_deleteprops(MapiObject $object, array $tags): bool {
	model_record(__FUNCTION__, [$object, $tags]);
	$data = &model_object($object);
	foreach ($tags as $tag) {
		unset($data['props'][$tag]);
	}

	return true;
}

function mapi_savechanges(MapiObject $object): bool {
	model_record(__FUNCTION__, [$object]);

	return true;
}

function mapi_message_getrecipienttable(MapiObject $message): MapiObject {
	model_record(__FUNCTION__, [$message]);

	return model_create('table', ['source' => $message, 'rows' => 'recipients']);
}

function mapi_message_modifyrecipients(MapiObject $message, int $flags, array $rows): bool {
	model_record(__FUNCTION__, [$message, $flags, $rows]);
	$data = &model_object($message);
	foreach ($rows as $row) {
		if ($flags === MODRECIP_ADD) {
			$row[PR_ROWID] = count($data['recipients']) + 1;
			$data['recipients'][] = $row;
		}
		else {
			foreach ($data['recipients'] as $index => $existing) {
				if (($existing[PR_ROWID] ?? null) === ($row[PR_ROWID] ?? false)) {
					if ($flags === MODRECIP_REMOVE) {
						unset($data['recipients'][$index]);
					}
					else {
						$data['recipients'][$index] = $row;
					}
				}
			}
			$data['recipients'] = array_values($data['recipients']);
		}
	}

	return true;
}

function mapi_message_getattachmenttable(MapiObject $message): MapiObject {
	model_record(__FUNCTION__, [$message]);

	return model_create('table', ['source' => $message, 'rows' => 'attachments']);
}

function mapi_message_createattach(MapiObject $message): MapiObject {
	model_record(__FUNCTION__, [$message]);
	$data = &model_object($message);
	$num = count($data['attachments']);
	$attachment = model_create('attachment', [PR_ATTACH_NUM => $num]);
	$data['attachments'][$num] = $attachment;

	return $attachment;
}

function mapi_message_openattach(MapiObject $message, int $num): MapiObject {
	model_record(__FUNCTION__, [$message, $num]);
	$attachment = model_object($message)['attachments'][$num] ?? null;
	if ($attachment === null) {
		throw new MAPIException('attachment not found', MAPI_E_NOT_FOUND);
	}

	return $attachment;
}

function mapi_message_deleteattach(MapiObject $message, int $num): bool {
	model_record(__FUNCTION__, [$message, $num]);
	$data = &model_object($message);
	unset($data['attachments'][$num]);

	return true;
}

function mapi_attach_openobj(MapiObject $attachment, int $flags = 0): MapiObject {
	model_record(__FUNCTION__, [$attachment, $flags]);
	$data = &model_object($attachment);
	if (empty($data['messages'])) {
		$data['messages'][] = model_create('message');
	}

	return $data['messages'][0];
}

function model_restriction_matches(array $restriction, array $row): bool {
	[$type, $res] = $restriction;

	switch ($type) {
		case RES_AND:
			foreach ($res as $sub) {
				if (!model_restriction_matches($sub, $row)) {
					return false;
				}
			}

			return true;

		case RES_OR:
			foreach ($res as $sub) {
				if (model_restriction_matches($sub, $row)) {
					return true;
				}
			}

			return false;

		case RES_NOT:
			return !model_restriction_matches($res[0], $row);

		case RES_EXIST:
			return isset($row[$res[ULPROPTAG]]);

		case RES_BITMASK:
			$bits = ($row[$res[ULPROPTAG]] ?? 0) & $res[ULMASK];

			return $res[ULTYPE] == BMR_EQZ ? $bits == 0 : $bits != 0;

		case RES_PROPERTY:
			$value = $res[VALUE];
			if (is_array($value)) {
				$value = $value[$res[ULPROPTAG]];
			}
			if (!isset($row[$res[ULPROPTAG]])) {
				return false;
			}
			$actual = $row[$res[ULPROPTAG]];

			return match ($res[RELOP]) {
				RELOP_EQ => $actual == $value,
				RELOP_NE => $actual != $value,
				RELOP_GT => $actual > $value,
				RELOP_GE => $actual >= $value,
				RELOP_LT => $actual < $value,
				RELOP_LE => $actual <= $value,
			};

		case RES_CONTENT:
			$value = $res[VALUE];
			if (is_array($value)) {
				$value = $value[$res[ULPROPTAG]];
			}
			$actual = $row[$res[ULPROPTAG]] ?? null;
			if (!is_string($actual)) {
				return false;
			}
			if ($res[FUZZYLEVEL] & FL_IGNORECASE) {
				$actual = strtolower($actual);
				$value = strtolower($value);
			}

			return match ($res[FUZZYLEVEL] & 0xFFFF) {
				FL_PREFIX => str_starts_with($actual, $value),
				FL_SUBSTRING => str_contains($actual, $value),
				default => $actual === $value,
			};
	}

	throw new RuntimeException('restriction type ' . $type . ' is not modelled');
}

function mapi_table_queryallrows(MapiObject $table, ?array $tags = null, ?array $restriction = null): array {
	model_record(__FUNCTION__, [$table, $tags, $restriction]);
	$props = model_object($table)['props'];
	if ($props['rows'] === 'stores') {
		$rows = $props['data'];
	}
	else {
		$source = model_object($props['source']);
		if ($props['rows'] === 'messages') {
			$rows = array_map(static fn (MapiObject $message) => model_object($message)['props'], $source['messages']);
		}
		elseif ($props['rows'] === 'attachments') {
			$rows = array_map(static fn (MapiObject $attachment) => model_object($attachment)['props'], $source['attachments']);
		}
		else {
			$rows = $source['recipients'];
		}
	}
	if ($restriction !== null) {
		$rows = array_values(array_filter($rows, static fn ($row) => model_restriction_matches($restriction, $row)));
	}
	if ($tags !== null) {
		$rows = array_map(static fn ($row) => array_intersect_key($row, array_flip($tags)), $rows);
	}

	return array_values($rows);
}

function mapi_folder_getcontentstable(MapiObject $folder, int $flags = 0): MapiObject {
	model_record(__FUNCTION__, [$folder]);

	return model_create('table', ['source' => $folder, 'rows' => 'messages']);
}

// a case may let the calendar refuse new messages
function model_refuse(MapiObject $folder): void {
	if (($GLOBALS['model']['fail'] ?? null) === 'calendar' &&
		model_object($folder)['props'][PR_ENTRYID] === model_object($GLOBALS['model']['calendar'])['props'][PR_ENTRYID]) {
		throw new MAPIException('calendar refused the message', MAPI_E_CALL_FAILED);
	}
}

function mapi_folder_createmessage(MapiObject $folder, int $flags = 0): MapiObject {
	model_record(__FUNCTION__, [$folder]);
	model_refuse($folder);
	$message = model_create('message', [PR_PARENT_ENTRYID => model_object($folder)['props'][PR_ENTRYID]]);
	$data = &model_object($folder);
	$data['messages'][] = $message;

	return $message;
}

function mapi_folder_copymessages(MapiObject $source, array $entryids, MapiObject $target, int $flags = 0): bool {
	model_refuse($target);
	model_record(__FUNCTION__, [$source, $entryids, $target, $flags]);
	$from = &model_object($source);
	$to = &model_object($target);
	foreach ($from['messages'] as $index => $message) {
		if (in_array(model_object($message)['props'][PR_ENTRYID], $entryids, true)) {
			$to['messages'][] = $message;
			if ($flags & MESSAGE_MOVE) {
				unset($from['messages'][$index]);
			}
		}
	}
	$from['messages'] = array_values($from['messages']);

	return true;
}

function mapi_folder_deletemessages(MapiObject $folder, array $entryids, int $flags = 0): bool {
	model_record(__FUNCTION__, [$folder, $entryids, $flags]);
	$data = &model_object($folder);
	$data['messages'] = array_values(array_filter(
		$data['messages'],
		static fn (MapiObject $message) => !in_array(model_object($message)['props'][PR_ENTRYID], $entryids, true)
	));

	return true;
}

function mapi_msgstore_openentry(MapiObject $store, ?string $entryid = null, int $flags = 0): MapiObject {
	model_record(__FUNCTION__, [$store, $entryid, $flags]);
	if ($entryid === null) {
		return model_object($store)['props']['root'];
	}
	$id = $GLOBALS['model']['entryids'][$entryid] ?? null;
	if ($id === null) {
		throw new MAPIException('entry not found', MAPI_E_NOT_FOUND);
	}

	return new MapiObject($GLOBALS['model']['objects'][$id]['type'], $id);
}

function mapi_msgstore_getreceivefolder(MapiObject $store): MapiObject {
	model_record(__FUNCTION__, [$store]);

	return model_object($store)['props']['inbox'];
}

function mapi_getmsgstorestable(MapiObject $session): MapiObject {
	model_record(__FUNCTION__, [$session]);
	$rows = [];
	foreach (model_object($session)['props']['stores'] as $store) {
		$rows[] = array_intersect_key(model_object($store)['props'], array_flip([PR_ENTRYID, PR_DEFAULT_STORE]));
	}

	return model_create('table', ['rows' => 'stores', 'data' => $rows]);
}

function mapi_openmsgstore(MapiObject $session, string $entryid): MapiObject {
	model_record(__FUNCTION__, [$session, $entryid]);
	foreach (model_object($session)['props']['stores'] as $store) {
		if (model_object($store)['props'][PR_ENTRYID] === $entryid) {
			return $store;
		}
	}

	throw new MAPIException('store not found', MAPI_E_NOT_FOUND);
}

function mapi_openaddressbook(MapiObject $session): MapiObject {
	model_record(__FUNCTION__, [$session]);

	return model_object($session)['props']['addressbook'];
}

function mapi_ab_openentry(MapiObject $addressbook, ?string $entryid = null, int $flags = 0): MapiObject {
	model_record(__FUNCTION__, [$addressbook, $entryid]);
	if (isset($GLOBALS['model']['users'][$entryid])) {
		return $GLOBALS['model']['users'][$entryid];
	}
	if (preg_match('/^ONEOFF:([^:]*):([^:]*):(.*)$/', (string) $entryid, $match)) {
		return model_create('mailuser', [
			PR_ENTRYID => $entryid,
			PR_ADDRTYPE => $match[1],
			PR_EMAIL_ADDRESS => $match[2],
			PR_DISPLAY_NAME => $match[3],
		]);
	}

	throw new MAPIException('address book entry not found', MAPI_E_NOT_FOUND);
}

function mapi_ab_resolvename(MapiObject $addressbook, array $names, int $flags = 0): array {
	model_record(__FUNCTION__, [$addressbook, $names, $flags]);
	$rows = [];
	foreach ($names as $name) {
		$found = null;
		foreach ($GLOBALS['model']['users'] as $user) {
			$props = model_object($user)['props'];
			if (strcasecmp($props[PR_SMTP_ADDRESS], $name[PR_DISPLAY_NAME]) == 0 ||
				strcasecmp($props[PR_DISPLAY_NAME], $name[PR_DISPLAY_NAME]) == 0) {
				$found = $props;
			}
		}
		if ($found === null) {
			throw new MAPIException('name not found', MAPI_E_NOT_FOUND);
		}
		$rows[] = $found;
	}

	return $rows;
}

function mapi_openproperty(MapiObject $object, int $tag, string $iid, int $interfaceflags, int $flags): MapiObject {
	model_record(__FUNCTION__, [$object, $tag]);

	return model_create('stream', ['source' => $object, 'tag' => $tag, 'data' => model_object($object)['props'][$tag] ?? '', 'pos' => 0]);
}

function mapi_stream_stat(MapiObject $stream): array {
	model_record(__FUNCTION__, [$stream]);

	return ['cb' => strlen(model_object($stream)['props']['data'])];
}

function mapi_stream_read(MapiObject $stream, int $length): string {
	model_record(__FUNCTION__, [$stream, $length]);
	$data = &model_object($stream);
	$chunk = substr($data['props']['data'], $data['props']['pos'], $length);
	$data['props']['pos'] += strlen($chunk);

	return $chunk;
}

function mapi_stream_seek(MapiObject $stream, int $offset, int $whence = STREAM_SEEK_SET): bool {
	model_record(__FUNCTION__, [$stream, $offset, $whence]);
	$data = &model_object($stream);
	$data['props']['pos'] = $whence === STREAM_SEEK_END ? strlen($data['props']['data']) + $offset :
		($whence === STREAM_SEEK_CUR ? $data['props']['pos'] + $offset : $offset);

	return true;
}

function mapi_stream_setsize(MapiObject $stream, int $size): bool {
	model_record(__FUNCTION__, [$stream, $size]);
	$data = &model_object($stream);
	$data['props']['data'] = substr(str_pad($data['props']['data'], $size, "\0"), 0, $size);

	return true;
}

function mapi_stream_write(MapiObject $stream, string $chunk): int {
	model_record(__FUNCTION__, [$stream, strlen($chunk)]);
	$data = &model_object($stream);
	$data['props']['data'] = substr($data['props']['data'], 0, $data['props']['pos']) . $chunk .
		substr($data['props']['data'], $data['props']['pos'] + strlen($chunk));
	$data['props']['pos'] += strlen($chunk);

	return strlen($chunk);
}

function mapi_stream_commit(MapiObject $stream): bool {
	model_record(__FUNCTION__, [$stream]);
	$props = model_object($stream)['props'];
	$data = &model_object($props['source']);
	$data['props'][$props['tag']] = $props['data'];

	return true;
}

function mapi_message_submitmessage(MapiObject $message): bool {
	model_record(__FUNCTION__, [$message]);

	return true;
}

function mapi_copyto(MapiObject $source, array $excludeiids, array $excludeprops, MapiObject $target, int $flags = 0): bool {
	model_record(__FUNCTION__, [$source, $excludeprops, $target, $flags]);
	$from = model_object($source);
	$to = &model_object($target);
	foreach ($from['props'] as $tag => $value) {
		if ($tag !== PR_ENTRYID && $tag !== PR_PARENT_ENTRYID && !in_array($tag, $excludeprops, true)) {
			$to['props'][$tag] = $value;
		}
	}
	if (!in_array(PR_MESSAGE_RECIPIENTS, $excludeprops, true)) {
		$to['recipients'] = $from['recipients'];
	}
	if (!in_array(PR_MESSAGE_ATTACHMENTS, $excludeprops, true)) {
		$to['attachments'] = $from['attachments'];
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
	}
}

foreach ([
	'gettext', 'mapi.util', 'mapidefs', 'mapitags', 'mapiguid',
	'class.baseexception', 'class.mapiexception', 'class.recurrenceexception',
	'class.baserecurrence', 'class.recurrence', 'class.meetingrequest', 'class.timezoneutil',
] as $file) {
	require $argv[1] . '/' . $file . '.php';
}

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
	$GLOBALS['model']['calls'][] = 'warning: ' . $message . ' (' . basename($file) . ':' . $line . ')';

	return true;
});

class ModelMeetingrequest extends Meetingrequest {
	// when set, accept() reports its arguments and returns this instead of running
	public mixed $acceptResult = null;

	public function accept(bool $tentative, bool $sendresponse, bool $move, array $proposeNewTimeProps, mixed $body, bool $userAction, mixed $store, mixed $calFolder, mixed $basedate = false): mixed {
		if ($this->acceptResult === null) {
			return parent::accept($tentative, $sendresponse, $move, $proposeNewTimeProps, $body, $userAction, $store, $calFolder, $basedate);
		}
		model_record('Meetingrequest::accept', [$tentative, $sendresponse, $move, $proposeNewTimeProps, $body, $userAction, $store, $calFolder, $basedate]);

		return $this->acceptResult;
	}
}

/**
 * A mailbox: the session with its address book, the default store with its
 * folders, and the users the address book knows.
 */
class Mailbox {
	public MapiObject $session;
	public MapiObject $store;
	public MapiObject $root;
	public MapiObject $inbox;
	public MapiObject $calendar;
	public MapiObject $sentmail;
	public MapiObject $outbox;
	public MapiObject $wastebasket;

	public function __construct() {
		model_reset();
		$users = [
			'user' => ['Test User', 'user@example.invalid'],
			'organizer' => ['Local Organizer', 'organizer@example.invalid'],
			'colleague' => ['Colleague', 'colleague@example.invalid'],
		];
		foreach ($users as $key => [$name, $smtp]) {
			$GLOBALS['model']['users']["AB:{$key}"] = model_create('mailuser', [
				PR_ENTRYID => "AB:{$key}",
				PR_DISPLAY_NAME => $name,
				PR_EMAIL_ADDRESS => '/o=grommunio/ou=first/cn=Recipients/cn=' . $key,
				PR_ADDRTYPE => 'EX',
				PR_SMTP_ADDRESS => $smtp,
				PR_SEARCH_KEY => 'EX:' . strtoupper('/o=grommunio/ou=first/cn=Recipients/cn=' . $key),
				PR_OBJECT_TYPE => MAPI_MAILUSER,
			]);
		}
		$this->session = model_create('session');
		$this->store = model_create('store', [PR_ENTRYID => 'EID:store:default']);
		$folders = [];
		foreach (['root', 'inbox', 'calendar', 'sentmail', 'outbox', 'wastebasket'] as $name) {
			$folders[$name] = model_create('folder', [
				PR_ENTRYID => "EID:folder:{$name}",
				PR_DISPLAY_NAME => $name,
				PR_ACCESS => MAPI_ACCESS_MODIFY | MAPI_ACCESS_READ | MAPI_ACCESS_CREATE_CONTENTS,
			]);
			$this->{$name} = $folders[$name];
		}
		mapi_setprops($this->root, [PR_IPM_APPOINTMENT_ENTRYID => 'EID:folder:calendar']);
		mapi_setprops($this->store, [
			PR_DEFAULT_STORE => true,
			PR_MDB_PROVIDER => ZARAFA_SERVICE_GUID,
			PR_MAILBOX_OWNER_ENTRYID => 'AB:user',
			PR_USER_ENTRYID => 'AB:user',
			PR_IPM_SENTMAIL_ENTRYID => 'EID:folder:sentmail',
			PR_IPM_OUTBOX_ENTRYID => 'EID:folder:outbox',
			PR_IPM_WASTEBASKET_ENTRYID => 'EID:folder:wastebasket',
			'root' => $this->root,
			'inbox' => $this->inbox,
		]);
		mapi_setprops($this->session, [
			'stores' => [$this->store],
			'addressbook' => model_create('addressbook'),
		]);
	}

	public function request(MapiObject $folder, array $props = [], array $without = [], bool $removeRequestOnResponse = false): ModelMeetingrequest {
		$message = mapi_folder_createmessage($folder);
		$request = new ModelMeetingrequest($this->store, $message, $this->session, true, $removeRequestOnResponse);
		$tags = $request->proptags;
		$without = array_map(static fn ($name) => is_string($name) ? $tags[$name] : $name, $without);
		mapi_setprops($message, array_diff_key($props + [
			PR_MESSAGE_CLASS => 'IPM.Schedule.Meeting.Request',
			PR_SUBJECT => 'Team meeting',
			PR_OWNER_APPT_ID => 1234,
			PR_SENT_REPRESENTING_ENTRYID => mapi_createoneoff('Organizer', 'SMTP', 'organizer@example.invalid'),
			PR_SENT_REPRESENTING_NAME => 'Organizer',
			PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'organizer@example.invalid',
			PR_SENT_REPRESENTING_ADDRTYPE => 'SMTP',
			PR_SENT_REPRESENTING_SEARCH_KEY => "SMTP:ORGANIZER@EXAMPLE.INVALID\0",
			PR_SENT_REPRESENTING_SMTP_ADDRESS => 'organizer@example.invalid',
			PR_SENDER_ENTRYID => mapi_createoneoff('Organizer', 'SMTP', 'organizer@example.invalid'),
			PR_RECEIVED_BY_ENTRYID => 'AB:user',
			$tags['goid'] => $this->goid('A'),
			$tags['goid2'] => $this->goid('A'),
			$tags['startdate'] => 1774864800,
			$tags['duedate'] => 1774868400,
			$tags['location'] => 'Room 1',
			$tags['recurring'] => false,
			$tags['updatecounter'] => 2,
			$tags['timezone'] => 'W. Europe Standard Time',
			$tags['timezone_data'] => TimezoneUtil::GetTimezoneStructFromTz(self::berlin()),
		], array_flip($without)));
		mapi_savechanges($message);

		return $request;
	}

	/**
	 * Central European Time as a timezone array: DST from the last Sunday of
	 * March at 2:00 to the last Sunday of October at 3:00.
	 */
	public static function berlin(): array {
		return [
			'bias' => -60, 'stdbias' => 0, 'dstbias' => -60,
			'dstendmonth' => 10, 'dstendday' => 0, 'dstendweek' => 5, 'dstendhour' => 3,
			'dstendminute' => 0, 'dstendsecond' => 0, 'dstendmillis' => 0,
			'dststartmonth' => 3, 'dststartday' => 0, 'dststartweek' => 5, 'dststarthour' => 2,
			'dststartminute' => 0, 'dststartsecond' => 0, 'dststartmillis' => 0,
		];
	}

	public function goid(string $tag): string {
		// a GlobalObjectId without basedate; the creation time and the data are fixed
		return hex2bin('040000008200E00074C5B7101A82E008') . "\0\0\0\0" . str_repeat("\0", 16) . pack('V', 4) . str_pad($tag, 4, '-');
	}

	/**
	 * A daily series of five occurrences from the request's day, 10:00 to 11:00.
	 */
	public function makeRecurring(MapiObject $message, array $tags): Recurrence {
		$recurrence = new Recurrence($this->store, $message);
		$recurrence->setRecurrence(null, [
			'type' => 10, 'subtype' => rptDay, 'everyn' => 1440, 'regen' => 0,
			'term' => 0x22, 'numoccur' => 5, 'start' => 1774828800,
			'end' => 1775174400, 'startocc' => 600, 'endocc' => 660,
		]);
		mapi_setprops($message, [
			$tags['recurring'] => true,
			$tags['clipstart'] => 1774828800,
			$tags['clipend'] => 1775260800,
		]);
		mapi_savechanges($message);

		return $recurrence;
	}

	public function calendarItem(array $props = [], array $recipients = []): MapiObject {
		$item = mapi_folder_createmessage($this->calendar);
		mapi_setprops($item, $props + [PR_MESSAGE_CLASS => 'IPM.Appointment']);
		if ($recipients !== []) {
			mapi_message_modifyrecipients($item, MODRECIP_ADD, $recipients);
		}
		mapi_savechanges($item);

		return $item;
	}

	public function recipient(string $name, string $smtp, int $type = MAPI_TO, array $props = []): array {
		return $props + [
			PR_ENTRYID => mapi_createoneoff($name, 'SMTP', $smtp),
			PR_DISPLAY_NAME => $name,
			PR_EMAIL_ADDRESS => $smtp,
			PR_ADDRTYPE => 'SMTP',
			PR_RECIPIENT_TYPE => $type,
			PR_RECIPIENT_FLAGS => recipSendable,
		];
	}
}

function run_case(array $case, callable $body): array {
	$GLOBALS['model']['calls'] = [];

	try {
		$case['result'] = $body();
	}
	catch (Throwable $e) {
		$case['error'] = $e::class . ': ' . $e->getMessage();
	}
	$case['calls'] = $GLOBALS['model']['calls'];

	return $case;
}

function message_props(MapiObject $message): array {
	return array_map('model_value', model_object($message)['props']);
}

function recipients_of(MapiObject $message): array {
	return array_map(static fn ($row) => array_intersect_key($row, array_flip([PR_EMAIL_ADDRESS, PR_DISPLAY_NAME, PR_ADDRTYPE, PR_RECIPIENT_TYPE, PR_RECIPIENT_FLAGS])), model_object($message)['recipients']);
}

function submitted_messages(Mailbox $mailbox): array {
	$messages = [];
	foreach (model_object($mailbox->outbox)['messages'] as $message) {
		$messages[] = ['props' => message_props($message), 'recipients' => recipients_of($message)];
	}

	return $messages;
}

$results = [];

switch ($argv[2] ?? '') {
	case 'response':
		$sources = ['single', 'recurring-no-series', 'recurring-series', 'exception', 'item-occurrence', 'item-occurrence-deleted', 'item-series'];
		foreach ([olResponseAccepted, olResponseDeclined, olResponseTentative, 99] as $status) {
			foreach ([false, true] as $proposal) {
				foreach ([false, 'A note'] as $body) {
					foreach ([false, 'text', 'html'] as $mti) {
						foreach ($sources as $source) {
							$mailbox = new Mailbox();
							$request = $mailbox->request($mailbox->inbox);
							$tags = $request->proptags;
							$basedate = false;
							if (str_starts_with($source, 'recurring') || str_starts_with($source, 'item')) {
								$recurrence = $mailbox->makeRecurring($request->message, $tags);
							}
							if ($source === 'recurring-series' || $source === 'item-series') {
								$series = $mailbox->calendarItem([$tags['goid'] => $mailbox->goid('A'), $tags['goid2'] => $mailbox->goid('A')]);
								(new Recurrence($mailbox->store, $series))->setRecurrence(null, $recurrence->getRecurrence());
							}
							if ($source === 'exception') {
								$basedate = 1774915200;
								mapi_setprops($request->message, [
									$tags['is_exception'] => true,
									$tags['goid'] => $request->setBasedateInGlobalID($mailbox->goid('A'), $basedate),
								]);
							}
							if (str_starts_with($source, 'item')) {
								mapi_setprops($request->message, [
									PR_MESSAGE_CLASS => 'IPM.Appointment',
									$tags['meetingstatus'] => olMeetingReceived,
									$tags['responsestatus'] => olResponseNotResponded,
								]);
								$basedate = $source === 'item-series' ? false : 1774915200;
							}
							if ($source === 'item-occurrence') {
								$recurrence->createException([
									$tags['startdate'] => 1774954800,
									$tags['duedate'] => 1774958400,
									$tags['location'] => 'Room 2',
									PR_SUBJECT => 'Moved occurrence',
									$tags['meetingstatus'] => olMeetingReceived,
									$tags['responsestatus'] => olResponseAccepted,
								], $basedate);
							}
							if ($mti !== false) {
								$request->setMeetingTimeInfo('When: tomorrow', $mti === 'html');
							}
							$proposeNewTimeProps = $proposal ? [
								$tags['proposed_start_whole'] => 1774872000,
								$tags['proposed_end_whole'] => 1774875600,
								$tags['proposed_duration'] => 60,
								$tags['counter_proposal'] => true,
							] : [];
							$results[] = run_case(
								['status' => $status, 'proposal' => $proposal, 'body' => $body, 'mti' => $mti, 'source' => $source, 'tags' => $tags],
								function () use ($request, $status, $proposeNewTimeProps, $body, $mailbox, $basedate) {
									$request->createResponse($status, $proposeNewTimeProps, $body, $mailbox->store, $basedate, $mailbox->calendar);

									return submitted_messages($mailbox);
								}
							);
						}
					}
				}
			}
		}
		break;

	case 'accept':
		// accept() itself is scripted: these cases are about what doAccept() decides around it
		$cases = [];
		foreach (['IPM.Schedule.Meeting.Request', 'IPM.Appointment'] as $class) {
			foreach ([false, true] as $userAction) {
				foreach (['unset', true, false] as $processed) {
					foreach (['none', 'processed', 'unprocessed'] as $calItem) {
						foreach ([false, true] as $isImported) {
							foreach ([false, true] as $selfSent) {
								$cases[] = compact('class', 'userAction', 'processed', 'calItem', 'isImported', 'selfSent') + ['extra' => ''];
							}
						}
					}
				}
			}
		}
		foreach ([false, true] as $userAction) {
			foreach (['sentItems', 'proposal', 'outOfDate', 'basedate', 'notAccepted', 'removeRequest', 'localOrganiser'] as $extra) {
				$class = in_array($extra, ['removeRequest', 'localOrganiser'], true) ? 'IPM.Appointment' : 'IPM.Schedule.Meeting.Request';
				$cases[] = ['class' => $class, 'userAction' => $userAction, 'processed' => 'unset', 'calItem' => $extra === 'outOfDate' ? 'unprocessed' : 'none', 'isImported' => false, 'selfSent' => false, 'extra' => $extra];
			}
		}
		foreach ($cases as $case) {
			$mailbox = new Mailbox();
			$folder = $case['extra'] === 'sentItems' ? $mailbox->sentmail : ($case['class'] === 'IPM.Appointment' ? $mailbox->calendar : $mailbox->inbox);
			$props = [PR_MESSAGE_CLASS => $case['class']];
			if ($case['processed'] !== 'unset') {
				$props[PR_PROCESSED] = $case['processed'];
			}
			if ($case['selfSent']) {
				$props[PR_SENT_REPRESENTING_ENTRYID] = 'AB:user';
			}
			$request = $mailbox->request($folder, $props, [], $case['extra'] === 'removeRequest');
			$tags = $request->proptags;
			if ($case['class'] === 'IPM.Appointment') {
				mapi_setprops($request->message, [
					$tags['meetingstatus'] => olMeetingReceived,
					$tags['responsestatus'] => $case['extra'] === 'localOrganiser' ? olResponseOrganized : olResponseNotResponded,
				]);
			}
			if ($case['calItem'] !== 'none') {
				$mailbox->calendarItem([
					$tags['goid'] => $mailbox->goid('A'),
					$tags['goid2'] => $mailbox->goid('A'),
					$tags['recurring'] => false,
					$tags['updatecounter'] => $case['extra'] === 'outOfDate' ? 5 : 2,
				] + ($case['calItem'] === 'processed' ? [PR_PROCESSED => true] : []));
			}
			if ($case['extra'] === 'removeRequest') {
				// the request the calendar item was accepted from is still in the inbox
				$mailbox->request($mailbox->inbox);
			}
			$request->acceptResult = $case['extra'] === 'notAccepted' ? false : 'EID:calendar:accepted';
			$results[] = run_case($case + ['tags' => $tags], function () use ($request, $case, $mailbox) {
				$proposal = $case['extra'] === 'proposal';
				$return = $request->doAccept(
					!$case['userAction'],
					$case['userAction'],
					false,
					$proposal ? 1774872000 : false,
					$proposal ? 1774875600 : false,
					false,
					$case['userAction'],
					false,
					$case['extra'] === 'basedate' ? 1774915200 : false,
					$case['isImported']
				);

				return [
					'return' => $return,
					'request' => message_props($request->message),
					'inbox' => count(model_object($mailbox->inbox)['messages']),
					'wastebasket' => count(model_object($mailbox->wastebasket)['messages']),
				];
			});
		}
		break;

	case 'forward':
		$cases = [];
		foreach (['none', 'external', 'local', 'self'] as $organizer) {
			foreach (['empty', 'bcc', 'plain', 'named', 'mixed'] as $recipients) {
				foreach (['single', 'series', 'occurrence', 'occurrence-exception', 'occurrence-deleted'] as $source) {
					foreach (['', 'Room 1'] as $location) {
						$cases[] = compact('organizer', 'recipients', 'source', 'location') + ['tz' => 'struct'];
					}
				}
			}
		}
		foreach (['none', 'tzdef'] as $tz) {
			$cases[] = ['organizer' => 'local', 'recipients' => 'named', 'source' => 'single', 'location' => 'Room 1', 'tz' => $tz];
		}
		foreach ($cases as $case) {
			$mailbox = new Mailbox();
			$props = [];
			$without = [];

			switch ($case['organizer']) {
				case 'none':
					$without = [PR_SENT_REPRESENTING_ENTRYID, PR_SENT_REPRESENTING_NAME, PR_SENT_REPRESENTING_EMAIL_ADDRESS,
						PR_SENT_REPRESENTING_ADDRTYPE, PR_SENT_REPRESENTING_SEARCH_KEY, PR_SENT_REPRESENTING_SMTP_ADDRESS];
					break;

				case 'external':
					$props = [PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'ext@elsewhere.invalid', PR_SENT_REPRESENTING_SMTP_ADDRESS => 'ext@elsewhere.invalid'];
					break;

				case 'self':
					$props = [PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'user@example.invalid', PR_SENT_REPRESENTING_SMTP_ADDRESS => 'user@example.invalid'];
					break;
			}
			if ($case['tz'] === 'none') {
				$without[] = 'timezone';
				$without[] = 'timezone_data';
			}
			$request = $mailbox->request($mailbox->inbox, $props, $without);
			$tags = $request->proptags;
			mapi_setprops($request->message, [$tags['location'] => $case['location']]);
			if ($case['tz'] === 'tzdef') {
				mapi_setprops($request->message, [$tags['tzdefstart'] => TimezoneUtil::GetTimezoneDefinitionFromTz(Mailbox::berlin(), 'W. Europe Standard Time')]);
			}
			$basedate = false;
			if ($case['source'] !== 'single') {
				$recurrence = $mailbox->makeRecurring($request->message, $tags);
			}
			if (str_starts_with($case['source'], 'occurrence')) {
				$basedate = 1774915200;
			}
			if ($case['source'] === 'occurrence-exception') {
				$recurrence->createException([
					$tags['startdate'] => 1774954800,
					$tags['duedate'] => 1774958400,
					$tags['location'] => 'Room 2',
					PR_SUBJECT => 'Moved occurrence',
				], $basedate);
			}
			elseif ($case['source'] === 'occurrence-deleted') {
				$recurrence->createException([], $basedate, true);
			}
			$recipients = match ($case['recipients']) {
				'empty' => [],
				'bcc' => [$mailbox->recipient('Hidden', 'hidden@example.invalid', MAPI_BCC)],
				'plain' => [$mailbox->recipient('', 'plain@example.invalid')],
				'named' => [$mailbox->recipient('Named Person', 'named@example.invalid')],
				'mixed' => [
					$mailbox->recipient('Named Person', 'named@example.invalid'),
					$mailbox->recipient('Hidden', 'hidden@example.invalid', MAPI_BCC),
					$mailbox->recipient('same@example.invalid', 'same@example.invalid', MAPI_CC),
					[PR_ENTRYID => 'AB:colleague', PR_DISPLAY_NAME => 'Colleague', PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => '/o=grommunio/ou=first/cn=Recipients/cn=colleague', PR_RECIPIENT_TYPE => MAPI_TO],
					[PR_ENTRYID => 'AB:unknown', PR_DISPLAY_NAME => 'Gone', PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => '/o=grommunio/ou=first/cn=Recipients/cn=gone', PR_RECIPIENT_TYPE => MAPI_TO],
				],
			};
			$results[] = run_case($case + ['tags' => $tags], function () use ($request, $recipients, $basedate, $mailbox) {
				$request->sendMeetingForwardNotification($recipients, $basedate);

				return submitted_messages($mailbox);
			});
		}
		break;

	case 'notification-many':
		// a forward to many addresses: duplicates in different case, blanks,
		// attendees already on the meeting and one local user among them
		$count = (int) ($argv[3] ?? 300);
		$mailbox = new Mailbox();
		$request = $mailbox->request($mailbox->inbox, [PR_MESSAGE_CLASS => 'IPM.Schedule.Meeting.Notification.Forward']);
		$tags = $request->proptags;
		$addresses = [];
		$names = [];
		for ($i = 0; $i < $count; ++$i) {
			$addresses[] = "person{$i}@example.invalid";
			$names[] = "Person {$i}";
		}
		foreach ([0, 1, 2] as $i) {
			$addresses[] = strtoupper("person{$i}@example.invalid");
			$names[] = "Person {$i} again";
		}
		$addresses[] = ' ';
		$names[] = 'Blank';
		$addresses[] = 'attendee@example.invalid';
		$names[] = 'Attendee';
		$addresses[] = 'colleague@example.invalid';
		$names[] = 'Colleague';
		mapi_setprops($request->message, [$tags['forward_recipients'] => $addresses, $tags['forward_recipient_names'] => $names]);
		$attendees = [$mailbox->recipient('Attendee', 'attendee@example.invalid'), $mailbox->recipient('Person 5', 'person5@example.invalid')];
		$item = $mailbox->calendarItem([
			$tags['goid'] => $mailbox->goid('A'),
			$tags['goid2'] => $mailbox->goid('A'),
			$tags['meetingstatus'] => olMeeting,
			$tags['responsestatus'] => olResponseOrganized,
			$tags['recurring'] => false,
			$tags['startdate'] => 1774864800,
			$tags['duedate'] => 1774868400,
		], $attendees);
		$results[] = run_case(['count' => $count, 'tags' => $tags], function () use ($request, $item) {
			$start = hrtime(true);
			$return = $request->processMeetingForwardNotification();

			return [
				'return' => $return,
				'seconds' => (hrtime(true) - $start) / 1e9,
				'calendar' => array_column(recipients_of($item), PR_EMAIL_ADDRESS),
			];
		});
		break;

	case 'accept-response':
		// accept() runs for real here: the response may only go out once the meeting is stored
		foreach (['single', 'single-move', 'recurring', 'calendar-refuses', 'calendar-refuses-move'] as $name) {
			$mailbox = new Mailbox();
			$request = $mailbox->request($mailbox->inbox);
			$tags = $request->proptags;
			if ($name === 'recurring') {
				$mailbox->makeRecurring($request->message, $tags);
			}
			$GLOBALS['model']['calendar'] = $mailbox->calendar;
			$GLOBALS['model']['fail'] = str_starts_with($name, 'calendar-refuses') ? 'calendar' : null;
			$case = run_case(['name' => $name, 'tags' => $tags], function () use ($request, $name) {
				return $request->doAccept(false, true, str_ends_with($name, '-move'), false, false, false, true);
			});
			$case['calendar'] = array_map('model_digest', model_object($mailbox->calendar)['messages']);
			$case['outbox'] = submitted_messages($mailbox);
			$results[] = $case;
		}
		$GLOBALS['model']['fail'] = null;
		break;

	case 'notification':
		$names = ['not-notification', 'processed', 'no-goid', 'in-sentmail', 'in-outbox', 'no-calendar-item', 'not-organized',
			'single-new', 'single-known', 'single-local', 'single-no-names', 'series', 'occurrence-deleted', 'occurrence-new',
			'occurrence-off-pattern', 'occurrence-exception', 'occurrence-exception-no-recipients'];
		foreach ($names as $name) {
			$mailbox = new Mailbox();
			$folder = match ($name) {
				'in-sentmail' => $mailbox->sentmail,
				'in-outbox' => $mailbox->outbox,
				default => $mailbox->inbox,
			};
			$request = $mailbox->request($folder, [
				PR_MESSAGE_CLASS => $name === 'not-notification' ? 'IPM.Note' : 'IPM.Schedule.Meeting.Notification.Forward',
			], $name === 'no-goid' ? ['goid'] : []);
			$tags = $request->proptags;
			$forwarded = [$tags['forward_recipients'] => ['new1@example.invalid', 'new2@example.invalid'], $tags['forward_recipient_names'] => ['New One', 'New Two']];
			if ($name === 'single-local') {
				$forwarded = [$tags['forward_recipients'] => ['colleague@example.invalid'], $tags['forward_recipient_names'] => ['Colleague']];
			}
			elseif ($name === 'single-no-names') {
				$forwarded = [$tags['forward_recipients'] => ['new1@example.invalid', ' ', 'new2@example.invalid'], $tags['forward_recipient_names'] => []];
			}
			mapi_setprops($request->message, $forwarded + ($name === 'processed' ? [PR_PROCESSED => true] : []));
			$basedate = str_starts_with($name, 'occurrence') ? ($name === 'occurrence-off-pattern' ? 1774828800 + 10 * 86400 : 1774915200) : false;
			if ($basedate !== false) {
				mapi_setprops($request->message, [$tags['goid'] => $request->setBasedateInGlobalID($mailbox->goid('A'), $basedate)]);
			}
			$attendees = [$mailbox->recipient('Attendee', 'attendee@example.invalid')];
			if ($name === 'single-known') {
				$attendees[] = $mailbox->recipient('Known', 'new1@example.invalid');
			}
			$item = null;
			if ($name !== 'no-calendar-item') {
				$item = $mailbox->calendarItem([
					$tags['goid'] => $mailbox->goid('A'),
					$tags['goid2'] => $mailbox->goid('A'),
					$tags['meetingstatus'] => olMeeting,
					$tags['responsestatus'] => $name === 'not-organized' ? olResponseNotResponded : olResponseOrganized,
					$tags['recurring'] => false,
					$tags['startdate'] => 1774864800,
					$tags['duedate'] => 1774868400,
					$tags['timezone_data'] => TimezoneUtil::GetTimezoneStructFromTz(Mailbox::berlin()),
				], $attendees);
				if ($name === 'series' || $basedate !== false) {
					$recurrence = $mailbox->makeRecurring($item, $tags);
					if ($name === 'occurrence-deleted') {
						$recurrence->createException([], $basedate, true);
					}
					elseif (str_starts_with($name, 'occurrence-exception')) {
						$recurrence->createException(
							[$tags['startdate'] => 1774954800, $tags['duedate'] => 1774958400],
							$basedate,
							false,
							$name === 'occurrence-exception' ? $attendees : []
						);
					}
				}
			}
			$results[] = run_case(['name' => $name, 'tags' => $tags], function () use ($request, $item) {
				$return = $request->processMeetingForwardNotification();
				$exceptions = [];
				if ($item !== null) {
					foreach (model_object($item)['attachments'] as $attachment) {
						foreach (model_object($attachment)['messages'] as $exception) {
							$exceptions[] = recipients_of($exception);
						}
					}
				}

				return [
					'return' => $return,
					'processed' => model_object($request->message)['props'][PR_PROCESSED] ?? null,
					'calendar' => $item === null ? [] : recipients_of($item),
					'exceptions' => $exceptions,
				];
			});
		}
		break;
}

echo json_encode($results, JSON_THROW_ON_ERROR);
