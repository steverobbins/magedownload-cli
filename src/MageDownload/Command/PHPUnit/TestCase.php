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
use MageDownload\Command\DownloadCommand;
use MageDownload\Command\InfoCommand;
use MageDownload\Config;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;

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
     * Cached user config
     *
     * @var Config
     */
    protected $config;

    /**
     * Set up the application
     *
     * @return Application
     */
    public function getApplication()
    {
        $app = new Application;
        $app->add(new ConfigureCommand);
        $app->add(new DownloadCommand);
        $app->add(new InfoCommand);
        return $app;
    }

    /**
     * Get the user's config
     *
     * @return Config
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = new Config;
        }
        return $this->config;
    }

    /**
     * Get Magento account id
     *
     * @return string
     */
    public function getAccountId()
    {
        if (isset($_SERVER['MAGENTO_ID'])) {
            return $_SERVER['MAGENTO_ID'];
        } elseif ($id = $this->getConfig()->getAccountId()) {
            return $id;
        }
        return '';
    }

    /**
     * Get Magento access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        if (isset($_SERVER['MAGENTO_TOKEN'])) {
            return $_SERVER['MAGENTO_TOKEN'];
        } elseif ($token = $this->getConfig()->getAccessToken()) {
            return $token;
        }
        return '';
    }
}
