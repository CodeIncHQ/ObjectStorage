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
// Time:     22:12
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\BackBlazeB2\Exceptions;
use CodeInc\ObjectStorage\Plateforms\BackBlazeB2\B2Object;
use CodeInc\ObjectStorage\Plateforms\Interfaces\Exceptions\StoreObjectExceptionInterface;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreObjectInterface;
use Throwable;


/**
 * Class B2ObjectException
 *
 * @package CodeInc\ObjectStorage\Plateforms\BackBlazeB2\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2ObjectException extends B2Exception implements StoreObjectExceptionInterface {
	/**
	 * @var B2Object
	 */
	private $object;

	/**
	 * BackBlazeB2ObjectException constructor.
	 *
	 * @param B2Object $object
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(B2Object $object, string $message, Throwable $previous = null) {
		$this->object = $object;
		parent::__construct($message, $previous);
	}

	/**
	 * @return B2Object
	 */
	public function getObject():StoreObjectInterface {
		return $this->object;
	}
}