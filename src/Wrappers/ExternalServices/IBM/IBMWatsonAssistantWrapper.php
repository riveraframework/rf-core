<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\ExternalServices\IBM;

use Rf\Core\Wrappers\ExternalServices\IBM\Services\IBMWatsonAssistant;

/**
 * Class IBMWatsonAssistantWrapper
 *
 * *************************************************************************************
 * **** /!\ The code in this module is experimental/incomplete use with caution /!\ ****
 * *************************************************************************************
 *
 * @package Rf\Core\Wrappers\ExternalServices\IBM
 */
class IBMWatsonAssistantWrapper {

    /** @var IBMWatsonAssistant  */
    protected $service;

    /**
     * IBMWatsonAssistantWrapper constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $workspaceId
     * @param string $version
     *
     * @throws \Exception
     */
    public function __construct($username, $password, $workspaceId, $version = null)
    {

        $this->service = new IBMWatsonAssistant($username, $password, $workspaceId, $version);

    }

    /**
     * Get the IBMWatsonAssistant service
     *
     * @return IBMWatsonAssistant
     */
    public function getService() {

        return $this->service;

    }

}
