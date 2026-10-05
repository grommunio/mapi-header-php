<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2007-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2026 grommunio GmbH
 */

/**
 * Timezone conversions shared by grommunio-sync and grommunio-web.
 *
 * A timezone is handled as an array with the fields of a TZREG/TZRULE
 * (MS-OXOCAL 2.2.1.39, 2.2.1.41.1), which is also the layout of the
 * ActiveSync TimeZone structure (MS-ASDTYPE 2.6.4):
 *   bias, stdbias, dstbias      offsets in minutes, UTC = local time + bias
 *   dstend*                     stStandardDate, the switch to standard time
 *   dststart*                   stDaylightDate, the switch to daylight time
 * where *day is the SYSTEMTIME wDayOfWeek and *week its wDay (1-5, 5 is the
 * last occurrence of the weekday in the month).
 */
class TimezoneUtil {
	public const LOG_DEBUG = 'debug';
	public const LOG_INFO = 'info';
	public const LOG_WARN = 'warn';
	public const LOG_ERROR = 'error';

	/**
	 * @var null|callable receives (string, string)
	 */
	private static $logger;

	/**
	 * Windows timezones and the IANA timezone CLDR maps them to (territory
	 * "001"), as in data/windowsZones.xml of gromox (CLDR 48).
	 * dev/generate_windowszones.php generates this list.
	 */
	private static $windowsZones = [
		"Dateline Standard Time" => "Etc/GMT+12",
		"UTC-11" => "Etc/GMT+11",
		"Aleutian Standard Time" => "America/Adak",
		"Hawaiian Standard Time" => "Pacific/Honolulu",
		"Marquesas Standard Time" => "Pacific/Marquesas",
		"Alaskan Standard Time" => "America/Anchorage",
		"UTC-09" => "Etc/GMT+9",
		"Pacific Standard Time (Mexico)" => "America/Tijuana",
		"UTC-08" => "Etc/GMT+8",
		"Pacific Standard Time" => "America/Los_Angeles",
		"US Mountain Standard Time" => "America/Phoenix",
		"Mountain Standard Time (Mexico)" => "America/Mazatlan",
		"Mountain Standard Time" => "America/Denver",
		"Yukon Standard Time" => "America/Whitehorse",
		"Central America Standard Time" => "America/Guatemala",
		"Central Standard Time" => "America/Chicago",
		"Easter Island Standard Time" => "Pacific/Easter",
		"Central Standard Time (Mexico)" => "America/Mexico_City",
		"Canada Central Standard Time" => "America/Regina",
		"SA Pacific Standard Time" => "America/Bogota",
		"Eastern Standard Time (Mexico)" => "America/Cancun",
		"Eastern Standard Time" => "America/New_York",
		"Haiti Standard Time" => "America/Port-au-Prince",
		"Cuba Standard Time" => "America/Havana",
		"US Eastern Standard Time" => "America/Indianapolis",
		"Turks And Caicos Standard Time" => "America/Grand_Turk",
		"Paraguay Standard Time" => "America/Asuncion",
		"Atlantic Standard Time" => "America/Halifax",
		"Venezuela Standard Time" => "America/Caracas",
		"Central Brazilian Standard Time" => "America/Cuiaba",
		"SA Western Standard Time" => "America/La_Paz",
		"Pacific SA Standard Time" => "America/Santiago",
		"Newfoundland Standard Time" => "America/St_Johns",
		"Tocantins Standard Time" => "America/Araguaina",
		"E. South America Standard Time" => "America/Sao_Paulo",
		"SA Eastern Standard Time" => "America/Cayenne",
		"Argentina Standard Time" => "America/Buenos_Aires",
		"Greenland Standard Time" => "America/Godthab",
		"Montevideo Standard Time" => "America/Montevideo",
		"Magallanes Standard Time" => "America/Punta_Arenas",
		"Saint Pierre Standard Time" => "America/Miquelon",
		"Bahia Standard Time" => "America/Bahia",
		"UTC-02" => "Etc/GMT+2",
		"Azores Standard Time" => "Atlantic/Azores",
		"Cape Verde Standard Time" => "Atlantic/Cape_Verde",
		"UTC" => "Etc/UTC",
		"GMT Standard Time" => "Europe/London",
		"Greenwich Standard Time" => "Atlantic/Reykjavik",
		"Sao Tome Standard Time" => "Africa/Sao_Tome",
		"Morocco Standard Time" => "Africa/Casablanca",
		"W. Europe Standard Time" => "Europe/Berlin",
		"Central Europe Standard Time" => "Europe/Budapest",
		"Romance Standard Time" => "Europe/Paris",
		"Central European Standard Time" => "Europe/Warsaw",
		"W. Central Africa Standard Time" => "Africa/Lagos",
		"Jordan Standard Time" => "Asia/Amman",
		"GTB Standard Time" => "Europe/Bucharest",
		"Middle East Standard Time" => "Asia/Beirut",
		"Egypt Standard Time" => "Africa/Cairo",
		"E. Europe Standard Time" => "Europe/Chisinau",
		"Syria Standard Time" => "Asia/Damascus",
		"West Bank Standard Time" => "Asia/Hebron",
		"South Africa Standard Time" => "Africa/Johannesburg",
		"FLE Standard Time" => "Europe/Kiev",
		"Israel Standard Time" => "Asia/Jerusalem",
		"South Sudan Standard Time" => "Africa/Juba",
		"Kaliningrad Standard Time" => "Europe/Kaliningrad",
		"Sudan Standard Time" => "Africa/Khartoum",
		"Libya Standard Time" => "Africa/Tripoli",
		"Namibia Standard Time" => "Africa/Windhoek",
		"Arabic Standard Time" => "Asia/Baghdad",
		"Turkey Standard Time" => "Europe/Istanbul",
		"Arab Standard Time" => "Asia/Riyadh",
		"Belarus Standard Time" => "Europe/Minsk",
		"Russian Standard Time" => "Europe/Moscow",
		"E. Africa Standard Time" => "Africa/Nairobi",
		"Iran Standard Time" => "Asia/Tehran",
		"Arabian Standard Time" => "Asia/Dubai",
		"Astrakhan Standard Time" => "Europe/Astrakhan",
		"Azerbaijan Standard Time" => "Asia/Baku",
		"Russia Time Zone 3" => "Europe/Samara",
		"Mauritius Standard Time" => "Indian/Mauritius",
		"Saratov Standard Time" => "Europe/Saratov",
		"Georgian Standard Time" => "Asia/Tbilisi",
		"Volgograd Standard Time" => "Europe/Volgograd",
		"Caucasus Standard Time" => "Asia/Yerevan",
		"Afghanistan Standard Time" => "Asia/Kabul",
		"West Asia Standard Time" => "Asia/Tashkent",
		"Ekaterinburg Standard Time" => "Asia/Yekaterinburg",
		"Pakistan Standard Time" => "Asia/Karachi",
		"Qyzylorda Standard Time" => "Asia/Qyzylorda",
		"India Standard Time" => "Asia/Calcutta",
		"Sri Lanka Standard Time" => "Asia/Colombo",
		"Nepal Standard Time" => "Asia/Katmandu",
		"Central Asia Standard Time" => "Asia/Bishkek",
		"Bangladesh Standard Time" => "Asia/Dhaka",
		"Omsk Standard Time" => "Asia/Omsk",
		"Myanmar Standard Time" => "Asia/Rangoon",
		"SE Asia Standard Time" => "Asia/Bangkok",
		"Altai Standard Time" => "Asia/Barnaul",
		"W. Mongolia Standard Time" => "Asia/Hovd",
		"North Asia Standard Time" => "Asia/Krasnoyarsk",
		"N. Central Asia Standard Time" => "Asia/Novosibirsk",
		"Tomsk Standard Time" => "Asia/Tomsk",
		"China Standard Time" => "Asia/Shanghai",
		"North Asia East Standard Time" => "Asia/Irkutsk",
		"Singapore Standard Time" => "Asia/Singapore",
		"W. Australia Standard Time" => "Australia/Perth",
		"Taipei Standard Time" => "Asia/Taipei",
		"Ulaanbaatar Standard Time" => "Asia/Ulaanbaatar",
		"Aus Central W. Standard Time" => "Australia/Eucla",
		"Transbaikal Standard Time" => "Asia/Chita",
		"Tokyo Standard Time" => "Asia/Tokyo",
		"North Korea Standard Time" => "Asia/Pyongyang",
		"Korea Standard Time" => "Asia/Seoul",
		"Yakutsk Standard Time" => "Asia/Yakutsk",
		"Cen. Australia Standard Time" => "Australia/Adelaide",
		"AUS Central Standard Time" => "Australia/Darwin",
		"E. Australia Standard Time" => "Australia/Brisbane",
		"AUS Eastern Standard Time" => "Australia/Sydney",
		"West Pacific Standard Time" => "Pacific/Port_Moresby",
		"Tasmania Standard Time" => "Australia/Hobart",
		"Vladivostok Standard Time" => "Asia/Vladivostok",
		"Lord Howe Standard Time" => "Australia/Lord_Howe",
		"Bougainville Standard Time" => "Pacific/Bougainville",
		"Russia Time Zone 10" => "Asia/Srednekolymsk",
		"Magadan Standard Time" => "Asia/Magadan",
		"Norfolk Standard Time" => "Pacific/Norfolk",
		"Sakhalin Standard Time" => "Asia/Sakhalin",
		"Central Pacific Standard Time" => "Pacific/Guadalcanal",
		"Russia Time Zone 11" => "Asia/Kamchatka",
		"New Zealand Standard Time" => "Pacific/Auckland",
		"UTC+12" => "Etc/GMT-12",
		"Fiji Standard Time" => "Pacific/Fiji",
		"Chatham Islands Standard Time" => "Pacific/Chatham",
		"UTC+13" => "Etc/GMT-13",
		"Tonga Standard Time" => "Pacific/Tongatapu",
		"Samoa Standard Time" => "Pacific/Apia",
		"Line Islands Standard Time" => "Pacific/Kiritimati",
	];

