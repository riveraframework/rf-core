<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Http\Transfer;

/**
 * Class FileDownload
 *
 * @package Rf\Core\Http\Transfer
 */
class FileDownload {

    /**
     * Force download for the target file
     *
     * @param  string $fileUri File uri or path to send
     *
     * @TODO: add protection for the app files
     */
    public function __construct($fileUri) {
        
        // Get file infos
        $fileName = strtolower(substr(strrchr($fileUri, '/'), 1));
        $fileContent = file_get_contents($fileUri);

        if($fileContent) {

            // Set response headers
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Type: application/octet-stream');

            // Send file
            die($fileContent);

        } else {
            die('Sorry, This file doesn\'t exist');
        }
        
    }
}