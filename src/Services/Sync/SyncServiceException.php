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
// Time:     22:57
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Services\Sync;
use CodeInc\ObjectStorage\Services\ServiceException;
use Throwable;


/**
 * Class SyncServiceException
 *
 * @package CodeInc\ObjectStorage\Services\Sync
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SyncServiceException extends ServiceException {
	/**
	 * @var SyncService
	 */
	private $syncService;

	/**
	 * SyncServiceException constructor.
	 *
	 * @param SyncService $syncService
	 * @param string|null $message
	 * @param Throwable|null $previous
	 */
	public function __construct(SyncService $syncService, string $message = null, Throwable $previous = null) {
		$this->syncService = $syncService;
		parent::__construct($message, $previous);
	}

	/**
	 * @return SyncService
	 */
	public function getSyncService():SyncService {
		return $this->syncService;
	}
}