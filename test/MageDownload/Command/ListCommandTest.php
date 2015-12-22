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

use MageDownload\Command\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test the list command
 */
class ListCommandTest extends TestCase
{
    /**
     * Test the list command
     *
     * @return void
     */
    public function testCommand()
    {
        $command       = $this->getApplication()->find('list');
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute(array(
            'command' => 'list',
        ));
        $this->assertEquals(0, $result);
        $this->assertContains('Available commands', $commandTester->getDisplay());
    }
}
