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
// Time:     13:22
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Sftp\Exceptions;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectExceptionInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Sftp\SftpFile;
use Throwable;


/**
 * Class SftpFileException
 *
 * @package CodeInc\ObjectStorage\Sftp\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SftpFileException extends SftpException implements StoreObjectExceptionInterface {
	/**
	 * @var SftpFile
	 */
	private $file;

	/**
	 * SftpObjectException constructor.
	 *
	 * @param SftpFile $file
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(SftpFile $file, string $message, Throwable $previous = null) {
		$this->file = $file;
		parent::__construct($message, $previous);
	}

	/**
	 * @return SftpFile
	 */
	public function getObject():StoreObjectInterface {
		return $this->file;
	}
}