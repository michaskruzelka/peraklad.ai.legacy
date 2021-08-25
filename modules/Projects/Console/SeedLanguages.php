<?php namespace Modules\Projects\Console;

use Illuminate\Console\Command;
use Modules\Projects\Services\LanguageSeeder;

class SeedLanguages extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'languages:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed languages.';

    /**
     * @var LanguageSeeder
     */
    protected $languageSeeder;

	/**
	 * @param LanguageSeeder $languageSeeder
	 */
	public function __construct(LanguageSeeder $languageSeeder)
	{
		$this->languageSeeder = $languageSeeder;
        parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        try {
            $this->languageSeeder->seed();
            $this->info('Languages collection has been seeded successfully');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
	}
}
