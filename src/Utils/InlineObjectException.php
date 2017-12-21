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
namespace CodeInc\ObjectStorage\Utils;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectExceptionInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\ObjectStorageException;
use Throwable;


/**
 * Class InlineObjectException
 *
 * @package CodeInc\ObjectStorage\Utils
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class InlineObjectException extends ObjectStorageException implements StoreObjectExceptionInterface {
	/**
	 * @var InlineObject
	 */
	private $object;

	/**
	 * InlineDataObjectException constructor.
	 *
	 * @param InlineObject $object
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(InlineObject $object, string $message, Throwable $previous = null) {
		$this->object = $object;
		parent::__construct($message, $previous);
	}

	/**
	 * @return InlineObject
	 */
	public function getObject():StoreObjectInterface {
		return $this->object;
	}
}