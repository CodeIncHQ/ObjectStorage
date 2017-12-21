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
// Time:     15:10
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils\Abstracts;
use CodeInc\ObjectStorage\ObjectStorageException;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectExceptionInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use Throwable;


/**
 * Class AbstractFileException
 *
 * @package CodeInc\ObjectStorage\Utils\Abstracts
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class AbstractFileException extends ObjectStorageException implements StoreObjectExceptionInterface {
	/**
	 * @var AbstractFile
	 */
	protected $file;

	/**
	 * AbstactDirectoryFileException constructor.
	 *
	 * @param AbstractFile $file
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(AbstractFile $file, string $message, Throwable $previous = null) {
		$this->file = $file;
		parent::__construct($message, $previous);
	}

	/**
	 * @return AbstractFile
	 */
	public function getObject():StoreObjectInterface {
		return $this->file;
	}
}