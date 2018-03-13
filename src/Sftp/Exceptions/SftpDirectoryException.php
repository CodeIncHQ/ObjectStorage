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
// Time:     13:14
// Project:  ObjectStorage
//
namespace CodeInc\ObjectStorage\Sftp\Exceptions;
use CodeInc\ObjectStorage\Interfaces\StoreContainerExceptionInterface;
use CodeInc\ObjectStorage\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Sftp\SftpDirectory;
use Throwable;


/**
 * Class SftpDirectoryException
 *
 * @package CodeInc\ObjectStorage\Sftp\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SftpDirectoryException extends SftpException implements StoreContainerExceptionInterface {
	/**
	 * @var SftpDirectory
	 */
	private $directory;

	/**
	 * SftpContainerException constructor.
	 *
	 * @param SftpDirectory $directory
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(SftpDirectory $directory, string $message, Throwable $previous = null) {
		$this->directory = $directory;
		parent::__construct($message, $previous);
	}

	/**
	 * @return SftpDirectory
	 */
	public function getContainer():StoreContainerInterface {
		return $this->directory;
	}
}