	/**
	 * Display names of Windows timezones, sent as the timezone names to
	 * ActiveSync devices. Timezones without one use their key.
	 */
	private static $displayNames = [
		"Dateline Standard Time" => "(GMT-12:00) International Date Line West",
		"UTC-11" => "(GMT-11:00) Midway Island, Samoa",
		"Hawaiian Standard Time" => "(GMT-10:00) Hawaii",
		"Alaskan Standard Time" => "(GMT-09:00) Alaska",
		"Pacific Standard Time" => "(GMT-08:00) Pacific Time (US and Canada); Tijuana",
		"US Mountain Standard Time" => "(GMT-07:00) Arizona",
		"Mountain Standard Time (Mexico)" => "(GMT-07:00) Chihuahua, La Paz, Mazatlan",
		"Mountain Standard Time" => "(GMT-07:00) Mountain Time (US and Canada)",
		"Central America Standard Time" => "(GMT-06:00) Central America",
		"Central Standard Time" => "(GMT-06:00) Central Time (US and Canada",
		"Central Standard Time (Mexico)" => "(GMT-06:00) Guadalajara, Mexico City, Monterrey",
		"Canada Central Standard Time" => "(GMT-06:00) Saskatchewan",
		"SA Pacific Standard Time" => "(GMT-05:00) Bogota, Lima, Quito",
		"Eastern Standard Time" => "(GMT-05:00) Eastern Time (US and Canada)",
		"US Eastern Standard Time" => "(GMT-05:00) Indiana (East)",
		"Atlantic Standard Time" => "(GMT-04:00) Atlantic Time (Canada)",
		"SA Western Standard Time" => "(GMT-04:00) Caracas, La Paz",
		"Pacific SA Standard Time" => "(GMT-04:00) Santiago",
		"Newfoundland Standard Time" => "(GMT-03:30) Newfoundland and Labrador",
		"E. South America Standard Time" => "(GMT-03:00) Brasilia",
		"SA Eastern Standard Time" => "(GMT-03:00) Buenos Aires, Georgetown",
		"Azores Standard Time" => "(GMT-01:00) Azores",
		"Cape Verde Standard Time" => "(GMT-01:00) Cape Verde Islands",
		"GMT Standard Time" => "(GMT) Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London",
		"Greenwich Standard Time" => "(GMT) Casablanca, Monrovia",
		"W. Europe Standard Time" => "(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
		"Central Europe Standard Time" => "(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
		"Romance Standard Time" => "(GMT+01:00) Brussels, Copenhagen, Madrid, Paris",
		"Central European Standard Time" => "(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb",
		"W. Central Africa Standard Time" => "(GMT+01:00) West Central Africa",
		"GTB Standard Time" => "(GMT+02:00) Athens, Istanbul, Minsk",
		"Egypt Standard Time" => "(GMT+02:00) Cairo",
		"E. Europe Standard Time" => "(GMT+02:00) Bucharest",
		"South Africa Standard Time" => "(GMT+02:00) Harare, Pretoria",
		"FLE Standard Time" => "(GMT+02:00) Helsinki, Kiev, Riga, Sofia, Tallinn, Vilnius",
		"Israel Standard Time" => "(GMT+02:00) Jerusalem",
		"Arabic Standard Time" => "(GMT+03:00) Baghdad",
		"Arab Standard Time" => "(GMT+03:00) Kuwait, Riyadh",
		"Russian Standard Time" => "(GMT+03:00) Moscow, St. Petersburg, Volgograd",
		"E. Africa Standard Time" => "(GMT+03:00) Nairobi",
		"Iran Standard Time" => "(GMT+03:30) Tehran",
		"Arabian Standard Time" => "(GMT+04:00) Abu Dhabi, Muscat",
		"Caucasus Standard Time" => "(GMT+04:00) Baku, Tbilisi, Yerevan",
		"Afghanistan Standard Time" => "(GMT+04:30) Kabul",
		"West Asia Standard Time" => "(GMT+05:00) Islamabad, Karachi, Tashkent",
		"Ekaterinburg Standard Time" => "(GMT+05:00) Ekaterinburg",
		"India Standard Time" => "(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
		"Nepal Standard Time" => "(GMT+05:45) Kathmandu",
		"Central Asia Standard Time" => "(GMT+06:00) Astana, Dhaka",
		"Myanmar Standard Time" => "(GMT+06:30) Yangon Rangoon",
		"SE Asia Standard Time" => "(GMT+07:00) Bangkok, Hanoi, Jakarta",
		"North Asia Standard Time" => "(GMT+07:00) Krasnoyarsk",
		"China Standard Time" => "(GMT+08:00) Beijing, Chongqing, Hong Kong SAR, Urumqi",
		"North Asia East Standard Time" => "(GMT+08:00) Irkutsk, Ulaanbaatar",
		"Singapore Standard Time" => "(GMT+08:00) Kuala Lumpur, Singapore",
		"W. Australia Standard Time" => "(GMT+08:00) Perth",
		"Taipei Standard Time" => "(GMT+08:00) Taipei",
		"Tokyo Standard Time" => "(GMT+09:00) Osaka, Sapporo, Tokyo",
		"Korea Standard Time" => "(GMT+09:00) Seoul",
		"Yakutsk Standard Time" => "(GMT+09:00) Yakutsk",
		"Cen. Australia Standard Time" => "(GMT+09:30) Adelaide",
		"AUS Central Standard Time" => "(GMT+09:30) Darwin",
		"E. Australia Standard Time" => "(GMT+10:00) Brisbane",
		"AUS Eastern Standard Time" => "(GMT+10:00) Canberra, Melbourne, Sydney",
		"West Pacific Standard Time" => "(GMT+10:00) Guam, Port Moresby",
		"Tasmania Standard Time" => "(GMT+10:00) Hobart",
		"Vladivostok Standard Time" => "(GMT+10:00) Vladivostok",
		"Central Pacific Standard Time" => "(GMT+11:00) Magadan, Solomon Islands, New Caledonia",
		"New Zealand Standard Time" => "(GMT+12:00) Auckland, Wellington",
		"Fiji Standard Time" => "(GMT+12:00) Fiji Islands, Kamchatka, Marshall Islands",
		"Tonga Standard Time" => "(GMT+13:00) Nuku'alofa",
	];

