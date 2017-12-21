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
// Time:     12:44
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Sftp;
use CodeInc\ObjectStorage\Utils\Abstracts\AbstractDirectory;
use CodeInc\ObjectStorage\Utils\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Sftp\Exceptions\SftpDirectoryException;
use CodeInc\ObjectStorage\Sftp\Exceptions\SftpDirectoryFactoryException;
use CodeInc\ObjectStorage\Sftp\Exceptions\SftpException;


/**
 * Class SftpDirectory
 *
 * @package CodeInc\ObjectStorage\Sftp
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SftpDirectory extends AbstractDirectory {
	/**
	 * SFTP session.
	 *
	 * @var resource
	 */
	private $sftpSession;

	/**
	 * SFTP path
	 *
	 * @var string
	 */
	private $sftpPath;

	/**
	 * @var string
	 */
	private $directoryPath;

	/**
	 * SftpContainer constructor.
	 *
	 * @param string $directoryPath
	 * @param resource $ssh2Session SSH session (see ssh2_connect())
	 * @throws SftpDirectoryException
	 */
	public function __construct(string $directoryPath, $ssh2Session) {
		try {
			// checking for the SSH2 extension
			if (!extension_loaded("ssh2")) {
				throw new SftpDirectoryException($this,"The ssh2 extension is required to user a SftpContainer");
			}

			// starting the sftp session
			if (($this->sftpSession = ssh2_sftp($ssh2Session)) === false) {
				throw new SftpDirectoryException($this, "Unable to start a SFTP session");
			}

			// building the SFTP path
			$this->sftpPath = "ssh2.sftp://$this->sftpSession";
			if (!substr($directoryPath, 0, 1) == "/") {
				$this->sftpPath .= "/";
			}
			$this->sftpPath .= $directoryPath;
			$this->directoryPath = $directoryPath;
		}
		catch (\Throwable $exception) {
			throw new SftpDirectoryException($this,
				"Error while loading the SFTP container \"$directoryPath\"", $exception);
		}
	}

	/**
	 * @return string
	 */
	public function getSftpPath():string {
		return $this->sftpPath;
	}

	/**
	 * Returns an object name.
	 *
	 * @param string $objectName
	 * @return string
	 */
	public function getObjectPath(string $objectName):string {
		return $this->sftpPath
			.(substr($this->sftpPath, -1) != "/" ? "/" : "")
			.$objectName;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->directoryPath;
	}

	/**
	 * Returns a container connected to a SSH server using no authentication.
	 *
	 * @param string $containerPath
	 * @param string $host
	 * @param int|null $port
	 * @return SftpDirectory
	 * @throws SftpDirectoryFactoryException
	 */
	public static function factoryNoAuth(string $containerPath, string $host, int $port = null):SftpDirectory {
		try {
			return new SftpDirectory($containerPath, self::ssh2Connect($host, $port));
		}
		catch (\Throwable $exception) {
			throw new SftpDirectoryFactoryException($containerPath, $exception);
		}
	}

	/**
	 * Returns a container connected to a SSH server using public key authentication.
	 *
	 * @param string $containerPath
	 * @param string $host
	 * @param string $username
	 * @param string $pubKeyPath
	 * @param string $privKeyPath
	 * @param string|null $passphrase
	 * @param int|null $port
	 * @return SftpDirectory
	 * @throws SftpDirectoryFactoryException
	 */
	public static function factoryPubKey(string $containerPath, string $host, string $username,
		string $pubKeyPath, string $privKeyPath, string $passphrase = null, int $port = null) {
		try {
			$ssh2 = self::ssh2Connect($host, $port);
			if (ssh2_auth_pubkey_file($ssh2, $username, $pubKeyPath, $privKeyPath, $passphrase) === false) {
				throw new SftpException("Unable to authenticate on the SSH host \"$host\" using a private key");
			}
			return new SftpDirectory($containerPath, $ssh2);
		}
		catch (\Throwable $exception) {
			throw new SftpDirectoryFactoryException($containerPath, $exception);
		}
	}

	/**
	 * Returns a container connected to a SSH server using user/password authentication.
	 *
	 * @param string $containerPath
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param int|null $port
	 * @return SftpDirectory
	 * @throws SftpDirectoryFactoryException
	 */
	public static function factoryPassword(string $containerPath, string $host, string $username, string $password, int $port = null) {
		try {
			$ssh2 = self::ssh2Connect($host, $port);
			if (ssh2_auth_password($ssh2, $username, $password) === false) {
				throw new SftpException("Unable to authenticate on the SSH host \"$host\" using a password");
			}
			return new SftpDirectory($containerPath, $ssh2);
		}
		catch (\Throwable $exception) {
			throw new SftpDirectoryFactoryException($containerPath, $exception);
		}
	}

	/**
	 * @param string $host
	 * @param int|null $port
	 * @return resource
	 * @throws SftpException
	 */
	private static function ssh2Connect(string $host, int $port = null) {
		if (($ssh2 = ssh2_connect($host, $port ?? 22)) === false) {
			throw new SftpException("Unable to connect to the SSH2 host \"$host\"");
		}
		return $ssh2;
	}

	/**
	 * @return SftpDirectoryIterator
	 */
	public function getIterator():SftpDirectoryIterator {
		return new SftpDirectoryIterator($this);
	}

	/**
	 * @param string $objectName
	 * @return StoreObjectInterface
	 */
	public function getObject(string $objectName):StoreObjectInterface {
		return new SftpFile($objectName, $this);
	}
}