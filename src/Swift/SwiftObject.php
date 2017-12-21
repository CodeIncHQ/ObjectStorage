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
// Time:     18:26
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Swift;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectMetadataInterface;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftObjectException;
use Guzzle\Http\EntityBody;
use OpenCloud\Common\Metadata;
use OpenCloud\ObjectStore\Resource\DataObject;


/**
 * Class SwiftObject
 *
 * @package CodeInc\ObjectStorage\Swift
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftObject implements StoreObjectInterface, StoreObjectMetadataInterface {
	/**
	 * @var DataObject
	 */
	private $swiftDataObject;

	/**
	 * @var SwiftContainer
	 */
	private $swiftContainer;

	/**
	 * @var bool
	 */
	private $isContentDownloaded = false;

	/**
	 * SwiftObject constructor.
	 *
	 * @param DataObject $swiftDataObject
	 * @param SwiftContainer $swiftContainer
	 */
	public function __construct(DataObject $swiftDataObject, SwiftContainer $swiftContainer) {
		$this->setSwiftDataObject($swiftDataObject);
		$this->setSwiftContainer($swiftContainer);
	}

	/**
	 * @param SwiftContainer $swiftContainer
	 */
	protected function setSwiftContainer(SwiftContainer $swiftContainer) {
		$this->swiftContainer = $swiftContainer;
	}

	/**
	 * @param DataObject $swiftDataObject
	 */
	protected function setSwiftDataObject(DataObject $swiftDataObject) {
		$this->swiftDataObject = $swiftDataObject;
	}

	/**
	 * @return SwiftContainer
	 */
	public function getSwiftContainer():SwiftContainer {
		return $this->swiftContainer;
	}

	/**
	 * @return DataObject
	 */
	public function getSwiftDataObject():DataObject {
		return $this->swiftDataObject;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->swiftDataObject->getName();
	}

	/**
	 * @return int
	 * @throws SwiftObjectException
	 */
	public function getSize():int {
		return $this->getContent()->getSize();
	}

	/**
	 * @return EntityBody
	 * @throws SwiftObjectException
	 */
	public function getContent():EntityBody {
		try {
			$this->downloadSwiftContent();
			return $this->swiftDataObject->getContent();
		}
		catch (\Throwable $exception) {
			throw new SwiftObjectException($this,
				"Error while returning the content of the object \"{$this->getName()}\" "
				."from the Swift Container \"{$this->swiftContainer->getContainerName()}\"",
				$exception);
		}
	}

	/**
	 * Verifies if the content of the Swift object is downloaded
	 *
	 * @return bool
	 */
	private function hasSwiftContent():bool {
		return $this->isContentDownloaded
			|| ($this->swiftDataObject->getContent() && $this->swiftDataObject->getContent()->getSize() > 0);
	}

	/**
	 * Downloads the content of the swift object.
	 *
	 * @throws SwiftObjectException
	 */
	private function downloadSwiftContent() {
		try {
			if (!$this->hasSwiftContent()) {
				$this->swiftDataObject = $this->swiftContainer->getObject($this->getName());
				$this->isContentDownloaded = true;
			}
		}
		catch (\Exception $exception) {
			throw new SwiftObjectException($this,
				"Error while downloading the content of the object \"{$this->getName()}\" "
				."from the Swift Container \"{$this->swiftContainer->getContainerName()}\"",
				$exception);
		}
	}

	/**
	 * @return array
	 * @throws SwiftObjectException
	 */
	public function getMetadata():array {
		$this->downloadSwiftContent();
		$metadata = $this->swiftDataObject->getMetadata();
		if ($metadata instanceof Metadata) {
			$metadata = $metadata->toArray();
		}
		return $metadata;
	}
}