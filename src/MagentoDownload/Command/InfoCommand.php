<?php
/**
 * Magento Download CLI
 *
 * PHP version 5
 *
 * @category  MagentoDownload
 * @package   MagentoDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magento-download-cli
 */

namespace MagentoDownload\Command;

use MagentoDownload\Info;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Info command
 *
 * @category  MagentoDownload
 * @package   MagentoDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magento-download-cli
 */
class InfoCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDescription('Get information about downloads modules')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'Info command',
                'help'
            );
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
        $info = new Info;
        $result = $info->sendCommand(
            $input->getArgument('action'),
            $input->getOption('id'),
            $input->getOption('token')
        );
        echo $result;
    }
}
