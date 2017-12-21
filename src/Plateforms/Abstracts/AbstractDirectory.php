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
// Time:     13:58
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\Abstracts;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreObjectInterface;


/**
 * Class AbstractDirectory
 *
 * @package CodeInc\ObjectStorage\Plateforms\Abstracts
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
abstract class AbstractDirectory implements StoreContainerInterface, \IteratorAggregate {
	/**
	 * Returns an object's path
	 *
	 * @param string $objectName
	 * @return string
	 */
	abstract protected function getObjectPath(string $objectName):string;

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @throws AbstractDirectoryException
	 */
	public function putObject(StoreObjectInterface $cloudStorageObject) {
		try {
			if (($f = fopen($this->getObjectPath($cloudStorageObject->getName()), "w")) === false) {
				throw new AbstractDirectoryException($this, "Unable to open the object for writing");
			}
			$content = $cloudStorageObject->getContent();
			while (!$content->feof()) {
				if (fwrite($f, $content->read(8192), 8192) === false) {
					throw new AbstractDirectoryException($this,"Unable to write in the object");
				}
			}
			fclose($f);
		}
		catch (\Throwable $exception) {
			throw new AbstractDirectoryException($this,
				"Unable to upload the object \"{$cloudStorageObject->getName()}\"", $exception);
		}
	}

	/**
	 * Verifies if an object exists.
	 *
	 * @param string $objectName
	 * @return bool
	 * @throws AbstractDirectoryException
	 */
	public function hasObject(string $objectName):bool {
		try {
			return file_exists($this->getObjectPath($objectName));
		}
		catch (\Throwable $exception) {
			throw new AbstractDirectoryException($this,
				"Error while checking if the object \"$objectName\" exists",
				$exception);
		}
	}
}