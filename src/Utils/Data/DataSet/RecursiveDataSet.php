<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Utils\Data\DataSet;

/**
 * Class RecursiveDataSet
 *
 * @package Rf\Core\Utils\Data\DataSet
 */
class RecursiveDataSet {

	/** @var array $firstLevel */
	protected $firstLevel = [];

	/** @var array $children */
	protected $children = [];

	/**
     * Get data
     *
	 * @return mixed
	 */
	public function getData() {

		return [
			'firstLevel' => $this->firstLevel,
			'children' => $this->children
		];

	}

    /**
     * Add data
     *
     * @param int|string $identifier
     * @param mixed $item
     * @param int|string|null $parentIdentifier
     */
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
     * Get flat ordered data
     *
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

    /**
     * Build the flat ordered data
     *
     * @param array $flatOrderedElements
     * @param int|string $parentIdentifier
     * @param int $level
     */
	protected function addChildren(&$flatOrderedElements, $parentIdentifier, $level) {

		$level += 1;

		if (!empty($this->children[$parentIdentifier])) {

			foreach ($this->children[$parentIdentifier] as $identifier => $item) {

				$flatOrderedElements[] = [$level, $item];

				$this->addChildren($flatOrderedElements, $identifier, $level);

			}

		}

	}

}