<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2005-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2024 grommunio GmbH
 */

define('NOERROR', 0);
define('SECONDS_PER_DAY', 86400);

// Load all mapi defs
mapi_load_mapidefs(1);

/**
 * Function to make a MAPIGUID from a php string.
 * The C++ definition for the GUID is:
 *  typedef struct _GUID
 *  {
 *   unsigned long        Data1;
 *   unsigned short       Data2;
 *   unsigned short       Data3;
 *   unsigned char        Data4[8];
 *  } GUID;.
 *
 * A GUID is normally represented in the following form:
 *  {00062008-0000-0000-C000-000000000046}
 *
 * @param string $guid
 */
function makeGuid($guid): string {
	return pack("vvvv", hexdec(substr($guid, 5, 4)), hexdec(substr($guid, 1, 4)), hexdec(substr($guid, 10, 4)), hexdec(substr($guid, 15, 4))) . hex2bin(substr($guid, 20, 4)) . hex2bin(substr($guid, 25, 12));
}

/**
 * Function to get a human readable string from a MAPI error code.
 *
 * @return string The defined name for the MAPI error code
 */
function get_mapi_error_name(mixed $errcode = null): string {
	if ($errcode === null) {
		$errcode = mapi_last_hresult();
	}

	if (str_starts_with((string) $errcode, '0x') || str_starts_with((string) $errcode, '0X')) {
		$errcode = hexdec($errcode);
	}

	if ($errcode !== 0) {
		// Retrieve constants categories, MAPI error names are defined in gromox.
		foreach (get_defined_constants(true)['Core'] as $key => $value) {
			/*
			 * If PHP encounters a number beyond the bounds of the integer type,
			 * it will be interpreted as a float instead, so when comparing these error codes
			 * we have to manually typecast value to integer, so float will be converted in integer,
			 * but still its out of bound for integer limit so it will be auto adjusted to minus value
			 */
			if ($errcode == (int) $value) {
				// Check that we have an actual MAPI error or warning definition
				$prefix = substr($key, 0, 7);
				if ($prefix === "MAPI_E_" || $prefix === "MAPI_W_") {
					return $key;
				}
				$prefix = substr($key, 0, 2);
				if ($prefix === "ec") {
					return $key;
				}
			}
		}
	}
	else {
		return "NOERROR";
	}

	// error code not found, return hex value (this is a fix for 64-bit systems, we can't use the dechex() function for this)
	$result = unpack("H*", pack("N", $errcode));

	return "0x" . $result[1];
}

/**
 * Parses properties from an array of strings. Each "string" may be either an ULONG, which is a direct property ID,
 * or a string with format "PT_TYPE:{GUID}:StringId" or "PT_TYPE:{GUID}:0xXXXX" for named
 * properties.
 *
 * @return array
 */
function getPropIdsFromStrings(mixed $store, array $mapping): array {
	$props = [];

	$ids = ["name" => [], "id" => [], "guid" => [], "type" => []]; // this array stores all the information needed to retrieve a named property
	$num = 0;

	// caching
	$guids = [];

	foreach ($mapping as $name => $val) {
		if (is_string($val)) {
			$split = explode(":", $val);

			if (count($split) != 3) { // invalid string, ignore
				trigger_error(sprintf("Invalid property: %s \"%s\"", $name, $val), E_USER_NOTICE);

				continue;
			}

			if (str_starts_with($split[2], "0x")) {
				$id = hexdec(substr($split[2], 2));
			}
			elseif (preg_match('/^[1-9][0-9]{0,12}$/', $split[2])) {
				$id = (int) $split[2];
			}
			else {
				$id = $split[2];
			}

			// have we used this guid before?
			if (!defined($split[1])) {
				if (!array_key_exists($split[1], $guids)) {
					$guids[$split[1]] = makeGuid($split[1]);
				}
				$guid = $guids[$split[1]];
			}
			else {
				$guid = constant($split[1]);
			}

			// temp store info about named prop, so we have to call mapi_getidsfromnames just one time
			$ids["name"][$num] = $name;
			$ids["id"][$num] = $id;
			$ids["guid"][$num] = $guid;
			$ids["type"][$num] = $split[0];
			++$num;
		}
		else {
			// not a named property
			$props[$name] = $val;
		}
	}

	if (empty($ids["id"])) {
		return $props;
	}

	// get the ids
	$named = mapi_getidsfromnames($store, $ids["id"], $ids["guid"]);
	foreach ($named as $num => $prop) {
		$props[$ids["name"][$num]] = mapi_prop_tag(constant($ids["type"][$num]), mapi_prop_id($prop));
	}

	return $props;
}

