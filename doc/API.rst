API Documentation
=================

This document provides detailed API documentation and usage examples for the
grommunio MAPI Header PHP library.

.. contents::
   :local:
   :depth: 2

Getting Started
---------------

Basic Setup
~~~~~~~~~~~

.. code-block:: php

    <?php
    // Load the MAPI headers
    require_once '/usr/share/php-mapi/bootstrap.php';

    // Open a MAPI session
    $session = mapi_logon_zarafa($username, $password, $server);
    $store = mapi_openmsgstore($session, $storeEntryId);

Meeting Requests
----------------

The ``Meetingrequest`` class handles all aspects of meeting request lifecycle
management.

Creating and Sending a Meeting Request
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Create appointment item in calendar
    $calendar = mapi_msgstore_openentry($store, $calendarEntryId);
    $message = mapi_folder_createmessage($calendar);

    // Set basic properties
    mapi_setprops($message, [
        PR_MESSAGE_CLASS => 'IPM.Appointment',
        PR_SUBJECT => 'Team Meeting',
        PR_LOCATION => 'Conference Room A'
    ]);

    // Add recipients
    $recipients = [
        [
            PR_RECIPIENT_TYPE => MAPI_TO,
            PR_DISPLAY_NAME => 'John Doe',
            PR_EMAIL_ADDRESS => 'john@example.com',
            PR_ADDRTYPE => 'SMTP'
        ]
    ];
    mapi_message_modifyrecipients($message, MODRECIP_ADD, $recipients);

    // Create meeting request
    $mr = new Meetingrequest($store, $message, $session);

    // Check calendar write access
    if (!$mr->checkCalendarWriteAccess($store)) {
        throw new Exception("No calendar write access");
    }

    // Set as meeting request
    $mr->setMeetingRequest();

    // Send the meeting request
    $mr->sendMeetingRequest();

    // Save changes
    mapi_savechanges($message);

Processing Incoming Meeting Requests
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Open received meeting request message
    $mr = new Meetingrequest($store, $message, $session);

    // Check message type
    if ($mr->isMeetingRequest()) {
        // Check if we're the organizer (should ignore if true)
        if ($mr->isLocalOrganiser()) {
            echo "You are the organizer of this meeting";
            return;
        }

        // Check if already in calendar
        if (!$mr->isInCalendar()) {
            // Add to calendar tentatively without sending response
            $mr->doAccept(true, false, false);
        }

        // User actions:
        // Accept and send response
        $mr->doAccept(false, true, true);

        // Accept tentatively and send response
        $mr->doAccept(true, true, true);

        // Decline and send response
        $mr->doDecline(true);
    }

Processing Meeting Responses
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $mr = new Meetingrequest($store, $message, $session);

    if ($mr->isMeetingRequestResponse()) {
        // Only process if we're the organizer
        if ($mr->isLocalOrganiser()) {
            // Process the response (updates recipient tracking status)
            $mr->processMeetingRequestResponse();

            // The corresponding calendar item is now updated
            // with the attendee's response status
        }
    }

Updating Meeting Requests
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Open existing meeting in calendar
    $message = mapi_msgstore_openentry($store, $meetingEntryId);
    $mr = new Meetingrequest($store, $message, $session);

    // Update meeting properties
    mapi_setprops($message, [
        PR_SUBJECT => 'Updated: Team Meeting',
        // ... other property changes
    ]);

    // Update the meeting request (increments counters)
    $mr->updateMeetingRequest();

    // Check for significant changes (clears responses if needed)
    $mr->checkSignificantChanges();

    // Send update to attendees
    $mr->sendMeetingRequest(true);  // true = update existing meeting

    mapi_savechanges($message);

Cancelling Meetings
~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $mr = new Meetingrequest($store, $message, $session);

    // Cancel the meeting and send cancellation to all attendees
    $mr->doCancelInvitation();
    // This sends cancellation emails and removes from calendar

Processing Meeting Cancellations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $mr = new Meetingrequest($store, $message, $session);

    if ($mr->isMeetingCancellation()) {
        // Only process if we're not the organizer
        if (!$mr->isLocalOrganiser() && $mr->isInCalendar()) {
            // Process the cancellation
            $mr->processMeetingCancellation();

            // User can then remove from calendar
            $mr->doRemoveFromCalendar();
        }
    }

