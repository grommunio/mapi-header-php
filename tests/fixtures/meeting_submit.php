<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n: messages live in memory and behave like gromox does for
// the subject, which is what submitMeetingRequest() has to get right.
// A PHP with gettext compiled in brings _() and ngettext() along.
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

function message_create(): int {
	$GLOBALS['messages'][] = ['props' => [], 'recipients' => []];

	return array_key_last($GLOBALS['messages']);
}

function mapi_getprops($message, ?array $tags = null): array {
	$props = $GLOBALS['messages'][$message]['props'];
	// gromox keeps the prefix and the normalized subject and computes
	// PR_SUBJECT from them, but only when it is asked for by tag
	unset($props[PR_SUBJECT]);
	if ($tags === null) {
		return $props;
	}
	$result = [];
	foreach ($tags as $tag) {
		if ($tag === PR_SUBJECT && isset($props[PR_NORMALIZED_SUBJECT])) {
			$result[$tag] = ($props[PR_SUBJECT_PREFIX] ?? '') . $props[PR_NORMALIZED_SUBJECT];
		}
		elseif (isset($props[$tag])) {
			$result[$tag] = $props[$tag];
		}
	}

	return $result;
}

function mapi_setprops($message, array $props): bool {
	if (isset($props[PR_SUBJECT])) {
		// a subject set on its own replaces both parts; when both parts come
		// along with it, gromox keeps those and the subject reads as their sum
		if (!isset($props[PR_NORMALIZED_SUBJECT], $props[PR_SUBJECT_PREFIX])) {
			$props[PR_NORMALIZED_SUBJECT] = $props[PR_SUBJECT];
			$props[PR_SUBJECT_PREFIX] = '';
		}
		unset($props[PR_SUBJECT]);
	}
	foreach ($props as $tag => $value) {
		if ($value === null) {
			unset($GLOBALS['messages'][$message]['props'][$tag]);
		}
		else {
			$GLOBALS['messages'][$message]['props'][$tag] = $value;
		}
	}

	return true;
}

function mapi_deleteprops($message, array $tags): bool {
	foreach ($tags as $tag) {
		unset($GLOBALS['messages'][$message]['props'][$tag]);
	}

	return true;
}

function mapi_savechanges($message): bool {
	return true;
}

function mapi_message_getrecipienttable($message): array {
	return ['message' => $message, 'rows' => 'recipients'];
}

function mapi_message_getattachmenttable($message): array {
	return ['message' => $message, 'rows' => 'attachments'];
}

function mapi_message_modifyrecipients($message, int $flags, array $rows): bool {
	foreach ($rows as $row) {
		$GLOBALS['messages'][$message]['recipients'][] = $row;
	}

	return true;
}

function restriction_matches(array $restriction, array $row): bool {
	[$type, $res] = $restriction;

	switch ($type) {
		case RES_AND:
			foreach ($res as $sub) {
				if (!restriction_matches($sub, $row)) {
					return false;
				}
			}

			return true;

		case RES_OR:
			foreach ($res as $sub) {
				if (restriction_matches($sub, $row)) {
					return true;
				}
			}

			return false;

		case RES_NOT:
			return !restriction_matches($res[0], $row);

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
			$equal = ($row[$res[ULPROPTAG]] ?? null) == $value;

			return $res[RELOP] == RELOP_NE ? !$equal : $equal;
	}

	throw new RuntimeException('restriction type ' . $type . ' is not modelled');
}

function mapi_table_queryallrows(array $table, ?array $tags = null, ?array $restriction = null): array {
	$rows = $GLOBALS['messages'][$table['message']][$table['rows']] ?? [];
	if ($restriction !== null) {
		$rows = array_values(array_filter($rows, static fn ($row) => restriction_matches($restriction, $row)));
	}
	if ($tags !== null) {
		$rows = array_map(static fn ($row) => array_intersect_key($row, array_flip($tags)), $rows);
	}

	return $rows;
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
	'class.baserecurrence', 'class.recurrence', 'class.meetingrequest',
] as $file) {
	require $argv[1] . '/' . $file . '.php';
}

class SubmitMeetingrequest extends Meetingrequest {
	public array $submitted = [];

	public function createOutgoingMessage(mixed $store = false): mixed {
		$message = message_create();
		mapi_setprops($message, [PR_SENDER_NAME => 'Transport identity']);

		return $message;
	}

	public function submitOutgoingMessage(mixed $outgoing, bool $allowSendAsSelf = false): void {
		$this->submitted[] = [
			'cancellation' => $allowSendAsSelf,
			'props' => mapi_getprops($outgoing, [PR_SUBJECT, PR_SENDER_NAME, PR_MESSAGE_CLASS, PR_IMPORTANCE]) + mapi_getprops($outgoing),
			'recipients' => array_column(mapi_table_queryallrows(mapi_message_getrecipienttable($outgoing)), PR_EMAIL_ADDRESS),
		];
	}
}

$results = [];
foreach ([false, true] as $cancel) {
	foreach ([false, true] as $directBooking) {
		foreach ([false, 'Update: '] as $prefix) {
			foreach ([false, true] as $removeAttendee) {
				$message = message_create();
				$request = new SubmitMeetingrequest(null, $message);
				$request->setDirectBooking($directBooking);
				$tags = $request->proptags;
				mapi_setprops($message, [
					PR_MESSAGE_CLASS => 'IPM.Appointment',
					// gromox splits a subject typed as 'FW: ...' into these two parts
					PR_SUBJECT_PREFIX => 'FW: ',
					PR_NORMALIZED_SUBJECT => 'Meeting workflow test',
					PR_SENDER_NAME => 'Original sender',
					PR_MESSAGE_FLAGS => MSGFLAG_READ | MSGFLAG_UNSENT,
					$tags['startdate'] => 1774864800,
					$tags['duedate'] => 1774868400,
					$tags['categories'] => ['Private category'],
					$tags['busystatus'] => fbBusy,
					$tags['last_updatecounter'] => 3,
				]);
				$recipients = [];
				foreach (['attendee', 'resource', 'organizer', 'removed'] as $name) {
					$email = $name . '@example.invalid';
					$recipients[$name] = [
						PR_ENTRYID => mapi_createoneoff($name, 'SMTP', $email),
						PR_DISPLAY_NAME => $name,
						PR_EMAIL_ADDRESS => $email,
						PR_ADDRTYPE => 'SMTP',
						PR_RECIPIENT_TYPE => $name === 'resource' ? MAPI_BCC : MAPI_TO,
						PR_RECIPIENT_FLAGS => recipSendable | ($name === 'organizer' ? recipOrganizer : 0),
					];
				}
				mapi_message_modifyrecipients($message, MODRECIP_ADD, [
					$recipients['attendee'], $recipients['resource'], $recipients['organizer'],
				]);
				$request->submitMeetingRequest(
					$message,
					$cancel,
					$prefix,
					false,
					false,
					true,
					false,
					$removeAttendee ? [$recipients['attendee'], $recipients['removed']] : false
				);
				$results[] = [
					'cancel' => $cancel,
					'directBooking' => $directBooking,
					'prefix' => $prefix,
					'removeAttendee' => $removeAttendee,
					'tags' => $tags,
					'submitted' => $request->submitted,
					'calendar' => mapi_getprops($message, [
						PR_SUBJECT, PR_MESSAGE_FLAGS, $tags['requestsent'], $tags['meetingstatus'],
						$tags['responsestatus'], $tags['updatecounter'],
					]),
				];
			}
		}
	}
}
echo json_encode($results, JSON_THROW_ON_ERROR);
