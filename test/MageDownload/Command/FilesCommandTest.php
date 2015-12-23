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

use MageDownload\Command\FilesCommand;
use MageDownload\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test the files command
 */
class FilesCommandTest extends TestCase
{
    /**
     * Test the files comman execution
     *
     * @return void
     */
    public function testExecute()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command' => FilesCommand::NAME,
            '--id'    => $this->getAccountId(),
            '--token' => $this->getAccessToken(),
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
    }

    /**
     * Test the files comman execution without giving an id or token
     *
     * @return void
     */
    public function testExecuteWithoutIdOrToken()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command' => FilesCommand::NAME,
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
    }

    /**
     * Test with an invalid format
     *
     * @return void
     */
    public function testExecuteFormatInvalid()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $this->setExpectedException('InvalidArgumentException', 'Format "foobar" is not supported');
        $result = $commandTester->execute(array(
            'command'  => FilesCommand::NAME,
            '--format' => 'foobar'
        ));
        $this->assertEquals(1, $result);
    }

    /**
     * Test the files comman execution without giving an id or token
     *
     * @return void
     */
    public function testExecuteFormatJson()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'  => FilesCommand::NAME,
            '--format' => 'json'
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
        $this->assertEquals(true, is_array(json_decode($commandTester->getDisplay())));
    }

    /**
     * Test files command with type filter
     *
     * @return void
     */
    public function testExecuteFilterType()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                               => FilesCommand::NAME,
            '--' . FilesCommand::OPTION_FILTER_TYPE => 'ce-full'
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
        $this->assertNotRegExp('/PATCH_SUPEE/', $commandTester->getDisplay());
    }

    /**
     * Test files command with type filter of invalid value
     *
     * @return void
     */
    public function testExecuteFilterTypeInvalid()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $this->setExpectedException('InvalidArgumentException');
        $result        = $commandTester->execute(array(
            'command'                               => FilesCommand::NAME,
            '--' . FilesCommand::OPTION_FILTER_TYPE => 'foobar'
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
        $this->assertNotRegExp('/PATCH_SUPEE/', $commandTester->getDisplay());
    }

    /**
     * Test files command with version filter
     *
     * @return void
     */
    public function testExecuteFilterVersion()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                               => FilesCommand::NAME,
            '--' . FilesCommand::OPTION_FILTER_VERSION => '1.9.1.*'
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
        $this->assertContains('Version', $commandTester->getDisplay());
        $this->assertNotRegExp('/magento-1\.9\.0\.0\.tar\.gz/', $commandTester->getDisplay());
    }

    /**
     * Test files command with version filter with no results
     *
     * @return void
     */
    public function testExecuteFilterVersionNoResults()
    {
        $command       = $this->getApplication()->find(FilesCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                               => FilesCommand::NAME,
            '--' . FilesCommand::OPTION_FILTER_VERSION => '1.0.0.0'
        ));
        $this->assertEquals(0, $result);
        $this->assertEquals('No results found.' . PHP_EOL, $commandTester->getDisplay());
    }
}
