2.4 (2026-10-10)
================

Fixes:

* Yearly series no longer turn into series every 12 (then 144) years after
  repeated expansion or after adding an exception.
* Monthly and yearly occurrences no longer drift on servers west of UTC, and
  days near the server's DST changes are no longer off by one.
* Timezones whose DST transition is not on a Sunday (e.g. Asia/Jerusalem,
  Africa/Cairo, America/Santiago) are now handled correctly; transition
  weekdays are read and written in full.
* The DST boundaries are cached per timezone instead of per year only.
* A deleted occurrence is no longer reported as a regular one right after
  being deleted, and the reminder of a new exception is now stored in the
  recurrence blob.
* Series with an interval of 0 are rejected instead of raising
  DivisionByZeroError.
* The recurrence pattern text for yearly series now states the interval in
  years.
* Recurrence no longer fails with an undefined pgettext() when loaded without
  bootstrap.php (grommunio-sync).
* A float-to-int deprecation warning when saving recurrences with seconds in
  the start time has been resolved.
* Meeting responses to an occurrence now name the right day in the GlobalId
  on servers west of UTC.
* PidLidStartRecurTime and related properties are no longer shifted by the
  server's UTC offset.
* Updates to a single occurrence now carry its original start time, so
  Outlook for Mac no longer reverts the change and creates a duplicate.
* Responding to a recurring request whose series is missing no longer opens
  the store root as the series.
* Resource booking no longer fails when the owner address is unknown or the
  resource store cannot be opened.
* Requests without update counter no longer emit "Undefined array key"
  warnings in the outdatedness check.
* Sending a meeting request with deleted attendees that lack PR_ENTRYID no
  longer aborts with a TypeError (seen with ActiveSync).
* Processing a cancellation for an occurrence already deleted by the attendee
  no longer aborts with a TypeError.
* Resending as self after a refused submit now drops the other user's SMTP
  addresses, so the second submit is no longer refused as well.
* Attendees (including the organizer and delegators) are no longer listed
  multiple times after accepting a request or opening an occurrence; already
  affected occurrences are cleaned up on their next update.
* Accepting a request is only treated as a delegate's action for actual
  delegates.
* Recurring tasks without a body can be completed again, the completion date
  no longer carries over to the next occurrence, and tasks regenerating after
  completion get their next occurrence.
* Invalid task recurrences no longer raise a TypeError.
* Task requests no longer operate on a missing or foreign embedded task.
* Free/busy lookups return false instead of the store root when the store has
  no free/busy entryids.
* MAPIException messages only get an error text appended for failing codes.

Enhancements:

* Added the TimezoneUtil class (taken over from grommunio-sync). Rules now
  come from gromox (mapi_ianatz_to_tzdef()) or the PHP timezone database
  instead of outdated tables, and DST transitions follow MS-OXOCAL.
* Added helpers to build PidLidAppointmentTimeZoneDefinition* blobs and to
  move all-day events between timezones.
* Added the TZDEFINITION flag constants.
* Meeting forward notifications are now processed: forwarded attendees are
  added to the organizer's meeting or occurrence.
* Forwarded meeting requests are now sent in the name of the organizer, as
  described in MS-OXOCAL, and a local organizer is notified.
* The test suite was migrated to PHPUnit 11.5.

2.3 (2026-09-24)
================

Fixes:

* Incorrect yearly recurrence periods after parsing and when
  calculating occurrences have been repaired.
* Broken recurrence data used to abort calendar processing, now it is skipped
  and logged.
* A delegate's meeting response was erroneously reported as the delegate, not
  the mailbox owner.
* Meeting responses will now have PidLidAttendeeCriticalChange set (and thus
  generate a DTSTAMP line when converted to iCal)
* No longer report a failure from the mail cleanup routine even when a meeting
  request is complete.
* Results from gmdate() are now compared as integers in recurrence month
  calculations.
* PidLidRecurrencePattern is now filled with grammatically correct
  translations.
* Keycloak token decoding was switched from base64 to base64url.
* Keycloak: On validation, tokens are checked for being active.
* Keycloak: On grant validations, refresh tokens are no longer validated.
* Keycloak: Avoid raising an undefined index warning when a public client
  without realm public key connects.
