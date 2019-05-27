<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Exec;

/**
 * Class ShellCommand
 *
 * @since 1.0
 *
 * @package Rf\Core\Exec
 */
class ShellCommand {

    /**
     * @var resource $resource
     * @since 1.0
     */
    public $resource;

    /**
     * @var string $output
     * @since 1.0
     */
    public $output;


    /**
     * Execute a new command
     *
     * @since 1.0
     *
     * @param $cmd Command to execute
     * @param $path Path to the directory where the command need to be executed
     */
    public function __construct($cmd, $path) {
        
        // Init pipes to get stdout and stderr output
        $descriptorspec = array(
            1 => array('pipe', 'w'), // stdout
            2 => array('pipe', 'w')  // stderr
        );
        
        // Execute command
        $this->resource = proc_open($cmd, $descriptorspec, $pipes, $path);
        
        if (is_resource($this->resource)) {
            
            // Extract output
            $output = stream_get_contents($pipes[2]) . PHP_EOL;
            $output .= stream_get_contents($pipes[1]) . PHP_EOL;
            $this->output = $output;
            
            // Close pipes and proc
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($this->resource);
            
        }
        
        return $this;
    }

    /**
     * Get the current command output
     *
     * @since 1.0
     *
     * @return bool|string
     */
    public function getOutput() {
        return isset($this->output) ? $this->output : false;
    }
}