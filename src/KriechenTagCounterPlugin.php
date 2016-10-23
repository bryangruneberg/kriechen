<?php
/**
 * Created by PhpStorm.
 * User: bryan
 * Date: 23/10/16
 * Time: 13:09
 */

namespace Kriechen;


use Spatie\Crawler\Url;
use Symfony\Component\Console\Style\SymfonyStyle;

class KriechenTagCounterPlugin extends KriechenPluginBase {

  /**
   * @var array
   */
  protected $TagCount = [];

  /**
   * @var string
   */
  protected $tag;

  /**
   * KriechenPluginTagCounter constructor.
   * @param string $tag
   */
  public function __construct(string $tag)
  {
    $this->tag = strtolower($tag);
  }

  /**
   * @param \Spatie\Crawler\Url $url
   * @return mixed
   */
  public function shouldCrawl(Url $url) {
    return TRUE;
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
    $c = (string) $response->getBody();
    $response->getBody()->rewind();

    $tags = substr_count(strtolower($c), "<" . $this->tag);
    if(!isset($this->TagCount[$tags])) {
      $this->TagCount[$tags] = [
        'count' => 0,
        'pages' => []
      ];
    }

    $this->TagCount[$tags]['count']++;
    $this->TagCount[$tags]['pages'][] = (string) $url;

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

    foreach($this->TagCount as $count => $countData) {
      if(!$count) { $count = "no"; }
      fprintf($fileHandler, "Pages containing " . $count . " ".strtoupper($this->tag)."s: %d\n", $countData['count']);
      sort($countData['pages']);
      foreach($countData['pages'] as $page) {
        fputs($fileHandler, " -- " . $page . "\n");
      }
    }

    fputs($fileHandler, "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n");

    foreach($this->TagCount as $count => $countData) {
      if(!$count) { $count = "no"; }
      fprintf($fileHandler, "Pages containing " . $count . " ".strtoupper($this->tag)."s: %d\n", $countData['count']);
      sort($countData['pages']);
    }

    return TRUE;
  }

  /**
   * @return mixed
   */
  public function printStats() {
    ksort($this->TagCount);
    $this->io->newLine();
    $this->io->title( strtoupper($this->tag) . ' Counts');
    foreach($this->TagCount as $count => $countData) {
      if(!$count) { $count = "no"; }
      $this->io->text($count . " ".strtoupper($this->tag)."s: " . $countData['count']);
    }

    return TRUE;
  }
}