Recurrence Patterns
-------------------

The ``Recurrence`` class handles parsing and generating recurrence patterns
according to MS-OXOCAL specifications.

Reading Recurrence Information
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $recurrence = new Recurrence($store, $message);

    // Get recurrence data
    $recurData = $recurrence->getRecurrence();

    if ($recurData) {
        echo "Recurrence type: " . $recurData['type'] . "\n";
        echo "Pattern: " . $recurData['subtype'] . "\n";
        echo "Start: " . date('Y-m-d', $recurData['start']) . "\n";
        echo "End: " . date('Y-m-d', $recurData['end']) . "\n";

        // Daily recurrence
        if ($recurData['type'] == 10) {
            echo "Every " . ($recurData['everyn'] / 1440) . " days\n";
        }

        // Weekly recurrence
        if ($recurData['type'] == 11) {
            echo "Every " . $recurData['everyn'] . " weeks\n";
            echo "Days: " . $recurData['weekdays'] . "\n";
        }

        // Check for exceptions
        if (!empty($recurData['changed_occurrences'])) {
            echo "Modified occurrences: " . count($recurData['changed_occurrences']) . "\n";
        }
        if (!empty($recurData['deleted_occurrences'])) {
            echo "Deleted occurrences: " . count($recurData['deleted_occurrences']) . "\n";
        }
    }

Creating a Recurring Appointment
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $recurrence = new Recurrence($store, $message);

    // Define daily recurrence (every 2 days)
    $pattern = [
        'type' => 10,           // Daily
        'subtype' => 0,         // Daily pattern
        'everyn' => 2880,       // Every 2 days (in minutes)
        'start' => mktime(9, 0, 0, 1, 1, 2025),   // Start date
        'end' => mktime(10, 0, 0, 12, 31, 2025),  // End date
        'term' => 0x21,         // End after date
        'startocc' => 540,      // 9:00 AM
        'endocc' => 600,        // 10:00 AM
    ];

    $recurrence->setRecurrence($pattern);

    // Define weekly recurrence (every Monday and Wednesday)
    $pattern = [
        'type' => 11,           // Weekly
        'subtype' => 1,         // Weekly pattern
        'everyn' => 1,          // Every 1 week
        'weekdays' => 0x0A,     // Monday (0x02) + Wednesday (0x08)
        'start' => mktime(14, 0, 0, 1, 1, 2025),
        'term' => 0x22,         // After N occurrences
        'numoccur' => 10,       // 10 occurrences
        'startocc' => 840,      // 2:00 PM
        'endocc' => 900,        // 3:00 PM
    ];

    // Define monthly recurrence (2nd Tuesday)
    $pattern = [
        'type' => 12,           // Monthly
        'subtype' => 3,         // Nth weekday
        'everyn' => 1,          // Every 1 month
        'weekdays' => 0x04,     // Tuesday
        'nday' => 2,            // 2nd occurrence
        'start' => mktime(10, 0, 0, 1, 1, 2025),
        'term' => 0x23,         // No end date
        'startocc' => 600,      // 10:00 AM
        'endocc' => 720,        // 12:00 PM
    ];

Getting Occurrences in a Date Range
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $recurrence = new Recurrence($store, $message);

    // Get all occurrences in January 2025
    $startDate = mktime(0, 0, 0, 1, 1, 2025);
    $endDate = mktime(23, 59, 59, 1, 31, 2025);

    $items = $recurrence->getItems($startDate, $endDate);

    foreach ($items as $item) {
        echo "Occurrence: " . date('Y-m-d H:i', $item['start']) . " - ";
        echo date('H:i', $item['end']) . "\n";

        if (isset($item['exception'])) {
            echo "  (Modified exception)\n";
        }
    }

    // Limit to 5 occurrences
    $items = $recurrence->getItems($startDate, $endDate, 5);

    // Get only occurrences with reminders
    $items = $recurrence->getItems($startDate, $endDate, 0, true);

