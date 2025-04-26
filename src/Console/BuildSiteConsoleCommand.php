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
            'phpunit-coverage-tools' => 'assets/repos/phpunit-coverage.png',
            'symfony-skeleton' => 'assets/repos/symfony-skeleton.png',
            'php-slim-skeleton' => 'assets/repos/slim-skeleton.png',
            'pokemon-card-generator' => 'assets/repos/pokemon-card-generator.png',
            'wca-rest-api' => 'assets/repos/wca-rest-api.png',
            'medium-rss-github' => 'assets/repos/medium-rss-feed.png',
            'docker-browsershot' => 'assets/repos/browsershot.png',
            'google-spreadsheets-improved-query' => 'assets/repos/google-spreadsheets.png',
            'playstation-easy-platinums' => 'assets/repos/playstation-trophies.png',
            'drupal-amqp-rabbitmq' => 'assets/repos/drupal-rabbitmq.png',
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
