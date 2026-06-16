<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

/**
 * Throw this exception when recurrence data (named property 0x8216) is broken:
 * invalid or missing values (start, end, type etc.).
 */
class RecurrenceException extends BaseException {}

