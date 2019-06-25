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

use Rf\Core\Base\ParameterSet;

/**
 * Class ServiceConfigurationSet
 *
 * @package Rf\Core\Service
 */
class ServiceConfigurationSet extends ParameterSet {

    /**
     * Get a property in a section of the configuration
     * This supports recursive lookup, e.g: my_section.my_sub_section.my_var
     *
     * @param string $section Section name
     *
     * @return ParameterSet|mixed
     */
    public function get($section) {

    	$sectionParts = explode('.', $section);

	    /** @var ParameterSet|mixed $section */
	    $section = $this->vars;

	    $value = false;
	    foreach($sectionParts as $sectionIndex => $sectionName) {

		    $section = $section->get($sectionName);

		    if(
		        $sectionIndex + 1 < count($sectionParts)
                && is_a($section, ParameterSet::class)
            ) {
		    	continue;
		    } else {
		    	$value = $section;
		    	break;
		    }

	    }

        return $value;

    }

}