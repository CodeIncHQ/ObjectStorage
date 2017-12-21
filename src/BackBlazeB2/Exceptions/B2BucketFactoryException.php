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
// Time:     12:53
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\BackBlazeB2\Exceptions;
use CodeInc\ObjectStorage\Interfaces\Exceptions\StoreContainerFactoryExceptionInterface;
use Throwable;


/**
 * Class B2BucketFactoryException
 *
 * @package CodeInc\ObjectStorage\BackBlazeB2\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2BucketFactoryException extends B2Exception implements StoreContainerFactoryExceptionInterface {
	/**
	 * @var string
	 */
	private $containerName;

	/**
	 * BackBlazeB2BucketFactoryException constructor.
	 *
	 * @param string $bucketName
	 * @param Throwable|null $previous
	 */
	public function __construct(string $bucketName, Throwable $previous = null) {
		$this->containerName = $bucketName;
		parent::__construct("Factory error for the B2 bucket \"$bucketName\"", $previous);
	}

	/**
	 * @return string
	 */
	public function getContainerName():string {
		return $this->containerName;
	}
}