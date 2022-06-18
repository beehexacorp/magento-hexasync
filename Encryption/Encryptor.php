<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Encryption;

use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey;
use phpseclib3\Crypt\RSA\PrivateKey;
use Beehexa\HexaSync\Helper\Data;

class Encryptor implements EncryptorInterface
{
    const MODULE_NAME = 'Beehexa_HexaSync';

    /**
     * @var ModuleDirReader
     */
    protected $moduleDirReader;

    /**
     * @var PublicKey|null
     */
    private $publicKey = null;

    /**
     * @var PrivateKey|null
     */
    private $privateKey = null;

    public function __construct(ModuleDirReader $moduleDirReader)
    {
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * @return string
     */
    private function getPublicKeyPath()
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team.pub';
    }

    /**
     * @return string
     */
    private function getPrivateKeyPath()
    {
        $moduleDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, self::MODULE_NAME);
        return rtrim($moduleDir, '/') . '/' . 'team';
    }

    /**
     * @return PublicKey
     * @throws \Exception
     */
    private function getPublicKey()
    {
        if (null == $this->publicKey) {
            $keyFile = $this->getPublicKeyPath();
            if (file_exists($keyFile)) {
                $this->publicKey = RSA::load(file_get_contents($keyFile));
                $this->publicKey->withPadding(PrivateKey::ENCRYPTION_PKCS1);
            } else {
                throw new \Exception("Public Key does not exists");
            }
        }
        return $this->publicKey;
    }

    /**
     * @return PrivateKey
     * @throws \Exception
     */
    private function getPrivateKey()
    {
        if (null == $this->privateKey) {
            $keyFile = $this->getPrivateKeyPath();
            if (file_exists($keyFile)) {
                $this->privateKey = RSA::load(file_get_contents($keyFile));
                $this->privateKey->withPadding(PrivateKey::ENCRYPTION_PKCS1);

            } else {
                throw new \Exception("Public Key does not exists");
            }
        }
        return $this->privateKey;
    }

    /**
     * @inheriDoc
     */
    public function encrypt($data)
    {
        $publicKey = $this->getPublicKey();
        return $publicKey->encrypt($data);
    }

    /**
     * @inheriDoc
     */
    public function decrypt($data)
    {
        $privateKey = $this->getPrivateKey();
        return $privateKey->decrypt($data);
    }
}
