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
// Date:     20/12/2017
// Time:     19:27
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms;
use CodeInc\ObjectStorage\Plateforms\StoreObjectInterface;
use CodeInc\ObjectStorage\ObjectStorageException;
use Throwable;


/**
 * Class StoreObjectException
 *
 * @package CodeInc\ObjectStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class StoreObjectException extends ObjectStorageException {
	/**
	 * @var StoreObjectInterface
	 */
	private $storeObject;

	/**
	 * StoreObjectException constructor.
	 *
	 * @param StoreObjectInterface $storeObject
	 * @param string|null $message
	 * @param int|null $code
	 * @param Throwable|null $previous
	 */
	public function __construct(StoreObjectInterface $storeObject, string $message, int $code = null, Throwable $previous = null) {
		$this->storeObject = $storeObject;
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return StoreObjectInterface
	 */
	public function getStoreObject():StoreObjectInterface {
		return $this->storeObject;
	}
}