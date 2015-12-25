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

use MageDownload\Command\VersionsCommand;
use MageDownload\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test the versions command
 */
class VersionsCommandTest extends TestCase
{
    /**
     * Test the versions command execution
     *
     * @return void
     */
    public function testExecute()
    {
        // Config file should be set by now, try using it (coverage)
        foreach (array('MAGENTO_ID', 'MAGENTO_TOKEN') as $key) {
            if (isset($_SERVER[$key])) {
                unset($_SERVER[$key]);
            }
        }
        $command       = $this->getApplication()->find(VersionsCommand::NAME);
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command' => VersionsCommand::NAME,
            '--id'    => $this->getAccountId(),
            '--token' => $this->getAccessToken(),
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('CE Versions', $commandTester->getDisplay());
        $this->assertContains('EE Versions', $commandTester->getDisplay());
    }
}
