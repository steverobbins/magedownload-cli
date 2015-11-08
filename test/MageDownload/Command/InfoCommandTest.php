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
 * Test the info command
 */
class ConfigureCommandTest extends TestCase
{
    /**
     * Test the files action
     *
     * @return void
     */
    public function testFilesAction()
    {
        $command       = $this->getApplication()->find('info');
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute([
            'command' => 'info',
            'action'  => 'files',
            '--id'    => $_SERVER['MAGENTO_ID'],
            '--token' => $_SERVER['MAGENTO_TOKEN'],
        ]);
        $this->assertEquals(0, $result);
        $this->assertContains('Configuration successfully updated', $commandTester->getDisplay());
    }
}