	/**
	 * Older display names, e.g. in PidLidTimeZoneDescription, of timezones
	 * which changed since.
	 */
	private static $legacyDisplayNames = [
		"(GMT-04:30) Caracas" => "Venezuela Standard Time",
		"(GMT-03:00) Greenland" => "Greenland Standard Time",
		"(GMT+06:00) Sri Jayawardenepura" => "Sri Lanka Standard Time",
		"(GMT+06:00) Almaty, Novosibirsk" => "N. Central Asia Standard Time",
		"(GMT-02:00) Mid-Atlantic" => "UTC-02",
	];

	/**
	 * Sets the function which receives the log messages of this class.
	 *
	 * @param null|callable $logger called with (string $level, string $message),
	 *                              $level being one of the LOG_* constants
	 */
	public static function SetLogger(?callable $logger): void {
		self::$logger = $logger;
	}

	private static function log(string $level, string $message): void {
		if (self::$logger !== null) {
			(self::$logger)($level, $message);
		}
	}

	/**
	 * Returns the binary timezone definition.
	 *
	 * @param false|string $phptimezone (opt) a php timezone string.
	 *                                  If omitted the env. default timezone is used.
	 *
	 * @return false|string
	 */
	public static function GetBinaryTZ($phptimezone = false) {
		if ($phptimezone === false) {
			$phptimezone = date_default_timezone_get();
		}
		self::log(self::LOG_DEBUG, sprintf("TimezoneUtil::GetBinaryTZ() for %s", $phptimezone));

		try {
			return mapi_ianatz_to_tzdef($phptimezone);
		}
		catch (Exception) {
			self::log(self::LOG_WARN, sprintf("TimezoneUtil::GetBinaryTZ() mapi_ianatz_to_tzdef() for '%s' failed!", $phptimezone));
		}

		return false;
	}