/**
 * Check whether a call to mapi_getprops returned errors for some properties.
 * mapi_getprops function tries to get values of properties requested but somehow if
 * if a property value can not be fetched then it changes type of property tag as PT_ERROR
 * and returns error for that particular property, probable errors
 * that can be returned as value can be MAPI_E_NOT_FOUND, MAPI_E_NOT_ENOUGH_MEMORY.
 *
 * @return false|mixed Gives back false when there is no error, if there is, gives the error
 */
function propIsError(int $property, array $propArray): mixed {
	if (array_key_exists(mapi_prop_tag(PT_ERROR, mapi_prop_id($property)), $propArray)) {
		return $propArray[mapi_prop_tag(PT_ERROR, mapi_prop_id($property))];
	}

	return false;
}

/**
 * Note: Static function, more like a utility function.
 *
 * Gets all the items (including recurring items) in the specified calendar in the given timeframe. Items are
 * included as a whole if they overlap the interval <$start, $end> (non-inclusive). This means that if the interval
 * is <08:00 - 14:00>, the item [6:00 - 8:00> is NOT included, nor is the item [14:00 - 16:00>. However, the item
 * [7:00 - 9:00> is included as a whole, and is NOT capped to [8:00 - 9:00>.
 *
 * @param resource $store          The store in which the calendar resides
 * @param resource $calendar       The calendar to get the items from
 * @param int      $viewstart      Timestamp of beginning of view window
 * @param int      $viewend        Timestamp of end of view window
 * @param array    $propsrequested Array of properties to return
 *
 * @psalm-return list<mixed>
 */
function getCalendarItems(mixed $store, mixed $calendar, int $viewstart, int $viewend, array $propsrequested): array {
	$result = [];
	$properties = getPropIdsFromStrings($store, [
		"duedate" => "PT_SYSTIME:PSETID_Appointment:" . PidLidAppointmentEndWhole,
		"startdate" => "PT_SYSTIME:PSETID_Appointment:" . PidLidAppointmentStartWhole,
		"enddate_recurring" => "PT_SYSTIME:PSETID_Appointment:" . PidLidClipEnd,
		"recurring" => "PT_BOOLEAN:PSETID_Appointment:" . PidLidRecurring,
		"recurring_data" => "PT_BINARY:PSETID_Appointment:" . PidLidAppointmentRecur,
		"timezone_data" => "PT_BINARY:PSETID_Appointment:" . PidLidTimeZoneStruct,
		"label" => "PT_LONG:PSETID_Appointment:0x8214",
	]);

	// Create a restriction that will discard rows of appointments that are definitely not in our
	// requested time frame

	$table = mapi_folder_getcontentstable($calendar);

	$restriction =
		// OR
		[
			RES_OR,
			[
				[RES_AND,	// Normal items: itemEnd must be after viewStart, itemStart must be before viewEnd
					[
						[
							RES_PROPERTY,
							[
								RELOP => RELOP_GT,
								ULPROPTAG => $properties["duedate"],
								VALUE => $viewstart,
							],
						],
						[
							RES_PROPERTY,
							[
								RELOP => RELOP_LT,
								ULPROPTAG => $properties["startdate"],
								VALUE => $viewend,
							],
						],
					],
				],
				// OR
				[
					RES_PROPERTY,
					[
						RELOP => RELOP_EQ,
						ULPROPTAG => $properties["recurring"],
						VALUE => true,
					],
				],
			],	// EXISTS OR
		];		// global OR

	// Get requested properties, plus whatever we need
	$proplist = [PR_ENTRYID, $properties["recurring"], $properties["recurring_data"], $properties["timezone_data"]];
	$proplist = array_merge($proplist, $propsrequested);

	$rows = mapi_table_queryallrows($table, $proplist, $restriction);

	// $rows now contains all the items that MAY be in the window; a recurring item needs expansion before including in the output.

	foreach ($rows as $row) {
		$items = [];

		if (isset($row[$properties["recurring"]]) && $row[$properties["recurring"]]) {
			// Recurring item
			$rec = new Recurrence($store, $row);

			// GetItems guarantees that the item overlaps the interval <$viewstart, $viewend>
			$occurrences = $rec->getItems($viewstart, $viewend);
			foreach ($occurrences as $occurrence) {
				// The occurrence takes all properties from the main row, but overrides some properties (like start and end obviously)
				$item = $occurrence + $row;
				$items[] = $item;
			}
		}
		else {
			// Normal item, it matched the search criteria and therefore overlaps the interval <$viewstart, $viewend>
			$items[] = $row;
		}

		$result = array_merge($result, $items);
	}

	// All items are guaranteed to overlap the interval <$viewstart, $viewend>. Note that we may be returning a few extra
	// properties that the caller did not request (recurring, etc). This shouldn't be a problem though.
	return $result;
}

