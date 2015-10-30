<?php
/**
 * Magento Download CLI
 *
 * PHP version 5
 *
 * @category  MagentoDownload
 * @package   MagentoDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magento-download-cli
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MagentoDownload\Command\DownloadCommand;
use MagentoDownload\Command\InfoCommand;
use Symfony\Component\Console\Application;

$app = new Application('Magento Download CLI');

$app->add(new DownloadCommand);
$app->add(new InfoCommand);

$app->run();