	/**
	 * Returns a full timezone array.
	 *
	 * @param false|string $phptimezone (opt) a php timezone string.
	 *                                  If omitted the env. default timezone is used.
	 *
	 * @return array
	 */
	public static function GetFullTZ($phptimezone = false) {
		if ($phptimezone === false) {
			$phptimezone = date_default_timezone_get();
		}

		self::log(self::LOG_DEBUG, "TimezoneUtil::GetFullTZ() for " . $phptimezone);

		return self::GetFullTZFromTZName($phptimezone);
	}

	/**
	 * Returns a full timezone array of a Windows or php timezone, with the
	 * rules gromox has for it.
	 *
	 * @param string $tzname a Windows timezone, e.g. "W. Europe Standard Time",
	 *                       or a php timezone, e.g. "Europe/Vienna"
	 *
	 * @return array
	 */
	public static function GetFullTZFromTZName($tzname) {
		$zone = self::getZone(self::getPhpTimezone((string) $tzname));
		if ($zone === null) {
			self::log(self::LOG_ERROR, sprintf("TimezoneUtil::GetFullTZFromTZName() no timezone found for '%s'. Returning 'GMT Standard Time'.", $tzname));
			$zone = self::getZone(self::$windowsZones["GMT Standard Time"]);
		}
		$tz = $zone['tz'];
		$tz['tzname'] = $tz['tznamedst'] = self::encodeTZName(self::getDisplayName($zone['name']));

		return $tz;
	}

	/**
	 * Sets the timezone names to those of the timezone with the same rules.
	 *
	 * @param mixed $tz
	 *
	 * @return array
	 */
	public static function FillTZNames($tz) {
		if (!isset($tz["bias"])) {
			self::log(self::LOG_WARN, "TimezoneUtil::FillTZNames() submitted TZ array does not have a bias");

			return $tz;
		}
		self::log(self::LOG_DEBUG, "TimezoneUtil::FillTZNames() filling up bias " . $tz["bias"]);

		$zone = self::findZone($tz) ?? self::findZone($tz, true);
		$tz['tzname'] = $tz['tznamedst'] = self::encodeTZName(self::getDisplayName($zone['name'] ?? "Customized Time Zone"));

		return $tz;
	}

	/**
	 * Encodes the tz name to UTF-16 compatible with a syncblob.
	 *
	 * @param string $name timezone name
	 *
	 * @return string
	 */
	private static function encodeTZName($name) {
		return substr(iconv('UTF-8', 'UTF-16', $name), 2, -1);
	}

	/**
	 * Pack timezone info for Sync.
	 *
	 * @param array $tz
	 *
	 * @return string
	 */
	public static function GetSyncBlobFromTZ($tz) {
		// set the correct TZ name (done using the Bias)
		if (!isset($tz["tzname"]) || !isset($tz["tznamedst"])) {
			$tz = TimezoneUtil::FillTZNames($tz);
		}

		return pack(
			"la64vvvvvvvvla64vvvvvvvvl",
			$tz["bias"],
			$tz["tzname"],
			0,
			$tz["dstendmonth"],
			$tz["dstendday"],
			$tz["dstendweek"],
			$tz["dstendhour"],
			$tz["dstendminute"],
			$tz["dstendsecond"],
			$tz["dstendmillis"],
			$tz["stdbias"],
			$tz["tznamedst"],
			0,
			$tz["dststartmonth"],
			$tz["dststartday"],
			$tz["dststartweek"],
			$tz["dststarthour"],
			$tz["dststartminute"],
			$tz["dststartsecond"],
			$tz["dststartmillis"],
			$tz["dstbias"]
		);
	}

	/**
	 * Returns the Windows timezone of a timezone description, like
	 * PidLidTimeZoneDescription. E.g. "W. Europe Standard Time" for
	 * "(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna".
	 * An unknown description gives a timezone with its offset.
	 *
	 * @param false|string $winTz Timezone name in windows
	 *
	 * @return string timezone name
	 */
	public static function GetTZNameFromWinTZ($winTz = false) {
		// Return "GMT Standard Time" per default
		if ($winTz === false) {
			return "GMT Standard Time";
		}

		if (isset(self::$windowsZones[$winTz])) {
			return $winTz;
		}
		// newer Windows writes e.g. "(UTC-05:00) Eastern Time (US & Canada)"
		$normalize = static fn (string $name): string => preg_replace(['/^\(utc/', '/ & /', '/[^a-z0-9()+:-]+/'], ['(gmt', ' and ', ' '], strtolower($name));
		$description = $normalize($winTz);
		foreach ([self::$displayNames, array_flip(self::$legacyDisplayNames)] as $names) {
			foreach ($names as $name => $displayName) {
				if ($normalize($displayName) === $description) {
					return $name;
				}
			}
		}

		// gromox' iCalendar import sets the TZID, often a php timezone
		if (str_contains($winTz, '/')) {
			$zone = self::getZone(self::getPhpTimezone($winTz));
			if ($zone !== null && isset(self::$windowsZones[$zone['name']])) {
				return $zone['name'];
			}
		}

		// "(GMT+01:00) Amsterdam, ...", "(UTC+01:00) ..." or "(GMT +01:00)" of BaseRecurrence
		if (preg_match('/^\((?:GMT|UTC)(?: ?([+-])(\d\d):(\d\d))?\)/', $winTz, $matches)) {
			$bias = isset($matches[1]) ? ($matches[1] === '-' ? 1 : -1) * ($matches[2] * 60 + $matches[3]) : 0;
			$zone = self::findZone(["bias" => $bias, "stdbias" => 0, "dstbias" => 0, "dststartmonth" => 0, "dstendmonth" => 0], true);
			if ($zone !== null && isset(self::$windowsZones[$zone['name']])) {
				return $zone['name'];
			}
		}

		return "GMT Standard Time";
	}

