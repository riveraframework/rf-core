<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Wrappers\InternalServices\Solr;

/**
 * Class SolrClientWrapper
 *
 * @package Rf\Core\Wrappers\InternalServices\Solr
 */
class SolrClientWrapper {

    /** @var \SolrClient  */
	protected $service;

	/**
	 * SolrClientWrapper constructor.
	 *
	 * @param array $options
	 *
	 * @throws \Exception
	 */
    public function __construct(array $options)
    {

    	if(!class_exists('\SolrClient')) {
    		throw new \Exception('The Solr pecl extension is not installed');
	    }

    	try {
		    $this->service = new \SolrClient($options);
	    } catch(\SolrIllegalArgumentException $e) {
		    throw new \Exception('Unable to init Solr');
	    }

    }

    /**
     * Get the Solr service
     *
     * @return \SolrClient
     */
    public function getService() {

        return $this->service;

    }

}