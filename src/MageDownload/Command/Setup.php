<?php
/**
 * Magedownload CLI
 *
 * PHP version 5
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */

namespace MageDownload\Command;

/**
 * Setup commands
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class Setup
{
    /**
     * Get list of command classes
     *
     * @return string[]
     */
    public static function getCommandClasses()
    {
        return array(
            'MageDownload\Command\ConfigureCommand',
            'MageDownload\Command\DownloadCommand',
            'MageDownload\Command\FilesCommand',
            'MageDownload\Command\VersionsCommand',
        );
    }
}
