<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

/*
 * PHP's gettext extension exposes no contextual lookup, so the context is glued
 * to the message with \004, exactly as the C macros and xgettext do.
 */
if (!function_exists('pgettext')) {
	/**
	 * Look up a message in the given context.
	 */
	function pgettext(string $msgctxt, string $msgid): string {
		$contextString = "{$msgctxt}\004{$msgid}";
		$translation = _($contextString);

		return $translation === $contextString ? $msgid : $translation;
	}

	/**
	 * Look up the plural form matching $num of a message in the given context.
	 */
	function npgettext(string $msgctxt, string $msgid, string $msgid_plural, int $num): string {
		$contextString = "{$msgctxt}\004{$msgid}";
		$contextStringPlural = "{$msgctxt}\004{$msgid_plural}";
		$translation = ngettext($contextString, $contextStringPlural, $num);
		if ($translation === $contextString) {
			return $msgid;
		}
		if ($translation === $contextStringPlural) {
			return $msgid_plural;
		}

		return $translation;
	}
}