Working with Exceptions
~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $recurrence = new Recurrence($store, $message);

    // Create an exception (modify one occurrence)
    $basedate = mktime(0, 0, 0, 1, 15, 2025);  // Jan 15, 2025

    $exceptionProps = [
        PR_SUBJECT => 'Modified: Team Meeting',
        PR_LOCATION => 'Conference Room B',
        // Start/end times will be calculated automatically
    ];

    $recurrence->createException($exceptionProps, $basedate);

    // Delete an occurrence
    $recurrence->createException([], $basedate, true);  // true = delete

    // Check if date is an exception
    if ($recurrence->isException($basedate)) {
        echo "This is an exception\n";
    }

    // Modify existing exception
    $recurrence->modifyException($exceptionProps, $basedate);

Task Management
---------------

Task Requests
~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Create a task
    $tasksFolder = mapi_msgstore_openentry($store, $tasksFolderEntryId);
    $task = mapi_folder_createmessage($tasksFolder);

    mapi_setprops($task, [
        PR_MESSAGE_CLASS => 'IPM.Task',
        PR_SUBJECT => 'Complete project documentation',
    ]);

    // Add assignees
    $recipients = [
        [
            PR_RECIPIENT_TYPE => MAPI_TO,
            PR_DISPLAY_NAME => 'John Doe',
            PR_EMAIL_ADDRESS => 'john@example.com',
            PR_ADDRTYPE => 'SMTP'
        ]
    ];
    mapi_message_modifyrecipients($task, MODRECIP_ADD, $recipients);

    // Create task request
    $tr = new TaskRequest($store, $task, $session);
    $tr->createTaskRequest();

    mapi_savechanges($task);
    mapi_message_submitmessage($task);

Processing Task Requests
~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $tr = new TaskRequest($store, $message, $session);

    if ($tr->isTaskRequest()) {
        // Accept the task
        $tr->doAccept();

        // Or decline
        $tr->doDecline();
    }

Task Recurrence
~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $taskRecurrence = new TaskRecurrence($store, $task);

    // Set daily regenerating task (1 day after completion)
    $pattern = [
        'type' => 10,       // Daily
        'everyn' => 1440,   // 1 day in minutes
        'regen' => 1,       // Regenerating
        'start' => time(),
        'term' => 0x23,     // No end
    ];

    $taskRecurrence->setRecurrence($pattern);

    // Mark task complete and move to next occurrence
    $taskRecurrence->moveToNextOccurrence();

Exception Handling
------------------

MAPI Exception Handling
~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    try {
        $store = mapi_openmsgstore($session, $entryId);
        $message = mapi_msgstore_openentry($store, $messageEntryId);

        // ... MAPI operations ...

    } catch (MAPIException $e) {
        // User-friendly message
        echo "Error: " . $e->getDisplayMessage() . "\n";

        // Technical details for logging
        error_log("MAPI Error: " . $e->getMessage());
        error_log("Error code: " . $e->getCode());

        // Set notification for UI
        $e->setTitle("Meeting Request Error");
        $e->setDisplayMessage("Unable to process meeting request");
    }

Custom Exception Handling
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    class MyMAPIException extends BaseException {
        public function __construct($message, $code = 0) {
            parent::__construct($message, $code);
            $this->setTitle("Custom Error");
        }
    }

    try {
        if (!$someCondition) {
            throw new MyMAPIException("Invalid meeting state");
        }
    } catch (BaseException $e) {
        if (!$e->isHandled()) {
            // Handle the exception
            logError($e->getDetailsMessage());
            showUserMessage($e->getDisplayMessage());
            $e->setHandled();
        }
    }

Utility Functions
-----------------

Property ID Resolution
~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Define named properties
    $properties = [
        'subject' => PR_SUBJECT,
        'startdate' => 'PT_SYSTIME:PSETID_Appointment:0x820d',
        'duedate' => 'PT_SYSTIME:PSETID_Appointment:0x820e',
        'location' => 'PT_STRING8:PSETID_Appointment:0x8208',
    ];

    // Resolve to actual property tags
    $proptags = getPropIdsFromStrings($store, $properties);

    // Use resolved tags
    $props = mapi_getprops($message, [
        $proptags['startdate'],
        $proptags['duedate'],
        $proptags['location']
    ]);

GUID Operations
~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Create GUID from hex string
    $guid = makeGuid("{00062002-0000-0000-C000-000000000046}");

    // Create meeting GUID
    $meetingGuid = createMeetingGuid();

Free/Busy
~~~~~~~~~

