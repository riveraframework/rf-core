<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Search\Solr;

/**
 * Class SolrService
 *
 * @package Rf\Core\Search\Solr
 */
class SolrService {

	protected $client;

	/**
	 * SolrService constructor.
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
		    $this->client = new \SolrClient($options);
	    } catch(\SolrIllegalArgumentException $e) {
		    throw new \Exception('Unable to init Solr');
	    }

    }

    /**
     * Get the Solr client
     *
     * @return \SolrClient
     */
    public function getClient() {

        return $this->client;

    }

}