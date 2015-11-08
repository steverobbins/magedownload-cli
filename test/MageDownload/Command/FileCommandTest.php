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
 * Test the file command
 */
class FileCommandTest extends TestCase
{
    /**
     * Test command
     *
     * @return void
     */
    public function testCommand()
    {
        $command       = $this->getApplication()->find('file');
        $commandTester = new CommandTester($command);
        $result        = $commandTester->execute([
            'command'     => 'file',
            'name'        => 'PATCH_SUPEE-6788_CE_1.9.2.1_v1.sh',
            'destination' => '/tmp/patch.sh',
            '--id'        => $_SERVER['MAGENTO_ID'],
            '--token'     => $_SERVER['MAGENTO_TOKEN'],
        ]);
        $this->assertEquals(0, $result);
        $this->assertContains('Complete', $commandTester->getDisplay());
        $this->assertFileExists('/tmp/patch.sh');
    }
}
