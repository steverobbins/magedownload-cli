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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

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
class DownloadCommand extends AbstractCommand
{
    const NAME = 'download';

    const ARGUMENT_NAME        = 'name';
    const ARGUMENT_DESTINATION = 'destination';

    const OPTION_EXTRACT = 'extract';

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Download a release or patch')
            ->addArgument(
                self::ARGUMENT_NAME,
                InputArgument::REQUIRED,
                'The name of the file to download'
            )
            ->addArgument(
                self::ARGUMENT_DESTINATION,
                InputArgument::OPTIONAL,
                'The destination where the file should be downloaded'
            )
            ->addOption(
                self::OPTION_EXTRACT,
                'x',
                InputOption::VALUE_NONE,
                'When given, the downloaded file will be extracted if possible'
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
        $destination = $this->getDestination();
        $this->output->writeln(sprintf('Downloading to <info>%s</info>...', $destination));
        $download = new Download;
        $result = $download->get(
            $this->input->getArgument(self::ARGUMENT_NAME),
            $this->getAccountId(),
            $this->getAccessToken()
        );
        $success = file_put_contents($destination, $result);
        if (!$success) {
            return $this->output->writeln('<error>Failed to download file</error>');
        }
        if ($input->getOption(self::OPTION_EXTRACT)) {
            $this->extract($destination, $output);
        }
        $this->output->writeln('Complete');
    }

    /**
     * Extract the downloaded file
     *
     * @param string          $file
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function extract($file, OutputInterface $output)
    {
        if (substr($file, -8) === '.tar.bz2' || substr($file, -7) === '.tar.gz') {
            $destination = substr($file, -8) === '.tar.bz2' ? substr($file, 0, -8) : substr($file, 0, -7);
            $output->writeln(sprintf('Extracting to <info>%s</info>...', $destination));
            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }
            exec("tar -xf $file -C $destination");
        } elseif (substr($file, -4) === '.zip') {
            $destination = substr($file, 0, -4);
            $output->writeln(sprintf('Extracting to <info>%s</info>...', $destination));
            $zip = new ZipArchive();
            if ($zip->open($file) === true) {
                $zip->extractTo($destination);
                $zip->close();
            }
        } else {
            return;
        }
        if (is_dir($destination)) {
            unlink($file);
            if (is_dir($mageDir = $destination . DIRECTORY_SEPARATOR . 'magento')) {
                $tmp = 'magedownload_' . microtime(true);
                exec("mv $mageDir /tmp/$tmp && rm -rf $destination && mv /tmp/$tmp $destination");
            }
            return;
        }
        $output->writeln('<error>Failed to extract contents</error>');
    }

    /**
     * Determine where the file should download to
     *
     * @return string
     */
    private function getDestination()
    {
        $dest = $this->input->getArgument(self::ARGUMENT_DESTINATION);
        if (!$dest) {
            return getcwd() . DIRECTORY_SEPARATOR . $this->input->getArgument(self::ARGUMENT_NAME);
        }
        if (is_dir($dest)) {
            if (substr($dest, -1) !== '/') {
                $dest .= DIRECTORY_SEPARATOR;
            }
            return $dest . $this->input->getArgument(self::ARGUMENT_NAME);
        }
        return $dest;
    }
}
