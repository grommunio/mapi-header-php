<?php
/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2005-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2022 grommunio GmbH
 */

/**
 * Defines a base exception class for all custom exceptions, so every exceptions that
 * is thrown/caught by this application should extend this base class and make use of it.
 *
 * Some basic function of Exception class
 * getMessage()- message of exception
 * getCode() - code of exception
 * getFile() - source filename
 * getLine() - source line
 * getTrace() - n array of the backtrace()
 * getTraceAsString() - formatted string of trace
 */
class BaseException extends Exception {
	/**
	 * Base name of the file, so we don't have to use static path of the file.
	 *
	 * @var null|string
	 */
	private $baseFile;

	/**
	 * Flag to check if exception is already handled or not.
	 *
	 * @var bool
	 */
	public $isHandled = false;

	/**
	 * The exception message to show at client side.
	 *
	 * @var null|string
	 */
	public $displayMessage;

	/**
	 * Flag for allow to exception details message or not.
	 *
	 * @var false
	 */
	public $allowToShowDetailsMessage = false;

	/**
	 * The exception title to show as a message box title at client side.
	 *
	 * @var null|string
	 */
	public $title;

	/**
	 * The notification type by which exception needs to be shown at client side.
	 */
	public $notificationType = "";

	/**
	 * Construct the exception.
	 *
	 * @param string    $errorMessage
	 * @param int       $code
	 * @param Exception $previous
	 * @param string    $displayMessage
	 */
	public function __construct($errorMessage, $code = 0, Exception $previous = null, $displayMessage = null) {
		// assign display message
		$this->displayMessage = $displayMessage;

		parent::__construct($errorMessage, (int) $code, $previous);
	}

	/**
	 * @return string returns file name and line number combined where exception occurred
	 */
	public function getFileLine(): string {
		return $this->getBaseFile() . ':' . $this->getLine();
	}

	/**
	 * @return string returns message that should be sent to client to display
	 */
	public function getDisplayMessage() {
		if (!is_null($this->displayMessage)) {
			return $this->displayMessage;
		}

		return $this->getMessage();
	}

	/**
	 * Function sets display message of an exception that will be sent to the client side
	 * to show it to user.
	 *
	 * @param string $message display message
	 */
	public function setDisplayMessage($message): void {
		$this->displayMessage = $message . " (" . mapi_strerror($this->getCode()) . ")";
	}

	/**
	 * Function sets title of an exception that will be sent to the client side
	 * to show it to user.
	 *
	 * @param string $title title of an exception
	 */
	public function setTitle($title): void {
		$this->title = $title;
	}

	/**
	 * @return null|string returns title that should be sent to client to display as a message box title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Function sets a flag in exception class to indicate that exception is already handled
	 * so if it is caught again in the top level of function stack then we have to silently
	 * ignore it.
	 */
	public function setHandled(): void {
		$this->isHandled = true;
	}

	/**
	 * @return string returns base path of the file where exception occurred
	 */
	public function getBaseFile() {
		if (is_null($this->baseFile)) {
			$this->baseFile = basename(parent::getFile());
		}

		return $this->baseFile;
	}

	/**
	 * Name of the class of exception.
	 *
	 * @return get-class-of<$this, BaseException&static>
	 */
	public function getName(): string {
		return get_class($this);
	}

	/**
	 * Function sets a type of notification by which exception needs to be shown at client side.
	 *
	 * @param string $notificationType type of notification to show an exception
	 */
	public function setNotificationType($notificationType) {
		$this->notificationType = $notificationType;
	}

	/**
	 * @return string a type of notification to show an exception
	 */
	public function getNotificationType() {
		return $this->notificationType;
	}

	/**
	 * It will return details error message if allowToShowDetailsMessage is set.
	 *
	 * @return string returns details error message
	 */
	public function getDetailsMessage() {
		return $this->allowToShowDetailsMessage ? $this->__toString() : '';
	}

	// @TODO getTrace and getTraceAsString
}
