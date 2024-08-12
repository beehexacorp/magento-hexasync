<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Encryption;

use Exception;
use LogicException;
use Magento\Framework\Filesystem\Io\File as FileManager;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PrivateKey;
use phpseclib3\Crypt\RSA\PublicKey;

class Encryptor implements EncryptorInterface
{
    public const string MODULE_NAME = 'Beehexa_HexaSync';

    /**
     *
     * @var ModuleDirReader
     */
    protected ModuleDirReader $moduleDirReader;

    /**
     *
     * @var PublicKey|null
     */
    private ?PublicKey $publicKey = null;

    /**
     *
     * @var PrivateKey|null
     */
    private ?PrivateKey $privateKey = null;

    /**
     *
     * @var FileManager
     */
    private FileManager $fileManager;

    /**
     * @param FileManager $fileManager
     * @param ModuleDirReader $moduleDirReader
     */
    public function __construct(
        FileManager     $fileManager,
        ModuleDirReader $moduleDirReader
    ) {
        $this->fileManager = $fileManager;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $data): string
    {
        $publicKey = $this->getPublicKey();
        return $publicKey->encrypt($data);
    }

    /**
     * Getting public key
     *
     * @return PublicKey|null
     */
    private function getPublicKey(): ?PublicKey
    {
        if (null == $this->publicKey) {
            $keyFile = $this->getPublicKeyPath();
            if ($this->fileManager->fileExists($keyFile)) {
                $publicKey = RSA::load($this->fileManager->read($keyFile));
                $this->publicKey = $publicKey->withPadding(RSA::ENCRYPTION_PKCS1);
            } else {
                throw new LogicException("Public Key does not exists");
            }
        }
        return $this->publicKey;
    }

    /**
     * Getter for public key path
     *
     * @return string
     */
    private function getPublicKeyPath(): string
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team.pub';
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $data): string
    {
        $privateKey = $this->getPrivateKey();
        return $privateKey->decrypt($data);
    }

    /**
     * Getting private key
     *
     * @return PrivateKey|null
     */
    private function getPrivateKey(): ?PrivateKey
    {
        if (null == $this->privateKey) {
            $keyFile = $this->getPrivateKeyPath();
            if ($this->fileManager->fileExists($keyFile)) {
                $privateKey = RSA::load($this->fileManager->read($keyFile));
                $this->privateKey = $privateKey->withPadding(PrivateKey::ENCRYPTION_PKCS1);
            } else {
                throw new LogicException("Private Key does not exists");
            }
        }
        return $this->privateKey;
    }

    /**
     * Getter for private key path
     *
     * @return string
     */
    private function getPrivateKeyPath(): string
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team';
    }
}
