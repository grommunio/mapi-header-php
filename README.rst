grommunio MAPI headers (PHP)
============================

mapi-headers-php is the central repository for PHP-based MAPI applications.
These header files are used by `grommunio Web
<https://github.com/grommunio/grommunio-web>`_, `grommunio Sync
<https://github.com/grommunio/grommunio-sync>`_ and `grommunio DAV
<https://github.com/grommunio/grommunio-dav>`_, eliminating the bundling of
headers.

|shield-agpl| |shield-release| |shield-scrut| |shield-loc|

.. |shield-agpl| image:: https://img.shields.io/badge/license-AGPL--3%2E0-green
                 :target: LICENSE.txt
.. |shield-release| image:: https://shields.io/github/v/tag/grommunio/mapi-header-php
                    :target: https://github.com/grommunio/mapi-header-php/tags
.. |shield-scrut| image:: https://img.shields.io/scrutinizer/build/g/grommunio/mapi-header-php
                  :target: https://scrutinizer-ci.com/g/grommunio/mapi-header-php/
.. |shield-loc| image:: https://img.shields.io/github/languages/code-size/grommunio/mapi-header-php
                :target: https://github.com/grommunio/mapi-header-php/

Features
========

* **MS-OX* Protocol Compliance**: Full compliance with Microsoft Exchange
  protocols (MS-OXOCAL, MS-OXOTASK, MS-OXOMSG, MS-OXCICAL, etc.)
* **Meeting Request Handling**: Complete lifecycle management for meeting
  requests, responses, updates, and cancellations
* **Recurrence Support**: Comprehensive recurrence pattern parsing and
  generation for appointments and tasks (daily, weekly, monthly, yearly)
* **Task Management**: Full task request and delegation support with
  multi-assignee capabilities
* **Exception Handling**: Robust error handling with detailed MAPI exception
  mapping
* **Authentication**: KeyCloak SSO integration and JWT token management
* **Free/Busy**: Calendar free/busy message and folder utilities

Compatibility
=============

* **PHP 8.2+** (Version 2.0+)
* Requires PHP MAPI extension

Support
=======

Support is available through grommunio GmbH and its partners. See
https://grommunio.com/ for details. A community forum is at
`<https://community.grommunio.com/>`_.

For direct contact and supplying information about a security-related
responsible disclosure, contact `dev@grommunio.com <dev@grommunio.com>`_.

Contributing
============

* https://docs.github.com/en/get-started/quickstart/contributing-to-projects
* Alternatively, upload commits to a git store of your choosing, or export the
  series as a patchset using `git format-patch
  <https://git-scm.com/docs/git-format-patch>`_, then convey the git
  link/patches through our direct contact address (above).

Coding style
------------

This repository follows a custom coding style, which can be validated anytime
using the repository's provided `configuration file <.phpcs>`_.

Installation
============

Via Composer
------------

.. code-block:: bash

    composer require grommunio/mapi-header-php

Manual Installation
-------------------

.. code-block:: bash

    git clone https://github.com/grommunio/mapi-header-php.git
    cd mapi-header-php
    make install

This installs the PHP files to ``/usr/share/php-mapi/``.

Library Structure
=================

Core Components
---------------

**Definition Files**

* ``mapidefs.php`` - Core MAPI constants, property types, object types
* ``mapiguid.php`` - MAPI GUID constants for property sets (PSETID_*)
* ``mapitags.php`` - Custom property tag definitions
* ``mapi.util.php`` - Utility functions for MAPI operations
* ``bootstrap.php`` - Bootstrap loader for all headers

**Class Files**

* ``class.meetingrequest.php`` - Meeting request lifecycle management
* ``class.recurrence.php`` - Appointment recurrence handling
* ``class.taskrecurrence.php`` - Task-specific recurrence
* ``class.taskrequest.php`` - Task delegation and request handling
* ``class.baserecurrence.php`` - Abstract recurrence base class
* ``class.baseexception.php`` - Base exception with display messages
* ``class.mapiexception.php`` - MAPI-specific exception handling
* ``class.freebusy.php`` - Free/busy utilities
* ``class.keycloak.php`` - KeyCloak SSO integration
* ``class.token.php`` - JWT token parsing and validation

Key Classes
-----------

Meetingrequest
~~~~~~~~~~~~~~

Handles the complete lifecycle of meeting requests:

* **Sending**: Create and send meeting requests to attendees
* **Receiving**: Process incoming meeting requests, responses, cancellations
* **Updating**: Update existing meetings with counter-proposals
* **Tracking**: Monitor attendee responses and track status
* **Resources**: Automatic resource booking and conflict detection

Example:

.. code-block:: php

    <?php
    require_once '/usr/share/php-mapi/bootstrap.php';

    $mr = new Meetingrequest($store, $message, $session);

    // Check if it's a meeting request
    if ($mr->isMeetingRequest()) {
        // Accept the meeting
        $mr->doAccept(false, true, true);
    }

Recurrence
~~~~~~~~~~

Parse and generate recurrence patterns according to MS-OXOCAL:

* Daily, weekly, monthly, yearly patterns
* Exception handling (modified and deleted occurrences)
* Timezone support
* Occurrence expansion within date ranges

Example:

.. code-block:: php

    <?php
    $recurrence = new Recurrence($store, $message);

    // Get occurrences in a date range
    $items = $recurrence->getCalendarItems(
        $store,
        $calendarFolder,
        $startDate,
        $endDate
    );

TaskRequest
~~~~~~~~~~~

Manage task assignments and delegation:

* Task assignment to multiple recipients
* Task acceptance/declination workflow
* Task history tracking
* Status updates and completion tracking

BaseException & MAPIException
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Structured error handling:

* User-friendly display messages
* Technical details for debugging
* MAPI error code mapping
* Exception handling state tracking

Testing
=======

This library includes a comprehensive PHPUnit test suite. See `tests/README.md
<tests/README.md>`_ for details.

Run tests:

.. code-block:: bash

    composer install
    vendor/bin/phpunit

Run with coverage:

.. code-block:: bash

    vendor/bin/phpunit --coverage-html coverage

Code Quality
============

The codebase maintains high quality standards:

* **PHP-CS-Fixer**: Automated code style enforcement
* **PHPUnit**: Comprehensive unit test coverage
* **Type Safety**: Extensive use of PHP 7.4+/8.x type hints
* **PHPstan***: Static code analysis

Check code style:

.. code-block:: bash

    vendor/bin/php-cs-fixer fix --dry-run --diff

Apply fixes:

.. code-block:: bash

    vendor/bin/php-cs-fixer fix

Static code analysis:

.. code-block:: bash

    vendor/bin/php-cs-fixer analyze

MS-OXPROPS Compliance
=====================

This library follows Microsoft Exchange protocol specifications:

* **MS-OXOCAL**: Calendar and Appointment Objects
* **MS-OXOTASK**: Task Objects
* **MS-OXOMSG**: Message Objects
* **MS-OXCICAL**: iCalendar to Appointment Object Conversion
* **MS-OXPROPS**: Property Set Definitions

All named properties use the PidLid naming convention and are resolved via
``getPropIdsFromStrings()`` to ensure compatibility.

Changelog
=========

See `doc/changelog.rst <doc/changelog.rst>`_ for version history and release
notes.

License
=======

AGPL-3.0-only. See LICENSE.txt for details.

Copyright 2020-2025 grommunio GmbH
