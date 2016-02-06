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
            $this->getAccessToken(),
            true
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
     * @param array $result
     *
     * @return void
     */
    protected function render(array $result)
    {
        if (count($result) == 1) {
            return $this->out(trim($result[0]));
        }
        $headers = array_keys($result[0]);
        $rows = array_map('array_values', $result);
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
        foreach (array_keys($a) as $key) {
            $test = strcmp($a[$key], $b[$key]);
            if ($test) {
                return $test;
            }
        }
    }
}
