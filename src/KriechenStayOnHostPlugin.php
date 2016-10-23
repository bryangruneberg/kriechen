<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:41
 */

namespace Kriechen;


use Spatie\Crawler\Url;
use Symfony\Component\Console\Style\SymfonyStyle;

class KriechenStayOnHostPlugin extends KriechenPluginBase  {

  /**
   * @var array
   */
  protected $foreignHosts = [];

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  public function shouldCrawl(Url $url) {
    $ret = $url->host == $this->host;

    if(!$ret && !in_array($url->host, $this->foreignHosts))
    {
      $this->foreignHosts[] = $url->host;
    }

    return $ret;
  }

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  public function willCrawl(Url $url) {
    return TRUE;
  }

  /**
   * @param \Spatie\Crawler\Url $url
   * @param $response
   * @return mixed
   */
  public function hasBeenCrawled(Url $url, $response) {
    return TRUE;
  }

  /**
   * @return mixed
   */
  public function finishedCrawling() {
    return TRUE;
  }

  /**
   * @param $fileHandler
   * @return mixed
   */
  public function writeResults($fileHandler) {
    fputs($fileHandler, "\n======================================\n");
    fprintf($fileHandler, "Foreign hosts not crawled: %d\n", count($this->foreignHosts));
    sort($this->foreignHosts);
    foreach($this->foreignHosts as $foreignHost) {
      fputs($fileHandler, " -- " . $foreignHost . "\n");
    }

    return TRUE;
  }

  /**
   * @return mixed
   */
  public function printStats() {
    $this->io->note("Foreign hosts not crawled: " . count($this->foreignHosts));
    return TRUE;
  }
}