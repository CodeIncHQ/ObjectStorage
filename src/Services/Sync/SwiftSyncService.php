<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE - CONFIDENTIAL                                |
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
// Date:     14/12/2017
// Time:     19:04
// Project:  sophos-backup
//
namespace CodeInc\ObjectStorage\Services\Sync;
use CodeInc\ObjectStorage\Plateforms\StoreContainerInterface;
use CodeInc\ObjectStorage\Plateforms\Swift\SwiftContainer;
use CodeInc\ObjectStorage\Plateforms\Swift\SwiftMetadataObject;
use CodeInc\ObjectStorage\Plateforms\Swift\SwiftObject;


/**
 * Class SwiftSyncService
 *
 * @package CodeInc\ObjectStorage\Services\Sync
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftSyncService extends SyncService {
	/**
	 * SwiftSyncService constructor.
	 *
	 * @param SwiftContainer $swiftContainer
	 * @param StoreContainerInterface $destContainer
	 */
	public function __construct(SwiftContainer $swiftContainer, StoreContainerInterface $destContainer) {
		parent::__construct($swiftContainer, $destContainer);
	}

	/**
	 * Lists the Swift objects which are not backup in the B2 bucket.
	 *
	 * @param bool $listSwiftMetadataAsObjects
	 * @return SwiftObject[]
	 * @throws SyncServiceException
	 */
	public function listMissingObjects(bool $listSwiftMetadataAsObjects = null):array {
		try {
			$destObjects =  $this->listDestContainerObjects();
			$missingObject = [];
			foreach ($this->listSrcContainerObject() as $swiftObject) {
				/** @var SwiftObject $swiftObject */

				// adds the Swift object if missing
				if (!in_array($swiftObject->getName(), $destObjects)) {
					$missingObject[$swiftObject->getName()] = $swiftObject;
				}

				// adds the Swift object's metadata if missing
				if ($listSwiftMetadataAsObjects) {
					$swiftMetadataObject = new SwiftMetadataObject($swiftObject);
					if (!in_array($swiftMetadataObject->getName(), $destObjects)) {
						$missingObject[$swiftMetadataObject->getName()] = $swiftMetadataObject;
					}
				}
			}
			return $missingObject;
		}
		catch (\Throwable $exception) {
			throw new SyncServiceException($this,
				"Error while listing Swift objects missing in the destination container",
				$exception);
		}
	}
}