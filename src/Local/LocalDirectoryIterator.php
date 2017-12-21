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
// Time:     15:50
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Local;
use CodeInc\ObjectStorage\Local\LocalDirectory;
use CodeInc\ObjectStorage\Utils\DirectoryIterator;


/**
 * Class LocalDirectoryIterator
 *
 * @package CodeInc\ObjectStorage\Local
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalDirectoryIterator extends DirectoryIterator {
	/**
	 * @var LocalDirectory
	 */
	private $localDirectory;

	/**
	 * LocalDirectoryIterator constructor.
	 *
	 * @param LocalDirectory $localDirectory
	 */
	public function __construct(LocalDirectory $localDirectory) {
		$this->localDirectory = $localDirectory;
		parent::__construct($localDirectory->getDirectoryPath());
		$this->ignoreHiddenFiles();
	}

	/**
	 * @return LocalFile
	 */
	public function current():LocalFile {
		return new LocalFile(parent::current()->getBasename(), $this->localDirectory);
	}
}