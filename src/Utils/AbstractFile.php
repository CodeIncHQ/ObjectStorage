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
// Time:     15:09
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils;
use CodeInc\ObjectStorage\Interfaces\StoreObjectInterface;
use Guzzle\Http\EntityBody;


/**
 * Class AbstractFile
 *
 * @package CodeInc\ObjectStorage\Abstracts
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
abstract class AbstractFile implements StoreObjectInterface {
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var EntityBody
	 * @see AbstractFile::getContent()
	 */
	private $content;

	/**
	 * AbstractFile constructor.
	 *
	 * @param string $name
	 */
	public function __construct(string $name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->name;
	}

	/**
	 * Returns the file path.
	 *
	 * @return string
	 */
	abstract public function getPath():string;

	/**
	 * @return int
	 * @throws AbstractFileException
	 */
	public function getSize():int {
		try {
			return filesize($this->getPath());
		}
		catch (\Throwable $exception) {
			throw new AbstractFileException($this,
				"Error while reading the size of the local file \"{$this->getPath()}\"",
				$exception);
		}
	}

	/**
	 * @return EntityBody
	 * @throws AbstractFileException
	 */
	public function getContent():EntityBody {
		if (!$this->content) {
			try {
				if (($f = fopen($this->getPath(), 'r'))) {
					throw new AbstractFileException($this,
						"Unable to open the local file \"{$this->getPath()}\" in reading mode");
				}
				$this->content = new EntityBody($f, $this->getSize());
			}
			catch (\Throwable $exception) {
				throw new AbstractFileException($this,
					"Unable to load the content of the local file \"{$this->getPath()}\"",
					$exception);
			}
		}
		return $this->content;
	}
}