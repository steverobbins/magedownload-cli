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
 * Test the configure command
 */
class ConfigureCommandTest extends TestCase
{
    /**
     * Test the configure command
     *
     * @return void
     */
    public function testCommand()
    {
        $command       = $this->getApplication()->find('configure');
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute([
            'command' => 'configure',
            '--id'    => 'MAG000000000',
            '--token' => 'abcdef1234567890abcdef1234567890abcdef12',
        ]);
        $this->assertEquals(0, $result);
        $this->assertContains('Configuration successfully updated', $commandTester->getDisplay());
    }
}
