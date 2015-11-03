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

use MageDownload\Info;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Info command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
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
            ->setDescription('Get information about downloads')
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
        $action = $this->input->getArgument('action');
        $info = new Info;
        $result = $info->sendCommand(
            $action,
            $this->getAccountId(),
            $this->getAccessToken()
        );
        switch ($action) {
            case 'files':
                return $this->renderFiles($result);
            case 'versions':
                return $this->renderVersions($result);
        }
        $this->out($result);
    }

    /**
     * Render the files action
     *
     * @param string          $result
     *
     * @return void
     */
    protected function renderFiles($result)
    {
        $bits = preg_split('/\-{5,}/', $result);
        $result = $bits[1];
        $rows = [];
        foreach (explode("\n", $result) as $row) {
            if (empty($row)) {
                continue;
            }
            $bits = preg_split('/ {2,}/', $row);
            $rows[] = [
                $bits[0],
                $bits[1],
                $bits[2],
            ];
        }
        $this->out([[
            'type' => 'table',
            'data' => [
                ['Description', 'Type', 'Name'],
                $rows
            ]
        ]]);
    }

    /**
     * Render the versions action
     *
     * @param string          $result
     *
     * @return void
     */
    protected function renderVersions($result)
    {
        $editions = preg_split('/\n{2}/', $result);
        foreach ($editions as $info) {
            $bits = preg_split('/\-{5,}/', $info);
            $versions = explode("\n", trim($bits[1]));
            array_walk($versions, function(&$value) {
                $value = [$value];
            });
            $this->out([[
                'type' => 'table',
                'data' => [
                    [trim($bits[0])],
                    $versions
                ]
            ]]);
        }
    }
}
