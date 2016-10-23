<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:06
 */

namespace Kriechen;


use Spatie\Crawler\Url;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class KriechenPluginBase implements KriechenPluginInterface {

  /**
   * @var SymfonyStyle
   */
  protected $io;
  protected $host;

  /**
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   * @param $host
   * @return mixed
   */
   public function initialize(SymfonyStyle $io, $host)
   {
     $this->io = $io;
     $this->host = $host;

     return TRUE;
   }

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  abstract public function shouldCrawl(Url $url);

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  abstract public function willCrawl(Url $url);

  /**
   * @param \Spatie\Crawler\Url $url
   * @param $response
   * @return mixed
   */
  abstract public function hasBeenCrawled(Url $url, $response);

  /**
   * @return mixed
   */
  abstract public function finishedCrawling();

  /**
   * @param $fileHandler
   * @return mixed
   */
  abstract public function writeResults($fileHandler);

  /**
   * @return mixed
   */
  abstract public function printStats();
}