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
use Symfony\Component\Console\Input\InputOption;
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
    const NAME = 'info';

    const ARGUMENT_ACTION = 'action';

    const OPTION_FILTER_VERSION = 'filter-version';
    const OPTION_FILTER_TYPE    = 'filter-type';

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Get information about downloads')
            ->addArgument(
                self::ARGUMENT_ACTION,
                InputArgument::REQUIRED,
                'The action ("files" or "versions")'
            )
            ->addOption(
                self::OPTION_FILTER_VERSION,
                null,
                InputOption::VALUE_REQUIRED,
                '"files" action only: Version to filter by (1.9.2.1, 1.9.*, etc)'
            )
            ->addOption(
                self::OPTION_FILTER_TYPE,
                null,
                InputOption::VALUE_REQUIRED,
                '"files" action only: Type to filter by (ce-full, ee-full, ce-patch, ee-patch, other)'
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
        $action  = $this->input->getArgument(self::ARGUMENT_ACTION);
        $filters = $this->getFilters();
        if ($filters) {
            $action = 'filter';
        }
        $info    = new Info;
        $result = $info->sendCommand(
            $action . $filters,
            $this->getAccountId(),
            $this->getAccessToken()
        );
        switch ($action) {
            case 'files':
            case 'filter':
                return $this->renderFiles($result);
            case 'versions':
                return $this->renderVersions($result);
        }
        $this->out($result);
    }

    /**
     * Get any applied filters
     *
     * @return string
     */
    protected function getFilters()
    {
        if ($this->input->getArgument(self::ARGUMENT_ACTION) !== 'files') {
            return;
        }
        $filters = array();
        if ($this->input->getOption(self::OPTION_FILTER_VERSION)) {
            $filters['version'] = $this->input->getOption(self::OPTION_FILTER_VERSION);
        }
        if ($this->input->getOption(self::OPTION_FILTER_TYPE)) {
            $filters['type'] = $this->input->getOption(self::OPTION_FILTER_TYPE);
        }
        if (!count($filters)) {
            return;
        }
        $result = '/';
        foreach ($filters as $type => $value) {
            $result .= $type . '/' . $value;
        }
        return $result;
    }

    /**
     * Render the files action
     *
     * @param string $result
     *
     * @return void
     */
    protected function renderFiles($result)
    {
        $bits = preg_split('/\-{5,}/', $result);
        if (count($bits) == 1) {
            return $this->out(trim($result));
        }
        $headers = array();
        foreach (preg_split('/ {2,}/', $bits[0]) as $value) {
            $headers[] = trim($value);
        }
        unset($headers[0]);
        $rows = array();
        foreach (explode("\n", $bits[1]) as $row) {
            if (empty($row)) {
                continue;
            }
            $row = preg_split('/ {2,}/', $row);
            unset($row[0]);
            $rows[] = $row;
        }
        usort($rows, array($this, 'sortFiles'));
        $this->out(array(array(
            'type' => 'table',
            'data' => array(
                $headers,
                $rows
            )
        )));
    }

    /**
     * Sort files by type and name
     *
     * @param string[] $a
     * @param string[] $b
     *
     * @return integer
     */
    protected function sortFiles($a, $b)
    {
        return strcmp($a[1], $b[1]) ?: strcmp($a[2], $a[2]);
    }

    /**
     * Render the versions action
     *
     * @param string $result
     *
     * @return void
     */
    protected function renderVersions($result)
    {
        $editions = preg_split('/\n{2}/', $result);
        foreach ($editions as $info) {
            $bits = preg_split('/\-{5,}/', $info);
            $versions = explode("\n", trim($bits[1]));
            usort($versions, 'version_compare');
            array_walk($versions, function (&$value) {
                $value = array($value);
            });
            $this->out(array(array(
                'type' => 'table',
                'data' => array(
                    array(trim($bits[0])),
                    $versions
                )
            )));
        }
    }
}
