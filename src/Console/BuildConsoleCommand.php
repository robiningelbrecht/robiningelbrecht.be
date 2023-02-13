<?php

namespace App\Console;

use App\Domain\GitHub;
use App\Infrastructure\Environment\Settings;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

#[AsCommand(name: 'app:build:site', description: 'Build site')]
class BuildConsoleCommand extends Command
{
    public function __construct(
        private readonly Environment $twig,
        private readonly GitHub $gitHub
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathToBuildDir = Settings::getAppRoot().'/build';

        $repos = $this->gitHub->getRepos();
        $template = $this->twig->load('index.html.twig');
        \Safe\file_put_contents($pathToBuildDir.'/index.html', $template->render([
            'repos' => array_map(fn (array $repo) => [
                'name' => $repo['name'],
                'description' => $repo['description'],
                'language' => $repo['language'],
                'topics' => $repo['topics'],
                'stars' => $repo['stargazers_count'],
                'url' => $repo['html_url'],
            ], $repos),
        ]));

        return Command::SUCCESS;
    }
}
