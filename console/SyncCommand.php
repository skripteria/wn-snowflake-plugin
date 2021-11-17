<?php namespace Skripteria\Snowflake\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Skripteria\Snowflake\Models\Settings;
use Cms\Classes\Page;

class SyncCommand extends Command
{
    protected $name = 'snowflake:sync';

    protected $description = 'Scan pages and update Snowflake database.';

    public function handle()
    {
        $this->output->text('test', 'fg=green');
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
        foreach (Page::all() as $page)
        {
            if ($page->hasComponent('sf_page'))  {
                $this->output->writeln('Syncing Snowflake: ' . $page->getFileName());
                \Skripteria\Snowflake\parse_snowflake($page, $this->option('cleanup'));
                $page->save();
            }
        }


    }
}
