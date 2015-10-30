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

use Symfony\Component\Yaml\Yaml;

/**
 * Config loader
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class Config
{
    const CONFIG_FILE_NAME = 'magedownload-cli.yaml';

    protected $userConfig;

    /**
     * Check for a config file and load it
     *
     * @return array
     */
    public function getUserConfig()
    {
        if ($this->userConfig === null) {
            $isWin = strtolower(substr(PHP_OS, 0, 3)) === 'win';
            if ($isWin) {
                $homeDir = getenv('USERPROFILE');
            } else {
                $homeDir = getenv('HOME');
            }
            if ($isWin) {
                $configFile = $homeDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;
            } else {
                $configFile = $homeDir . DIRECTORY_SEPARATOR . '.' . self::CONFIG_FILE_NAME;
            }
            if ($homeDir && file_exists($configFile)) {
                $this->userConfig = Yaml::parse(file_get_contents($configFile));
            } else {
                $this->userConfig = false;
            }
        }
        return $this->userConfig;
    }

    /**
     * Get the account id from config file
     *
     * @return string|boolean
     */
    public function getAccountId()
    {
        $config = $this->getUserConfig();
        if (!$config || !isset($config['user']) || !isset($config['user']['id'])) {
            return false;
        }
        return $config['user']['id'];
    }

    /**
     * Get the access token from config file
     *
     * @return string|boolean
     */
    public function getAccessToken()
    {
        $config = $this->getUserConfig();
        if (!$config || !isset($config['user']) || !isset($config['user']['token'])) {
            return false;
        }
        return $config['user']['token'];
    }
}
