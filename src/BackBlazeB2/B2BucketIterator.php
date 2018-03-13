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
// Time:     17:11
// Project:  ObjectStorage
//
namespace CodeInc\ObjectStorage\BackBlazeB2;
use ChrisWhite\B2\File;
use CodeInc\ObjectStorage\BackBlazeB2\Exceptions\B2BucketIteratorException;
use CodeInc\ObjectStorage\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Interfaces\StoreContainerIteratorInterface;


/**
 * Class B2BucketIterator
 *
 * @package CodeInc\ObjectStorage\BackBlazeB2
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2BucketIterator implements StoreContainerIteratorInterface {
	const RETRY_ON_FAILURE = B2Bucket::RETRY_ON_FAILURE; // times
	const WAIT_BETWEEN_FAILURES = B2Bucket::WAIT_BETWEEN_FAILURES; // seconds

	/**
	 * @var B2Bucket
	 */
	private $b2Bucket;

	/**
	 * @var File[]
	 */
	private $files = [];

	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * B2BucketIterator constructor.
	 *
	 * @param B2Bucket $b2Bucket
	 * @throws B2BucketIteratorException
	 */
	public function __construct(B2Bucket $b2Bucket) {
		$this->b2Bucket = $b2Bucket;
		$this->listObjects();
	}

	/**
	 * @return B2Bucket
	 */
	public function getContainer():StoreContainerInterface {
		return $this->b2Bucket;
	}

	/**
	 * @param int $retryOnFailure
	 * @throws B2BucketIteratorException
	 */
	private function listObjects(int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			if (!empty($this->files)) {
				$this->files = [];
			}
			foreach ($this->b2Bucket->getB2Client()->listFiles(['BucketName' => $this->b2Bucket->getName()]) as $file) {
				/** @var File $file */
				if (basename($file->getName()) != ".bzEmpty") {
					$this->files[] = $file;
				}
			}
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->listObjects(--$retryOnFailure);
			}
			else {
				throw new B2BucketIteratorException($this,
					"Unable to list the objects of the B2 bucket \"{$this->b2Bucket->getName()}\"",
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
	 * @return B2Object
	 */
	public function current():B2Object {
		return new B2Object($this->files[$this->position], $this->b2Bucket);
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
		return array_key_exists($this->position, $this->files);
	}
}