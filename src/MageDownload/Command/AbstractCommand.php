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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
    /**
     * Input object
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * Output object
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Cached user config
     *
     * @var Config
     */
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
                InputOption::VALUE_REQUIRED,
                'Magento account ID'
            )
            ->addOption(
                'token',
                null,
                InputOption::VALUE_REQUIRED,
                'Magento access token'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify output format (default, json)',
                'default'
            );
    }

    /**
     * Initialize command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Get the account id specified, or from the config
     *
     * @return string|boolean
     */
    public function getAccountId()
    {
        if ($this->input->getOption('id')) {
            return $this->input->getOption('id');
        } elseif ($this->getConfig()->getAccountId()) {
            return $this->getConfig()->getAccountId();
        }
        throw new \InvalidArgumentException('You must specify an account id');
    }

    /**
     * Get the access token specified, or from the config
     *
     * @return string|boolean
     */
    public function getAccessToken()
    {
        if ($this->input->getOption('token')) {
            return $this->input->getOption('token');
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

    /**
     * Output information in the correct format
     *
     * @param array|string $messages
     *
     * @return void
     */
    protected function out($messages = [])
    {
        $format = $this->input->getOption('format');
        $method = 'outputFormat' . ucfirst($format);
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(
                'Format "' . $format . '" is not supported'
            );
        }
        $this->$method($messages);
    }

    /**
     * Output in default format
     *
     * @param array|string $messages
     *
     * @return void
     */
    protected function outputFormatDefault($messages)
    {
        if (!is_array($messages)) {
            return $this->output->writeln($messages);
        }
        foreach ($messages as $message) {
            switch (isset($message['type']) ? $message['type'] : false) {
                case 'table':
                    $tableHelper = new Table($this->output);
                    $tableHelper
                        ->setHeaders($message['data'][0])
                        ->setRows($message['data'][1])
                        ->render();
                    break;
                default:
                    $this->output->writeln(is_array($message) ? $message['data'] : $message);
            }
        }
    }

    /**
     * Output in json format
     *
     * @param array|string $messages
     *
     * @return void
     */
    protected function outputFormatJson($messages)
    {
        $json = [];
        if (!is_array($messages)) {
            $json[] = strip_tags($messages);
        } else {
            foreach ($messages as $message) {
                switch (isset($message['type']) ? $message['type'] : false) {
                    case 'table':
                        $result = [];
                        $headers = $message['data'][0];
                        array_map('strtolower', $headers);
                        foreach ($message['data'][1] as $row) {
                            foreach ($headers as $key => $name) {
                                $result[$name] = strip_tags($row[$key]);
                            }
                            $json[] = $result;
                        }
                        break;
                    default:
                        $json[] = strip_tags(is_array($message) ? $message['data'] : $message);
                }
            }
        }
        $this->output->write(json_encode($json), false, OutputInterface::OUTPUT_RAW);
    }
}
