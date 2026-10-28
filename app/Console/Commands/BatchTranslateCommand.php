<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;

class BatchTranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:batch-translate {--force : Force re-translation of existing keys using API} {--scan : Scan blade views for localization keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch translate Indonesian UI interface texts to English using Translation API and save to localization JSON files';

    public function handle(TranslationService $translationService): int
    {
        $this->info('Starting UI localization batch process...');

        $curated = $translationService->getCuratedFallbackDictionary();
        $scanned = $this->option('scan') ? $translationService->scanBladeFiles() : [];

        $existingEn = $translationService->loadLocaleJson('en');
        $existingId = $translationService->loadLocaleJson('id');

        $force = (bool) $this->option('force');

        // Combine all distinct Indonesian keys
        $allKeys = array_unique(array_merge(array_keys($curated), $scanned));

        $missingKeys = [];
        $finalEn = $existingEn;
        $finalId = $existingId;

        foreach ($allKeys as $key) {
            $finalId[$key] = $key; // In ID, key maps to identical Indonesian string

            if ($force || ! isset($finalEn[$key]) || empty($finalEn[$key])) {
                if (isset($curated[$key])) {
                    $finalEn[$key] = $curated[$key];
                } else {
                    $missingKeys[] = $key;
                }
            }
        }

        $this->info('Found '.count($allKeys).' total UI keys. Missing English translations: '.count($missingKeys));

        if (! empty($missingKeys)) {
            $this->info('Calling Translation API for '.count($missingKeys).' missing keys...');
            $apiTranslations = $translationService->translateBatch($missingKeys);

            foreach ($apiTranslations as $key => $translated) {
                $finalEn[$key] = $translated;
            }
        }

        // Save into lang/en.json and lang/id.json
        $translationService->saveLocaleJson('en', $finalEn);
        $translationService->saveLocaleJson('id', $finalId);

        $this->info('Successfully saved '.count($finalEn).' translations to lang/en.json and lang/id.json!');

        return Command::SUCCESS;
    }
}
