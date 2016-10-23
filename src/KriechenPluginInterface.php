<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:02
 */

namespace Kriechen;

use Symfony\Component\Console\Style\SymfonyStyle;
use \Spatie\Crawler\Url;
use \Spatie\Crawler\ResponseInterface;

interface KriechenPluginInterface {
  public function initialize(SymfonyStyle $io, $host);
  public function shouldCrawl(Url $url);
  public function willCrawl(Url $url);
  public function hasBeenCrawled(Url $url, $response);
  public function finishedCrawling();
  public function writeResults($fileHandler);
  public function printStats();
}