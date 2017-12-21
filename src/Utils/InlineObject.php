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
// Time:     19:33
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Utils;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\ObjectStorageException;
use Guzzle\Http\EntityBody;


/**
 * Class InlineObject
 *
 * @package CodeInc\ObjectStorage\Utils
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class InlineObject implements StoreObjectInterface {
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var EntityBody
	 */
	protected $content;

	/**
	 * CloudStorageObject constructor.
	 *
	 * @param string $name
	 */
	public function __construct(string $name) {
		$this->setName($name);
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	/**
	 * @return int
	 * @throws ObjectStorageException
	 */
	public function getSize():int {
		return $this->size ?? $this->getContent()->getSize();
	}

	/**
	 * @param int $size
	 */
	public function setSize(int $size) {
		$this->size = $size;
	}

	/**
	 * @return EntityBody
	 * @throws InlineObjectException
	 */
	public function getContent():EntityBody {
		if (!$this->content) {
			throw new InlineObjectException($this, "No content is set for the object \"{$this->getName()}\"");
		}
		return $this->content;
	}

	/**
	 * Sets the content.
	 *
	 * @param EntityBody $content
	 */
	public function setContent(EntityBody $content) {
		$this->content = $content;
	}

	/**
	 * Sets the content from a string.
	 *
	 * @param string $content
	 */
	public function setStringContent(string $content) {
		$this->setContent(EntityBody::fromString($content));
	}
}