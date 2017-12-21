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
// Time:     15:43
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Sftp;
use CodeInc\ObjectStorage\Utils\DirectoryIterator;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerIteratorInterface;


/**
 * Class SftpDirectoryIterator
 *
 * @package CodeInc\ObjectStorage\Sftp
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SftpDirectoryIterator extends DirectoryIterator implements StoreContainerIteratorInterface {
	/**
	 * @var SftpDirectory
	 */
	private $sftpDirectory;

	/**
	 * SftpDirectoryIterator constructor.
	 *
	 * @param SftpDirectory $sftpDirectory
	 */
	public function __construct(SftpDirectory $sftpDirectory) {
		$this->sftpDirectory = $sftpDirectory;
		parent::__construct($sftpDirectory->getSftpPath());
		$this->ignoreHiddenFiles();
	}

	/**
	 * @return SftpDirectory
	 */
	public function getContainer():StoreContainerInterface {
		return $this->sftpDirectory;
	}

	/**
	 * Rewinding is not available on SFTP directories.
	 */
	public function rewind() {
		if (!$this->isCurrentItemValid()) {
			$this->next();
		}
	}

	/**
	 * @return SftpFile
	 */
	public function current():SftpFile {
		return new SftpFile(parent::current()->getBasename(), $this->sftpDirectory);
	}
}