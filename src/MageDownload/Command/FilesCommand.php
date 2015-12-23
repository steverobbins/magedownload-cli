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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Files command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class FilesCommand extends AbstractCommand
{
    const NAME = 'files';

    const API_ACTION_FILES  = 'files';
    const API_ACTION_FILTER = 'filter';

    const OPTION_FILTER_TYPE    = 'filter-type';
    const OPTION_FILTER_VERSION = 'filter-version';

    protected $typeFilters = array(
        'ce-full',
        'ce-patch',
        'ee-full',
        'ee-patch',
        'other',
    );

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('List files available for download')
            ->addOption(
                self::OPTION_FILTER_VERSION,
                null,
                InputOption::VALUE_REQUIRED,
                'Version to filter by (1.9.2.1, 1.9.*, etc)'
            )
            ->addOption(
                self::OPTION_FILTER_TYPE,
                null,
                InputOption::VALUE_REQUIRED,
                'Type to filter by (' . implode(', ', $this->typeFilters) . ')'
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
        $info    = new Info;
        $action  = self::API_ACTION_FILES;
        $filters = $this->getFilters();
        if ($filters) {
            $action = self::API_ACTION_FILTER;
        }
        return $this->render($info->sendCommand(
            $action . $filters,
            $this->getAccountId(),
            $this->getAccessToken()
        ));
    }

    /**
     * Get any applied filters
     *
     * @return string
     */
    protected function getFilters()
    {
        $filters = array();
        if ($this->input->getOption(self::OPTION_FILTER_VERSION)) {
            $filters['version'] = $this->input->getOption(self::OPTION_FILTER_VERSION);
        }
        if ($this->input->getOption(self::OPTION_FILTER_TYPE)) {
            $filters['type'] = $this->input->getOption(self::OPTION_FILTER_TYPE);
            if (!in_array($filters['type'], $this->typeFilters)) {
                throw new \InvalidArgumentException(
                    "Invalid filter type.  Must be one of: \n    " . implode("\n    ", $this->typeFilters)
                );
            }
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
    protected function render($result)
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
}
