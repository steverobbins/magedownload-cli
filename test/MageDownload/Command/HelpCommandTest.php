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

use MageDownload\Command\ConfigureCommand;
use MageDownload\Command\FileCommand;
use MageDownload\Command\InfoCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;

/**
 * Test the help command
 */
class HelpCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the help command
     *
     * @return void
     */
    public function testCommand()
    {
        $app = new Application;
        $app->add(new ConfigureCommand);
        $app->add(new FileCommand);
        $app->add(new InfoCommand);
        $command = $app->find('help');
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute([
            'command' => 'help',
        ]);
        $this->assertEquals(0, $result);
    }
}
