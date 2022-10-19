<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Encryption;

use Magento\Framework\Filesystem\Io\File as FileManager;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use phpseclib3\Crypt\RSA\PublicKey;

class Encryptor implements EncryptorInterface
{
    public const MODULE_NAME = 'Beehexa_HexaSync';

    /**
     *
     * @var ModuleDirReader
     */
    protected $moduleDirReader;

    /**
     *
     * @var PublicKey|null
     */
    private $publicKey = null;

    /**
     *
     * @var PrivateKey|null
     */
    private $privateKey = null;

    /**
     *
     * @var FileManager
     */
    private $fileManager;

    /**
     * @param \Magento\Framework\Filesystem\Io\File $fileManager
     * @param ModuleDirReader                       $moduleDirReader
     */
    public function __construct(
        FileManager     $fileManager,
        ModuleDirReader $moduleDirReader
    ) {
        $this->fileManager = $fileManager;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * Getter for public key path
     *
     * @return string
     */
    private function getPublicKeyPath()
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team.pub';
    }

    /**
     * Getter for private key path
     *
     * @return string
     */
    private function getPrivateKeyPath()
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team';
    }

    /**
     * Getting public key
     *
     * @return PublicKey
     * @throws \Exception
     */
    private function getPublicKey()
    {
        if (null == $this->publicKey) {
            $keyFile = $this->getPublicKeyPath();
            if ($this->fileManager->fileExists($keyFile)) {
                $publicKey = RSA::load($this->fileManager->read($keyFile));
                $this->publicKey = $publicKey->withPadding(PrivateKey::ENCRYPTION_PKCS1);
            } else {
                throw new \LogicException("Public Key does not exists");
            }
        }
        return $this->publicKey;
    }

    /**
     * Getting private key
     *
     * @return PrivateKey
     * @throws \Exception
     */
    private function getPrivateKey()
    {
        if (null == $this->privateKey) {
            $keyFile = $this->getPrivateKeyPath();
            if ($this->fileManager->fileExists($keyFile)) {
                $privateKey = RSA::load($this->fileManager->read($keyFile));
                $this->privateKey = $privateKey->withPadding(PrivateKey::ENCRYPTION_PKCS1);
            } else {
                throw new \LogicException("Private Key does not exists");
            }
        }
        return $this->privateKey;
    }

    /**
     * @inheritDoc
     */
    public function encrypt($data)
    {
        $publicKey = $this->getPublicKey();
        return $publicKey->encrypt($data);
    }

    /**
     * @inheritDoc
     */
    public function decrypt($data)
    {
        $privateKey = $this->getPrivateKey();
        return $privateKey->decrypt($data);
    }
}