	/**
	 * Returns an GMT timezone array.
	 *
	 * @return array
	 */
	public static function GetGMTTz() {
		return [
			"bias" => 0,
			"tzname" => "",
			"dstendyear" => 0,
			"dstendmonth" => 10,
			"dstendday" => 0,
			"dstendweek" => 5,
			"dstendhour" => 2,
			"dstendminute" => 0,
			"dstendsecond" => 0,
			"dstendmillis" => 0,
			"stdbias" => 0,
			"tznamedst" => "",
			"dststartyear" => 0,
			"dststartmonth" => 3,
			"dststartday" => 0,
			"dststartweek" => 5,
			"dststarthour" => 1,
			"dststartminute" => 0,
			"dststartsecond" => 0,
			"dststartmillis" => 0,
			"dstbias" => -60,
		];
	}

	/**
	 * Converts timezone definition to timezonetag prop
	 * ("PT_BINARY:PSETID_Appointment:0x8233" aka
	 * "PT_BINARY:PSETID_Appointment:" . PidLidTimeZoneStruct)
	 * compatible structure.
	 *
	 * @param array $tzdef timezone definition array
	 *
	 * @return array
	 */
	public static function GetTzFromTimezoneDef($tzdef) {
		$rule = getEffectiveTimezoneRule($tzdef);
		// Fallback if there isn't effective timezone
		$tz = $rule !== null ? self::getTzFromRule($rule) : self::GetGMTTz();

		// Make the structure compatible with class.recurrence.php
		$tz['timezone'] = $tz['bias'];
		$tz['timezonedst'] = $tz['dstbias'];

		return $tz;
	}

	/**
	 * Timezone array of a TZRULE of a parsed timezone definition.
	 */
	private static function getTzFromRule(array $rule): array {
		return [
			'tzname' => '',
			'tznamedst' => '',
			'bias' => $rule['bias'],
			'dstendyear' => $rule['stStandardDate']['year'],
			'dstendmonth' => $rule['stStandardDate']['month'],
			'dstendday' => $rule['stStandardDate']['dayofweek'],
			'dstendweek' => $rule['stStandardDate']['day'],
			'dstendhour' => $rule['stStandardDate']['hour'],
			'dstendminute' => $rule['stStandardDate']['minute'],
			'dstendsecond' => $rule['stStandardDate']['second'],
			'dstendmillis' => $rule['stStandardDate']['miliseconds'],
			'stdbias' => $rule['stdbias'],
			'dststartyear' => $rule['stDaylightDate']['year'],
			'dststartmonth' => $rule['stDaylightDate']['month'],
			'dststartday' => $rule['stDaylightDate']['dayofweek'],
			'dststartweek' => $rule['stDaylightDate']['day'],
			'dststarthour' => $rule['stDaylightDate']['hour'],
			'dststartminute' => $rule['stDaylightDate']['minute'],
			'dststartsecond' => $rule['stDaylightDate']['second'],
			'dststartmillis' => $rule['stDaylightDate']['miliseconds'],
			'dstbias' => $rule['dstbias'],
		];
	}

	/**
	 * Unpacks a PidLidTimeZoneStruct (TZREG, MS-OXOCAL 2.2.1.39).
	 *
	 * @param string $data
	 *
	 * @return array|false
	 */
	public static function GetTzFromTimezoneStruct($data) {
		return unpack("lbias/lstdbias/ldstbias/" .
						   "vconst1/vdstendyear/vdstendmonth/vdstendday/vdstendweek/vdstendhour/vdstendminute/vdstendsecond/vdstendmillis/" .
						   "vconst2/vdststartyear/vdststartmonth/vdststartday/vdststartweek/vdststarthour/vdststartminute/vdststartsecond/vdststartmillis", $data);
	}

	/**
	 * Packs a timezone array as PidLidTimeZoneStruct (TZREG, MS-OXOCAL 2.2.1.39).
	 *
	 * @param array $tz
	 *
	 * @return string
	 */
	public static function GetTimezoneStructFromTz($tz) {
		return pack(
			"lllvvvvvvvvvvvvvvvvvv",
			$tz["bias"],
			$tz["stdbias"],
			$tz["dstbias"],
			0,
			0,
			$tz["dstendmonth"],
			$tz["dstendday"],
			$tz["dstendweek"],
			$tz["dstendhour"],
			$tz["dstendminute"],
			$tz["dstendsecond"],
			$tz["dstendmillis"],
			0,
			0,
			$tz["dststartmonth"],
			$tz["dststartday"],
			$tz["dststartweek"],
			$tz["dststarthour"],
			$tz["dststartminute"],
			$tz["dststartsecond"],
			$tz["dststartmillis"]
		);
	}

	/**
	 * Unpacks an ActiveSync TimeZone structure (MS-ASDTYPE 2.6.4).
	 *
	 * @param string $data
	 *
	 * @return array|false
	 */
	public static function GetTzFromSyncBlob($data) {
		$tz = unpack("lbias/a64tzname/vdstendyear/vdstendmonth/vdstendday/vdstendweek/vdstendhour/vdstendminute/vdstendsecond/vdstendmillis/" .
						"lstdbias/a64tznamedst/vdststartyear/vdststartmonth/vdststartday/vdststartweek/vdststarthour/vdststartminute/vdststartsecond/vdststartmillis/" .
						"ldstbias", $data);

		// Make the structure compatible with class.recurrence.php
		$tz["timezone"] = $tz["bias"];
		$tz["timezonedst"] = $tz["dstbias"];

		return $tz;
	}

