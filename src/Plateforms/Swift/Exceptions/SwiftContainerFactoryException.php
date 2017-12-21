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
// Time:     12:58
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\Swift\Exceptions;
use CodeInc\ObjectStorage\Plateforms\Interfaces\Exceptions\StoreContainerFactoryExceptionInterface;
use Throwable;


/**
 * Class SwiftContainerFactoryException
 *
 * @package CodeInc\ObjectStorage\Plateforms\Swift\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftContainerFactoryException extends SwiftException implements StoreContainerFactoryExceptionInterface {
	/**
	 * @var string
	 */
	private $containerName;

	/**
	 * SwiftContainerFactoryException constructor.
	 *
	 * @param string $containerName
	 * @param Throwable|null $previous
	 */
	public function __construct(string $containerName, Throwable $previous = null) {
		$this->containerName = $containerName;
		parent::__construct("Factory error for the Swift container \"$containerName\"", $previous);
	}

	public function getContainerName():string {
		$this->containerName;
	}
}