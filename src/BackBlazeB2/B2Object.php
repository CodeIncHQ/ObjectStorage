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
// Time:     18:19
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\BackBlazeB2;
use ChrisWhite\B2\File;
use CodeInc\ObjectStorage\BackBlazeB2\Exceptions\B2ObjectException;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use Guzzle\Http\EntityBody;


/**
 * Class B2Object
 *
 * @package CodeInc\ObjectStorage\BackBlazeB2
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2Object implements StoreObjectInterface {
	/**
	 * @var File
	 */
	private $b2File;

	/**
	 * @var EntityBody|null
	 */
	private $content;

	/**
	 * @var B2Bucket
	 */
	private $b2bucket;

	/**
	 * B2Object constructor.
	 *
	 * @param File $b2File
	 * @param B2Bucket $b2Bucket
	 */
	public function __construct(File $b2File, B2Bucket $b2Bucket) {
		$this->setB2File($b2File);
		$this->setB2bucket($b2Bucket);
	}

	/**
	 * @param B2Bucket $b2bucket
	 */
	protected function setB2bucket(B2Bucket $b2bucket) {
		$this->b2bucket = $b2bucket;
	}

	/**
	 * @param File $b2File
	 */
	protected function setB2File(File $b2File) {
		$this->b2File = $b2File;
	}

	/**
	 * @return B2Bucket
	 */
	public function getB2bucket():B2Bucket {
		return $this->b2bucket;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->b2File->getName();
	}

	/**
	 * @return EntityBody
	 * @throws B2ObjectException
	 */
	public function getContent():EntityBody {
		if (!$this->content) {
			try {
				$this->content = EntityBody::factory($this->b2bucket->getB2Client()->download([
					'BucketName' => $this->b2bucket->getBucketName(),
					'FileName' => $this->getName()
				]));
			}
			catch (\Throwable $exception) {
				throw new B2ObjectException($this,
					"Error while downloading the file \"{$this->getName()}\"",
					$exception);
			}
		}
		return $this->content;
	}

	/**
	 * @return int
	 * @throws B2ObjectException
	 */
	public function getSize():int {
		return $this->getContent()->getSize();
	}
}