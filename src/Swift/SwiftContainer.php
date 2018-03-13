<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE - CONFIDENTIAL                                |
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
// Date:     14/12/2017
// Time:     19:05
// Project:  sophos-backup
//
namespace CodeInc\ObjectStorage\Swift;
use CodeInc\ObjectStorage\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftContainerException;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftContainerFactoryException;
use CodeInc\ObjectStorage\Interfaces\StoreObjectMetadataInterface;
use OpenCloud\ObjectStore\Resource\Container;
use OpenCloud\ObjectStore\Resource\DataObject;
use OpenCloud\OpenStack;


/**
 * Class SwiftContainer
 *
 * @package CodeInc\ObjectStorage\Swift
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftContainer implements StoreContainerInterface, \IteratorAggregate {
	const RETRY_ON_FAILURE = 3; // times
	const WAIT_BETWEEN_FAILURES = 5; // seconds

	/**
	 * @var Container
	 */
	private $containerClient;

	/**
	 * @var OpenStack
	 */
	private $openStackClient;

	/**
	 * @var string
	 */
	private $containerRegion;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * SwiftContainer constructor.
	 *
	 * @param string $containerName
	 * @param string $containerRegion
	 * @param OpenStack $openStackClient
	 * @throws SwiftContainerException
	 */
	public function __construct(string $containerName, string $containerRegion, OpenStack $openStackClient) {
		$this->setName($containerName);
		$this->setContainerRegion($containerRegion);
		$this->setOpenStackClient($openStackClient);
		$this->loadContainerClient();
	}

	/**
	 * SwiftContainer factory.
	 *
	 * @param string $containerName
	 * @param string $containerRegion
	 * @param string $openStackAuthUrl
	 * @param string $openStackUsername
	 * @param string $openStackPassword
	 * @param string $openStackTenantId
	 * @param string $openStackTenantName
	 * @param array|null $openStackClientOptions
	 * @return SwiftContainer
	 * @throws SwiftContainerFactoryException
	 */
	public static function factory(string $containerName, string $containerRegion, string $openStackAuthUrl,
		string $openStackUsername, string $openStackPassword, string $openStackTenantId,
		string $openStackTenantName, array $openStackClientOptions = null):SwiftContainer {
		try {
			return new SwiftContainer($containerName, $containerRegion,
				new OpenStack($openStackAuthUrl, [
					'username' => $openStackUsername,
					'password' => $openStackPassword,
					'tenantId' => $openStackTenantId,
					'tenantName' => $openStackTenantName
				], $openStackClientOptions ?? [])
			);
		}
		catch (\Throwable $exception) {
			throw new SwiftContainerFactoryException($containerName, $exception);
		}
	}

	/**
	 * @param string $name
	 * @throws SwiftContainerException
	 */
	protected function setName(string $name) {
		if (empty($name)) {
			throw new SwiftContainerException($this,"The container name can not be empty");
		}
		$this->name = $name;
	}

	/**
	 * @param string $containerRegion
	 * @throws SwiftContainerException
	 */
	protected function setContainerRegion(string $containerRegion) {
		if (empty($containerRegion)) {
			throw new SwiftContainerException($this,"The container region can not be empty");
		}
		$this->containerRegion = $containerRegion;
	}

	/**
	 * @param OpenStack $openStackClient
	 */
	protected function setOpenStackClient(OpenStack $openStackClient) {
		$this->openStackClient = $openStackClient;
	}

	/**
	 * @throws SwiftContainerException
	 */
	private function loadContainerClient() {
		try {
			$this->containerClient = $this->openStackClient
				->objectStoreService('swift', $this->containerRegion)
				->getContainer($this->name);
		}
		catch (\Throwable $exception) {
			throw new SwiftContainerException($this,
				"Unable to load the OpenStack client for the container \"$this->name\" "
				."from the region \"$this->containerRegion\"",
				$exception);
		}
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->name;
	}

	/**
	 * @return Container
	 */
	public function getContainerClient():Container {
		return $this->containerClient;
	}

	/**
	 * Returns the iterator.
	 *
	 * @return SwiftContainerIterator
	 */
	public function getIterator():SwiftContainerIterator {
		return new SwiftContainerIterator($this);
	}

	/**
	 * Verifies if an object exists in the container.
	 *
	 * @param string $objectName
	 * @return bool
	 * @throws SwiftContainerException
	 */
	public function hasObject(string $objectName):bool {
		try {
			return $this->containerClient->objectExists($objectName);
		}
		catch (\Throwable $exception) {
			throw new SwiftContainerException($this,
				"Error while checking for the existance of the object \"$objectName\" "
				."in the Swift container \"$this->name\"",
				$exception);
		}
	}

	/**
	 * Uploads an object.
	 *
	 * @param StoreObjectInterface $storeObject
	 * @param string|null $objectName
	 * @param array|null $httpHeaders
	 * @param bool|null $allowStreaming
	 * @param int $retryOnFailure
	 * @throws SwiftContainerException
	 */
	public function uploadObject(StoreObjectInterface $storeObject, string $objectName = null,
		bool $allowStreaming = null, array $httpHeaders = null, int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			// preparing the headers
			$headers = DataObject::stockHeaders(
				$storeObject instanceof StoreObjectMetadataInterface
					? DataObject::stockHeaders($storeObject->getMetadata())
					: []
			);
			if ($httpHeaders) {
				$headers = array_merge($headers, $httpHeaders);
			}

			// preparing the content
			$content = $storeObject->getContent();
			if ($allowStreaming === false) {
				$content = $content->__toString();
			}

			// upload the object
			$this->containerClient->uploadObject(
				$objectName ?? $storeObject->getName(),
				$content,
				$headers
			);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->uploadObject($storeObject, $httpHeaders, --$retryOnFailure);
			}
			else {
				throw new SwiftContainerException($this,
					"Error while uploading the object \"{$storeObject->getName()}\" "
					."to the Swift container \"$this->name\"",
					$exception);
			}
		}
	}

	/**
	 * Returns an object.
	 *
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @return SwiftObject
	 * @throws SwiftContainerException
	 */
	public function getObject(string $objectName, int $retryOnFailure = self::RETRY_ON_FAILURE):StoreObjectInterface {
		try {
			return new SwiftObject($this->containerClient->getObject($objectName), $this);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->getObject($objectName, --$retryOnFailure);
			}
			else {
				throw new SwiftContainerException(
					$this,
					"Error while uploading the object \"$objectName\" "
					."to the Swift Container bucket \"$this->name\"",
					$exception
				);
			}
		}
	}

	/**
	 * Deletes an object.
	 *
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @throws SwiftContainerException
	 */
	public function deleteObject(string $objectName, int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			$this->containerClient->deleteObject($objectName);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->deleteObject($objectName, --$retryOnFailure);
			}
			else {
				throw new SwiftContainerException(
					$this,
					"Error while deleting the object \"$objectName\" "
					."to the Swift Container bucket \"$this->name\"",
					$exception
				);
			}
		}
	}
}