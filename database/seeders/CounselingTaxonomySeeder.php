<?php

namespace Database\Seeders;

use App\Models\ChatSession;
use App\Models\ExpertRule;
use App\Models\KnowledgeBase;
use App\Models\ProblemCategory;
use App\Models\ProblemSubcategory;
use App\Models\ProblemTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CounselingTaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/counseling_taxonomy.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("Taxonomy JSON file not found at: {$jsonPath}");

            return;
        }

        $taxonomy = json_decode(File::get($jsonPath), true);

        if (! is_array($taxonomy)) {
            $this->command->error("Invalid JSON format in: {$jsonPath}");

            return;
        }

        if (ProblemCategory::count() >= 70 && ProblemTopic::count() >= 2422) {
            $this->command->info('Taxonomy already seeded with 70 categories and 2,422 topics.');

            return;
        }

        $this->command->info('Seeding 70 Categories, 346 Subcategories, and 2,422 Topics...');

        DB::transaction(function () use ($taxonomy, &$totalCategories, &$totalSubcategories, &$totalTopics) {
            $categoryOrder = 1;
            foreach ($taxonomy as $catData) {
                $category = ProblemCategory::updateOrCreate(
                    ['slug' => $catData['slug']],
                    [
                        'name' => $catData['name'],
                        'description' => $catData['description'] ?? '',
                        'icon' => $catData['icon'] ?? 'folder',
                        'sort_order' => $categoryOrder++,
                        'is_active' => true,
                    ]
                );
                $totalCategories++;

                $subcatOrder = 1;
                foreach ($catData['subcategories'] as $subcatData) {
                    $subcategory = ProblemSubcategory::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'slug' => $subcatData['slug'],
                        ],
                        [
                            'name' => $subcatData['name'],
                            'description' => $subcatData['description'] ?? '',
                            'sort_order' => $subcatOrder++,
                            'is_active' => true,
                        ]
                    );
                    $totalSubcategories++;

                    $topicOrder = 1;
                    foreach ($subcatData['topics'] as $topicData) {
                        ProblemTopic::updateOrCreate(
                            [
                                'subcategory_id' => $subcategory->id,
                                'slug' => $topicData['slug'],
                            ],
                            [
                                'name' => $topicData['name'],
                                'description' => $topicData['description'] ?? '',
                                'prompt_context' => $topicData['prompt_context'] ?? '',
                                'suggested_chips' => $topicData['suggested_chips'] ?? [],
                                'sort_order' => $topicOrder++,
                                'is_active' => true,
                            ]
                        );
                        $totalTopics++;
                    }
                }
            }
        });

        $this->command->info("Successfully seeded {$totalCategories} Categories, {$totalSubcategories} Subcategories, and {$totalTopics} Topics.");

        // Migrate and remap existing chat sessions to the new taxonomy
        $this->remapExistingSessions();

        // Update KnowledgeBase and ExpertRule mappings
        $this->updateKnowledgeAndRules();
    }

    /**
     * Ensure existing chat sessions are cleanly mapped to the new taxonomy models.
     */
    protected function remapExistingSessions(): void
    {
        $topicMap = [
            // Map old topic slugs to new topic slugs
            'academic-stress' => 'tekanan-pekan-ujian-utsuas-ketakutan-nilai-anjlok',
            'burnout-kelelahan-mental' => 'kelelahan-emosional-total-emotional-exhaustion-akibat-beban-rutinitas',
            'overthinking-masa-depan' => 'kebingungan-arah-karier-menentukan-jalan-hidup-pasca-kampus',
            'social-anxiety' => 'social-anxiety-ketakutan-berbicara-di-depan-publik-kelas',
            'fomo-peer-pressure' => 'fomo-terpaksa-mengikuti-gaya-hidup-mewah-circle',
            'konflik-boundaries' => 'konflik-argumen-salah-paham-ketegangan-circle-sahabat',
            'ekspektasi-orang-tua' => 'beban-standar-tinggi-tuntutan-prestasi-dari-orang-tua',
            'gangguan-jam-tidur' => 'insomnia-kronis-jam-tidur-terbalik-menjelang-subuh',
            'prokrastinasi-sulit-fokus' => 'prokrastinasi-kronis-siklus-panik-menjelang-batas-akhir',
        ];

        $sessions = ChatSession::all();
        $remappedCount = 0;

        foreach ($sessions as $session) {
            $currentTopic = ProblemTopic::find($session->topic_id);
            $newTopic = null;

            if ($currentTopic) {
                if (isset($topicMap[$currentTopic->slug])) {
                    $newSlug = $topicMap[$currentTopic->slug];
                    $newTopic = ProblemTopic::where('slug', $newSlug)->with('subcategory.category')->first();
                } else {
                    $newTopic = $currentTopic->loadMissing('subcategory.category');
                }
            } else {
                $newTopic = ProblemTopic::where('is_active', true)->with('subcategory.category')->first();
            }

            if ($newTopic && $newTopic->subcategory) {
                $session->update([
                    'topic_id' => $newTopic->id,
                    'subcategory_id' => $newTopic->subcategory_id,
                    'category_id' => $newTopic->subcategory->category_id,
                ]);
                $remappedCount++;
            }
        }

        $this->command->info("Remapped {$remappedCount} existing chat sessions to the new taxonomy.");

        // Clean up legacy categories, subcategories and topics (ids 1..3 for cats, 1..5 for subcats, 1..9 for topics if old)
        $legacyCategorySlugs = ['mental-emotional', 'relasi-sosial', 'diri-kebiasaan'];
        foreach ($legacyCategorySlugs as $legacySlug) {
            $cat = ProblemCategory::where('slug', $legacySlug)->first();
            if ($cat) {
                $cat->delete(); // Cascades subcategories and topics
            }
        }
    }

    /**
     * Update knowledge base and expert rule associations.
     */
    protected function updateKnowledgeAndRules(): void
    {
        // Re-link KnowledgeBase to active categories
        $academicCat = ProblemCategory::where('slug', 'akademik-perjalanan-karier')->first();
        $lifestyleCat = ProblemCategory::where('slug', 'gaya-hidup-kebiasaan-regulasi-diri')->first();

        if ($academicCat) {
            KnowledgeBase::where('title', 'like', '%Akademik%')->update(['category_id' => $academicCat->id]);
        }
        if ($lifestyleCat) {
            KnowledgeBase::where('title', 'like', '%Sleep%')->update(['category_id' => $lifestyleCat->id]);
        }

        // Re-link ExpertRule #1 to the new academic exam stress topic
        $examTopic = ProblemTopic::where('slug', 'tekanan-pekan-ujian-utsuas-ketakutan-nilai-anjlok')->first();
        if ($examTopic) {
            ExpertRule::where('name', 'like', '%Akademik%')->update(['topic_id' => $examTopic->id]);
        }
    }
}
