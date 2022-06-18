<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Encryption;


interface EncryptorInterface
{
    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypt a string
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data);
}
