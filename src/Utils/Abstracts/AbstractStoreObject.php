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
// Time:     21:00
// Project:  ObjectStorage
//
namespace CodeInc\ObjectStorage\Utils\Abstracts;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectContainerInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectDeleteInterface;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;


/**
 * Class AbstractObject
 *
 * @package CodeInc\ObjectStorage\Utils\Abstracts
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
abstract class AbstractStoreObject implements StoreObjectInterface,
	StoreObjectContainerInterface, StoreObjectDeleteInterface {
	
	/**
	 * Returns the object's size.
	 *
	 * @return int
	 * @throws
	 */
	public function getSize():int {
		return $this->getContent()->getSize();
	}

	/**
	 * Deletes the object.
	 *
	 * @throws
	 */
	public function delete() {
		$this->getParentContainer()->deleteObject($this->getName());
	}
}