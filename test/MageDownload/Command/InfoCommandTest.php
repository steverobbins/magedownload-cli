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

use MageDownload\Command\InfoCommand;
use MageDownload\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test the info command
 */
class InfoCommandTest extends TestCase
{
    /**
     * Test the files action
     *
     * @return void
     */
    public function testFilesAction()
    {
        $command       = $this->getApplication()->find(InfoCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                    => InfoCommand::NAME,
            InfoCommand::ARGUMENT_ACTION => 'files',
            '--id'                       => $this->getAccountId(),
            '--token'                    => $this->getAccessToken(),
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('File Name', $commandTester->getDisplay());
    }

    /**
     * Test the files action
     *
     * @return void
     */
    public function testVersionsAction()
    {
        $command       = $this->getApplication()->find(InfoCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command'                    => InfoCommand::NAME,
            InfoCommand::ARGUMENT_ACTION => 'versions',
            '--id'                       => $this->getAccountId(),
            '--token'                    => $this->getAccessToken(),
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('CE Versions', $commandTester->getDisplay());
        $this->assertContains('EE Versions', $commandTester->getDisplay());
    }
}
