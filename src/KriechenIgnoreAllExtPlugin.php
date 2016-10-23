<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:34
 */

namespace Kriechen;

use Spatie\Crawler\Url;

class KriechenIgnoreAllExtPlugin extends KriechenPluginBase {

  /**
   * @var array
   */
  protected $crawlExt = [];

  /**
   * @var array
   */
  protected $ignoredExt = [];

  /**
   * KriechenIgnoreAllExtPlugin constructor.
   * @param array $crawlExt
   */
  public function __construct(array $crawlExt)
  {
    $this->crawlExt = $crawlExt;
  }

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  public function shouldCrawl(Url $url) {
    $ret = TRUE;

    if(preg_match('/\.(\w{3,4})$/', (string) $url, $MAT))
    {
      if(!in_array($MAT[1], $this->crawlExt)) {
        $ret = FALSE;

        if(!in_array($MAT[1], $this->ignoredExt)) {
          $this->ignoredExt[] = $MAT[1];
        }
      }
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
    return TRUE;
  }

  /**
   * @return mixed
   */
  public function printStats() {
    return TRUE;
  }
}