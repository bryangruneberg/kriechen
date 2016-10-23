<?php

namespace Kriechen\Command;

use Kriechen\KriechenCrawler;
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
      ->setName('kriechen')
      ->setDescription('Crawl a site')
      ->setHelp("This command crawls a url")
      ->addArgument('url', InputArgument::REQUIRED, 'The site to crawl');

  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $url = new Url($input->getArgument('url'));
    $io = new SymfonyStyle($input, $output);
    $io->title("Crawling: " . $url);

    $kriechenCrawler = new KriechenCrawler();
    $kriechenCrawler->setIO($io);
    $kriechenCrawler->setHost($url->host);

    Crawler::create()
      ->setCrawlObserver($kriechenCrawler)
      ->setCrawlProfile($kriechenCrawler)
      ->startCrawling($url);
  }
}
