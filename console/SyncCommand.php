<?php namespace Skripteria\Snowflake\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Skripteria\Snowflake\Models\Settings;
use Cms\Classes\Page;
use Cms\Classes\Layout;
use Cms\Classes\Theme;

class SyncCommand extends Command
{
    protected $name = 'snowflake:sync';

    protected $description = 'Scan pages and update Snowflake database.';

    public function handle()
    {

        if ($this->option('cleanup')) {
            $this->output->writeln('Cleaning up unused cms_keys...');
        }

        $this->syncPages();
        $this->output->success('Snowflake sync complete');
    }

    protected function getOptions()
    {
        return [
            ['cleanup', 'null', InputOption::VALUE_NONE, 'Cleanup unused cms_keys.', null],
        ];
    }

    protected function syncPages() {
        $active_theme = Theme::getActiveTheme();

        foreach (Page::listInTheme($active_theme) as $page) {
            $this->output->writeln('Syncing Snowflake Page: ' . $page->getFileName());
            \Skripteria\Snowflake\parse_snowflake($page,'page', $this->option('cleanup'));
        }

        foreach (Layout::listInTheme($active_theme) as $layout) {
            $this->output->writeln('Syncing Snowflake Layout: ' . $layout->getFileName());
            \Skripteria\Snowflake\parse_snowflake($layout,'layout', $this->option('cleanup'));
        }
    }

}
