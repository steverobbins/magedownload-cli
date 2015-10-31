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
 * Test the help command
 */
class HelpCommandTest extends TestCase
{
    /**
     * Test the help command
     *
     * @return void
     */
    public function testCommand()
    {
        $app           = $this->getApplication();
        $command       = $app->find('help');
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute([
            'command' => 'help',
        ]);
        $this->assertEquals(0, $result);
    }
}
