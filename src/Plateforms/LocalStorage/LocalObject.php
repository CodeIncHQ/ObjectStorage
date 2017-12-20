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
namespace CodeInc\ObjectStorage\Plateforms\LocalStorage;
use CodeInc\ObjectStorage\Plateforms\StoreObjectInterface;
use Guzzle\Http\EntityBody;


/**
 * Class LocalObject
 *
 * @package CodeInc\ObjectStorage\Plateforms\LocalStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalObject implements StoreObjectInterface {
	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @var EntityBody
	 */
	private $content;

	/**
	 * LocalObject constructor.
	 *
	 * @param string $filePath
	 * @throws LocalObjectException
	 */
	public function __construct(string $filePath) {
		$this->setFilePath($filePath);
	}

	/**
	 * @param string $filePath
	 * @throws LocalObjectException
	 */
	protected function setFilePath(string $filePath) {
		if (empty($filePath)) {
			throw new LocalObjectException($this,
				"The file path can not be empty");
		}
		if (!file_exists($filePath)) {
			throw new LocalObjectException($this,
				"The file \"$filePath\" does not exist");
		}
		if (!is_file($filePath)) {
			throw new LocalObjectException($this,
				"The path \"$filePath\" is not a file");
		}
		$this->filePath = $filePath;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return basename($this->filePath);
	}

	/**
	 * @return int
	 * @throws LocalObjectException
	 */
	public function getSize():int {
		try {
			return filesize($this->filePath);
		}
		catch (\Throwable $exception) {
			throw new LocalObjectException($this,
				"Error while reading the size of the local file \"$this->filePath\"",
				$exception);
		}
	}

	/**
	 * @return EntityBody
	 * @throws LocalObjectException
	 */
	public function getContent():EntityBody {
		if (!$this->content) {
			try {
				if (($fp = fopen($this->filePath, 'r'))) {
					throw new LocalObjectException($this,
						"Unable to open the local file \"$this->filePath\" in reading mode");
				}
				$this->content = EntityBody::factory($fp);
			}
			catch (\Throwable $exception) {
				throw new LocalObjectException($this,
					"Unable to load the content of the local file \"$this->filePath\"",
					$exception);
			}
		}
		return $this->content;
	}


}