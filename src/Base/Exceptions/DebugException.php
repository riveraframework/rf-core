<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Base\Exceptions;

use Rf\Core\Log\Log;

/**
 * Class BaseException
 *
 * @package Rf\Core\Base\Exceptions
 */
class DebugException extends \Exception {

    /** @var string Exception type*/
    public $type;

    /**
     * Create a new Exception
     *
     * @param string $type
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($type, $message, $code = 0, \Exception $previous = null) {

        parent::__construct($message, $code, $previous);
        $this->type = $type;
        $this->call();

    }

	public function toArray() {

		return [
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
		];

	}

    /**
     *
     *
     */
    protected function call() {

        if((rf_config('options.log-mode') == true)) {
            $this->log();
        }

        if((rf_config('options.debug-mode') == true)) {
            $this->debug();
        }

    }

    /**
     * Log the exception
     */
    protected function log() {

        new Log(
            $this->type,
            'File: ' . $this->getFile() . ' (' . $this->getLine() . ') - ' . $this->getMessage() . ' (' . $this->code . ')'
        );
    }

    /**
     * Display debug information
     */
    protected function debug() {

        echo '<em><strong>Debug</strong></em><br/>';
        echo 'Exception Type : ' . $this->type . '<br/>';
        echo 'Error code : ' . $this->code . '<br/>';
        echo 'Message : ' . $this->getMessage() . '<br/>';
        echo 'Error in <strong>' . $this->getFile() . '</strong> on line <strong>' . $this->getLine() . '</strong><br/>';
        echo '<pre>';
        debug_print_backtrace(0, 10);
        echo '</pre>';

    }

}