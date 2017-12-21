<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2017 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material  is strictly forbidden unless prior   |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     21/12/2017
// Time:     15:17
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils\Abstracts;
use CodeInc\ObjectStorage\ObjectStorageException;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerExceptionInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerInterface;
use Throwable;


/**
 * Class AbstractDirectoryException
 *
 * @package CodeInc\ObjectStorage\Utils\Abstracts
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class AbstractDirectoryException extends ObjectStorageException implements StoreContainerExceptionInterface {
	/**
	 * @var AbstractDirectory
	 */
	private $directory;

	/**
	 * AbstractDirectoryException constructor.
	 *
	 * @param AbstractDirectory $directory
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(AbstractDirectory $directory, string $message, Throwable $previous = null) {
		$this->directory = $directory;
		parent::__construct($message, $previous);
	}

	/**
	 * @return AbstractDirectory
	 */
	public function getContainer():StoreContainerInterface {
		return $this->directory;
	}
}