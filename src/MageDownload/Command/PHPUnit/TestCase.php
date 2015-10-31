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

namespace MageDownload\Command\PHPUnit;

use MageDownload\Command\ConfigureCommand;
use MageDownload\Command\FileCommand;
use MageDownload\Command\InfoCommand;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;

/**
 * Project test case
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Set up the application
     *
     * @return Application
     */
    public function getApplication()
    {
        $app = new Application;
        $app->add(new ConfigureCommand);
        $app->add(new FileCommand);
        $app->add(new InfoCommand);
        return $app;
    }
}
