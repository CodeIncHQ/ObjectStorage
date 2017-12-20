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
// Time:     19:49
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\LocalStorage;
use CodeInc\ObjectStorage\Plateforms\StoreContainerInterface;
use CodeInc\ObjectStorage\Plateforms\StoreObjectInterface;


/**
 * Class LocalDirectory
 *
 * @package CodeInc\ObjectStorage\Plateforms\LocalStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalDirectory implements StoreContainerInterface {
	/**
	 * @var string
	 */
	private $dirPath;

	/**
	 * LocalDirectory constructor.
	 *
	 * @param string $dirPath
	 * @throws LocalDirectoryException
	 */
	public function __construct(string $dirPath) {
		$this->setDirPath($dirPath);
	}

	/**
	 * @param string $dirPath
	 * @throws LocalDirectoryException
	 */
	protected function setDirPath(string $dirPath) {
		if (empty($dirPath)) {
			throw new LocalDirectoryException($this,
				"The diretory path can not be empty");
		}
		if (($this->dirPath = realpath($dirPath)) === false) {
			throw new LocalDirectoryException($this,
				"The directory path \"$dirPath\" is not valid");
		}
	}

	/**
	 * @param bool|null $ignoreHiddenFiles
	 * @return array
	 * @throws LocalDirectoryException
	 */
	public function listObjects(bool $ignoreHiddenFiles = null):array {
		try {
			$objects = [];
			foreach (new \DirectoryIterator($this->dirPath) as $item) {
				if (!$item->isDir() && $item->isFile() && (!$ignoreHiddenFiles || !$item->isDot())) {
					$objects[] = new LocalObject($item->getPathname());
				}
			}
			return $objects;
		}
		catch (\Throwable $exception) {
			throw new LocalDirectoryException($this,
				"Error while listing the objects of the loal directory \"$this->dirPath\"",
				$exception);
		}
	}

	/**
	 * @param string $objectName
	 * @return bool
	 * @throws LocalDirectoryException
	 */
	public function hasObject(string $objectName):bool {
		try {
			return file_exists($this->getObjectPath($objectName));
		}
		catch (\Throwable $exception) {
			throw new LocalDirectoryException($this,
				"Error while checking if the object \"$objectName\" exists in the local directory \"$this->dirPath\"",
				$exception);
		}
	}

	/**
	 * @param string $objectName
	 * @return StoreObjectInterface
	 * @throws LocalDirectoryException
	 */
	public function getObject(string $objectName):StoreObjectInterface {
		try {
			return new LocalObject($this->getObjectPath($objectName));
		}
		catch (\Exception $exception) {
			throw new LocalDirectoryException($this,
				"Error while getting the object \"$objectName\" from the local directory \"$this->dirPath\"",
				$exception);
		}
	}

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @throws LocalDirectoryException
	 */
	public function putObject(StoreObjectInterface $cloudStorageObject) {
		try {
			file_put_contents(
				$this->getObjectPath($cloudStorageObject->getName()),
				$cloudStorageObject->getContent()->__toString()
			);
		}
		catch (\Throwable $exception) {
			throw new LocalDirectoryException($this,
				"Unable to write the file \"{$cloudStorageObject->getName()}\" "
				."in the local directory \"$this->dirPath\"",
				$exception);
		}
	}

	/**
	 * @param string $objectName
	 * @return string
	 */
	private function getObjectPath(string $objectName):string {
		return $this->dirPath.DIRECTORY_SEPARATOR.$objectName;
	}
}