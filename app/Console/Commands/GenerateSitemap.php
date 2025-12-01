<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(
            Url::create(route('properties.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Add all published properties
        Property::published()
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($properties) use ($sitemap) {
                foreach ($properties as $property) {
                    $sitemap->add(
                        Url::create(route('property.show', $property->slug))
                            ->setLastModificationDate($property->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                }
            });

        // Write sitemap to storage directory (has write permission)
        $sitemapPath = storage_path('app/public/sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        // Cache sitemap for 24 hours
        Cache::put('sitemap_last_generated', now(), 86400);

        $this->info('Sitemap generated successfully at: ' . $sitemapPath);

        return Command::SUCCESS;
    }
}
