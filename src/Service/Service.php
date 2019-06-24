<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Service;

use Rf\Core\Application\Components\ConfigurationSet;
use Rf\Core\Service\Interfaces\ServiceInterface;

/**
 * Class Service
 *
 * @package Rf\Core\Service
 */
class Service implements ServiceInterface {

    /** @var string */
    protected $type;

    /** @var string */
    protected $name;

    /** @var ConfigurationSet */
    protected $configuration;

    /** @var bool */
    protected $enabled = true;

    /** @var bool */
    protected $default = false;

    /**
     * {@inheritDoc}
     */
    public function __construct($type, $name, array $configuration = [], $default = false) {

        $this->type = $type;
        $this->name = $name;
        $this->default = $default;

        $this->loadConfiguration($configuration);

    }

    /**
     * {@inheritDoc}
     */
    public function getType() {

        return $this->type;

    }

    /**
     * {@inheritDoc}
     */
    public function getName() {

        return $this->name;

    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled() {

        return $this->enabled;

    }

    /**
     * {@inheritDoc}
     */
    public function isDefault() {

        return $this->default;

    }

    /**
     * {@inheritDoc}
     */
    public function loadConfiguration(array $configuration) {

        $this->configuration = new ConfigurationSet($configuration);

    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration() {

        return $this->configuration;

    }

}