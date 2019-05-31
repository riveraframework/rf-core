<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Security;

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Log\Log;

/**
 * Class Uuid
 *
 * @package Rf\Core\Security
 */
class Uuid {

    const FORMAT_UUID_V4 = 'uuid-v4';
    const FORMAT_MASK = 'mask';

    protected $value;

    /**
     * Uuid constructor.
     *
     * @param string $format
     * @param string $mask
     * @param null|int $length
     */
    public function __construct($format, $mask = '', $length = null)
    {

        $this->value = self::generateValue($format, $mask, $length);

    }

    /**
     * Get the Uuid value
     *
     * @return string
     */
    public function getValue() {

        return $this->value;

    }

    /**
     * Generate the uuid value
     *
     * @param string $format
     * @param string $mask
     * @param null|int $length
     *
     * @return string
     * @throws DebugException
     */
    public static function generateValue($format, $mask = '', $length = null) {

        switch ($format) {

            case self::FORMAT_UUID_V4:
                return self::generateUuidV4();
                break;

            case self::FORMAT_MASK:
                return self::generateUuidFromMask($mask, $length);
                break;

        }

        throw new DebugException(Log::TYPE_WARNING, 'Unknown Uuid format');

    }

    /**
     *
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     *
     * @return string
     */
    public static function generateUuidV4() {

        $t = unpack('S8', openssl_random_pseudo_bytes(16));

        // four most significant bits of 3rd group hold version number 4
        $t[3] = $t[3] | 0x4000;

        // two most significant bits of 4th group hold zero and one for variant DCE1.1
        $t[4] = $t[4] | 0x8000;

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', ...$t);

    }

    /**
     * Generate uuid value from mask
     *
     * @param string $mask
     * @param null|int $length
     *
     * @return string
     */
    public static function generateUuidFromMask($mask, $length = null) {

        if(empty($length)) {
            $length = 8;
        }

        return Guardian::generateSecurityKey($length, $mask);

    }

}