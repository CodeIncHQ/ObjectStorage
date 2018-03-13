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
// Project:  ObjectStorage
//
namespace CodeInc\ObjectStorage\Abstracts;
use CodeInc\ObjectStorage\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Interfaces\StoreObjectInterface;


/**
 * Class AbstractDirectory
 *
 * @package CodeInc\ObjectStorage\Utils\Abstracts
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
	 * @param StoreObjectInterface $storeObject
	 * @param string|null $objectName
	 * @param bool|null $allowStreaming
	 * @throws AbstractDirectoryException
	 */
	public function uploadObject(StoreObjectInterface $storeObject, string $objectName = null,
		bool $allowStreaming = null) {
		try {
			$objectPath = $this->getObjectPath($objectName ?? $storeObject->getName());

			// if streaming the feed content is allowed
			if ($allowStreaming !== false) {
				if (($f = fopen($objectPath, "w")) === false) {
					throw new AbstractDirectoryException($this,
						"Unable to open the destination file for writing");
				}
				$content = $storeObject->getContent();
				$content->rewind();
				while (!$content->feof()) {
					if (fwrite($f, $content->read(8192), 8192) === false) {
						throw new AbstractDirectoryException($this,
							"Unable to write 8192 bytes in the destination file");
					}
				}
				fclose($f);
			}

			// if streaming is disabled
			else {
				if (file_put_contents($objectPath, $storeObject->getContent()->__toString()) === false) {
					throw new AbstractDirectoryException($this,
						"Unable to write the whole object content in the destination file");
				}
			}
		}
		catch (\Throwable $exception) {
			throw new AbstractDirectoryException($this,
				"Unable to upload the object \"{$storeObject->getName()}\"", $exception);
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
			throw new AbstractDirectoryException(
				$this,
				"Error while checking if the object \"$objectName\" exists",
				$exception
			);
		}
	}

	/**
	 * @param string $objectName
	 * @throws AbstractDirectoryException
	 */
	public function deleteObject(string $objectName) {
		if (!unlink($this->getObjectPath($objectName))) {
			throw new AbstractDirectoryException(
				$this,
				"Unknow error while deleting the object \"$objectName\""
			);
		}
	}
}