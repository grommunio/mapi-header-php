<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2005-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2024 grommunio GmbH
 */

/**
 * MAPIException
 * if enabled using mapi_enable_exceptions then php-ext can throw exceptions when
 * any error occurs in mapi calls. this exception will only be thrown when severity bit is set in
 * error code that means it will be thrown only for mapi errors not for mapi warnings.
 */
class MAPIException extends BaseException {
	/**
	 * Returns display message of exception if its set by the callee.
	 * If it is not set then we are generating some default display messages based
	 * on mapi error code.
	 */
	#[Override]
	public function getDisplayMessage(): string {
		if (!empty($this->displayMessage)) {
			return $this->displayMessage;
		}

		return match ($this->getCode()) {
			MAPI_E_NO_ACCESS => _("You have insufficient privileges to open this object."),
			ecUnknownUser, MAPI_E_LOGON_FAILED, MAPI_E_UNCONFIGURED => _("Logon Failed. Please check your name/password."),
			MAPI_E_NETWORK_ERROR => _("Can not connect to Gromox."),
			MAPI_E_UNKNOWN_ENTRYID => _("Can not open object with provided id."),
			MAPI_E_NO_RECIPIENTS => _("There are no recipients in the message."),
			MAPI_E_NOT_FOUND => _("Can not find object."),
			MAPI_E_NOT_ENOUGH_MEMORY => _("Operation failed: Server does not have enough memory."),
			default => sprintf(_("Unknown MAPI Error: %s"), get_mapi_error_name($this->getCode())),
		};
	}
}

// Tell the PHP extension which exception class to instantiate
if (function_exists('mapi_enable_exceptions')) {
	mapi_enable_exceptions("mapiexception");
}
