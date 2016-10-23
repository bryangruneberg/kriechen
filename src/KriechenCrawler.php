<?php

namespace Kriechen;

use \Spatie\Crawler\CrawlObserver;
use \Spatie\Crawler\CrawlProfile;
use \Spatie\Crawler\Url;

use Symfony\Component\Console\Style\SymfonyStyle;

class KriechenCrawler implements CrawlObserver, CrawlProfile
{
  /**
   * @var SymfonyStyle
   */
  protected $io;

  /**
   * @var
   */
  protected $host;

  /**
   * @var array
   */
  protected $notCrawled = [];

  /**
   * @var array
   */
  protected $crawled = [];


  /**
   * @var KriechenPluginInterface[]
   */
  protected $plugins = [];

  /**
   * @var int
   */
  protected $crawlCount = 0;

  /**
   * @var
   */
  protected $tmpFile = NULL;

  public function __construct(SymfonyStyle $io, $host, $totalItems = 0)
  {
    $this->setIO($io);
    $this->setHost($host);
    $this->io->progressStart($totalItems);
  }

  /**
   * @param \Symfony\Component\Console\Style\SymfonyStyle $io
   */
  public function setIO(SymfonyStyle $io) 
  {
    $this->io = $io;
  }

  /**
   * @param $host
   */
  public function setHost($host) 
  {
    $this->host = $host;
  }

  /**
   * @param \Kriechen\KriechenPluginInterface $plugin
   */
  public function addPlugin(KriechenPluginInterface $plugin)
  {
    $plugin->initialize($this->io, $this->host);
    $this->plugins[] = $plugin;
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
    $ret = TRUE;

    foreach($this->plugins as $plugin)
    {
      if(!$plugin->shouldCrawl($url))
      {
        $ret = FALSE;
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
    foreach($this->plugins as $plugin)
    {
      $plugin->willCrawl($url);
    }
  }

  /**
   * Called when the crawler has crawled the given url.
   *
   * @param \Spatie\Crawler\Url $url
   * @param \Psr\Http\Message\ResponseInterface $response
   * @return bool
   */
  public function hasBeenCrawled(Url $url, $response)
  {
    foreach($this->plugins as $plugin)
    {
      $plugin->hasBeenCrawled($url, $response);
    }

    $this->crawled[] = (string) $url;

    $this->io->progressAdvance();
    $this->crawlCount++;

    if(($this->crawlCount % 50) == 0)
    {
      $this->printStats();
    }

    if(($this->crawlCount % 100) == 0)
    {
      $this->io->note("Report @: " . $this->writeResults());
    }

    return TRUE;
  }

  public function printStats() 
  {
    $this->io->newLine(2);
    foreach($this->plugins as $plugin)
    {
      $plugin->printStats();
    }
  }

  public function writeResults()
  {

    if(!$this->tmpFile) 
    {
      $this->tmpFile = tempnam(NULL, 'crawl-');
    }

    $f = fopen($this->tmpFile, "w");
    fputs($f, "Results for " . $this->host ."\n\n");

    foreach($this->plugins as $plugin)
    {
      $plugin->writeResults($f);
    }

    fclose($f);

    return $this->tmpFile;
  }

  /**
   * Called when the crawl has ended.
   */
  public function finishedCrawling()
  {
    $this->io->newLine();

    $this->io->text("Done Crawling.");
    $this->io->text("Results in " . $this->writeResults());

    foreach($this->plugins as $plugin)
    {
      $plugin->finishedCrawling();
    }
  }
}