.. code-block:: php

    <?php
    // Get free/busy message
    $fbMessage = FreeBusy::getFreeBusyMessage(
        $session,
        $store,
        $userEntryId,
        FreeBusy::CALENDAR_ENTRYID
    );

    if ($fbMessage) {
        // Access free/busy data
        $props = mapi_getprops($fbMessage);
        // ... process free/busy information ...
    }

Authentication & Security
-------------------------

KeyCloak Integration
~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Get KeyCloak instance (singleton)
    $keycloak = KeyCloak::getInstance();

    // Access token information
    $accessToken = $keycloak->access_token;
    $refreshToken = $keycloak->refresh_token;
    $idToken = $keycloak->id_token;

    // Get configuration
    $realm = $keycloak->realm;
    $clientId = $keycloak->client_id;

JWT Token Parsing
~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $token = new Token($jwtString);

    // Check if token is expired
    if ($token->is_expired()) {
        echo "Token has expired\n";
    }

    // Get token claims
    $claims = $token->get_claims();
    echo "Subject: " . $claims['sub'] . "\n";
    echo "Issuer: " . $claims['iss'] . "\n";
    echo "Expiration: " . date('Y-m-d H:i:s', $claims['exp']) . "\n";

    // Access token components
    $header = $token->token_header;
    $payload = $token->token_payload;
    $signature = $token->token_signature;

Best Practices
--------------

Error Handling
~~~~~~~~~~~~~~

Always wrap MAPI operations in try-catch blocks:

.. code-block:: php

    <?php
    try {
        $mr = new Meetingrequest($store, $message, $session);
        $mr->sendMeetingRequest();
    } catch (MAPIException $e) {
        // Handle gracefully
        error_log($e->getMessage());
        return false;
    }

Resource Management
~~~~~~~~~~~~~~~~~~~

Always save changes after modifications:

.. code-block:: php

    <?php
    mapi_setprops($message, $props);
    mapi_savechanges($message);  // Don't forget this!

Session Management
~~~~~~~~~~~~~~~~~~

Pass the session when creating Meetingrequest or TaskRequest objects for full
functionality:

.. code-block:: php

    <?php
    // Good - full functionality
    $mr = new Meetingrequest($store, $message, $session);

    // Limited - no resource booking, no SMTP address resolution
    $mr = new Meetingrequest($store, $message);

Property Resolution
~~~~~~~~~~~~~~~~~~~

Always resolve named properties before use:

.. code-block:: php

    <?php
    // Good
    $proptags = getPropIdsFromStrings($store, $properties);
    $props = mapi_getprops($message, [$proptags['startdate']]);

    // Bad - may not work across different stores
    $props = mapi_getprops($message, [0x820d]);

Common Patterns
---------------

Check Before Modify
~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $mr = new Meetingrequest($store, $message, $session);

    // Check calendar access first
    if (!$mr->checkCalendarWriteAccess($store)) {
        throw new Exception("No calendar write permissions");
    }

    // Proceed with modifications
    $mr->updateMeetingRequest();

Batch Recipient Operations
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Build complete recipient list
    $recipients = [];
    foreach ($attendees as $attendee) {
        $recipients[] = [
            PR_RECIPIENT_TYPE => MAPI_TO,
            PR_DISPLAY_NAME => $attendee['name'],
            PR_EMAIL_ADDRESS => $attendee['email'],
            PR_ADDRTYPE => 'SMTP',
            PR_RECIPIENT_TRACKSTATUS => olRecipientTrackStatusNone,
        ];
    }

    // Single batch add
    mapi_message_modifyrecipients($message, MODRECIP_ADD, $recipients);

Property Caching
~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    // Get all needed properties at once
    $proptags = getPropIdsFromStrings($store, [
        'subject' => PR_SUBJECT,
        'startdate' => 'PT_SYSTIME:PSETID_Appointment:0x820d',
        'duedate' => 'PT_SYSTIME:PSETID_Appointment:0x820e',
        'location' => 'PT_STRING8:PSETID_Appointment:0x8208',
    ]);

    // Single mapi_getprops call
    $props = mapi_getprops($message, array_values($proptags));

    // Use cached properties
    $subject = $props[$proptags['subject']];
    $start = $props[$proptags['startdate']];
