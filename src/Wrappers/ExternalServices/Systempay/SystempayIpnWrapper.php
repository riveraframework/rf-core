<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\ExternalServices\Systempay;

use Rf\Core\Wrappers\ExternalServices\Systempay\Services\SystempayIpn;

/**
 * Class SystempayIpnWrapper
 *
 * *************************************************************************************
 * **** /!\ The code in this module is experimental/incomplete use with caution /!\ ****
 * *************************************************************************************
 *
 * @package Rf\Core\Wrappers\ExternalServices\Systempay
 */
class SystempayIpnWrapper {

    /** @var SystempayIpn  */
    protected $service;

    /**
     * SystempayIpnWrapper constructor.
     *
     * @param array $requestParams
     *
     * @throws \Exception
     */
    public function __construct(array $requestParams)
    {

        $this->service = new SystempayIpn($requestParams);

    }

    /**
     * Get the SystempayIpn service
     *
     * @return SystempayIpn
     */
    public function getService() {

        return $this->service;

    }

}
