<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2005-2016 Zarafa Deutschland GmbH
 * SPDX-FileCopyrightText: Copyright 2020-2024 grommunio GmbH
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
	 * Flag for allow to exception details message or not.
	 *
	 * @var bool
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
	 *
	 * @var string
	 */
	public $notificationType = "";

	/**
	 * Constructs the exception.
	 *
	 * @param string    $errorMessage
	 * @param int       $code
	 * @param Throwable $previous
	 * @param string    $displayMessage
	 */
	public function __construct($errorMessage, $code = 0, $previous = null, /**
	 * The exception message to show at client side.
	 */
		public $displayMessage = null) {
		parent::__construct($errorMessage, (int) $code, $previous);
	}

	/**
	 * Returns file name and line number combined where exception occurred.
	 */
	public function getFileLine(): string {
		return $this->getBaseFile() . ':' . $this->getLine();
	}

	/**
	 * Returns message that should be sent to client to display.
	 */
	public function getDisplayMessage(): string {
		if (!is_null($this->displayMessage)) {
			return $this->displayMessage;
		}

		return $this->getMessage();
	}

	/**
	 * Sets display message of an exception that will be sent to the client side
	 * to show it to user.
	 */
	public function setDisplayMessage(string $message): void {
		$this->displayMessage = $message . " (" . mapi_strerror($this->getCode()) . ")";
	}

	/**
	 * Sets the  title of an exception that will be sent to the client side
	 * to show it to user.
	 */
	public function setTitle(string $title): void {
		$this->title = $title;
	}

	/**
	 * Returns title that should be sent to client to display as a message box title.
	 */
	public function getTitle(): ?string {
		return $this->title;
	}

	/**
	 * Sets a flag in exception class to indicate that exception is already handled
	 * so if it is caught again in the top level of function stack
	 * then we have to silently ignore it.
	 */
	public function setHandled(): void {
		$this->isHandled = true;
	}

	/**
	 * Returns base path of the file where exception occurred.
	 */
	public function getBaseFile(): string {
		if (is_null($this->baseFile)) {
			$this->baseFile = basename(parent::getFile());
		}

		return $this->baseFile;
	}

	/**
	 * Returns the name of the class of exception.
	 */
	public function getName(): string {
		return static::class;
	}

	/**
	 * Sets a type of notification by which exception needs to be shown at client side.
	 */
	public function setNotificationType(string $notificationType): void {
		$this->notificationType = $notificationType;
	}

	/**
	 * Returns a type of notification to show an exception.
	 */
	public function getNotificationType(): string {
		return $this->notificationType;
	}

	/**
	 * Returns details of the error message if allowToShowDetailsMessage is set.
	 *
	 * @return string returns details error message
	 */
	public function getDetailsMessage(): string {
		return $this->allowToShowDetailsMessage ? $this->__toString() : '';
	}

	// @TODO getTrace and getTraceAsString
}
