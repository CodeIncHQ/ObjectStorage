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
// Time:     18:18
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils\Interfaces;
use Guzzle\Http\EntityBody;


/**
 * Interface StoreObjectInterface
 *
 * @package CodeInc\ObjectStorage\Utils\Interfaces
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
interface StoreObjectInterface {
	/**
	 * @return string
	 */
	public function getName():string;

	/**
	 * @return EntityBody
	 */
	public function getContent():EntityBody;

	/**
	 * @return int
	 */
	public function getSize():int;
}