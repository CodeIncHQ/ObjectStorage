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
// Time:     17:05
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Swift\Exceptions;
use CodeInc\ObjectStorage\Swift\SwiftContainerIterator;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerIteratorExceptionInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreContainerIteratorInterface;
use Throwable;


/**
 * Class SwiftContainerIteratorException
 *
 * @package CodeInc\ObjectStorage\Swift\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftContainerIteratorException extends SwiftContainerException implements StoreContainerIteratorExceptionInterface {
	/**
	 * @var SwiftContainerIterator
	 */
	private $iterator;

	/**
	 * SwiftContainerIteratorException constructor.
	 *
	 * @param SwiftContainerIterator $iterator
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(SwiftContainerIterator $iterator, string $message, Throwable $previous = null) {
		$this->iterator = $iterator;
		parent::__construct($iterator->getContainer(), $message, $previous);
	}

	/**
	 * @return SwiftContainerIterator
	 */
	public function getIterator():StoreContainerIteratorInterface {
		return $this->iterator;
	}
}