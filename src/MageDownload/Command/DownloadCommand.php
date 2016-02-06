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
use MageDownload\Info;
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
     * Organized files that can be downloaded
     *
     * I.e. array(
     *     'Community Edition - Full' => array(
     *         '1.9.1.1' => array(
     *             '1.9.1.1.tar.bz2',
     *             '1.9.1.1.tar.gz',
     *             '1.9.1.1.zip',
     *         ),
     *         ...
     *     ),
     *     ...
     * )
     *
     * @var array
     */
    protected $downloads;

    /**
     * Interactively select a file to download
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument(self::ARGUMENT_NAME)) {
            $info = new Info;
            $this->prepareDownloads($info->sendCommand(
                'filter/version/*',
                $this->getAccountId(),
                $this->getAccessToken(),
                true
            ));
            $dialog = $this->getHelper('dialog');
            $selectedMsg = 'You have selected: <info>%s</info>';

            // Pick a type
            $types = array_keys($this->downloads);
            sort($types);
            $type = $dialog->select(
                $this->output,
                '<question>Choose a type of download:</question>',
                $types,
                0
            );
            $type = $types[$type];
            $this->output->writeln(sprintf($selectedMsg, $type));

            // Pick a version
            $versions = array_keys($this->downloads[$type]);
            sort($versions);
            $version = $dialog->select(
                $this->output,
                '<question>Choose a version:</question>',
                $versions,
                0
            );
            $version = $versions[$version];
            $this->output->writeln(sprintf($selectedMsg, $version));

            // Pick a file
            $files = $this->downloads[$type][$version];
            sort($files);
            $file = $dialog->select(
                $this->output,
                '<question>Choose a file:</question>',
                $files,
                0
            );
            $file = $files[$file];
            $this->output->writeln(sprintf($selectedMsg, $file));

            $this->input->setArgument(self::ARGUMENT_NAME, $file);
        }
    }

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

    /**
     * Sort files in their types and versions
     *
     * @param array $files
     *
     * @return void
     */
    protected function prepareDownloads(array $files)
    {
        $this->downloads = array();
        foreach ($files as $file) {
            if (!isset($this->downloads[$file['File Type']])) {
                $this->downloads[$file['File Type']] = array();
            }
            if (!isset($this->downloads[$file['File Type']][$file['Version']])) {
                $this->downloads[$file['File Type']][$file['Version']] = array();
            }
            $this->downloads[$file['File Type']][$file['Version']][] = $file['File Name'];
        }
    }
}
