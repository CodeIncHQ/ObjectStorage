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
// Time:     18:48
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\BackBlazeB2;
use CodeInc\ObjectStorage\BackBlazeB2\Exceptions\B2BucketException;
use CodeInc\ObjectStorage\BackBlazeB2\Exceptions\B2BucketFactoryException;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerInterface;
use ChrisWhite\B2\Client;
use ChrisWhite\B2\File;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;


/**
 * Class B2Bucket
 *
 * @package CodeInc\ObjectStorage\BackBlazeB2
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2Bucket implements StoreContainerInterface, \IteratorAggregate {
	const RETRY_ON_FAILURE = 3; // times
	const WAIT_BETWEEN_FAILURES = 5; // seconds

	/**
	 * @var Client
	 */
	private $b2Client;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * B2Bucket constructor.
	 *
	 * @param string $bucketName
	 * @param Client $b2Client
	 * @throws B2BucketException
	 */
	public function __construct(string $bucketName, Client $b2Client) {
		$this->setName($bucketName);
		$this->setB2Client($b2Client);
	}

	/**
	 * B2Bucket factory.
	 *
	 * @param string $bucketName
	 * @param string $b2AccountId
	 * @param string $b2ApplicationKey
	 * @param array $b2ClientOptions
	 * @return B2Bucket
	 * @throws B2BucketFactoryException
	 */
	public static function factory(string $bucketName, string $b2AccountId, string $b2ApplicationKey,
		array $b2ClientOptions = []):B2Bucket {
		try {
			return new B2Bucket($bucketName,
				new Client($b2AccountId, $b2ApplicationKey, $b2ClientOptions ?? []));
		}
		catch (\Throwable $exception) {
			throw new B2BucketFactoryException($bucketName, $exception);
		}
	}

	/**
	 * @param string $name
	 * @throws B2BucketException
	 */
	protected function setName(string $name) {
		if (empty($name)) {
			throw new B2BucketException($this,"The bucket name can not be empty");
		}
		$this->name = $name;
	}

	/**
	 * @param Client $b2Client
	 */
	protected function setB2Client(Client $b2Client) {
		$this->b2Client = $b2Client;
	}

	/**
	 * @return Client
	 */
	public function getB2Client():Client {
		return $this->b2Client;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->name;
	}

	/**
	 * @param int $retryOnFailure
	 * @return StoreObjectInterface[]
	 * @throws B2BucketException
	 */
	public function listObjects(int $retryOnFailure = self::RETRY_ON_FAILURE):array {
		try {
			$objects = [];
			foreach ($this->b2Client->listFiles(['BucketName' => $this->name]) as $file) {
				/** @var File $file */
				$objects[$file->getName()] = new B2Object($file, $this);
			}

			return $objects;
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->listObjects(--$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Unable to list the objects of the B2 bucket \"$this->name\"",
					$exception);
			}
		}
	}

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @throws B2BucketException
	 */
	public function uploadObject(StoreObjectInterface $cloudStorageObject, string $objectName = null,
		int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			$this->b2Client->upload([
				'BucketName' => $this->name,
				'FileName' => $objectName ?? $cloudStorageObject->getName(),
				'Body' => $cloudStorageObject->getContent()
			]);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->uploadObject($cloudStorageObject, --$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Error while uploading the object \"{$cloudStorageObject->getName()}\" "
					."to the B2 bucket \"$this->name\"",
					$exception);
			}
		}
	}

	/**
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @return StoreObjectInterface
	 * @throws B2BucketException
	 */
	public function getObject(string $objectName, int $retryOnFailure = self::RETRY_ON_FAILURE):StoreObjectInterface {
		try {
			return new B2Object(
				$this->b2Client->getFile([
					'BucketName' => $this->name,
					'FileName' => $objectName
				]),
				$this
			);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->getObject($objectName, --$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Error while downloading the object \"$objectName\" from the B2 bucket \"$this->name\"",
					$exception);
			}
		}
	}

	/**
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @throws B2BucketException
	 */
	public function deleteObject(string $objectName, int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			if ($this->b2Client->deleteFile(['BucketName' => $this->name, 'FileName' => $objectName]) === false) {
				throw new B2BucketException($this, "Unknow error");
			}
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->deleteObject($objectName, --$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Error while delete the object \"$objectName\" from the B2 bucket \"$this->name\"",
					$exception);
			}
		}
	}

	/**
	 * @param string $objectName
	 * @return bool
	 * @throws B2BucketException
	 */
	public function hasObject(string $objectName):bool {
		try {
			return $this->b2Client->fileExists([
				'BucketName' => $this->name,
				'FileName' => $objectName
			]);
		}
		catch (\Throwable $exception) {
			throw new B2BucketException($this,
				"Error while checking for the object \"$objectName\" in the B2 bucket \"$this->name\"",
				$exception);
		}
	}

	/**
	 * @return B2BucketIterator
	 */
	public function getIterator():B2BucketIterator {
		return new B2BucketIterator($this);
	}

}