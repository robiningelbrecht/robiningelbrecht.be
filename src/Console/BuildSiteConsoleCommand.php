<?php

namespace App\Console;

use App\Domain\GitHub;
use App\Domain\MediumRss;
use App\Infrastructure\Environment\Settings;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use function Symfony\Component\String\s;

#[AsCommand(name: 'app:build:site', description: 'Build site')]
class BuildSiteConsoleCommand extends Command
{
    public function __construct(
        private readonly Environment $twig,
        private readonly GitHub $gitHub,
        private readonly MediumRss $mediumRss,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathToBuildDir = Settings::getAppRoot() . '/build';

        $allGithubRepos = $this->gitHub->getUserRepos('robiningelbrecht');
        $blogPosts = $this->mediumRss->getFeed();


        $reposToInclude = [
            'statistics-for-strava' => 'https://raw.githubusercontent.com/robiningelbrecht/statistics-for-strava/refs/heads/master/public/assets/images/logo-square.svg',
            'phpunit-pretty-print' => 'assets/repos/phpunit-pretty-print.png',
            'phpunit-coverage-tools' => 'https://cdn-images-1.medium.com/max/1024/0*9S8ABCsl4jSg01ah.png',
            'symfony-skeleton' => 'https://symfony.com/logos/symfony_black_03.png',
            'php-slim-skeleton' => 'https://raw.githubusercontent.com/robiningelbrecht/php-slim-skeleton/master/readme/slim-new.webp',
            'pokemon-card-generator' => 'https://github.com/robiningelbrecht/pokemon-card-generator/raw/master/readme/banner.png',
            'wca-rest-api' => 'https://raw.githubusercontent.com/robiningelbrecht/wca-rest-api/master/docs/logo.png',
            'medium-rss-github' => 'https://github.com/robiningelbrecht/medium-rss-github/raw/master/readme/medium.png',
            'docker-browsershot' => 'https://spatie.be/packages/header/browsershot/html/light.webp',
            'google-spreadsheets-improved-query' => 'assets/repos/google-spreadsheets.png',
            'playstation-easy-platinums' => 'https://github.com/robiningelbrecht/playstation-easy-platinums/raw/master/assets/ps-logo.png',
            'drupal-amqp-rabbitmq' => 'https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/raw/master/readme/rabbitmq.png',
        ];

        $repos = [];
        foreach ($reposToInclude as $repoName => $image) {
            $githubRepo = current(array_filter($allGithubRepos, fn(array $githubRepo) => $repoName === $githubRepo['name']));

            $repos[] = [
                'name' => $repoName,
                'description' => $githubRepo['description'],
                'language' => $githubRepo['language'],
                'stars' => $githubRepo['stargazers_count'],
                'url' => $githubRepo['html_url'],
                'image' => $image,
            ];
        }

        $template = $this->twig->load('index.html.twig');
        \Safe\file_put_contents($pathToBuildDir . '/index.html', $template->render([
            'blogPosts' => array_slice($blogPosts, 0, 8),
            'repos' => $repos,
        ]));

        return Command::SUCCESS;
    }
}
