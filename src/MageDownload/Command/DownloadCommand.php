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

use MageDownload\Download;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Download command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class DownloadCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('download')
            ->setDescription('Download a release or patch')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The file to download'
            )
            ->addArgument(
                'destination',
                InputArgument::OPTIONAL,
                'The destination where the file should be downloaded'
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
        $download = new Download;
        $result = $download->get(
            $input->getArgument('file'),
            $this->getAccountId($input),
            $this->getAccessToken($input)
        );
        $destination = $this->getDestination($input);
        $output->writeln(sprintf('Downloading to <info>%s</info>...', $destination));
        $success = file_put_contents($destination, $result);
        if ($success) {
            $output->writeln('Complete');
        } else {
            $output->writeln('<error>Failed to download file</error>');
        }
    }

    /**
     * Determine where the file should download to
     *
     * @param InputInterface $input
     *
     * @return string
     */
    private function getDestination(InputInterface $input)
    {
        $dest = $input->getArgument('destination');
        if (!$dest) {
            return getcwd() . DIRECTORY_SEPARATOR . $input->getArgument('file');
        }
        if (is_dir($dest)) {
            if (substr($dest, -1) !== '/') {
                $dest .= DIRECTORY_SEPARATOR;
            }
            return $dest . $input->getArgument('file');
        }
        return $dest;
    }
}
