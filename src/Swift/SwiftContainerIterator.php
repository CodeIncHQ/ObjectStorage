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
// Time:     16:55
// Project:  ObjectStorage
//
namespace CodeInc\ObjectStorage\Swift;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftContainerIteratorException;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerIteratorInterface;
use OpenCloud\ObjectStore\Resource\DataObject;


/**
 * Class SwiftContainerIterator
 *
 * @package CodeInc\ObjectStorage\Swift
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftContainerIterator implements StoreContainerIteratorInterface {
	const RETRY_ON_FAILURE = SwiftContainer::RETRY_ON_FAILURE; // times
	const WAIT_BETWEEN_FAILURES = SwiftContainer::WAIT_BETWEEN_FAILURES; // seconds

	/**
	 * @var SwiftContainer
	 */
	private $swiftContainer;

	/**
	 * @var DataObject[]
	 */
	private $objects = [];

	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * SwiftContainerIterator constructor.
	 *
	 * @param SwiftContainer $swiftContainer
	 * @throws SwiftContainerIteratorException
	 */
	public function __construct(SwiftContainer $swiftContainer) {
		$this->swiftContainer = $swiftContainer;
		$this->listObjects();
	}

	/**
	 * @return SwiftContainer
	 */
	public function getContainer():StoreContainerInterface {
		return $this->swiftContainer;
	}

	/**
	 * @param int $retryOnFailure
	 * @throws
	 */
	private function listObjects(int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			if (!empty($this->objects)) {
				$this->objects = [];
			}
			$containerClient = $this->swiftContainer->getContainerClient();
			$objectsCount = $containerClient->getMetadata()->getProperty('object-count');
			if ($objectsCount > 0) {
				$processObjects = 0;
				$marker = '';
				while ($marker !== null) {
					$dataObjects = $containerClient->objectList(['marker' => $marker]);
					if (!$dataObjects->count()) {
						break;
					}
					foreach ($dataObjects as $dataObject) {
						/** @var $dataObject DataObject */
						$this->objects[] = $dataObject;
						$processObjects++;
						$marker = $processObjects < $objectsCount ? $dataObject->getName() : null;
					}
				}
			}
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->listObjects(--$retryOnFailure);
			}
			else {
				throw new SwiftContainerIteratorException($this,
					"Error while listing the objects of the Swift container \"{$this->swiftContainer->getName()}\"",
					$exception);
			}
		}
	}

	/**
	 * Iterator method.
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * Iterator method.
	 *
	 * @return SwiftObject
	 */
	public function current():SwiftObject {
		return new SwiftObject($this->objects[$this->position], $this->swiftContainer);
	}

	/**
	 * Iterator method.
	 *
	 * @return int
	 */
	public function key():int {
		return $this->position;
	}

	/**
	 * Iterator method.
	 */
	public function next() {
		$this->position++;
	}

	/**
	 * Iterator method.
	 *
	 * @return bool
	 */
	public function valid():bool {
		return array_key_exists($this->position, $this->objects);
	}
}