/**
 * Compares two entryIds. It is possible to have two different entryIds that should match as they
 * represent the same object (in multiserver environments).
 *
 * @return bool Result of the comparison
 */
function compareEntryIds(mixed $entryId1, mixed $entryId2): bool {
	if (!is_string($entryId1) || !is_string($entryId2)) {
		return false;
	}

	// if normal comparison succeeds then we can directly say that entryids are same
	return $entryId1 === $entryId2;
}

/**
 * Creates a goid from an ical uuid.
 *
 * @return string binary string representation of goid
 */
function getGoidFromUid(string $uid): string {
	return hex2bin("040000008200E00074C5B7101A82E0080000000000000000000000000000000000000000" .
				bin2hex(pack("V", 12 + strlen($uid)) . "vCal-Uid" . pack("V", 1) . $uid));
}

/**
 * Returns zero terminated goid. It is required for backwards compatibility.
 *
 * @return string an OL compatible GlobalObjectID
 */
function getGoidFromUidZero(string $uid): string {
	if (strlen((string) $uid) <= 64) {
		return hex2bin("040000008200E00074C5B7101A82E0080000000000000000000000000000000000000000" .
			bin2hex(pack("V", 13 + strlen((string) $uid)) . "vCal-Uid" . pack("V", 1) . $uid) . "00");
	}

	return hex2bin((string) $uid);
}

/**
 * Creates an ical uuid from a goid.
 *
 * @return null|string ical uuid
 */
function getUidFromGoid(string $goid): ?string {
	// check if "vCal-Uid" is somewhere in outlookid case-insensitive
	$uid = stristr($goid, "vCal-Uid");
	if ($uid !== false) {
		// get the length of the ical id - go back 4 position from where "vCal-Uid" was found
		$begin = unpack("V", substr($goid, strlen($uid) * (-1) - 4, 4));

		// remove "vCal-Uid" and packed "1" and use the ical id length
		return trim(substr($uid, 12, $begin[1] - 12));
	}

	return null;
}

/**
 * Converts a MAPI property tag into a human readable value.
 *
 * This depends on the definition of the property tag in core
 *
 * @example prop2Str(0x0037001e) => 'PR_SUBJECT'
 *
 * @return string the symbolic name of the property tag
 */
function prop2Str(mixed $property): string {
	if (is_int($property)) {
		// Retrieve constants categories, zcore provides them in 'Core'
		foreach (get_defined_constants(true)['Core'] as $key => $value) {
			if ($property === $value && str_starts_with($key, 'PR_')) {
				return $key;
			}
		}

		return sprintf("0x%08X", $property);
	}

	return $property;
}

/**
 * Converts RELOP constant to human readable string.
 *
 * @param int $relOp
 *
 * @return string
 */
function relOpToString(int $relOp): string {
	return match ($relOp) {
		RELOP_LT => "RELOP_LT",
		RELOP_LE => "RELOP_LE",
		RELOP_GT => "RELOP_GT",
		RELOP_GE => "RELOP_GE",
		RELOP_EQ => "RELOP_EQ",
		RELOP_NE => "RELOP_NE",
		RELOP_RE => "RELOP_RE",
		default => "",
	};
}

/**
 * Converts all constants of restriction into a human readable strings.
 *
 * @param mixed $restriction
 */
