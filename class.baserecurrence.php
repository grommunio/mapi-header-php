<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2005-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2026 grommunio GmbH
 */

/**
 * BaseRecurrence
 * this class is superclass for recurrence for appointments and tasks. This class provides all
 * basic features of recurrence.
 */
abstract class BaseRecurrence {
	/**
	 * @var mixed Mapi Message (may be null if readonly)
	 */
	public $message;

	/**
	 * @var array Message Properties
	 */
	public $messageprops;

	/**
	 * @var array list of property tags
	 */
	public $proptags;

	/**
	 * @var mixed recurrence data of this calendar item
	 */
	public $recur;

	/**
	 * @var mixed Timezone data of this calendar item
	 */
	public $tz;

	/**
	 * @var array Cache for gmtime() results keyed by timestamp.
	 */
	private array $gmtimeCache = [];

	/**
	 * @var array Cache for daysInMonth() results keyed by "date:months".
	 */
	private array $daysInMonthCache = [];

	/**
	 * @var array Cache for DST boundaries keyed by tm_year and the DST rules.
	 */
	private array $dstBoundaryCache = [];

	/**
	 * var int First day of week set by the user (default: 1 - Monday)
	 */
	private int $firstDayOfWeek = 1;
	/**
	 * Constructor.
	 *
	 * @param resource $store   MAPI Message Store Object
	 * @param mixed    $message the MAPI (appointment) message
	 */
	public function __construct(public $store, mixed $message) {
		if (is_array($message)) {
			$this->messageprops = $message;
		}
		else {
			$this->message = $message;
			$this->messageprops = mapi_getprops($this->message, $this->proptags);
		}

		if (isset($this->messageprops[$this->proptags["recurring_data"]])) {
			// There is a possibility that recurr blob can be more than 255 bytes so get full blob through stream interface
			if (strlen((string) $this->messageprops[$this->proptags["recurring_data"]]) >= 255) {
				$this->getFullRecurrenceBlob();
			}

			$this->recur = $this->parseRecurrence($this->messageprops[$this->proptags["recurring_data"]]);
		}
		if (isset($this->proptags["timezone_data"], $this->messageprops[$this->proptags["timezone_data"]])) {
			$this->tz = $this->parseTimezone($this->messageprops[$this->proptags["timezone_data"]]);
		}

		// Get the first day of week from the user's settings, fallback to 1 (Monday)
		// if it is not explicitely set.
		$websettings = readMapiProp($store, PR_EC_WEBACCESS_SETTINGS_JSON, mapi_getprops($store, [PR_EC_WEBACCESS_SETTINGS_JSON]));
		if (!empty($websettings)) {
			$settings = json_decode($websettings, true);
			$this->firstDayOfWeek = $settings['settings']['grommunio']['v1']['main']['week_start'] ?? 1;
		}
	}

	public function getRecurrence(): ?array {
		return $this->recur;
	}

	public function getFullRecurrenceBlob(): void {
		$message = mapi_msgstore_openentry($this->store, $this->messageprops[PR_ENTRYID]);

		$recurrBlob = '';
		$stream = mapi_openproperty($message, $this->proptags["recurring_data"], IID_IStream, 0, 0);
		$stat = mapi_stream_stat($stream);

		for ($i = 0; $i < $stat['cb']; $i += 1024) {
			$recurrBlob .= mapi_stream_read($stream, 1024);
		}

		if (!empty($recurrBlob)) {
			$this->messageprops[$this->proptags["recurring_data"]] = $recurrBlob;
		}
	}

	/**
	 * Function for parsing the Recurrence value of a Calendar item.
	 *
	 * Retrieve it from Named Property 0x8216 as a PT_BINARY and pass the
	 * data to this function
	 *
	 * Returns a structure containing the data:
	 *
	 * type  - type of recurrence: day=10, week=11, month=12, year=13
	 * subtype - type of day recurrence: 2=monthday (ie 21st day of month), 3=nday'th weekdays (ie. 2nd Tuesday and Wednesday)
	 * start - unix timestamp of first occurrence
	 * end  - unix timestamp of last occurrence (up to and including), so when start == end -> occurrences = 1
	 * numoccur     - occurrences (may be very large when there is no end data)
	 * first_dow    - first day-of-week for weekly recurrences (0 = Sunday, 1 = Monday, ...)
	 *
	 * then, for each type:
	 *
	 * Daily:
	 *  everyn - every [everyn] days in minutes
	 *  regen - regenerating event (like tasks)
	 *
	 * Weekly:
	 *  everyn - every [everyn] weeks in weeks
	 *  regen - regenerating event (like tasks)
	 *  weekdays - bitmask of week days, where each bit is one weekday (weekdays & 1 = Sunday, weekdays & 2 = Monday, etc)
	 *
	 * Monthly:
	 *  everyn - every [everyn] months
	 *  regen - regenerating event (like tasks)
	 *
	 *  subtype 2:
	 *   monthday - on day [monthday] of the month
	 *
	 *  subtype 3:
	 *   weekdays - bitmask of week days, where each bit is one weekday (weekdays & 1 = Sunday, weekdays & 2 = Monday, etc)
	 *   nday - on [nday]'th [weekdays] of the month
	 *
	 * Yearly:
	 *  everyn - every [everyn] months (12, 24, 36, ...)
	 *  month - in month [month] (although the month is encoded in minutes since the startning of the year ........)
	 *  regen - regenerating event (like tasks)
	 *
	 *  subtype 2:
	 *   monthday - on day [monthday] of the month
	 *
	 *  subtype 3:
	 *   weekdays - bitmask of week days, where each bit is one weekday (weekdays & 1 = Sunday, weekdays & 2 = Monday, etc)
	 *   nday - on [nday]'th [weekdays] of the month [month]
	 *
	 * @param string $rdata Binary string
	 *
	 * @return null|(((false|int|mixed|string)[]|int)[]|int|mixed)[] recurrence data
	 *
	 * @psalm-return array{changed_occurrences: array<int, array{basedate: false|int, start: int, end: int, bitmask: mixed, subject?: false|string, remind_before?: mixed, reminder_set?: mixed, location?: false|string, busystatus?: mixed, alldayevent?: mixed, label?: mixed, ex_start_datetime?: mixed, ex_end_datetime?: mixed, ex_orig_date?: mixed}>, deleted_occurrences: list<int>, type?: int|mixed, subtype?: mixed, month?: mixed, everyn?: mixed, regen?: mixed, monthday?: mixed, weekdays?: 0|mixed, nday?: mixed, term?: int|mixed, numoccur?: mixed, numexcept?: mixed, first_dow?: mixed, numexceptmod?: mixed, start?: int, end?: int, startocc?: mixed, endocc?: mixed}|null
	 */
	public function parseRecurrence(string $rdata): ?array {
		if (strlen($rdata) < 10) {
			return null;
		}

		$ret = [];
		$ret["changed_occurrences"] = [];
		$ret["deleted_occurrences"] = [];

		$data = unpack("vReaderVersion/vWriterVersion/vrtype/vrtype2/vCalendarType", $rdata);

		// Do some recurrence validity checks
		if ($data['ReaderVersion'] != 0x3004 || $data['WriterVersion'] != 0x3004) {
			return $ret;
		}

		if (!in_array($data["rtype"], [IDC_RCEV_PAT_ORB_DAILY, IDC_RCEV_PAT_ORB_WEEKLY, IDC_RCEV_PAT_ORB_MONTHLY, IDC_RCEV_PAT_ORB_YEARLY], true)) {
			return $ret;
		}

		if (!in_array($data["rtype2"], [rptDay, rptWeek, rptMonth, rptMonthNth, rptMonthEnd, rptHjMonth, rptHjMonthNth, rptHjMonthEnd], true)) {
			return $ret;
		}

		if (!in_array($data['CalendarType'], [MAPI_CAL_DEFAULT, MAPI_CAL_GREGORIAN], true)) {
			return $ret;
		}

		$ret["type"] = (int) $data["rtype"] > 0x2000 ? (int) $data["rtype"] - 0x2000 : $data["rtype"];
		$ret["subtype"] = $data["rtype2"];
		$rdata = substr($rdata, 10);

		$valid = match ($data["rtype"]) {
			IDC_RCEV_PAT_ORB_DAILY => $this->parseDailyPattern($rdata, $ret),
			IDC_RCEV_PAT_ORB_WEEKLY => $this->parseWeeklyPattern($rdata, $ret),
			default => $this->parseMonthlyPattern(
				$rdata,
				$ret,
				$data["rtype"] == IDC_RCEV_PAT_ORB_YEARLY
			),
		};
		if (!$valid) {
			return $ret;
		}

		$exc_base_dates = $this->parseRecurrenceRange($rdata, $ret);
		if ($exc_base_dates === null) {
			return $ret;
		}

		$this->parseAppointmentRecurrence($rdata, $ret, $exc_base_dates);

		return $ret;
	}

