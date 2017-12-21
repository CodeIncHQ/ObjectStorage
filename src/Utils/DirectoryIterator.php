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
// Time:     15:38
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils;


/**
 * Class DirectoryIterator
 *
 * @package CodeInc\ObjectStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class DirectoryIterator extends \DirectoryIterator {
	/**
	 * @var bool
	 */
	protected $ignoreHiddenFiles = false;

	/**
	 * Ignores the hidden files (starting with a dot) in the directory.
	 */
	public function ignoreHiddenFiles() {
		$this->ignoreHiddenFiles = true;
	}

	/**
	 * Rewrinds the iterator.
	 */
	public function rewind() {
		parent::rewind();
		if (!$this->isCurrentItemValid()) {
			$this->next();
		}
	}

	/**
	 * Moves to the next file.
	 */
	public function next() {
		do {
			parent::next();
		}
		while ($this->valid() && !$this->isCurrentItemValid());
	}

	/**
	 * Valides if an item needs to be ignored.
	 *
	 * @return bool
	 */
	protected function isCurrentItemValid():bool {
		$item = parent::current();
		return ($item->isFile() && (!$this->ignoreHiddenFiles || substr($item->getBasename(), 0, 1) != "."));
	}
}