<?php

namespace Skripteria\Snowflake\Console;

use Cms\Classes\Layout;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Illuminate\Console\Command;
use Skripteria\Snowflake\Classes\SnowflakeParser;
use Symfony\Component\Console\Input\InputOption;

class SyncCommand extends Command
{
    protected $name = 'snowflake:sync';

    protected $description = 'Scan pages and update Snowflake database.';

    public function handle()
    {
        if ($this->option('cleanup')) {
            $this->output->writeln('Cleaning up unused Snowflake Keys...');
        }

        $this->syncPages();
        $this->output->success('Snowflake sync complete');
    }

    protected function getOptions()
    {
        return [
            ['cleanup', 'null', InputOption::VALUE_NONE, 'Clean-up unused Snowflake Keys.', null],
        ];
    }

    protected function syncPages()
    {
        $active_theme = Theme::getActiveTheme();

        foreach (Page::listInTheme($active_theme) as $page) {
            $this->output->writeln('Syncing Snowflake Page: ' . $page->getFileName());

            SnowflakeParser::parseSnowflake($page, 'page', $this->option('cleanup'));
        }

        foreach (Layout::listInTheme($active_theme) as $layout) {
            $this->output->writeln('Syncing Snowflake Layout: ' . $layout->getFileName());

            SnowflakeParser::parseSnowflake($layout, 'layout', $this->option('cleanup'));
        }
    }
}