	private function parseAppointmentRecurrence(string &$rdata, array &$ret, array $exc_base_dates): void {
		// this is where task recurrence stop
		if (strlen($rdata) < 16) {
			return;
		}

		$data = unpack("Vreaderversion/Vwriterversion/Vstartmin/Vendmin", $rdata);
		$rdata = substr($rdata, 16);

		$ret["startocc"] = $data["startmin"];
		$ret["endocc"] = $data["endmin"];
		$writerversion = $data["writerversion"];
		// base dates without an exception record are deleted occurrences
		$ret["deleted_occurrences"] = $exc_base_dates;

		if (strlen($rdata) < 2) {
			return;
		}
		$data = unpack("vnumber", $rdata);
		$rdata = substr($rdata, 2);

		$nexceptions = $data["number"];
		if ($nexceptions === 0) {
			return;
		}
		$exc_changed_details = [];

		for ($i = 0; $i < $nexceptions; ++$i) {
			$item = $this->parseRecurrenceException($rdata);
			if ($item === null) {
				break;
			}
			$exc_changed_details[] = $item;
		}

		// Base dates without a modified exception represent deletions.
		$changed_dates = array_fill_keys(array_column($exc_changed_details, "basedate"), true);
		$ret["deleted_occurrences"] = array_values(array_filter(
			$exc_base_dates,
			static fn ($date) => !isset($changed_dates[$date])
		));
		$ret["changed_occurrences"] = $exc_changed_details;

		// enough data for normal exception (no extended data); a cut record has none
		if (count($exc_changed_details) < $nexceptions || strlen($rdata) < 8) {
			return;
		}

		if (!$this->skipRecurrenceBlock($rdata)) {
			return;
		}

		for ($i = 0; $i < $nexceptions; ++$i) {
			$item = $this->parseExtendedException(
				$rdata,
				$exc_changed_details[$i],
				$writerversion
			);
			if ($item === null) {
				break;
			}
			$exc_changed_details[$i] = $item;
		}

		// update with extended data
		$ret["changed_occurrences"] = $exc_changed_details;
	}

	private function parseDailyPattern(string &$rdata, array &$ret): bool {
		if (strlen($rdata) < 12) {
			return false;
		}

		$data = unpack("Vunknown/Veveryn/Vregen", $rdata);
		if ($data["everyn"] > 1438560) { // minutes for 999 days
			return false;
		}
		$ret["everyn"] = $data["everyn"];
		$ret["regen"] = $data["regen"];

		switch ($ret["subtype"]) {
			case rptDay:
				$rdata = substr($rdata, 12);
				break;

			case rptWeek:
				$rdata = substr($rdata, 16);
				break;
		}

		return true;
	}

	private function parseWeeklyPattern(string &$rdata, array &$ret): bool {
		if (strlen($rdata) < 16) {
			return false;
		}

		$data = unpack("Vconst1/Veveryn/Vregen", $rdata);
		if ($data["everyn"] > 99) {
			return false;
		}

		$rdata = substr($rdata, 12);
		$ret["everyn"] = $data["everyn"];
		$ret["regen"] = $data["regen"];
		$ret["weekdays"] = 0;

		if ($data["regen"] == 0) {
			$data = unpack("Vweekdays", $rdata);
			$rdata = substr($rdata, 4);
			$ret["weekdays"] = $data["weekdays"];
		}

		return true;
	}

	private function parseMonthlyPattern(string &$rdata, array &$ret, bool $yearly): bool {
		if (strlen($rdata) < 16) {
			return false;
		}

		$data = unpack("Vmonth/Veveryn/Vregen/Vmonthday", $rdata);
		if ($yearly) {
			// recurring yearly tasks and events have a period in months multiple by 12
			if ($data["everyn"] % 12 != 0) {
				return false;
			}
			$ret["month"] = $data["month"];
			$ret["everyn"] = $data["everyn"] / 12;
		}
		else {
			if ($data["everyn"] > 99) {
				return false;
			}
			$ret["everyn"] = $data["everyn"];
		}
		$ret["regen"] = $data["regen"];
		$rdata = substr($rdata, 16);

		if ($ret["subtype"] != rptMonthNth) {
			$ret["monthday"] = $data["monthday"];

			return true;
		}

		$ret["weekdays"] = $data["monthday"];
		if (strlen($rdata) < 4) {
			return false;
		}
		$data = unpack("Vnday", $rdata);
		// Sanity check for valid values (and opportunistically try to fix)
		if ($data["nday"] == 0xFFFFFFFF || $data["nday"] == -1) {
			$data["nday"] = 5;
		}
		elseif ($data["nday"] < 0 || $data["nday"] > 5) {
			$data["nday"] = 0;
		}
		$ret["nday"] = $data["nday"];
		$rdata = substr($rdata, 4);

		return true;
	}

	private function parseRecurrenceRange(string &$rdata, array &$ret): ?array {
		if (strlen($rdata) < 16) {
			return null;
		}

		$data = unpack("Vterm/Vnumoccur/Vconst2/Vnumexcept", $rdata);
		$rdata = substr($rdata, 16);
		if (!in_array($data["term"], [IDC_RCEV_PAT_ERB_END, IDC_RCEV_PAT_ERB_AFTERNOCCUR, IDC_RCEV_PAT_ERB_NOEND, 0xFFFFFFFF], true)) {
			return null;
		}

		$ret["term"] = (int) $data["term"] > 0x2000 ? (int) $data["term"] - 0x2000 : $data["term"];
		$ret["numoccur"] = $data["numoccur"];
		$ret["first_dow"] = $data["const2"];
		$ret["numexcept"] = $data["numexcept"];

		// exc_base_dates are *all* the base dates that have been either deleted or modified
		$exc_base_dates = $this->parseRecurrenceDates($rdata, $ret["numexcept"]);
		if ($exc_base_dates === null) {
			return null;
		}

		if (strlen($rdata) < 4) {
			return null;
		}

		$data = unpack("Vnumexceptmod", $rdata);
		$rdata = substr($rdata, 4);

		$ret["numexceptmod"] = $data["numexceptmod"];

		// exc_changed are the base dates of *modified* occurrences. exactly what is modified
		if ($this->parseRecurrenceDates($rdata, $ret["numexceptmod"]) === null) {
			return null;
		}

		if (strlen($rdata) < 8) {
			return null;
		}

		$data = unpack("Vstart/Vend", $rdata);
		$rdata = substr($rdata, 8);

		$ret["start"] = $this->recurDataToUnixData($data["start"]);
		$ret["end"] = $this->recurDataToUnixData($data["end"]);

		return $exc_base_dates;
	}

	private function parseRecurrenceDates(string &$rdata, int $count): ?array {
		if ($count === 0) {
			return [];
		}
		if ($count > intdiv(strlen($rdata), 4)) {
			return null;
		}
		$dates = [];
		for ($offset = 0; $offset < $count; $offset += 256) {
			$length = min(256, $count - $offset);
			foreach (unpack("V{$length}", $rdata, $offset * 4) as $value) {
				$dates[] = $this->recurDataToUnixData($value);
			}
		}
		$rdata = substr($rdata, $count * 4);

		return $dates;
	}

	private function parseRecurrenceException(string &$rdata): ?array {
		$dataLength = strlen($rdata);
		if ($dataLength < 14) {
			return null;
		}
		$data = unpack("Vstartdate/Venddate/Vbasedate/vbitmask", $rdata);
		$offset = 14;
		$bitmask = $data["bitmask"];
		$item = [
			"basedate" => $this->dayStartOf($this->recurDataToUnixData($data["basedate"])),
			"start" => $this->recurDataToUnixData($data["startdate"]),
			"end" => $this->recurDataToUnixData($data["enddate"]),
			"bitmask" => $bitmask,
		];

		// ExceptionInfo fields in the order of MS-OXOCAL 2.2.1.44.2
		$fields = [
			0x01 => "subject", // ARO_SUBJECT
			0x02 => null, // ARO_MEETINGTYPE
			0x04 => "remind_before", // ARO_REMINDERDELTA
			0x08 => "reminder_set", // ARO_REMINDER
			0x10 => "location", // ARO_LOCATION
			0x20 => "busystatus", // ARO_BUSYSTATUS
			0x40 => null, // ARO_ATTACHMENT
			0x80 => "alldayevent", // ARO_SUBTYPE
			0x100 => "label", // ARO_APPTCOLOR
		];
		foreach ($fields as $flag => $name) {
			if (!($bitmask & $flag)) {
				continue;
			}
			if ($dataLength - $offset < 4) {
				return null;
			}
			$length = 4;
			if ($flag & 0x11) {
				$size = unpack("vlength", $rdata, $offset + 2)["length"];
				$length += $size;
				if ($dataLength - $offset < $length) {
					return null;
				}
				$item[$name] = substr($rdata, $offset + 4, $size);
			}
			elseif ($name !== null) {
				$item[$name] = unpack("Vvalue", $rdata, $offset)["value"];
			}
			$offset += $length;
		}
		$rdata = substr($rdata, $offset);

		return $item;
	}

	private function parseExtendedException(string &$rdata, array $item, int $writerversion): ?array {
		// subject and location in ucs-2 to utf-8
		if ($writerversion >= 0x3009) {
			if (!$this->skipRecurrenceBlock($rdata)) {
				return null;
			}
		}

		if (!$this->skipRecurrenceBlock($rdata)) {
			return null;
		}

		// ARO_SUBJECT(0x01) | ARO_LOCATION(0x10)
		if ($item["bitmask"] & 0x11) {
			if (strlen($rdata) < 12) {
				return null;
			}
			$data = unpack("Vstart/Vend/Vorig", $rdata);
			$rdata = substr($rdata, 4 * 3);

			$item["ex_start_datetime"] = $data["start"];
			$item["ex_end_datetime"] = $data["end"];
			$item["ex_orig_date"] = $data["orig"];
		}

		if ($item["bitmask"] & 0x01) {
			$item["subject"] = $this->parseExceptionString($rdata);
			if ($item["subject"] === null) {
				return null;
			}
		}
		if ($item["bitmask"] & 0x10) {
			$item["location"] = $this->parseExceptionString($rdata);
			if ($item["location"] === null) {
				return null;
			}
		}

		// ARO_SUBJECT(0x01) | ARO_LOCATION(0x10)
		if ($item["bitmask"] & 0x11) {
			if (!$this->skipRecurrenceBlock($rdata)) {
				return null;
			}
		}

		return $item;
	}

	private function parseExceptionString(string &$rdata): false|string|null {
		if (strlen($rdata) < 2) {
			return null;
		}
		$data = unpack("vlength", $rdata);
		$rdata = substr($rdata, 2);
		$length = $data["length"];
		if ($length > intdiv(strlen($rdata), 2)) {
			return null;
		}
		$data = substr($rdata, 0, $length * 2);
		$rdata = substr($rdata, $length * 2);

		return iconv("UCS-2LE", "UTF-8", $data);
	}