* readMapiPropStream() now returns an empty string when the stream cannot be
  opened.

Enhancements:

* Added helpers writeMapiPropStream(), propIsTooLarge(), readMapiProp() ,
  readMapiPropStream(), parseTimezoneDefinition(), getEffectiveTimezoneRule()
  getCalendarRestriction().
* Added getCodepageCharset() with iconv-verified charset names.
* Added proptag defines for PR_CONVERSATION_ID, PR_CONVERSATION_INDEX and
  PR_CONVERSATION_TOPIC.
* Restrictions are applied more efficiently (less runtime) within
  getCalendarItems().
* deleteRecurrence() turns a series back into a single item
* Categories are applied to the whole series now.
* The MeetingRequest class constructor can take an additional parameter to
  indicate that requests should be deleted when responding.
* For new recurrences, the first day of the week is now taken from the user's
  settings.
* mapi_strerror is now used for generic exception explanation strings.
* Added API documentation for the new helpers.

2.2 (2026-07-27)
================

Fixes:

* Calendar events occuring every n years are broken
* Meeting request processing of delegates opens wrong store
* PR_RECEIVED_BY_SMTP_ADDRESS tag is undefined
* MSGFLAG_UNSENT flag is set after sending a meeting request
* Remove recipients from a meeting request fails
* Task owner is empty
* Daystart is not available in a recurrence
* Shared calendar is unresolveable
* Canceling meeting request in a shared calendar fails
* Wrong GOID for uids longer than 64 chars

Enhancements:

* Cache gmtime() results in BaseRecurrence
* Cache DST boundaries and daysInMonth results
* Add hash index infrastructure for exception lookups
* Use hash-indexed lookups for exception methods
* Category handling for the attendee
* Set PR_HTML body message with fallback to PR_BODY
* RecurrenceException class
* mapi_linkmessages stub
* Delegate Wastebasket style constants

2.1 (2025-12-16)
================

Fixes:

* Correct PidLidAppointmentStateFlags value for meetings

Enhancements:

* HTML-based meetingTimeInfo
* ecRights* definitions


2.0 (2025-10-24)
================
* Correct logic bug in TaskRequest::isTaskRequestUpdated() that could
  cause soft-deleted task requests to be incorrectly processed when an
  associated task exists in the active folder
* Enhanced recurrence pattern validation with better error handling for 
  invalid nday values
* More robust address book entry comparison in compareABEntryIDs()
* Code quality improvements and stricter type safety throughout
* Performance optimizations through optimized function usage (str*)


1.7 (2025-09-26)
================
* Drop support for PHP <= 8.1
* Evaluate mapi_ab_openentry result before using it, preventing soft-crash
* Guard against bogus recurrence.{monthly,yearly}.nday values


1.6 (2025-02-19)
================
* Add PidTagWlinkSection/PR_WLINK_SECTION value definitions
* Util to convert restriction consts into strings
* Fix invitees disappearing from meetings with resources


1.5 (2025-01-23)
================
* Added defines for USER_PRIVILEGE bits exposed through
  PR_EC_ENABLED_FEATURES_L; needed by grommunio-web >= 3.9+git236 (gd301ef731)
* Fix invited participants disappearing from meetings when their tracking
  status is cleared.


1.4 (2024-10-08)
================

* Conditionally provide ``PR_EC_WEBAPP_PERSISTENT_SETTINGS_JSON``,
  ``PR_EC_RECIPIENT_HISTORY_JSON`` if they is not already made available from
  mapi.so
* Occurrence exceptions are treated as localtime
* Fix erroneous end time for never-ending recurrences
* Fix duplicate participants appearing in meeting objects
* Do not process meeting requests locate in the "Sent Items" folder


1.3 (2023-10-31)
================

* Add ``KeyCloak`` and ``Token`` classes


1.2 (2023-08-28)
================

* Rename CAL_DEFAULT to MAPI_CAL_DEFAULT
* Do not mark meeting requests as read
* Add freebusy permission bits to mapidefs
* Define MAPIException::setNotificationType()
