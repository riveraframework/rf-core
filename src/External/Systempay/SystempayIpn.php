<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\External\Systempay;

/**
 * Class SystempayIpn
 *
 * @package Rf\Core\External\Systempay
 */
class SystempayIpn {

    protected $requestParams = [];

    public function __construct(array $requestParams) {

        $this->requestParams = $requestParams;

    }

    public function checkSignature($certificate) {

        // Sort fields alphabetically
        ksort($this->requestParams);

        // Create signature
        $signature = '';
        foreach ($this->requestParams as $name => $value) {

            if(substr($name,0,5) == 'vads_') {
                $signature .= $value . '+';
            }

        }

        $signature .= $certificate;
        $signature = sha1($signature);

        // Compare signature
        if(isset($this->requestParams['signature']) && ($signature == $this->requestParams['signature'])) {
            return true;
        }

        return false;

    }

}