	private function skipRecurrenceBlock(string &$rdata): bool {
		if (strlen($rdata) < 4) {
			return false;
		}
		$size = unpack("Vsize", $rdata)["size"];
		if ($size > strlen($rdata) - 4) {
			return false;
		}
		$rdata = substr($rdata, 4 + $size);

		return true;
	}

	/**
	 * Saves the recurrence data to the recurrence property.
	 */
	public function saveRecurrence(): void {
		// Only save if a message was passed
		if (!isset($this->message)) {
			return;
		}

		// Abort if no recurrence was set
		if (!isset(
			$this->recur["type"],
			$this->recur["subtype"],
			$this->recur["start"],
			$this->recur["end"],
			$this->recur["startocc"],
			$this->recur["endocc"])
		) {
			return;
		}

		$rtype = 0x2000 + (int) $this->recur["type"];

		// Don't allow invalid type and subtype values
		if (!in_array($rtype, [IDC_RCEV_PAT_ORB_DAILY, IDC_RCEV_PAT_ORB_WEEKLY, IDC_RCEV_PAT_ORB_MONTHLY, IDC_RCEV_PAT_ORB_YEARLY], true)) {
			return;
		}

		if (!in_array((int) $this->recur["subtype"], [rptDay, rptWeek, rptMonth, rptMonthNth, rptMonthEnd, rptHjMonth, rptHjMonthNth, rptHjMonthEnd], true)) {
			return;
		}

		$rdata = pack("vvvvv", 0x3004, 0x3004, $rtype, (int) $this->recur["subtype"], MAPI_CAL_DEFAULT);
		$forwardcount = 0;
		$restocc = 0;

		// Terminate
		$term = (int) $this->recur["term"] < 0x2000 ? 0x2000 + (int) $this->recur["term"] : (int) $this->recur["term"];

		$pattern = match ($rtype) {
			IDC_RCEV_PAT_ORB_DAILY => $this->serializeDailyPattern(),
			IDC_RCEV_PAT_ORB_WEEKLY => $this->serializeWeeklyPattern($term, $forwardcount, $restocc),
			default => $this->serializeMonthlyPattern($rtype, $term, $forwardcount),
		};
		if ($pattern === null) {
			return;
		}
		$rdata .= $pattern;

		$range = $this->serializeRecurrenceRange($rtype, $term, $forwardcount, $restocc);
		if ($range === null) {
			return;
		}
		$rdata .= $range;

		$propsToSet = $this->getRecurrenceProperties();

		// Default data
		// Second item (0x08) indicates the Outlook version (see documentation at the bottom of this file for more information)
		if (isset($this->recur["startocc"], $this->recur["endocc"])) {
			// Set start and endtime in minutes
			$rdata .= pack("VVVV", 0x3006, 0x3009, (int) $this->recur["startocc"], (int) $this->recur["endocc"]);
		}
		else {
			$rdata .= pack("VV", 0x3006, 0x3009);
		}

		$rdata .= $this->serializeRecurrenceExceptions($this->recur["changed_occurrences"]);

		// Set props
		$propsToSet[$this->proptags["recurring_data"]] = $rdata;
		$propsToSet[$this->proptags["recurring"]] = true;
		$propsToSet[$this->proptags["meetingrecurring"]] = true;
		$this->setRecurrenceTimezone($propsToSet);
		mapi_setprops($this->message, $propsToSet);
	}

	private function setRecurrenceTimezone(array &$propsToSet): void {
		if (isset($this->tz) && $this->tz) {
			$timezone = "GMT";
			if ($this->tz["timezone"] != 0) {
				// Create user readable timezone information
				$timezone = sprintf(
					"(GMT %s%02d:%02d)",-$this->tz["timezone"] > 0 ? "+" : "-",
					abs($this->tz["timezone"] / 60),
					abs($this->tz["timezone"] % 60)
				);
			}
			$propsToSet[$this->proptags["timezone_data"]] = $this->getTimezoneData($this->tz);
			$propsToSet[$this->proptags["timezone"]] = $timezone;
		}
	}

	private function getRecurrenceProperties(): array {
		// UTC date
		$utcstart = $this->getClipProp("start");
		$utcend = $this->getClipProp("end");

		// utc date+time
		$utcfirstoccstartdatetime = (isset($this->recur["startocc"])) ? $utcstart + (((int) $this->recur["startocc"]) * 60) : $utcstart;
		$utcfirstoccenddatetime = (isset($this->recur["endocc"])) ? $utcstart + (((int) $this->recur["endocc"]) * 60) : $utcstart;

		$propsToSet = [];
		// update reminder time
		$propsToSet[$this->proptags["reminder_time"]] = $utcfirstoccstartdatetime;

		// update first occurrence date
		$propsToSet[$this->proptags["startdate"]] = $propsToSet[$this->proptags["commonstart"]] = $utcfirstoccstartdatetime;
		$propsToSet[$this->proptags["duedate"]] = $propsToSet[$this->proptags["commonend"]] = $utcfirstoccenddatetime;

		// Set Outlook properties, if it is an appointment
		if (isset($this->messageprops[$this->proptags["message_class"]]) && $this->messageprops[$this->proptags["message_class"]] == "IPM.Appointment") {
			// update real begin and real end date
			$propsToSet[$this->proptags["startdate_recurring"]] = $utcstart;
			$propsToSet[$this->proptags["enddate_recurring"]] = $utcend;

			// recurrencetype
			// Strange enough is the property recurrencetype, (type-0x9) and not the CDO recurrencetype
			$propsToSet[$this->proptags["recurrencetype"]] = ((int) $this->recur["type"]) - 0x9;

			// set named prop 'side_effects' to 369, needed for Outlook to ask for single or total recurrence when deleting
			$propsToSet[$this->proptags["side_effects"]] = 369;
		}
		else {
			$propsToSet[$this->proptags["side_effects"]] = 3441;
		}

		$this->setRecurrenceReminder($propsToSet);

		return $propsToSet;
	}

	private function setRecurrenceReminder(array &$propsToSet): void {
		// FlagDueBy is datetime of the first reminder occurrence. Outlook gives on this time a reminder popup dialog
		// Any change of the recurrence (including changing and deleting exceptions) causes the flagdueby to be reset
		// to the 'next' occurrence; this makes sure that deleting the next occurrence will correctly set the reminder to
		// the occurrence after that. The 'next' occurrence is defined as being the first occurrence that starts at moment X (server time)
		// with the reminder flag set.
		$reminderprops = mapi_getprops($this->message, [$this->proptags["reminder_minutes"], $this->proptags["flagdueby"]]);
		if (isset($reminderprops[$this->proptags["reminder_minutes"]])) {
			$occ = false;
			$occurrences = $this->getItems(time(), 0x7FF00000, 3, true);

			for ($i = 0, $len = count($occurrences); $i < $len; ++$i) {
				// This will actually also give us appointments that have already started, but not yet ended. Since we want the next
				// reminder that occurs after time(), we may have to skip the first few entries. We get 3 entries since that is the maximum
				// number that would be needed (assuming reminder for item X cannot be before the previous occurrence starts). Worst case:
				// time() is currently after start but before end of item, but reminder of next item has already passed (reminder for next item
				// can be DURING the previous item, eg daily allday events). In that case, the first and second items must be skipped.

				if (($occurrences[$i][$this->proptags["startdate"]] - $reminderprops[$this->proptags["reminder_minutes"]] * 60) > time()) {
					$occ = $occurrences[$i];
					break;
				}
			}

			if ($occ) {
				if (isset($reminderprops[$this->proptags["flagdueby"]])) {
					$propsToSet[$this->proptags["flagdueby"]] = $reminderprops[$this->proptags["flagdueby"]];
				}
				else {
					$propsToSet[$this->proptags["flagdueby"]] = $occ[$this->proptags["startdate"]] - ($reminderprops[$this->proptags["reminder_minutes"]] * 60);
				}
			}
			else {
				// Last reminder passed, no reminders any more.
				$propsToSet[$this->proptags["reminder"]] = false;
				$propsToSet[$this->proptags["flagdueby"]] = 0x7FF00000;
			}
		}
	}

	private function serializeRecurrenceEnd(int $rtype, int $term, mixed $forwardcount, mixed $restocc): string {
		// Set enddate
		switch ($term) {
			// After the given enddate
			case IDC_RCEV_PAT_ERB_END:
				$rdata = pack("V", $this->unixDataToRecurData((int) $this->recur["end"]));
				break;

				// After a number of times
			case IDC_RCEV_PAT_ERB_AFTERNOCCUR:
				// @todo: calculate enddate with intval($this->recur["startocc"]) + intval($this->recur["duration"]) > 24 hour
				$occenddate = (int) $this->recur["start"];

				$occenddate = match ($rtype) {
					IDC_RCEV_PAT_ORB_DAILY => $this->getDailyEndDate($occenddate),
					IDC_RCEV_PAT_ORB_WEEKLY => $this->getWeeklyEndDate($occenddate, $forwardcount, $restocc),
					default => $this->getMonthlyEndDate($occenddate, $forwardcount),
				};

				if (defined("PHP_INT_MAX") && $occenddate > PHP_INT_MAX) {
					$occenddate = PHP_INT_MAX;
				}

				$this->recur["end"] = $occenddate;

				$rdata = pack("V", $this->unixDataToRecurData((int) $this->recur["end"]));
				break;

				// Never ends
			case IDC_RCEV_PAT_ERB_NOEND:
			default:
				$this->recur["end"] = 0x7FFFFFFF; // max date -> 2038
				$rdata = pack("V", 0x5AE980DF);
				break;
		}

		return $rdata;
	}

