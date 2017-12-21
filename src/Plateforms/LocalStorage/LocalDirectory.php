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
use CodeInc\ObjectStorage\Plateforms\Abstracts\AbstractDirectory;
use CodeInc\ObjectStorage\Plateforms\LocalStorage\Exceptions\LocalDirectoryException;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreObjectInterface;


/**
 * Class LocalDirectory
 *
 * @package CodeInc\ObjectStorage\Plateforms\LocalStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalDirectory extends AbstractDirectory {
	/**
	 * Directory's path.
	 *
	 * @var string
	 */
	private $directoryPath;

	/**
	 * LocalDirectory constructor.
	 *
	 * @param string $directoryPath
	 * @throws LocalDirectoryException
	 */
	public function __construct(string $directoryPath) {
		try {
			// checks and sets the directory path
			if (empty($directoryPath)) {
				throw new LocalDirectoryException($this,
					"The diretory path can not be empty");
			}
			if (($this->directoryPath = realpath($directoryPath)) === false) {
				throw new LocalDirectoryException($this,
					"The directory path \"$directoryPath\" is not valid");
			}
		}
		catch (\Throwable $exception) {
			throw new LocalDirectoryException($this,
				"Error while opening the local directory container \"$directoryPath\"", $exception);
		}
	}

	/**
	 * Returns the directory iterator.
	 *
	 * @return LocalDirectoryIterator
	 */
	public function getIterator():LocalDirectoryIterator {
		return new LocalDirectoryIterator($this);
	}

	/**
	 * Returns an object's path.
	 *
	 * @param string $objectName
	 * @return string
	 */
	public function getObjectPath(string $objectName):string {
		return $this->directoryPath.DIRECTORY_SEPARATOR.$objectName;
	}

	/**
	 * @param string $objectName
	 * @return StoreObjectInterface
	 */
	public function getObject(string $objectName):StoreObjectInterface {
		return new LocalFile($objectName, $this);
	}

	/**
	 * Returns the directory's path.
	 *
	 * @return string
	 */
	public function getDirectoryPath():string {
		return $this->directoryPath;
	}
}