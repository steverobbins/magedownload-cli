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

use MageDownload\Config;
use MageDownload\Download;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Download file command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class ConfigureCommand extends AbstractCommand
{
    const NAME = 'configure';

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Configure your account ID and access token');
        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $currentId = $this->getAccountId();
        } catch (\InvalidArgumentException $e) {
            // Ignore this exception
            $currentId = false;
        }
        try {
            $currentToken = $this->getAccessToken();
        } catch (\InvalidArgumentException $e) {
            // Ignore this exception
            $currentToken = false;
        }
        if ($this->input->getOption('id')) {
            $newId = $this->input->getOption('id');
        } else {
            $newId = $this->promptFor('account id', $currentId);
        }
        if ($this->input->getOption('token')) {
            $newToken = $this->input->getOption('token');
        } else {
            $newToken = $this->promptFor('access token', $currentToken);
        }
        $config = new Config;
        $success = $config->saveConfig([
            'user' => [
                'id'    => $newId,
                'token' => $newToken,
            ]
        ]);
        if ($success) {
            $this->out('<info>Configuration successfully updated</info>');
        } else {
            $this->out('<error>Failed to update configuration</error>');
        }
    }

    /**
     * Get the new value for config option
     *
     * @param string         $name
     * @param string|boolean $currentValue
     *
     * @return string
     */
    protected function promptFor($name, $currentValue)
    {
        $dialog   = $this->getHelper('dialog');
        $newValue = $dialog->ask(
            $this->output,
            sprintf('Please enter the %s%s: ', $name, $currentValue ? sprintf(' (%s)', $currentValue) : ''),
            $currentValue
        );
        if (!$newValue) {
            $this->output->writeln('<error>Value cannot be empty</error>');
            return $this->promptFor($name, $currentValue);
        }
        return $newValue;
    }
}
