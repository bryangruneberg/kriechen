<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:49
 */

namespace Kriechen;


use Spatie\Crawler\Url;

class KriechenIgnorePathPatternPlugin extends KriechenPluginBase {

  /**
   * @var array
   */
  protected $patterns = [];

  /**
   * KriechenIgnorePathPatternPlugin constructor.
   * @param array $patterns
   */
  public function __construct(array $patterns)
  {
    $this->patterns = $patterns;
  }

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  public function shouldCrawl(Url $url) {
    $ret = TRUE;

    foreach($this->patterns as $pattern)
    {
      if(preg_match($pattern, $url->path)) {
        $ret = FALSE;
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