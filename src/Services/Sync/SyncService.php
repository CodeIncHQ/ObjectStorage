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
// Time:     22:37
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Services\Sync;
use CodeInc\ObjectStorage\Plateforms\StoreContainerInterface;
use CodeInc\ObjectStorage\Plateforms\StoreObjectInterface;
use CodeInc\Service\Service\ServiceInterface;


/**
 * Class SyncService
 *
 * @package CodeInc\ObjectStorage\Services\Sync
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SyncService implements ServiceInterface {
	/**
	 * @var StoreContainerInterface
	 */
	protected $srcContainer;

	/**
	 * @var StoreContainerInterface
	 */
	protected $destContainer;

	/**
	 * DiffService constructor.
	 *
	 * @param StoreContainerInterface $srcContainer
	 * @param StoreContainerInterface $destContainer
	 */
	public function __construct(StoreContainerInterface $srcContainer, StoreContainerInterface $destContainer) {
		$this->srcContainer = $srcContainer;
		$this->destContainer = $destContainer;
	}

	/**
	 * @return StoreContainerInterface
	 */
	public function getSrcContainer():StoreContainerInterface {
		return $this->srcContainer;
	}

	/**
	 * @return StoreContainerInterface
	 */
	public function getDestContainer():StoreContainerInterface {
		return $this->destContainer;
	}

	/**
	 * @return StoreObjectInterface[]
	 */
	protected function listSrcContainerObject():array {
		return $this->srcContainer->listObjects();
	}

	/**
	 * @return StoreObjectInterface[]
	 */
	protected function listDestContainerObjects():array {
		return $this->destContainer->listObjects();
	}

	/**
	 * @return StoreObjectInterface[]
	 * @throws SyncServiceException
	 */
	public function listMissingObjects():array {
		try {
			$destObjects = $this->listDestContainerObjects();
			$missingObject = [];
			foreach ($this->listSrcContainerObject() as $srcObject) {
				// if the object is missing in the desintation container
				if (!array_key_exists($srcObject->getName(), $destObjects)) {
					$missingObject[$srcObject->getName()] = $srcObject;
				}
			}
			return $missingObject;
		}
		catch (\Throwable $exception) {
			throw new SyncServiceException($this,"Unable to sync the containers", $exception);
		}
	}
}