	/**
	 * Returns the UTC time of a local time in the given timezone.
	 *
	 * @param int        $localtime
	 * @param null|array $tz
	 *
	 * @return int
	 */
	public static function GetUtcTimeByTz($localtime, $tz) {
		if (!isset($tz) || !is_array($tz)) {
			return $localtime;
		}

		return $localtime + self::getBias($tz, self::IsDst($localtime, $tz)) * 60;
	}

	/**
	 * Returns the local time of a UTC time in the given timezone.
	 *
	 * @param int        $utctime
	 * @param null|array $tz
	 *
	 * @return int
	 */
	public static function GetLocalTimeByTz($utctime, $tz) {
		if (!isset($tz) || !is_array($tz)) {
			return $utctime;
		}

		return $utctime - self::GetBiasAtUtc($utctime, $tz) * 60;
	}

	/**
	 * Returns the bias in effect at a UTC time in the given timezone,
	 * so that UTC = local time + bias.
	 *
	 * @param int   $utctime
	 * @param array $tz
	 *
	 * @return int bias in minutes
	 */
	public static function GetBiasAtUtc($utctime, $tz) {
		if (!is_array($tz)) {
			return 0;
		}

		return self::getBias($tz, self::IsDstAtUtc($utctime, $tz));
	}

	/**
	 * Returns true if daylight saving time is in effect at a local time
	 * (wall clock) in the given timezone.
	 *
	 * The switch to daylight time is given in local standard time and the
	 * switch back in local daylight time (MS-OXOCAL 2.2.1.41.1), which is how
	 * a wall clock shows them.
	 *
	 * @param int   $localtime
	 * @param array $tz
	 *
	 * @return bool
	 */
	public static function IsDst($localtime, $tz) {
		if (!is_array($tz) || !self::hasDst($tz)) {
			return false;
		}

		$year = (int) gmdate("Y", $localtime);

		return self::isBetweenTransitions(
			$localtime,
			self::getTransitionTime($year, $tz, "dststart"),
			self::getTransitionTime($year, $tz, "dstend")
		);
	}

	/**
	 * Returns true if daylight saving time is in effect at a UTC time in the
	 * given timezone.
	 *
	 * @param int   $utctime
	 * @param array $tz
	 *
	 * @return bool
	 */
	public static function IsDstAtUtc($utctime, $tz) {
		if (!is_array($tz) || !self::hasDst($tz)) {
			return false;
		}

		$year = (int) gmdate("Y", $utctime - self::getBias($tz, false) * 60);

		return self::isBetweenTransitions(
			$utctime,
			self::getTransitionTime($year, $tz, "dststart") + self::getBias($tz, false) * 60,
			self::getTransitionTime($year, $tz, "dstend") + self::getBias($tz, true) * 60
		);
	}

