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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Versions command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class VersionsCommand extends AbstractCommand
{
    const NAME = 'versions';

    const API_ACTION = 'versions';

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('List files available for download');
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
        return $this->render($info->sendCommand(
            self::API_ACTION,
            $this->getAccountId(),
            $this->getAccessToken()
        ));
    }

    /**
     * Render the versions action
     *
     * @param string $result
     *
     * @return void
     */
    protected function render($result)
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
