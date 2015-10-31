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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Abstract scan command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
abstract class AbstractCommand extends Command
{
    protected $config;

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->addOption(
                'id',
                null,
                InputOption::VALUE_OPTIONAL,
                'Magento account ID'
            )
            ->addOption(
                'token',
                null,
                InputOption::VALUE_OPTIONAL,
                'Magento access token'
            );
    }

    /**
     * Get the account id specified, or from the config
     *
     * @param InputInterface $input
     *
     * @return string
     */
    public function getAccountId(InputInterface $input)
    {
        if ($input->getOption('id')) {
            return $input->getOption('id');
        } elseif ($this->getConfig()->getAccountId()) {
            return $this->getConfig()->getAccountId();
        }
        throw new \InvalidArgumentException('You must specify an account id');
    }

    /**
     * Get the access token specified, or from the config
     *
     * @param InputInterface $input
     *
     * @return string
     */
    public function getAccessToken(InputInterface $input)
    {
        if ($input->getOption('token')) {
            return $input->getOption('token');
        } elseif ($this->getConfig()->getAccessToken()) {
            return $this->getConfig()->getAccessToken();
        }
        throw new \InvalidArgumentException('You must specify an access token');
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
}