	/**
	 * Returns true if two timezone arrays describe the same offsets and
	 * daylight saving time transitions.
	 *
	 * @param array $tz1
	 * @param array $tz2
	 *
	 * @return bool
	 */
	public static function TzEquals($tz1, $tz2) {
		if (!is_array($tz1) || !is_array($tz2) || !isset($tz1["bias"], $tz2["bias"])) {
			return false;
		}
		if ($tz1["bias"] != $tz2["bias"] || ($tz1["stdbias"] ?? 0) != ($tz2["stdbias"] ?? 0)) {
			return false;
		}
		$hasDst = self::hasDst($tz1);
		if ($hasDst !== self::hasDst($tz2)) {
			return false;
		}
		if (!$hasDst) {
			return true;
		}
		if ($tz1["dstbias"] != $tz2["dstbias"]) {
			return false;
		}
		foreach (["dststart", "dstend"] as $transition) {
			foreach (["month", "day", "week", "hour", "minute", "second"] as $field) {
				if (($tz1[$transition . $field] ?? 0) != ($tz2[$transition . $field] ?? 0)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns true if the effective rule of a PidLidAppointmentTimeZoneDefinition*
	 * blob describes the timezone array.
	 *
	 * @param mixed $tzdef
	 * @param array $tz
	 *
	 * @return bool
	 */
	public static function IsTimezoneDefinitionOf($tzdef, $tz) {
		if (!is_string($tzdef)) {
			return false;
		}
		$rule = getEffectiveTimezoneRule(parseTimezoneDefinition($tzdef));

		return $rule !== null && self::TzEquals($tz, self::getTzFromRule($rule));
	}

	/**
	 * Builds a PidLidAppointmentTimeZoneDefinition* blob (TZDEFINITION,
	 * MS-OXOCAL 2.2.1.41) with a single rule from a timezone array.
	 *
	 * @param array  $tz
	 * @param string $keyname name of the timezone, e.g. "W. Europe Standard Time"
	 * @param int    $flags   TZRULE_FLAG_* of the rule
	 *
	 * @return string
	 */
	public static function GetTimezoneDefinitionFromTz($tz, $keyname, $flags = TZRULE_FLAG_EFFECTIVE_TZREG) {
		$keyname = iconv('UTF-8', 'UTF-16LE', $keyname);
		$cchKeyName = intdiv(strlen($keyname), 2);
		$hasDst = self::hasDst($tz);
		$systemtime = static fn (string $transition): string => $hasDst ?
			pack(
				"vvvvvvvv",
				0,
				$tz[$transition . "month"],
				$tz[$transition . "day"],
				$tz[$transition . "week"],
				$tz[$transition . "hour"],
				$tz[$transition . "minute"],
				$tz[$transition . "second"] ?? 0,
				$tz[$transition . "millis"] ?? 0
			) :
			str_repeat("\0", 16);

		return pack("CCvvv", 2, 1, 6 + 2 * $cchKeyName, TZDEFINITION_FLAG_VALID_KEYNAME, $cchKeyName) . $keyname .
			pack("v", 1) .
			pack("CCvvv", 2, 1, 0x003E, $flags, 1601) . str_repeat("\0", 14) .
			pack("lll", $tz["bias"], $tz["stdbias"] ?? 0, $tz["dstbias"] ?? 0) .
			$systemtime("dstend") . $systemtime("dststart");
	}

	/**
	 * Sets the TZRULE flags of the effective rule of a timezone definition
	 * and clears them on all other rules, as MS-OXOCAL 2.2.1.41.2 requires.
	 * PidLidAppointmentTimeZoneDefinitionRecur needs
	 * TZRULE_FLAG_EFFECTIVE_TZREG | TZRULE_FLAG_RECUR_CURRENT_TZREG.
	 *
	 * @param string $tzdef
	 * @param int    $flags
	 *
	 * @return false|string false if the definition has no effective rule
	 */
	public static function SetTimezoneDefinitionFlags($tzdef, $flags) {
		$parsed = parseTimezoneDefinition($tzdef);
		if (empty($parsed) || getEffectiveTimezoneRule($parsed) === null) {
			return false;
		}
		// the rules follow the key name, as parseTimezoneDefinition() reads them
		$offset = 8 + strlen($parsed['keyname']) + 2;
		foreach ($parsed['rules'] as $rule) {
			$ruleFlags = ($rule['tzruleflags'] & TZRULE_FLAG_EFFECTIVE_TZREG) ? $flags : 0;
			$tzdef = substr_replace($tzdef, pack("v", $ruleFlags), $offset + 4, 2);
			$offset += 66;
		}

		return $tzdef;
	}

	/**
	 * Returns a PidLidAppointmentTimeZoneDefinition* blob for a timezone array.
	 *
	 * The definition gromox has for a timezone with the same rules is
	 * preferred, the server's timezone first. Otherwise the definition is
	 * built from the timezone array.
	 *
	 * @param array $tz
	 *
	 * @return string
	 */
	public static function GetTimezoneDefinitionForTz($tz) {
		$zone = self::findZone($tz);
		if (isset($zone['tzdef'])) {
			return $zone['tzdef'];
		}
		$keyname = $zone['name'] ?? "Customized Time Zone";
		self::log(self::LOG_DEBUG, sprintf("TimezoneUtil::GetTimezoneDefinitionForTz(): no timezone of gromox matches, building '%s'", $keyname));

		return self::GetTimezoneDefinitionFromTz($tz, $keyname);
	}

	/**
	 * Moves the start of an all-day event, which is midnight in the timezone
	 * $fromTzdef, to midnight of the same day in the timezone $toTzdef.
	 *
	 * @param int    $start     UTC timestamp
	 * @param string $fromTzdef PidLidAppointmentTimeZoneDefinition* blob
	 * @param string $toTzdef   PidLidAppointmentTimeZoneDefinition* blob
	 *
	 * @return null|int null if a definition has no effective rule
	 */
	public static function ConvertAllDayStart($start, $fromTzdef, $toTzdef) {
		$fromRule = getEffectiveTimezoneRule(parseTimezoneDefinition($fromTzdef));
		$toRule = getEffectiveTimezoneRule(parseTimezoneDefinition($toTzdef));
		if ($fromRule === null || $toRule === null) {
			return null;
		}

		// the wall clock time in $fromTzdef is the same in $toTzdef
		return self::GetUtcTimeByTz(self::GetLocalTimeByTz($start, self::getTzFromRule($fromRule)), self::getTzFromRule($toRule));
	}

	private static function hasDst(array $tz): bool {
		return !empty($tz["dstbias"]) && !empty($tz["dststartmonth"]) && !empty($tz["dstendmonth"]);
	}

	private static function getBias(array $tz, bool $dst): int {
		return $tz["bias"] + ($dst ? $tz["dstbias"] : ($tz["stdbias"] ?? 0));
	}

	/**
	 * Daylight saving time lasts from $start (inclusive) to $end (exclusive),
	 * across the turn of the year on the southern hemisphere.
	 */
	private static function isBetweenTransitions(int $time, int $start, int $end): bool {
		if ($start < $end) {
			return $time >= $start && $time < $end;
		}

		return !($time >= $end && $time < $start);
	}

	/**
	 * Wall clock time of a transition ("dststart" or "dstend") in $year, as
	 * a timestamp. The transition happens on the n-th (*week, 1-5 where 5 is
	 * the last) weekday (*day) of the month.
	 */
	private static function getTransitionTime(int $year, array $tz, string $transition): int {
		$month = (int) $tz[$transition . "month"];
		$firstOfMonth = gmmktime(0, 0, 0, $month, 1, $year);
		$day = 1 + (((int) $tz[$transition . "day"] - (int) gmdate("w", $firstOfMonth) + 7) % 7) +
			7 * (max(1, (int) $tz[$transition . "week"]) - 1);
		$daysInMonth = (int) gmdate("t", $firstOfMonth);
		while ($day > $daysInMonth) {
			$day -= 7;
		}

		return gmmktime((int) $tz[$transition . "hour"], (int) $tz[$transition . "minute"], (int) ($tz[$transition . "second"] ?? 0), $month, $day, $year);
	}

	/**
	 * The php timezone of a Windows or php timezone name.
	 */
	private static function getPhpTimezone(string $name): ?string {
		if (isset(self::$windowsZones[$name])) {
			return self::$windowsZones[$name];
		}
		// older names had their dots removed, e.g. "W Europe Standard Time"
		foreach (self::$windowsZones as $windowsZone => $phptimezone) {
			if (str_replace('.', '', $windowsZone) === $name) {
				return $phptimezone;
			}
		}

		try {
			new DateTimeZone($name);
		}
		catch (Exception) {
			return null;
		}

		return $name;
	}

	/**
	 * The rules of a php timezone, as gromox has them (mapi_ianatz_to_tzdef()),
	 * or else as the php timezone database has them for this year.
	 *
	 * @return null|array{tz: array, name: string, tzdef: null|string} name
	 *                                                                 is the Windows timezone, if known
	 */
	private static function getZone(?string $phptimezone): ?array {
		static $zones = [];
		if ($phptimezone === null) {
			return null;
		}
		if (array_key_exists($phptimezone, $zones)) {
			return $zones[$phptimezone];
		}

		try {
			$tzdef = mapi_ianatz_to_tzdef($phptimezone);
		}
		catch (Exception) {
			$tzdef = false;
		}
		$parsed = is_string($tzdef) ? parseTimezoneDefinition($tzdef) : [];
		$rule = getEffectiveTimezoneRule($parsed);
		if ($rule !== null) {
			return $zones[$phptimezone] = [
				'tz' => self::getTzFromRule($rule),
				'name' => iconv('UTF-16LE', 'UTF-8', $parsed['keyname']),
				'tzdef' => $tzdef,
			];
		}

		self::log(self::LOG_DEBUG, sprintf("TimezoneUtil: gromox has no definition of '%s', using the php timezone database", $phptimezone));
		$tz = self::getTzFromPhpTimezone($phptimezone);
		if ($tz === null) {
			return $zones[$phptimezone] = null;
		}
		$name = array_search($phptimezone, self::$windowsZones, true);
		if ($name === false) {
			// a Windows timezone with the same rules
			$name = $phptimezone;
			foreach (self::$windowsZones as $windowsZone => $other) {
				if ($other !== $phptimezone && ($zone = self::getZone($other)) !== null && self::TzEquals($tz, $zone['tz'])) {
					$name = $windowsZone;

					break;
				}
			}
		}

		return $zones[$phptimezone] = ['tz' => $tz, 'name' => $name, 'tzdef' => null];
	}

	/**
	 * The rules of a php timezone in the current year.
	 */
	private static function getTzFromPhpTimezone(string $phptimezone): ?array {
		try {
			$zone = new DateTimeZone($phptimezone);
		}
		catch (Exception) {
			return null;
		}
		$year = (int) gmdate("Y");
		$transitions = $zone->getTransitions(gmmktime(0, 0, 0, 1, 1, $year), gmmktime(0, 0, 0, 1, 1, $year + 1));
		if (empty($transitions)) {
			return null;
		}
		$toDst = $toStd = null;
		$offset = $transitions[0]['offset'];
		foreach (array_slice($transitions, 1) as $transition) {
			// e.g. only the abbreviation changes
			if ($transition['offset'] === $offset) {
				continue;
			}
			$offset = $transition['offset'];
			if ($transition['isdst']) {
				$toDst ??= $transition;
			}
			else {
				$toStd ??= $transition;
			}
		}

		$tz = [
			'tzname' => '',
			'tznamedst' => '',
			'stdbias' => 0,
		];
		if ($toDst === null || $toStd === null) {
			$offset = $transitions[count($transitions) - 1]['offset'];
			foreach ($transitions as $transition) {
				if (!$transition['isdst']) {
					$offset = $transition['offset'];
				}
			}
			$tz['bias'] = -intdiv($offset, 60);
			$tz['dstbias'] = 0;
			foreach (["dststart", "dstend"] as $name) {
				foreach (["year", "month", "day", "week", "hour", "minute", "second", "millis"] as $field) {
					$tz[$name . $field] = 0;
				}
			}

			return $tz;
		}

		$tz['bias'] = -intdiv($toStd['offset'], 60);
		$tz['dstbias'] = -intdiv($toDst['offset'] - $toStd['offset'], 60);
		// the switch to daylight time in standard time, the switch back in daylight time
		foreach (["dststart" => $toDst['ts'] + $toStd['offset'], "dstend" => $toStd['ts'] + $toDst['offset']] as $name => $wallclock) {
			$day = (int) gmdate("j", $wallclock);
			$tz[$name . "year"] = 0;
			$tz[$name . "month"] = (int) gmdate("n", $wallclock);
			$tz[$name . "day"] = (int) gmdate("w", $wallclock);
			$tz[$name . "week"] = $day + 7 > (int) gmdate("t", $wallclock) ? 5 : intdiv($day - 1, 7) + 1;
			$tz[$name . "hour"] = (int) gmdate("G", $wallclock);
			$tz[$name . "minute"] = (int) gmdate("i", $wallclock);
			$tz[$name . "second"] = (int) gmdate("s", $wallclock);
			$tz[$name . "millis"] = 0;
		}

		return $tz;
	}

	/**
	 * The timezone with the rules of $tz, the server's timezone first, or the
	 * first one with the same bias if $biasOnly. A timezone gromox has a
	 * definition of is preferred.
	 *
	 * @return null|array see getZone()
	 */
	private static function findZone(array $tz, bool $biasOnly = false): ?array {
		static $found = [];
		$key = serialize([$biasOnly, $tz["bias"] ?? null, $tz["stdbias"] ?? 0, self::hasDst($tz) ? array_intersect_key($tz, array_flip([
			"dstbias", "dststartmonth", "dststartday", "dststartweek", "dststarthour", "dststartminute", "dststartsecond",
			"dstendmonth", "dstendday", "dstendweek", "dstendhour", "dstendminute", "dstendsecond",
		])) : null]);
		if (array_key_exists($key, $found)) {
			return $found[$key];
		}

		// one gromox has a definition of comes first
		$match = null;
		foreach (array_merge([date_default_timezone_get()], array_values(self::$windowsZones)) as $phptimezone) {
			$zone = self::getZone($phptimezone);
			if ($zone === null || !($biasOnly ? $zone['tz']['bias'] == $tz['bias'] : self::TzEquals($tz, $zone['tz']))) {
				continue;
			}
			if ($zone['tzdef'] !== null) {
				return $found[$key] = $zone;
			}
			$match ??= $zone;
		}

		return $found[$key] = $match;
	}

	private static function getDisplayName(string $name): string {
		return self::$displayNames[$name] ?? $name;
	}
}
