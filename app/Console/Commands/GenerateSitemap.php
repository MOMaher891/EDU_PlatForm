<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Course;
use App\Models\Category;

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
    protected $description = 'Generate dynamic sitemap.xml for the Educational Platform';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating dynamic sitemap...');

        $sitemap = Sitemap::create();

        // 1. Static Pages
        $sitemap->add(
            Url::create(route('home'))
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        $sitemap->add(
            Url::create(route('student.courses.index'))
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        if (\Route::has('about')) {
            $sitemap->add(
                Url::create(route('about'))
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }

        if (\Route::has('contact')) {
            $sitemap->add(
                Url::create(route('contact'))
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }

        if (\Route::has('compliance.terms')) {
            $sitemap->add(
                Url::create(route('compliance.terms'))
                    ->setPriority(0.3)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            );
        }

        if (\Route::has('compliance.privacy')) {
            $sitemap->add(
                Url::create(route('compliance.privacy'))
                    ->setPriority(0.3)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            );
        }

        if (\Route::has('compliance.refund')) {
            $sitemap->add(
                Url::create(route('compliance.refund'))
                    ->setPriority(0.3)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            );
        }

        // 2. Dynamic Categories
        $categoriesCount = 0;
        Category::all()->each(function (Category $category) use ($sitemap, &$categoriesCount) {
            $sitemap->add(
                Url::create(route('student.courses.index', ['category' => $category->id]))
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
            $categoriesCount++;
        });

        // 3. Dynamic Published Courses
        $coursesCount = 0;
        Course::where('is_published', true)->get()->each(function (Course $course) use ($sitemap, &$coursesCount) {
            $tag = Url::create(route('student.courses.show', $course))
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);

            if ($course->updated_at) {
                $tag->setLastModificationDate($course->updated_at);
            }

            $sitemap->add($tag);
            $coursesCount++;
        });

        // Write output file to public/sitemap.xml
        $sitemapPath = public_path('sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        $this->info("Sitemap successfully generated at {$sitemapPath}");
        $this->info("Included static pages, {$categoriesCount} categories, and {$coursesCount} published courses.");

        return Command::SUCCESS;
    }
}
