<?php

namespace Kriechen\Command;

use Kriechen\KriechenCrawler;

use Kriechen\KriechenIgnoreAllExtPlugin;
use Kriechen\KriechenIgnorePathPatternPlugin;
use Kriechen\KriechenStayOnHostPlugin;
use Kriechen\KriechenTagCounterPlugin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\Url;

class KriechenCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('start')
      ->setDescription('Crawl a site')
      ->setHelp("This command crawls a url")
      ->addArgument('url', InputArgument::REQUIRED, 'The site to crawl');

  }

  /**
   * @param \Symfony\Component\Console\Input\InputInterface $input
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $url = new Url($input->getArgument('url'));
    $io = new SymfonyStyle($input, $output);
    $io->title("Crawling: " . $url);

    $kriechenCrawler = new KriechenCrawler($io, $url->host);

    $kriechenCrawler->addPlugin( new KriechenTagCounterPlugin("h1") );
    $kriechenCrawler->addPlugin( new KriechenStayOnHostPlugin() );
    $kriechenCrawler->addPlugin( new KriechenIgnoreAllExtPlugin( ['html','htm']) );
    $kriechenCrawler->addPlugin( new KriechenIgnorePathPatternPlugin(
     [
       '|/comment/|',
       '|^/en|'
     ]
    ));

    Crawler::create()
      ->setCrawlObserver($kriechenCrawler)
      ->setCrawlProfile($kriechenCrawler)
      ->startCrawling($url);
  }
}
