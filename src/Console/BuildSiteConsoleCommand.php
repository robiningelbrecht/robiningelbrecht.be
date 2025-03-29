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
            'statistics-for-strava',
            'phpunit-pretty-print',
            'phpunit-coverage-tools',
            'symfony-skeleton',
            'php-slim-skeleton',
            'pokemon-card-generator',
            'medium-rss-github',
            'wca-rest-api',
            'puzzle-generator',
            'docker-browsershot',
            'google-spreadsheets-improved-query',
            'playstation-easy-platinums',
            'continuous-integration-example',
            'drupal-amqp-rabbitmq',
        ];

        $template = $this->twig->load('index.html.twig');
        \Safe\file_put_contents($pathToBuildDir . '/index.html', $template->render([
            'blogPosts' => array_slice($blogPosts, 0, 8),
            'repos' => array_map(fn(array $repo) => [
                'name' => $repo['name'],
                'description' => $repo['description'],
                'language' => $repo['language'],
                'stars' => $repo['stargazers_count'],
                'url' => $repo['html_url'],
            ], array_map(fn(string $repoName) => current(array_filter($allGithubRepos, fn(array $githubRepo) => $githubRepo['name'] === $repoName)), $reposToInclude)),
        ]));

        return Command::SUCCESS;
    }
}