	private function getDailyEndDate(int $occenddate): float|int {
		if ($this->recur["subtype"] == rptWeek) {
			// Daily every workday
			$restocc = (int) $this->recur["numoccur"];

			// Get starting weekday
			$nowtime = $this->gmtime($occenddate);
			$j = $nowtime["tm_wday"];

			while (1) {
				if (($j % 7) > 0 && ($j % 7) < 6) {
					--$restocc;
				}

				++$j;

				if ($restocc <= 0) {
					break;
				}

				$occenddate += 24 * 60 * 60;
			}
		}
		else {
			// -1 because the first day already counts (from 1-1-1980 to 1-1-1980 is 1 occurrence)
			$occenddate += (((int) $this->recur["everyn"]) * 60 * ((int) $this->recur["numoccur"] - 1));
		}

		return $occenddate;
	}

	private function getWeeklyEndDate(int $occenddate, mixed $forwardcount, mixed $restocc): float|int {
		$weekstart = $this->firstDayOfWeek;
		// Needed values
		// $forwardcount - number of weeks we can skip forward
		// $restocc - number of remaining occurrences after the week skip

		// Add the weeks till the last item
		$occenddate += ($forwardcount * 7 * 24 * 60 * 60);

		$dayofweek = (int) gmdate("w", (int) $occenddate);

		// Loop through the last occurrences until we have had them all
		for ($j = 1; $restocc > 0; ++$j) {
			// Jump to the next week (which may be N weeks away) when going over the week boundary
			if ((($dayofweek + $j) % 7) == $weekstart) {
				$occenddate += (((int) $this->recur["everyn"]) - 1) * 7 * 24 * 60 * 60;
			}

			// If this is a matching day, once less occurrence to process
			if (((int) $this->recur["weekdays"]) & (1 << (($dayofweek + $j) % 7))) {
				--$restocc;
			}

			// Next day
			$occenddate += 24 * 60 * 60;
		}

		return $occenddate;
	}

	private function getMonthlyEndDate(int $occenddate, mixed $forwardcount): float|int {
		switch ((int) $this->recur["subtype"]) {
			case rptMonth: // on D day of every M month
				$occenddate = $this->advanceRecurrenceMonths($occenddate, $forwardcount);

				// compensation between 28 and 31
				if (((int) $this->recur["monthday"]) >= 28 && ((int) $this->recur["monthday"]) <= 31 &&
					(int) gmdate("j", $occenddate) < ((int) $this->recur["monthday"])) {
					if ((int) gmdate("j", $occenddate) < 28) {
						$occenddate -= (int) gmdate("j", $occenddate) * 24 * 60 * 60;
					}
					else {
						$occenddate += ((int) gmdate("t", $occenddate) - (int) gmdate("j", $occenddate)) * 24 * 60 * 60;
					}
				}

				break;

			case rptMonthNth: // on Nth weekday of every M month
				$nday = (int) $this->recur["nday"]; // 1 tot 5
				$weekdays = (int) $this->recur["weekdays"];

				$occenddate = $this->advanceRecurrenceMonths($occenddate, $forwardcount);

				$occenddate = $this->getMonthWeekdayEndDate($occenddate, $nday, $weekdays);

				break; // case rptMonthNth
		}

		return $occenddate;
	}

	private function advanceRecurrenceMonths(int $occenddate, mixed $forwardcount): float|int {
		$curyear = (int) gmdate("Y", (int) $this->recur["start"]);
		$curmonth = (int) gmdate("n", (int) $this->recur["start"]);
		// $forwardcount = months

		while ($forwardcount > 0) {
			$occenddate += $this->getMonthInSeconds($curyear, $curmonth);
			if ($curmonth >= 12) {
				$curmonth = 1;
				++$curyear;
			}
			else {
				++$curmonth;
			}

			--$forwardcount;
		}

		return $occenddate;
	}

	private function getMonthWeekdayEndDate(float|int $occenddate, int $nday, int $weekdays): float|int {
		if ($nday == 5) {
			// Set date on the last day of the last month
			$occenddate += ((int) gmdate("t", $occenddate) - (int) gmdate("j", $occenddate)) * 24 * 60 * 60;
		}
		else {
			// Set date on the first day of the last month
			$occenddate -= ((int) gmdate("j", $occenddate) - 1) * 24 * 60 * 60;
		}

		$dayofweek = (int) gmdate("w", (int) $occenddate);
		for ($i = 0; $i < 7; ++$i) {
			if ($nday == 5 && (($dayofweek - $i) % 7) >= 0 && (1 << (($dayofweek - $i) % 7)) & $weekdays) {
				$occenddate -= $i * 24 * 60 * 60;
				break;
			}
			if ($nday != 5 && (1 << (($dayofweek + $i) % 7)) & $weekdays) {
				$occenddate += ($i + (($nday - 1) * 7)) * 24 * 60 * 60;
				break;
			}
		}

		return $occenddate;
	}

	private function serializeRecurrenceRange(int $rtype, int $term, mixed $forwardcount, mixed $restocc): ?string {
		$rdata = "";
		if (!isset($this->recur["term"])) {
			return null;
		}

		$rdata .= pack("V", $term);

		switch ($term) {
			// After the given enddate
			case IDC_RCEV_PAT_ERB_END:
				$rdata .= pack("V", 10);
				break;

				// After a number of times
			case IDC_RCEV_PAT_ERB_AFTERNOCCUR:
				if (!isset($this->recur["numoccur"])) {
					return null;
				}

				$rdata .= pack("V", (int) $this->recur["numoccur"]);
				break;

				// Never ends
			case IDC_RCEV_PAT_ERB_NOEND:
				$rdata .= pack("V", 0);
				break;
		}

		// Persist first day of week (previously saved recurrences maintain the fdow)
		$firstDow = $this->recur["first_dow"] ?? $this->firstDayOfWeek;
		$rdata .= pack("V", (int) $firstDow);

		// Exception data

		// Get all exceptions
		$deleted_items = $this->recur["deleted_occurrences"];
		$changed_items = $this->recur["changed_occurrences"];
		if ($deleted_items === [] && $changed_items === []) {
			$start = $this->unixDataToRecurData((int) $this->recur["start"]);

			return $rdata . pack("VVV", 0, 0, $start) .
				$this->serializeRecurrenceEnd($rtype, $term, $forwardcount, $restocc);
		}

		// Merge deleted and changed items into one list
		$items = $deleted_items;

		foreach ($changed_items as $changed_item) {
			$items[] = $this->dayStartOf($changed_item["basedate"]);
		}

		sort($items);

		// Add the merged list in to the rdata
		$rdata .= pack("V", count($items));
		foreach ($items as $item) {
			$rdata .= pack("V", $this->unixDataToRecurData($item));
		}

		// Loop through the changed exceptions (not deleted)
		$rdata .= pack("V", count($changed_items));
		$items = [];

		foreach ($changed_items as $changed_item) {
			$items[] = $this->dayStartOf($changed_item["start"]);
		}

		sort($items);

		// Add the changed items list int the rdata
		foreach ($items as $item) {
			$rdata .= pack("V", $this->unixDataToRecurData($item));
		}

		// Set start date
		$rdata .= pack("V", $this->unixDataToRecurData((int) $this->recur["start"]));

		$rdata .= $this->serializeRecurrenceEnd($rtype, $term, $forwardcount, $restocc);

		return $rdata;
	}

	private function serializeDailyPattern(): ?string {
		if (!isset($this->recur["everyn"]) || (int) $this->recur["everyn"] > 1438560 || (int) $this->recur["everyn"] < 0) { // minutes for 999 days
			return null;
		}

		// The interval of "every N days" divides the start below
		if ($this->recur["subtype"] != rptWeek && (int) $this->recur["everyn"] == 0) {
			return null;
		}

		if ($this->recur["subtype"] == rptWeek) {
			// Daily every workday
			return pack("VVVV", 6 * 24 * 60, 1, 0, 0x3E);
		}
		$firstocc = $this->unixDataToRecurData($this->recur["start"]) % ((int) $this->recur["everyn"]);

		return pack("VVV", $firstocc, (int) $this->recur["everyn"], $this->recur["regen"] ? 1 : 0);
	}

	private function serializeWeeklyPattern(int $term, mixed &$forwardcount, mixed &$restocc): ?string {
		if (!isset($this->recur["everyn"]) || $this->recur["everyn"] > 99 || (int) $this->recur["everyn"] <= 0) {
			return null;
		}

		if (!$this->recur["regen"] && empty($this->recur["weekdays"])) {
			return null;
		}

		// No need to calculate startdate if sliding flag was set.
		if (!$this->recur['regen']) {
			$this->setWeeklyStart($term, $forwardcount, $restocc);
		}

		// Calc first occ
		$firstocc = $this->unixDataToRecurData($this->recur["start"]) % (((int) $this->recur["everyn"]) * 7 * 24 * 60);

		$firstocc -= (((int) gmdate("w", (int) $this->recur["start"])) - 1) * 24 * 60;

		if ($this->recur["regen"]) {
			return pack("VVV", $firstocc, (int) $this->recur["everyn"], 1);
		}

		return pack("VVVV", $firstocc, (int) $this->recur["everyn"], 0, (int) $this->recur["weekdays"]);
	}

