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

namespace MageDownload\Test\Command;

use MageDownload\Command\DownloadCommand;
use MageDownload\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test the file command
 */
class DownloadCommandTest extends TestCase
{
    /**
     * Test command
     *
     * @return void
     */
    public function testCommand()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                             => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME        => 'PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh',
            DownloadCommand::ARGUMENT_DESTINATION => '/tmp/patch.sh',
            '--id'                                => $this->getAccountId(),
            '--token'                             => $this->getAccessToken(),
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/patch.sh');
    }

    /**
     * Test command with zip extraction
     *
     * @return void
     */
    public function testCommandExtractZip()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'magento-1.9.0.0.zip',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/magento-zip.zip',
            '--id'                                 => $this->getAccountId(),
            '--token'                              => $this->getAccessToken(),
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/magento-zip/index.php');
    }

    /**
     * Test command with .tar.gz extraction
     *
     * @return void
     */
    public function testCommandExtractTarGz()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'magento-1.9.0.0.tar.gz',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/magento-targz.tar.gz',
            '--id'                                 => $this->getAccountId(),
            '--token'                              => $this->getAccessToken(),
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/magento-targz/index.php');
    }

    /**
     * Test command with .tar.bz2 extraction
     *
     * @return void
     */
    public function testCommandExtractTarBz2()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'magento-1.9.0.0.tar.bz2',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/magento-tarbz2.tar.bz2',
            '--id'                                 => $this->getAccountId(),
            '--token'                              => $this->getAccessToken(),
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/magento-tarbz2/index.php');
    }
}
