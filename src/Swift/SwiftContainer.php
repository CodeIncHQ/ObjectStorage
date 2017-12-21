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
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftContainerException;
use CodeInc\ObjectStorage\Swift\Exceptions\SwiftContainerFactoryException;
use OpenCloud\ObjectStore\Resource\Container;
use OpenCloud\ObjectStore\Resource\DataObject;
use OpenCloud\OpenStack;


/**
 * Class SwiftContainer
 *
 * @package CodeInc\ObjectStorage\Swift
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftContainer implements StoreContainerInterface {
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
	private $containerName;

	/**
	 * SwiftContainer constructor.
	 *
	 * @param string $containerName
	 * @param string $containerRegion
	 * @param OpenStack $openStackClient
	 * @throws SwiftContainerException
	 */
	public function __construct(string $containerName, string $containerRegion, OpenStack $openStackClient) {
		$this->setContainerName($containerName);
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
	 * @param string $containerName
	 * @throws SwiftContainerException
	 */
	protected function setContainerName(string $containerName) {
		if (empty($containerName)) {
			throw new SwiftContainerException($this,"The container name can not be empty");
		}
		$this->containerName = $containerName;
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
				->getContainer($this->containerName);
		}
		catch (\Throwable $exception) {
			throw new SwiftContainerException($this,
				"Unable to load the OpenStack client for the container \"$this->containerName\" "
				."from the region \"$this->containerRegion\"",
				$exception);
		}
	}

	/**
	 * @return string
	 */
	public function getContainerName():string {
		return $this->containerName;
	}

	/**
	 * @return Container
	 */
	public function getContainerClient():Container {
		return $this->containerClient;
	}

	/**
	 * @return string
	 */
	public function getContainerRegion():string {
		return $this->containerRegion;
	}

	/**
	 * @return OpenStack
	 */
	public function getOpenStackClient():OpenStack {
		return $this->openStackClient;
	}

	/**
	 * @param int $retryOnFailure
	 * @return SwiftObject[]
	 * @throws
	 */
	public function listObjects(int $retryOnFailure = self::RETRY_ON_FAILURE):array {
		try {
			$object = [];
			$objectsCount = $this->containerClient->getMetadata()->getProperty('object-count');
			if ($objectsCount > 0) {
				$processObjects = 0;
				$marker = '';
				while ($marker !== null) {
					$dataObjects = $this->containerClient->objectList(['marker' => $marker]);
					if (!$dataObjects->count()) {
						break;
					}
					foreach ($dataObjects as $dataObject) {
						/** @var $dataObject DataObject */
						$object[$dataObject->getName()] = new SwiftObject($dataObject, $this);
						$processObjects++;
						$marker = $processObjects < $objectsCount ? $dataObject->getName() : null;
					}
				}
			}
			return $object;
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->listObjects(--$retryOnFailure);
			}
			else {
				throw new SwiftContainerException($this,
					"Error while listing the objects of the Swift container \"$this->containerName\"",
					$exception);
			}
		}
	}

	/**
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
				."in the Swift container \"$this->containerName\"",
				$exception);
		}
	}

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @param array|null $httpHeaders
	 * @throws SwiftContainerException
	 */
	public function putObject(StoreObjectInterface $cloudStorageObject, string $objectName = null,
		array $httpHeaders = null, int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {

			$this->containerClient->uploadObject(
				$objectName ?? $cloudStorageObject->getName(),
				$cloudStorageObject->getContent(),
				$this->buildObjectHeaders($cloudStorageObject, $httpHeaders)
			);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->putObject($cloudStorageObject, $httpHeaders, --$retryOnFailure);
			}
			else {
				throw new SwiftContainerException($this,
					"Error while uploading the object \"{$cloudStorageObject->getName()}\" "
					."to the Swift container \"$this->containerName\"",
					$exception);
			}
		}
	}

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @param array|null $httpHeaders
	 * @return array
	 */
	private function buildObjectHeaders(StoreObjectInterface $cloudStorageObject, array $httpHeaders = null) {
		$headers = DataObject::stockHeaders($cloudStorageObject->getMetadata());
		return $httpHeaders ? array_merge($headers, $httpHeaders) : $headers;
	}

	/**
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
				throw new SwiftContainerException($this, "Error while uploading the object \"$objectName\" "
					."to the Swift Container bucket \"$this->containerName\"", $exception);
			}
		}
	}
}