	private function setWeeklyStart(int $term, mixed &$forwardcount, mixed &$restocc): void {
		$weekstart = $this->firstDayOfWeek;
		$dayofweek = (int) gmdate("w", (int) $this->recur["start"]);
		// Calculate start date of recurrence

		// Find the first day that matches one of the weekdays selected
		$daycount = 0;
		$dayskip = -1;
		for ($j = 0; $j < 7; ++$j) {
			if (((int) $this->recur["weekdays"]) & (1 << (($dayofweek + $j) % 7))) {
				if ($dayskip == -1) {
					$dayskip = $j;
				}

				++$daycount;
			}
		}

		// $dayskip is the number of days to skip from the startdate until the first occurrence
		// $daycount is the number of days per week that an occurrence occurs

		$weekskip = 0;
		if (($dayofweek < $weekstart && $dayskip > 0) || ($dayofweek + $dayskip) > 6) {
			$weekskip = 1;
		}

		// Check if the recurrence ends after a number of occurrences, in that case we must calculate the
		// remaining occurrences based on the start of the recurrence.
		if ($term == IDC_RCEV_PAT_ERB_AFTERNOCCUR) {
			// $weekskip is the amount of weeks to skip from the startdate before the first occurrence
			// $forwardcount is the maximum number of week occurrences we can go ahead after the first occurrence that
			// is still inside the recurrence. We subtract one to make sure that the last week is never forwarded over
			// (eg when numoccur = 2, and daycount = 1)
			$forwardcount = floor((int) ($this->recur["numoccur"] - 1) / $daycount);

			// $restocc is the number of occurrences left after $forwardcount whole weeks of occurrences, minus one
			// for the occurrence on the first day
			$restocc = ((int) $this->recur["numoccur"]) - ($forwardcount * $daycount) - 1;

			// $forwardcount is now the number of weeks we can go forward and still be inside the recurrence
			$forwardcount *= (int) $this->recur["everyn"];
		}

		// The real start is start + dayskip + weekskip-1 (since dayskip will already bring us into the next week)
		$this->recur["start"] = ((int) $this->recur["start"]) + ($dayskip * 24 * 60 * 60) + ($weekskip * (((int) $this->recur["everyn"]) - 1) * 7 * 24 * 60 * 60);
	}

	private function serializeMonthlyPattern(int $rtype, int $term, mixed &$forwardcount): ?string {
		$rdata = "";
		$everyn = $this->getMonthlyPeriod($rtype);
		if ($everyn === null) {
			return null;
		}

		// Check if the recurrence ends after a number of occurrences, in that case we must calculate the
		// remaining occurrences based on the start of the recurrence.
		if ($term == IDC_RCEV_PAT_ERB_AFTERNOCCUR) {
			// $forwardcount is the number of occurrences we can skip and still be inside the recurrence range (minus
			// one to make sure there are always at least one occurrence left)
			$forwardcount = ((((int) $this->recur["numoccur"]) - 1) * $everyn);
		}

		// Get month for yearly on D'th day of month M
		$selmonth = (int) gmdate("n", (int) $this->recur["start"]);
		if ($rtype == IDC_RCEV_PAT_ORB_YEARLY) {
			$selmonth = floor(((int) $this->recur["month"]) / (24 * 60 * 29)) + 1; // 1=jan, 2=feb, eg
		}

		switch ((int) $this->recur["subtype"]) {
			// on D day of every M month
			case rptMonth:
				if (!isset($this->recur["monthday"])) {
					return null;
				}
				$this->setMonthlyStart($rtype, $everyn, $selmonth);

				$firstocc = $this->getMonthlyFirstOccurrence($rtype, $everyn);
				$rdata .= pack("VVVV", $firstocc, $everyn, $this->recur["regen"], (int) $this->recur["monthday"]);
				break;

			case rptMonthNth:
				// monthly: on Nth weekday of every M month
				// yearly: on Nth weekday of M month
				if (!isset($this->recur["weekdays"], $this->recur["nday"])) {
					return null;
				}

				$weekdays = (int) $this->recur["weekdays"];
				$nday = (int) $this->recur["nday"];

				$firstocc = $this->getMonthlyFirstOccurrence($rtype, $everyn);
				$rdata .= pack("VVVVV", $firstocc, $everyn, 0, $weekdays, $nday);
				break;
		}

		return $rdata;
	}

	private function getMonthlyPeriod(int $rtype): ?int {
		if (!isset($this->recur["everyn"])) {
			return null;
		}
		if ($rtype == IDC_RCEV_PAT_ORB_YEARLY && !isset($this->recur["month"])) {
			return null;
		}

		if ($rtype == IDC_RCEV_PAT_ORB_MONTHLY) {
			$everyn = (int) $this->recur["everyn"];
			if ($everyn > 99 || $everyn <= 0) {
				return null;
			}
		}
		else {
			if ((int) $this->recur["everyn"] <= 0) {
				return null;
			}
			$everyn = ((int) $this->recur["everyn"]) * 12;
		}

		return $everyn;
	}

	private function setMonthlyStart(int $rtype, int $everyn, mixed $selmonth): void {
		$curmonthday = (int) gmdate("j", (int) $this->recur["start"]);
		$curyear = (int) gmdate("Y", (int) $this->recur["start"]);
		$curmonth = (int) gmdate("n", (int) $this->recur["start"]);
		$monthday = (int) $this->recur["monthday"];
		// Go the beginning of the month
		$this->recur["start"] -= ($curmonthday - 1) * 24 * 60 * 60;
		// Go the the correct month day
		$this->recur["start"] += ($monthday - 1) * 24 * 60 * 60;

		$count = 0;
		if ($rtype == IDC_RCEV_PAT_ORB_YEARLY && $curmonth != $selmonth) {
			$count = $selmonth - $curmonth;
			if ($curmonth > $selmonth) {
				$count += $everyn;
			}
		}
		elseif ($monthday < $curmonthday) {
			$count = $everyn;
		}
		for ($i = 0; $i < $count; ++$i) {
			$this->recur["start"] += $this->getMonthInSeconds($curyear, $curmonth);

			if ($curmonth == 12) {
				++$curyear;
				$curmonth = 0;
			}
			++$curmonth;
		}

		// "start" is now pointing to the first occurrence, except that it will overshoot if the
		// month in which it occurs has less days than specified as the day of the month. So 31st
		// of each month will overshoot in february (29 days). We compensate for that by checking
		// if the day of the month we got is wrong, and then back up to the last day of the previous
		// month.
		if ($monthday >= 28 && $monthday <= 31 &&
			(int) gmdate("j", (int) $this->recur["start"]) < $monthday) {
			$this->recur["start"] -= (int) gmdate("j", (int) $this->recur["start"]) * 24 * 60 * 60;
		}
	}

	private function getMonthlyFirstOccurrence(int $rtype, int $everyn): float|int {
		$monthIndex = (int) gmdate("n", $this->recur["start"]) - 1;
		if ($rtype == IDC_RCEV_PAT_ORB_MONTHLY) {
			$year = (int) gmdate("Y", $this->recur["start"]) - 1601;
			$monthIndex = (((12 % $everyn) * ($year % $everyn)) % $everyn + $monthIndex) % $everyn;
		}
		$firstocc = 0;
		for ($i = 0; $i < $monthIndex; ++$i) {
			$firstocc += $this->getMonthInSeconds(1601 + floor($i / 12), ($i % 12) + 1) / 60;
		}

		return $firstocc;
	}

	private function serializeRecurrenceExceptions(array $items): string {
		if (!$items) {
			return pack("vVV", 0, 0, 0);
		}
		$rdata = pack("v", count($items));
		foreach ($items as $item) {
			$rdata .= $this->serializeRecurrenceException($item);
		}
		$rdata .= pack("V", 0);
		foreach ($items as $item) {
			$rdata .= $this->serializeExtendedException($item);
		}

		return $rdata . pack("V", 0);
	}

	private function serializeRecurrenceException(array $item): string {
		$rdata = $this->serializeExceptionDates($item);
		$fields = [
			"subject" => 0x01, "remind_before" => 0x04, "reminder_set" => 0x08,
			"location" => 0x10, "busystatus" => 0x20,
			"alldayevent" => 0x80, "label" => 0x100,
		];
		$bitmask = 0;
		$values = "";
		foreach ($fields as $field => $flag) {
			if (!isset($item[$field])) {
				continue;
			}
			$bitmask |= $flag;
			if ($flag & 0x11) {
				$value = iconv("UTF-8", "windows-1252//TRANSLIT", $item[$field]);
				$length = strlen($value);
				$values .= pack("vv", $length + 1, $length) . $value;
			}
			else {
				$values .= pack("V", $item[$field]);
			}
		}

		return $rdata . pack("v", $bitmask) . $values;
	}

	private function serializeExtendedException(array $item): string {
		// ChangeHighlightSize, ChangeHighlightValue, ReservedBlockEE1Size.
		$rdata = pack("VVV", 4, 0, 0);
		if (!isset($item["subject"]) && !isset($item["location"])) {
			return $rdata;
		}
		$rdata .= $this->serializeExceptionDates($item);
		foreach (["subject", "location"] as $field) {
			if (isset($item[$field])) {
				$value = iconv("UTF-8", "UCS-2LE", $item[$field]);
				$length = iconv_strlen($value, "UCS-2LE");
				$rdata .= pack("v", $length) . $value;
			}
		}

		return $rdata . pack("V", 0);
	}

	private function serializeExceptionDates(array $item): string {
		return pack(
			"VVV",
			$this->unixDataToRecurData($item["start"]),
			$this->unixDataToRecurData($item["end"]),
			$this->unixDataToRecurData(
				$this->dayStartOf($item["basedate"]) + ((int) $this->recur["startocc"] ?? 0) * 60
			)
		);
	}

	/**
	 * Function which converts a recurrence date timestamp to an unix date timestamp.
	 *
	 * @author Steve Hardy
	 *
	 * @param int $rdate the date which will be converted
	 *
	 * @return int the converted date
	 */
	public function recurDataToUnixData(int $rdate): int {
		return ($rdate - 194074560) * 60;
	}

	/**
	 * Function which converts an unix date timestamp to recurrence date timestamp.
	 *
	 * @author Johnny Biemans
	 *
	 * @param int $date the date which will be converted
	 *
	 * @return float|int the converted date in minutes
	 */
	public function unixDataToRecurData(int $date): float|int {
		// whole minutes; a float with seconds in it is deprecated as operand of %
		return intdiv($date, 60) + 194074560;
	}

