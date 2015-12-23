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
}
