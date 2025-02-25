1.6 (2026-02-19)
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
