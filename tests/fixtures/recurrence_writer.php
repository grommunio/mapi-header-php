<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run without php-mapi to capture exactly what saveRecurrence writes.
function mapi_load_mapidefs($flags): void {}

function mapi_getprops($message, $tags): array {
	return [];
}

function mapi_setprops($message, $props): bool {
	$GLOBALS['written'] = $props;

	return true;
}

require $argv[1] . '/mapi.util.php';
require $argv[1] . '/mapidefs.php';
require $argv[1] . '/class.baserecurrence.php';

class WriterRecurrence extends BaseRecurrence {
	public function __construct() {
		$this->message = true;
		$this->proptags = array_flip([
			'reminder_time', 'startdate', 'commonstart', 'duedate', 'commonend',
			'message_class', 'startdate_recurring', 'enddate_recurring',
			'recurrencetype', 'side_effects', 'reminder_minutes', 'flagdueby',
			'reminder', 'recurring_data', 'recurring', 'meetingrecurring',
		]);
		$this->messageprops = [5 => 'IPM.Appointment'];
	}

	public function processOccurrenceItem(array &$items, false|int $start, int $end, false|int $basedate, mixed $startocc, mixed $endocc, mixed $tz, mixed $reminderonly): ?false {
		return null;
	}
}

$results = [];
foreach (json_decode(file_get_contents('php://stdin'), true, 512, JSON_THROW_ON_ERROR) as $case) {
	$recurrence = new WriterRecurrence();
	$recurrence->recur = $case;
	$written = null;
	$recurrence->saveRecurrence();
	if ($written !== null) {
		$written[13] = bin2hex($written[13]);
	}
	$results[] = [$recurrence->recur, $written];
}
echo json_encode($results, JSON_THROW_ON_ERROR);