	/**
	 * gmtime() doesn't exist in standard PHP, so we have to implement it ourselves.
	 *
	 * @author Steve Hardy
	 */
	public function GetTZOffset(mixed $ts): float|int {
		$Offset = date("O", $ts);

		$Parity = $Offset < 0 ? -1 : 1;
		$Offset = $Parity * $Offset;
		$Offset = ($Offset - ($Offset % 100)) / 100 * 60 + $Offset % 100;

		return $Parity * $Offset;
	}

	/**
	 * gmtime() doesn't exist in standard PHP, so we have to implement it ourselves.
	 *
	 * @author Steve Hardy
	 *
	 * @return array GMT Time, with the keys of localtime($time, true)
	 */
	public function gmtime(int $time): array {
		if (isset($this->gmtimeCache[$time])) {
			return $this->gmtimeCache[$time];
		}

		// Shifting the timestamp by the server offset for localtime() is
		// wrong around the DST changes of the server timezone.
		[$sec, $min, $hour, $mday, $mon, $year, $wday, $yday] = array_map('intval', explode(' ', gmdate('s i G j n Y w z', $time)));

		return $this->gmtimeCache[$time] = [
			'tm_sec' => $sec,
			'tm_min' => $min,
			'tm_hour' => $hour,
			'tm_mday' => $mday,
			'tm_mon' => $mon - 1,
			'tm_year' => $year - 1900,
			'tm_wday' => $wday,
			'tm_yday' => $yday,
			'tm_isdst' => 0,
		];
	}

