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
// Time:     13:20
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Sftp;
use CodeInc\ObjectStorage\Utils\AbstractFile;
use CodeInc\ObjectStorage\Sftp\SftpDirectory;


/**
 * Class SftpFile
 *
 * @package CodeInc\ObjectStorage\Sftp
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SftpFile extends AbstractFile {
	/**
	 * @var SftpDirectory
	 */
	private $sftpDirectory;


	/**
	 * SftpFile constructor.
	 *
	 * @param string $name
	 * @param SftpDirectory $sftpDirectory
	 */
	public function __construct(string $name, SftpDirectory $sftpDirectory) {
		parent::__construct($name);
		$this->sftpDirectory = $sftpDirectory;
	}

	/**
	 * @return string
	 */
	public function getPath():string {
		return $this->sftpDirectory->getObjectPath($this->getName());
	}
}