function simplifyRestriction(mixed $restriction): mixed {
	if (!is_array($restriction)) {
		return $restriction;
	}

	switch ($restriction[0]) {
		case RES_AND:
			$restriction[0] = "RES_AND";
			if (isset($restriction[1][0]) && is_array($restriction[1][0])) {
				foreach ($restriction[1] as &$res) {
					$res = simplifyRestriction($res);
				}
				unset($res);
			}
			elseif (isset($restriction[1]) && $restriction[1]) {
				$restriction[1] = simplifyRestriction($restriction[1]);
			}
			break;

		case RES_OR:
			$restriction[0] = "RES_OR";
			if (isset($restriction[1][0]) && is_array($restriction[1][0])) {
				foreach ($restriction[1] as &$res) {
					$res = simplifyRestriction($res);
				}
				unset($res);
			}
			elseif (isset($restriction[1]) && $restriction[1]) {
				$restriction[1] = simplifyRestriction($restriction[1]);
			}
			break;

		case RES_NOT:
			$restriction[0] = "RES_NOT";
			$restriction[1][0] = simplifyRestriction($restriction[1][0]);
			break;

		case RES_COMMENT:
			$restriction[0] = "RES_COMMENT";
			$res = simplifyRestriction($restriction[1][RESTRICTION]);
			$props = $restriction[1][PROPS];

			foreach ($props as &$prop) {
				$propTag = $prop[ULPROPTAG];
				$propValue = $prop[VALUE];

				unset($prop);

				$prop["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
				$prop["VALUE"] = is_array($propValue) ? $propValue[$propTag] : $propValue;
			}
			unset($prop, $restriction[1]);

			$restriction[1]["RESTRICTION"] = $res;
			$restriction[1]["PROPS"] = $props;
			break;

		case RES_PROPERTY:
			$restriction[0] = "RES_PROPERTY";
			$propTag = $restriction[1][ULPROPTAG];
			$propValue = $restriction[1][VALUE];
			$relOp = $restriction[1][RELOP];

			unset($restriction[1]);

			$restriction[1]["RELOP"] = relOpToString($relOp);
			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			$restriction[1]["VALUE"] = is_array($propValue) ? $propValue[$propTag] : $propValue;
			break;

		case RES_CONTENT:
			$restriction[0] = "RES_CONTENT";
			$propTag = $restriction[1][ULPROPTAG];
			$propValue = $restriction[1][VALUE];
			$fuzzyLevel = $restriction[1][FUZZYLEVEL];

			unset($restriction[1]);

			// fuzzy level flags
			$levels = [];

			if (($fuzzyLevel & FL_SUBSTRING) == FL_SUBSTRING) {
				$levels[] = "FL_SUBSTRING";
			}
			elseif (($fuzzyLevel & FL_PREFIX) == FL_PREFIX) {
				$levels[] = "FL_PREFIX";
			}
			else {
				$levels[] = "FL_FULLSTRING";
			}

			if (($fuzzyLevel & FL_IGNORECASE) == FL_IGNORECASE) {
				$levels[] = "FL_IGNORECASE";
			}

			if (($fuzzyLevel & FL_IGNORENONSPACE) == FL_IGNORENONSPACE) {
				$levels[] = "FL_IGNORENONSPACE";
			}

			if (($fuzzyLevel & FL_LOOSE) == FL_LOOSE) {
				$levels[] = "FL_LOOSE";
			}

			$fuzzyLevelFlags = implode(" | ", $levels);

			$restriction[1]["FUZZYLEVEL"] = $fuzzyLevelFlags;
			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			$restriction[1]["VALUE"] = is_array($propValue) ? $propValue[$propTag] : $propValue;
			break;

		case RES_COMPAREPROPS:
			$propTag1 = $restriction[1][ULPROPTAG1];
			$propTag2 = $restriction[1][ULPROPTAG2];

			unset($restriction[1]);

			$restriction[1]["ULPROPTAG1"] = is_string($propTag1) ? $propTag1 : prop2Str($propTag1);
			$restriction[1]["ULPROPTAG2"] = is_string($propTag2) ? $propTag2 : prop2Str($propTag2);
			break;

		case RES_BITMASK:
			$restriction[0] = "RES_BITMASK";
			$propTag = $restriction[1][ULPROPTAG];
			$maskType = $restriction[1][ULTYPE];
			$maskValue = $restriction[1][ULMASK];

			unset($restriction[1]);

			// relop flags
			$maskTypeFlags = "";
			if ($maskType == BMR_EQZ) {
				$maskTypeFlags = "BMR_EQZ";
			}
			elseif ($maskType == BMR_NEZ) {
				$maskTypeFlags = "BMR_NEZ";
			}

			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			$restriction[1]["ULTYPE"] = $maskTypeFlags;
			$restriction[1]["ULMASK"] = $maskValue;
			break;

		case RES_SIZE:
			$restriction[0] = "RES_SIZE";
			$propTag = $restriction[1][ULPROPTAG];
			$propValue = $restriction[1][CB];
			$relOp = $restriction[1][RELOP];

			unset($restriction[1]);

			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			$restriction[1]["RELOP"] = relOpToString($relOp);
			$restriction[1]["CB"] = $propValue;
			break;

		case RES_EXIST:
			$propTag = $restriction[1][ULPROPTAG];

			unset($restriction[1]);

			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			break;

		case RES_SUBRESTRICTION:
			$propTag = $restriction[1][ULPROPTAG];
			$res = simplifyRestriction($restriction[1][RESTRICTION]);

			unset($restriction[1]);

			$restriction[1]["ULPROPTAG"] = is_string($propTag) ? $propTag : prop2Str($propTag);
			$restriction[1]["RESTRICTION"] = $res;
			break;
	}

	return $restriction;
}
