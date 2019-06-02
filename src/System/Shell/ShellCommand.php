<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Shell;

/**
 * Class ShellCommand
 *
 * @package Rf\Core\Shell
 */
class ShellCommand {

    /** @var resource $resource */
    public $resource;

    /** @var string $output */
    public $output;

    /**
     * Execute a new command
     *
     * @param string $cmd Command to execute
     * @param string $path Path to the directory where the command need to be executed
     */
    public function __construct($cmd, $path) {
        
        // Init pipes to get stdout and stderr output
        $descriptorSpec = [
            'stdout' => ['pipe', 'w'],
            'stderr' => ['pipe', 'w']
        ];
        
        // Execute command
        $this->resource = proc_open($cmd, $descriptorSpec, $pipes, $path);
        
        if (is_resource($this->resource)) {
            
            // Extract output
            $output = stream_get_contents($pipes['stdout']) . PHP_EOL;
            $output .= stream_get_contents($pipes['stderr']) . PHP_EOL;
            $this->output = $output;
            
            // Close pipes and proc
            fclose($pipes['stderr']);
            fclose($pipes['stdout']);
            proc_close($this->resource);
            
        }
        
        return $this;

    }

    /**
     * Get the current command output
     *
     * @return bool|string
     */
    public function getOutput() {

        return isset($this->output) ? $this->output : false;

    }

}