	public function isLeapYear(float|string $year): bool {
		return $year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0);
	}

	public function getMonthInSeconds(float|string $year, int|string $month): int {
		if (in_array($month, [1, 3, 5, 7, 8, 10, 12], true)) {
			$day = 31;
		}
		elseif (in_array($month, [4, 6, 9, 11], true)) {
			$day = 30;
		}
		else {
			$day = 28;
			if ($this->isLeapYear($year) == 1) {
				++$day;
			}
		}

		return $day * 24 * 60 * 60;
	}

	/**
	 * Function to get a date by Year Nr, Month Nr, Week Nr, Day Nr, and hour.
	 *
	 * @param int $year  years since 1900
	 * @param int $month month (1..12)
	 * @param int $week  occurrence of the weekday in the month (1..4, 5 = last)
	 * @param int $day   weekday (0 = Sunday .. 6 = Saturday)
	 * @param int $hour  hour of the day
	 *
	 * @return int the timestamp of the given date, timezone-independent
	 */
	public function getDateByYearMonthWeekDayHour(int $year, int $month, int $week, int $day, int $hour): int {
		// get first day of month
		$date = gmmktime(0, 0, 0, $month, 1, $year + 1900);

		// go to the first $day of the month, then to the correct week nr
		$gmdate = $this->gmtime($date);
		$date += (($day - $gmdate["tm_wday"] + 7) % 7) * 24 * 60 * 60;
		$date += ($week - 1) * 7 * 24 * 60 * 60;
		$date += $hour * 60 * 60;

		$gmdate = $this->gmtime($date);

		// if we are in the next month, then back up a week, because week '5' means
		// 'last week of month'

		if ($month != $gmdate["tm_mon"] + 1) {
			$date -= 7 * 24 * 60 * 60;
		}

		return $date;
	}

	/**
	 * getTimezone gives the timezone offset (in minutes) of the given
	 * local date/time according to the given TZ info.
	 */
	public function getTimezone(mixed $tz, mixed $date): int {
		// No timezone -> GMT (+0)
		if (!isset($tz["timezone"])) {
			return 0;
		}

		[$dststart, $dstend] = $this->getDstBoundaries($tz, $date);

		$dst = false;
		if ($dststart <= $dstend) {
			// Northern hemisphere, eg DST is during Mar-Oct
			if ($date > $dststart && $date < $dstend) {
				$dst = true;
			}
		}
		else {
			// Southern hemisphere, eg DST is during Oct-Mar
			if ($date < $dstend || $date > $dststart) {
				$dst = true;
			}
		}

		if ($dst) {
			return $tz["timezone"] + $tz["timezonedst"];
		}

		return $tz["timezone"];
	}

	/**
	 * Returns the local start and end of DST in the year of the given local date.
	 */
	private function getDstBoundaries(mixed $tz, int $date): array {
		$gmdate = $this->gmtime($date);
		$year = $gmdate["tm_year"];

		// The timezone of the object may change (setRecurrence()), and getTimezone()
		// may be asked for another one, so the rules are part of the key
		$key = $year . ':' . implode(',', [
			$tz["dststartmonth"], $tz["dststartweek"], $tz["dststartday"] ?? 0, $tz["dststarthour"], $tz["dststartminute"] ?? 0, $tz["dststartsecond"] ?? 0, $tz["dststartmillis"] ?? 0,
			$tz["dstendmonth"], $tz["dstendweek"], $tz["dstendday"] ?? 0, $tz["dstendhour"], $tz["dstendminute"] ?? 0, $tz["dstendsecond"] ?? 0, $tz["dstendmillis"] ?? 0,
		]);
		if (!isset($this->dstBoundaryCache[$key])) {
			// a rule may change at 23:59:59.999, eg America/Santiago
			$this->dstBoundaryCache[$key] = [
				$this->getDateByYearMonthWeekDayHour($year, $tz["dststartmonth"], $tz["dststartweek"], $tz["dststartday"] ?? 0, $tz["dststarthour"]) +
					(int) ceil(($tz["dststartminute"] ?? 0) * 60 + ($tz["dststartsecond"] ?? 0) + ($tz["dststartmillis"] ?? 0) / 1000),
				$this->getDateByYearMonthWeekDayHour($year, $tz["dstendmonth"], $tz["dstendweek"], $tz["dstendday"] ?? 0, $tz["dstendhour"]) +
					(int) ceil(($tz["dstendminute"] ?? 0) * 60 + ($tz["dstendsecond"] ?? 0) + ($tz["dstendmillis"] ?? 0) / 1000),
			];
		}

		return $this->dstBoundaryCache[$key];
	}

	/**
	 * parseTimezone parses the timezone as specified in named property 0x8233
	 * in Outlook calendar messages. Returns the timezone in minutes negative
	 * offset (GMT +2:00 -> -120).
	 */
	public function parseTimezone(mixed $data): array|false|null {
		if (strlen((string) $data) < 48) {
			return null;
		}

		// lBias, lStandardBias, lDaylightBias, wStandardYear, stStandardDate, wDaylightYear,
		// stDaylightDate; a SYSTEMTIME is wYear, wMonth, wDayOfWeek, wDay, wHour, wMinute,
		// wSecond, wMilliseconds, where wDay is the occurrence of wDayOfWeek in the month
		return unpack("ltimezone/lunk/ltimezonedst/vunk/vunk/vdstendmonth/vdstendday/vdstendweek/vdstendhour/vdstendminute/vdstendsecond/vdstendmillis/vunk/vunk/vdststartmonth/vdststartday/vdststartweek/vdststarthour/vdststartminute/vdststartsecond/vdststartmillis", (string) $data);
	}

	public function getTimezoneData(mixed $tz): false|string {
		return pack(
			"lllvvvvvvvvvvvvvvvvvv",
			$tz["timezone"],
			0,
			$tz["timezonedst"],
			0,
			0,
			$tz["dstendmonth"],
			$tz["dstendday"] ?? 0,
			$tz["dstendweek"],
			$tz["dstendhour"],
			$tz["dstendminute"] ?? 0,
			$tz["dstendsecond"] ?? 0,
			$tz["dstendmillis"] ?? 0,
			0,
			0,
			$tz["dststartmonth"],
			$tz["dststartday"] ?? 0,
			$tz["dststartweek"],
			$tz["dststarthour"],
			$tz["dststartminute"] ?? 0,
			$tz["dststartsecond"] ?? 0,
			$tz["dststartmillis"] ?? 0
		);
	}

	/**
	 * toGMT returns a timestamp in GMT time for the time and timezone given.
	 */
	public function toGMT(mixed $tz, int $date): int {
		if (!isset($tz['timezone'])) {
			return $date;
		}
		$offset = $this->getTimezone($tz, $date);
		// A time skipped at the start of DST is moved forward like Outlook does,
		// and not back, eg to the previous day in America/Havana.
		[$dststart] = $this->getDstBoundaries($tz, $date);
		if ($date > $dststart && $date < $dststart - ($tz['timezonedst'] ?? 0) * 60) {
			$offset = $tz['timezone'];
		}

		return $date + $offset * 60;
	}

	/**
	 * fromGMT returns a timestamp in the local timezone given from the GMT time given.
	 */
	public function fromGMT(mixed $tz, int $date): int {
		if (!isset($tz['timezone'])) {
			return $date;
		}
		$standard = $date - $tz['timezone'] * 60;
		$daylight = $standard - ($tz['timezonedst'] ?? 0) * 60;
		if ($daylight == $standard) {
			return $standard;
		}
		// The DST rules are in local time: DST starts at a standard time and
		// ends at a daylight time. Transitions are not at the turn of a year.
		[$dststart, $dstend] = $this->getDstBoundaries($tz, $standard);
		$dst = $dststart <= $dstend ?
			$standard >= $dststart && $daylight < $dstend :
			$standard >= $dststart || $daylight < $dstend;

		return $dst ? $daylight : $standard;
	}

	/**
	 * Function to get timestamp of the beginning of the day of the timestamp given.
	 *
	 * @return false|int timestamp referring to same day but at 00:00:00
	 */
	public function dayStartOf(int $date): false|int {
		$time1 = $this->gmtime($date);

		return gmmktime(0, 0, 0, $time1["tm_mon"] + 1, $time1["tm_mday"], $time1["tm_year"] + 1900);
	}

	/**
	 * Function to get timestamp of the beginning of the month of the timestamp given.
	 *
	 * @return false|int Timestamp referring to same month but on the first day, and at 00:00:00
	 */
	public function monthStartOf(int $date): false|int {
		$time1 = $this->gmtime($date);

		return gmmktime(0, 0, 0, $time1["tm_mon"] + 1, 1, $time1["tm_year"] + 1900);
	}

	/**
	 * Function to get timestamp of the beginning of the year of the timestamp given.
	 *
	 * @return false|int Timestamp referring to the same year but on Jan 01, at 00:00:00
	 */
	public function yearStartOf(int $date): false|int {
		$time1 = $this->gmtime($date);

		return gmmktime(0, 0, 0, 1, 1, $time1["tm_year"] + 1900);
	}

	/**
	 * Function which returns the items in a given interval. This included expansion of the recurrence and
	 * processing of exceptions (modified and deleted).
	 *
	 * @param int $start start time of the interval (GMT)
	 * @param int $end   end time of the interval (GMT)
	 *
	 * @return array<int, array|mixed>
	 */
	public function getItems(int $start, int $end, mixed $limit = 0, mixed $remindersonly = false): array {
		$items = [];

		if (!isset($this->recur)) {
			return $items;
		}

		// Optimization: remindersonly and default reminder is off; since only exceptions with reminder set will match, just look which
		// exceptions are in range and have a reminder set
		if ($remindersonly && (!isset($this->messageprops[$this->proptags["reminder"]]) || $this->messageprops[$this->proptags["reminder"]] === false)) {
			// Sort exceptions by start time
			uasort($this->recur["changed_occurrences"], $this->sortExceptionStart(...));

			// Loop through all changed exceptions
			foreach ($this->recur["changed_occurrences"] as $exception) {
				// Check reminder set
				if (empty($exception["reminder_set"])) {
					continue;
				}

				// Convert to GMT
				$occstart = $this->toGMT($this->tz, $exception["start"]);
				$occend = $this->toGMT($this->tz, $exception["end"]);

				// Check range criterium
				if ($occstart > $end || $occend < $start) {
					continue;
				}

				// OK, add to items.
				$items[] = $this->getExceptionProperties($exception);
				if ($limit && (count($items) == $limit)) {
					break;
				}
			}

			uasort($items, $this->sortStarttime(...));

			return $items;
		}

		// From here on, the dates of the occurrences are calculated in local time, so the days we're looking
		// at are calculated from the local time dates of $start and $end

		if (isset($this->recur["start"])) {
			$daystart = $this->dayStartOf($this->recur["start"]); // start on first day of occurrence
		}
		else {
			throw new RecurrenceException('Cannot calculate daystart', RECURR_NO_START);
		}

		// Calculate the last day on which we want to be looking at a recurrence; this is either the end of the view
		// or the end of the recurrence, whichever comes first
		if ($end > $this->toGMT($this->tz, $this->recur["end"])) {
			$rangeend = $this->toGMT($this->tz, $this->recur["end"]);
		}
		else {
			$rangeend = $end;
		}

		$dayend = $this->dayStartOf($this->fromGMT($this->tz, $rangeend));

		// Loop through the entire recurrence range of dates, and check for each occurrence whether it is in the view range.
		$recurType = (int) $this->recur["type"] < 0x2000 ? (int) $this->recur["type"] + 0x2000 : (int) $this->recur["type"];

		$items = match ($recurType) {
			IDC_RCEV_PAT_ORB_DAILY => $this->getDailyItems($start, $end, $daystart, $dayend, $limit, $remindersonly),
			IDC_RCEV_PAT_ORB_WEEKLY => $this->getWeeklyItems($start, $end, $daystart, $dayend, $limit, $remindersonly),
			IDC_RCEV_PAT_ORB_MONTHLY => $this->getMonthlyItems($start, $end, $daystart, $dayend, $limit, $remindersonly),
			IDC_RCEV_PAT_ORB_YEARLY => $this->getYearlyItems($start, $end, $daystart, $dayend, $limit, $remindersonly),
			default => [],
		};
		// to get all exception items
		if (!empty($this->recur['changed_occurrences'])) {
			$this->processExceptionItems($items, $start, $end);
		}

		// sort items on starttime
		usort($items, $this->sortStarttime(...));

		// Return the MAPI-compatible list of items for this object
		return $items;
	}

	private function getDailyItems(int $start, int $end, int $daystart, int $dayend, mixed $limit, mixed $remindersonly): array {
		$items = [];

		if ($this->recur["everyn"] <= 0) {
			$this->recur["everyn"] = 1440;
		}

		if ($this->recur["subtype"] == rptDay) {
			// Every Nth day
			for ($now = $daystart; $now <= $dayend && ($limit == 0 || count($items) < $limit); $now += 60 * $this->recur["everyn"]) {
				$this->processOccurrenceItem($items, $start, $end, $now, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
			}

			return $items;
		}
		// Every workday
		for ($now = $daystart; $now <= $dayend && ($limit == 0 || count($items) < $limit); $now += 60 * 1440) {
			$nowtime = $this->gmtime($now);
			if ($nowtime["tm_wday"] > 0 && $nowtime["tm_wday"] < 6) { // only add items in the given timespace
				$this->processOccurrenceItem($items, $start, $end, $now, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
			}
		}

		return $items;
	}

	private function getWeeklyItems(int $start, int $end, int $daystart, int $dayend, mixed $limit, mixed $remindersonly): array {
		$items = [];

		if ($this->recur["everyn"] <= 0) {
			$this->recur["everyn"] = 1;
		}

		// If sliding flag is set then move to 'n' weeks
		$weekSeconds = 60 * 60 * 24 * 7;
		if ($this->recur['regen']) {
			$daystart += ($weekSeconds * $this->recur["everyn"]);
		}

		$loopStart = $daystart;
		if (!$this->recur['regen']) {
			$weekStartDow = isset($this->recur["first_dow"]) ? (int) $this->recur["first_dow"] : 1;
			$weekStartDow = ($weekStartDow % 7 + 7) % 7;
			$currentDow = (int) $this->gmtime($loopStart)["tm_wday"];
			$offset = ($currentDow - $weekStartDow + 7) % 7;
			$loopStart -= $offset * 24 * 60 * 60;
		}

		for ($now = $loopStart; $now <= $dayend && ($limit == 0 || count($items) < $limit); $now += ($weekSeconds * $this->recur["everyn"])) {
			if ($this->recur['regen']) {
				$this->processOccurrenceItem($items, $start, $end, $now, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
				break;
			}
			// Loop through the whole following week to the first occurrence of the week, add each day that is specified
			for ($wday = 0; $wday < 7 && ($limit == 0 || count($items) < $limit); ++$wday) {
				$daynow = $now + $wday * 60 * 60 * 24;
				if ($daynow < $daystart) {
					continue; // @phpcs:ignore - intentional continue, not break
				}
				// checks whether the next coming day in recurring pattern is less than or equal to end day of the recurring item
				if ($daynow > $dayend) {
					break; // @phpcs:ignore - intentional break, not continue
				}
				$nowtime = $this->gmtime($daynow); // Get the weekday of the current day
				if ($this->recur["weekdays"] & (1 << $nowtime["tm_wday"])) { // Selected ?
					$this->processOccurrenceItem($items, $start, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
				}
			}
		}

		return $items;
	}

	private function getMonthlyItems(int $start, int $end, int $daystart, int $dayend, mixed $limit, mixed $remindersonly): array {
		$items = [];
		$firstday = 0;

		if ($this->recur["everyn"] <= 0) {
			$this->recur["everyn"] = 1;
		}

		if ($this->recur['regen'] && !isset($this->recur["nday"], $this->recur["weekdays"])) {
			return $this->getRegeneratedItem($daystart, $end, $dayend, (int) $this->recur["everyn"], $remindersonly);
		}

		// Loop through all months from start to end of occurrence, starting at beginning of first month
		for ($now = $this->monthStartOf($daystart); $now <= $dayend && ($limit == 0 || count($items) < $limit); $now += $this->daysInMonth($now, $this->recur["everyn"]) * 24 * 60 * 60) {
			if (isset($this->recur["monthday"]) && ($this->recur['monthday'] != "undefined") && !$this->recur['regen']) { // Day M of every N months
				$difference = 1;
				if ($this->daysInMonth($now, $this->recur["everyn"]) < $this->recur["monthday"]) {
					$difference = $this->recur["monthday"] - $this->daysInMonth($now, $this->recur["everyn"]) + 1;
				}
				$daynow = $now + (($this->recur["monthday"] - $difference) * 24 * 60 * 60);
				// checks weather the next coming day in recurrence pattern is less than or equal to end day of the recurring item
				if ($daynow <= $dayend) {
					$this->processOccurrenceItem($items, $start, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
				}
			}
			elseif (isset($this->recur["nday"], $this->recur["weekdays"])) { // Nth [weekday] of every N months
				// Sanitize input
				if ($this->recur["weekdays"] == 0) {
					$this->recur["weekdays"] = 1;
				}

				// If nday is not set to the last day in the month
				if ($this->recur["nday"] < 5) {
					// keep the track of no. of time correct selection pattern (like 2nd weekday, 4th friday, etc.) is matched
					$ndaycounter = 0;
					// Find matching weekday in this month
					for ($day = 0, $total = $this->daysInMonth($now, 1); $day < $total; ++$day) {
						$daynow = $now + $day * 60 * 60 * 24;
						$nowtime = $this->gmtime($daynow); // Get the weekday of the current day

						if ($this->recur["weekdays"] & (1 << $nowtime["tm_wday"])) { // Selected ?
							++$ndaycounter;
						}
						// check the selected pattern is same as asked Nth weekday,If so set the firstday
						if ($this->recur["nday"] == $ndaycounter) {
							$firstday = $day;
							break;
						}
					}
					// $firstday is the day of the month on which the asked pattern of nth weekday matches
					$daynow = $now + $firstday * 60 * 60 * 24;
				}
				else {
					// Find last day in the month ($now is the firstday of the month)
					$NumDaysInMonth = $this->daysInMonth($now, 1);
					$daynow = $now + (($NumDaysInMonth - 1) * 24 * 60 * 60);

					$nowtime = $this->gmtime($daynow);
					while (($this->recur["weekdays"] & (1 << $nowtime["tm_wday"])) == 0) {
						$daynow -= SECONDS_PER_DAY;
						$nowtime = $this->gmtime($daynow);
					}
				}

				/*
				* checks weather the next coming day in recurrence pattern is less than or equal to end day of the			* recurring item.Also check weather the coming day in recurrence pattern is greater than or equal to start * of recurring pattern, so that appointment that fall under the recurrence range are only displayed.
				*/
				if ($daynow <= $dayend && $daynow >= $daystart) {
					$this->processOccurrenceItem($items, $start, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
				}
			}
		}

		return $items;
	}

	private function getYearlyItems(int $start, int $end, int $daystart, int $dayend, mixed $limit, mixed $remindersonly): array {
		$items = [];
		$firstday = 0;

		// everyn is the period in years, but it is calculated in months.
		// Keep that out of $this->recur, which saveRecurrence() writes back.
		$everyn = $this->recur["everyn"] <= 0 ? 12 : $this->recur["everyn"] * 12;

		if ($this->recur['regen'] && !isset($this->recur["nday"], $this->recur["weekdays"])) {
			return $this->getRegeneratedItem($daystart, $end, $dayend, (int) $everyn, $remindersonly);
		}

		for ($now = $this->yearStartOf($daystart); $now <= $dayend && ($limit == 0 || count($items) < $limit); $now += $this->daysInMonth($now, $everyn) * 24 * 60 * 60) {
			if (isset($this->recur["monthday"]) && !$this->recur['regen']) { // same as monthly, but in a specific month
				// recur["month"] is in minutes since the beginning of the year
				$month = $this->monthOfYear($this->recur["month"]); // $month is now month of year [0..11]
				$monthday = $this->recur["monthday"]; // $monthday is day of the month [1..31]
				$monthstart = $now + $this->daysInMonth($now, $month) * 24 * 60 * 60; // $monthstart is the timestamp of the beginning of the month
				if ($monthday > $this->daysInMonth($monthstart, 1)) {
					$monthday = $this->daysInMonth($monthstart, 1);
				}	// Cap $monthday on month length (eg 28 feb instead of 29 feb)
				$daynow = $monthstart + ($monthday - 1) * 24 * 60 * 60;
				$this->processOccurrenceItem($items, $start, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
			}
			elseif (isset($this->recur["nday"], $this->recur["weekdays"])) { // Nth [weekday] in month X of every N years
				// Go the correct month
				$monthnow = $now + $this->daysInMonth($now, $this->monthOfYear($this->recur["month"])) * 24 * 60 * 60;

				// Find first matching weekday in this month
				for ($wday = 0; $wday < 7; ++$wday) {
					$daynow = $monthnow + $wday * 60 * 60 * 24;
					$nowtime = $this->gmtime($daynow); // Get the weekday of the current day

					if ($this->recur["weekdays"] & (1 << $nowtime["tm_wday"])) { // Selected ?
						$firstday = $wday;
						break;
					}
				}

				// Same as above (monthly)
				$daynow = $monthnow + ($firstday + ($this->recur["nday"] - 1) * 7) * 60 * 60 * 24;

				while ($this->monthStartOf($daynow) != $this->monthStartOf($monthnow)) {
					$daynow -= 7 * 60 * 60 * 24;
				}

				$this->processOccurrenceItem($items, $start, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
			}
		}

		return $items;
	}

	/**
	 * A regenerating series has a single occurrence, the interval after its
	 * start, like the weekly one. The day of month is limited to the last day
	 * of the target month.
	 */
	private function getRegeneratedItem(int $daystart, int $end, int $dayend, int $months, mixed $remindersonly): array {
		$items = [];
		$time = $this->gmtime($daystart);
		$month = $time['tm_mon'] + 1 + $months;
		$year = $time['tm_year'] + 1900;
		$day = min($time['tm_mday'], (int) gmdate('t', gmmktime(0, 0, 0, $month, 1, $year)));
		$daynow = gmmktime(0, 0, 0, $month, $day, $year);
		if ($daynow <= $dayend) {
			$this->processOccurrenceItem($items, $daystart, $end, $daynow, $this->recur["startocc"], $this->recur["endocc"], $this->tz, $remindersonly);
		}

		return $items;
	}

	/**
	 * @psalm-return -1|0|1
	 */
	public function sortStarttime(mixed $a, mixed $b): int {
		$aTime = $a[$this->proptags["startdate"]];
		$bTime = $b[$this->proptags["startdate"]];

		return $aTime == $bTime ? 0 : ($aTime > $bTime ? 1 : -1);
	}

	/**
	 * daysInMonth.
	 *
	 * Returns the number of days in the upcoming number of months. If you specify 1 month as
	 * $months it will give you the number of days in the month of $date. If you specify more it
	 * will also count the days in the upcoming months and add that to the number of days. So
	 * if you have a date in march and you specify $months as 2 it will return 61.
	 *
	 * @param int $date   specified date as timestamp from which you want to know the number
	 *                    of days in the month
	 * @param int $months number of months you want to know the number of days in
	 *
	 * @return float|int number of days in the specified amount of months
	 */
	public function daysInMonth(int $date, int $months): float|int {
		$key = $date . ':' . $months;
		if (isset($this->daysInMonthCache[$key])) {
			return $this->daysInMonthCache[$key];
		}

		$days = 0;
		for ($i = 0; $i < $months; ++$i) {
			$days += (int) gmdate("t", $date + $days * 24 * 60 * 60);
		}

		return $this->daysInMonthCache[$key] = $days;
	}

	// Converts MAPI-style 'minutes' into the month of the year [0..11]
	public function monthOfYear(int $minutes): int {
		$d = gmmktime(0, 0, 0, 1, 1, 2001); // The year 2001 was a non-leap year, and the minutes provided are always in non-leap-year-minutes

		$d += $minutes * 60;

		$dtime = $this->gmtime($d);

		return $dtime["tm_mon"];
	}

	/**
	 * @psalm-return -1|0|1
	 */
	public function sortExceptionStart(mixed $a, mixed $b): int {
		return $a["start"] == $b["start"] ? 0 : ($a["start"] > $b["start"] ? 1 : -1);
	}

	/**
	 * Function to get all exception items in the given range.
	 *
	 * @param array $items reference to the array to be added to
	 * @param int   $start start of timeframe in GMT TIME
	 * @param int   $end   end of timeframe in GMT TIME
	 */
	public function processExceptionItems(&$items, $start, $end): void {
		$limit = 0;
		foreach ($this->recur["changed_occurrences"] as $exception) {
			// Convert to GMT
			$occstart = $this->toGMT($this->tz, $exception["start"]);
			$occend = $this->toGMT($this->tz, $exception["end"]);

			// Check range criterium. Exact matches (eg when $occstart == $end), do NOT match since you cannot
			// see any part of the appointment. Partial overlaps DO match.
			if ($occstart >= $end || $occend <= $start) {
				continue;
			}

			$items[] = $this->getExceptionProperties($exception);
			if (count($items) == $limit) {
				break;
			}
		}
	}

	/**
	 * Function to get all properties of a single changed exception.
	 *
	 * @return (mixed|true)[] associative array of properties for the exception
	 *
	 * @psalm-return array<mixed|true>
	 */
	public function getExceptionProperties(mixed $exception): array {
		// Exception has same properties as main object, with some properties overridden:
		$item = $this->messageprops;

		// Special properties
		$item["exception"] = true;
		$item["basedate"] = $exception["basedate"]; // note that the basedate is always in local time !

		// MAPI-compatible properties (you can handle an exception as a normal calendar item like this)
		$item[$this->proptags["startdate"]] = $this->toGMT($this->tz, $exception["start"]);
		$item[$this->proptags["duedate"]] = $this->toGMT($this->tz, $exception["end"]);
		$item[$this->proptags["commonstart"]] = $item[$this->proptags["startdate"]];
		$item[$this->proptags["commonend"]] = $item[$this->proptags["duedate"]];

		if (isset($exception["subject"])) {
			$item[$this->proptags["subject"]] = $exception["subject"];
		}

		if (isset($exception["label"])) {
			$item[$this->proptags["label"]] = $exception["label"];
		}

		if (isset($exception["alldayevent"])) {
			$item[$this->proptags["alldayevent"]] = $exception["alldayevent"];
		}

		if (isset($exception["location"])) {
			$item[$this->proptags["location"]] = $exception["location"];
		}

		if (isset($exception["remind_before"])) {
			$item[$this->proptags["reminder_minutes"]] = $exception["remind_before"];
		}

		if (isset($exception["reminder_set"])) {
			$item[$this->proptags["reminder"]] = $exception["reminder_set"];
		}

		if (isset($exception["busystatus"])) {
			$item[$this->proptags["busystatus"]] = $exception["busystatus"];
		}

		return $item;
	}
	abstract public function processOccurrenceItem(array &$items, false|int $start, int $end, false|int $basedate, mixed $startocc, mixed $endocc, mixed $tz, mixed $reminderonly): ?false;

	/**
	 * Calculate PidLidClipStart/PidLidClipEnd values.
	 *
	 * @param string $type only possible values are "start" or "end"
	 *
	 * @return int
	 */
	public function getClipProp(string $type): int {
		if ($type === "end" || $type === "start") {
			return $this->toGMT($this->tz, (int) $this->recur[$type]);
		}

		throw new MAPIException("ClipProp type must be either \"start\" or \"end\"");
	}
}
