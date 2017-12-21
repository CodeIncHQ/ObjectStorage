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
// Time:     18:47
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils\Interfaces;
use CodeInc\Service\Service\ServiceInterface;


/**
 * Interface StoreContainerInterface
 *
 * @package CodeInc\ObjectStorage\Utils\Interfaces
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
interface StoreContainerInterface extends ServiceInterface {
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @param string $objectName
	 * @return void
	 * @throws
	 */
	public function putObject(StoreObjectInterface $cloudStorageObject, string $objectName = null);

	/**
	 * @param string $objectName
	 * @return StoreObjectInterface
	 * @throws
	 */
	public function getObject(string $objectName):StoreObjectInterface;

	/**
	 * @param string $objectName
	 * @return bool
	 * @throws
	 */
	public function hasObject(string $objectName):bool;
}