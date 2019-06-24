<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database\MySQL\QueryBuilder\Clauses;

/**
 * Class LimitTrait
 *
 * @package Rf\Core\Database\MySQL\QueryBuilder\Clauses
 */
trait LimitTrait {

    /** @var string $limitClause */
    public $limitClause;

	/**
     * Set the limit clause
     *
	 * @param int $offset
	 * @param int $number
	 *
	 * @return $this
	 */
    public function limit($offset, $number = null) {

        if($offset == 0 && !isset($number)) {

            $this->limitClause = '';

        } else {

            $this->limitClause = (int)$offset;

            if(isset($number)) {
                $this->limitClause .= ',' . (int)$number;
            }

        }

        return $this;

    }

    /**
     * Set the limit clause using a pager
     *
     * @param int $page
     * @param int $nbByPage
     *
     * @return $this
     */
    public function pager($page, $nbByPage) {

        $offset = ($page - 1) * $nbByPage;

        return $this->limit($offset, $nbByPage);

    }

}