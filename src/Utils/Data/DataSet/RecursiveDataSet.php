<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Data\DataSet;

/**
 * Class RecursiveDataSet
 *
 * @package Rf\Core\Data\DataSet
 */
class RecursiveDataSet {

	/** @var array $firstLevel */
	protected $firstLevel = [];

	/** @var array $children */
	protected $children = [];

	/**
	 * @return mixed
	 */
	public function getData() {

		return [
			'firstLevel' => $this->firstLevel,
			'children' => $this->children
		];

	}

	public function addData($identifier, $item, $parentIdentifier = null) {

		if(isset($parentIdentifier)) {
			if(!isset($this->children[$parentIdentifier])) {
				$this->children[$parentIdentifier] = [];
			}
			$this->children[$parentIdentifier][$identifier] = $item;
		} else {
			$this->firstLevel[$identifier] = $item;
		}

	}

	/**
	 * @return array
	 */
	public function getFlatOrderedElements() {

		$flatOrderedElements = [];

		foreach ($this->firstLevel as $identifier => $item) {

			$flatOrderedElements[] = [0, $item];

			$this->addChildren($flatOrderedElements, $identifier, 0);

		}

		return $flatOrderedElements;

	}

	public function addChildren(&$flatOrderedElements, $parentIdentifier, $level) {

		$level += 1;

		if (!empty($this->children[$parentIdentifier])) {

			foreach ($this->children[$parentIdentifier] as $identifier => $item) {

				$flatOrderedElements[] = [$level, $item];

				$this->addChildren($flatOrderedElements, $identifier, $level);

			}

		}

	}

}