2.3 (2026-09-24)
================

Fixes:

* Yearly recurrence period wrong after parsing and when calculating occurrences
* Broken recurrence data aborted calendar processing, it is skipped and logged
* A delegate's meeting response was given as the delegate, not the mailbox owner
* Meeting responses carried no DTSTAMP
* Request mail cleanup could fail an otherwise complete meeting response
* Meeting request recurrence helper opened the wrong store
* gmdate() results were compared as strings in recurrence month calculations
* Recurrence pattern sentence could not be translated grammatically
* Token: base64url decoding
* KeyCloak: token activity checked on validation, refresh token not validated
  on grant validation, missing realm-public-key of a public client
* readMapiPropStream() returns an empty string when the stream cannot be opened

Enhancements:

* writeMapiPropStream(), propIsTooLarge() and readMapiProp() helpers
* readMapiPropStream() helper
* parseTimezoneDefinition() and getEffectiveTimezoneRule() helpers
* getCodepageCharset() with iconv-verified charset names
* getCalendarRestriction() helper
* PR_CONVERSATION_ID, PR_CONVERSATION_INDEX and PR_CONVERSATION_TOPIC
* Calendar view restriction pushed into the table load
* deleteRecurrence() turns a series back into a single item
* Categories applied to the whole series
* Optional removal of the request mail on meeting response
* First day of week taken from the user's settings for new recurrences
* mapi_strerror() for generic exception explanations
* API documentation for the new helpers

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
