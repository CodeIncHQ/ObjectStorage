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
// Date:     19/12/2017
// Time:     19:50
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Local;
use CodeInc\ObjectStorage\Utils\AbstractFile;
use CodeInc\ObjectStorage\Local\LocalDirectory;


/**
 * Class LocalObject
 *
 * @package CodeInc\ObjectStorage\Local
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalFile extends AbstractFile {
	/**
	 * @var LocalDirectory
	 */
	private $localDirectory;

	/**
	 * LocalFile constructor.
	 *
	 * @param string $name
	 * @param LocalDirectory $localDirectory
	 */
	public function __construct(string $name, LocalDirectory $localDirectory) {
		parent::__construct($name);
		$this->localDirectory = $localDirectory;
	}

	/**
	 * @return string
	 */
	public function getPath():string {
		return $this->localDirectory->getObjectPath($this->getName());
	}
}