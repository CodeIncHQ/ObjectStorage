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
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils\Interfaces;


/**
 * Interface StoreContainerIteratorInterface
 *
 * @package CodeInc\ObjectStorage\Utils\Interfaces
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
interface StoreContainerIteratorInterface extends \Iterator {
	/**
	 * @return StoreContainerInterface
	 */
	public function getContainer():StoreContainerInterface;
}