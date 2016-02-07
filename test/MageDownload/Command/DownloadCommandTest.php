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
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\ArgvInput;

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
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/magento-tarbz2/index.php');
    }

    /**
     * Test using --extract on a file that doesn't need it
     *
     * @return void
     */
    public function testCommandSkipExtract()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/patch.sh',
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/patch.sh');
    }

    /**
     * Test a failed extraction
     *
     * @return void
     */
    public function testCommandBadExtract()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/patch.zip',
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Failed to extract contents', $commandTester->getDisplay());
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/patch.zip');
    }

    /**
     * Test downloading when destination is a folder
     *
     * @return void
     */
    public function testCommandFolderDestination()
    {
        $command       = $this->getApplication()->find(DownloadCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                              => DownloadCommand::NAME,
            DownloadCommand::ARGUMENT_NAME         => 'PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh',
            DownloadCommand::ARGUMENT_DESTINATION  => '/tmp/',
            '--' . DownloadCommand::OPTION_EXTRACT => true,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh');
    }

    /**
     * Test downloading using prompts
     *
     * @return void
     */
    public function testCommandWithPrompts()
    {
        $command = $this->getApplication()->find(DownloadCommand::NAME);
        $command->setHelperSet(new HelperSet([new DialogHelper]));
        $commandTester = new CommandTester($command);
        $command->getHelper('dialog')->setInputStream($this->getInputStream("1\n0\n0\n"));
        $result = $commandTester->execute(array(
            'command' => DownloadCommand::NAME,
        ), array('interactive' => new ArgvInput));
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
    }

    /**
     * Send prompts to PHP
     *
     * @param sring $input
     *
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);
        return $stream;
    }
}
