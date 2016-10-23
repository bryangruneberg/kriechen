<?php

namespace Kriechen;

use \Spatie\Crawler\CrawlObserver;
use \Spatie\Crawler\CrawlProfile;
use \Spatie\Crawler\Url;
use \Spatie\Crawler\ResponseInterface;

use Symfony\Component\Console\Style\SymfonyStyle;

class KriechenCrawler implements CrawlObserver, CrawlProfile
{
  protected $io;
  protected $host;

  protected $notCrawled = [];
  protected $foreignHosts = [];
  protected $crawled = [];

  protected $crawlExtentions = ['html','htm'];
  protected $ignoredExtensions = [];

  protected $H1Count = [];

  protected $pages = 0;

  protected $tmpFile = NULL;

  public function setIO(SymfonyStyle $io) 
  {
    $this->io = $io;
    $this->io->progressStart(2000);
  }

  public function setHost($host) 
  {
    $this->host = $host;
  }

  /**
   * Determine if the given url should be crawled.
   *
   * @param \Spatie\Crawler\Url $url
   *
   * @return bool
   */
  public function shouldCrawl(Url $url) 
  {
    $ret = $url->host == $this->host;

    if(!$ret && !in_array($url->host, $this->foreignHosts)) 
    {
      $this->foreignHosts[] = $url->host;
    }

    if(preg_match('|/comment/|', $url->path)) {
      $ret = FALSE;
    }

    if(preg_match('|^/en|', $url->path)) {
      $ret = FALSE;
    } 

    if($ret && preg_match('/\.(\w{3,4})$/', (string) $url, $MAT))
    {
      if(!in_array($MAT[1], $this->crawlExtentions)) {
        $ret = FALSE;

        if(!in_array($MAT[1], $this->ignoredExtensions)) {
          $this->ignoredExtensions[] = $MAT[1];
        }
      }
    }

    return $ret;
  }

  /**
   * Called when the crawler will crawl the given url.
   *
   * @param \Spatie\Crawler\Url $url
   */
  public function willCrawl(Url $url) 
  {
  } 

  /**
   * Called when the crawler has crawled the given url.
   *
   * @param \Spatie\Crawler\Url       $url
   * @param \Psr\Http\Message\ResponseInterface $response
   */
  public function hasBeenCrawled(Url $url, $response)
  {
    $this->crawled[] = $url->__toString();

    $c = (string) $response->getBody();
    $response->getBody()->rewind();

    $h1 = substr_count(strtolower($c), "<h1");
    if(!isset($this->H1Count[$h1])) {
      $this->H1Count[$h1] = [
        'count' => 0,
        'pages' => []
      ];
    }

    $this->H1Count[$h1]['count']++;
    $this->H1Count[$h1]['pages'][] = (string) $url;

    //    $this->io->text("Crawled: " . $url . " (size: " . strlen($c) . ") [H1: ".$h1."]");

    $this->io->progressAdvance();
    $this->pages++;

    if(($this->pages % 50) == 0)
    {
      ksort($this->H1Count);
      $this->printStats();
    }

    if(($this->pages % 100) == 0)
    {
      $this->io->note("Report @: " . $this->writeResults());
    }

    return TRUE;
  }

  public function printStats() 
  {
    $this->io->newLine(2);
    $this->io->title('H1 Counts');
    foreach($this->H1Count as $count => $countData) {
      if(!$count) { $count = "no"; }
      $this->io->text($count . ' H1s: ' . $countData['count']);
    }

    $this->io->newLine(2);
  }

  public function writeResults()
  {

    if(!$this->tmpFile) 
    {
      $this->tmpFile = tempnam(NULL, 'crawl-');
    }

    $f = fopen($this->tmpFile, "w");
    fputs($f, "Results for " . $this->host ."\n\n");
    foreach($this->H1Count as $count => $countData) {
      if(!$count) { $count = "no"; }
      fprintf($f, $count . " H1s: %d\n", $countData['count']);
      sort($countData['pages']);
      foreach($countData['pages'] as $page) {
        fputs($f, " -- " . $page . "\n");
      }
    }

    fputs($f, "\n\n======================================\n\n");

    foreach($this->H1Count as $count => $countData) {
      if(!$count) { $count = "no"; }
      fprintf($f, $count . " H1s: %d\n", $countData['count']);
      sort($countData['pages']);
    }
    fclose($f);

    return $this->tmpFile;
  }

  /**
   * Called when the crawl has ended.
   */
  public function finishedCrawling()
  {
    $this->io->text("Done Crawling.");
    $this->io->text("Results in " . $this->writeResults());
  }
}
