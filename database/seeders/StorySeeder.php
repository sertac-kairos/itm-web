<?php

namespace Database\Seeders;

use App\Models\Story;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stories = [
            [
                'thumbnail' => null,
                'sort_order' => 1,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir\'in Antik Tarihi',
                        'description' => 'İzmir\'in M.Ö. 3000 yıllarına dayanan zengin tarihi ve kültürel mirası hakkında detaylı bilgiler.',
                        'image' => null,
                    ],
                    'en' => [
                        'title' => 'Ancient History of Izmir',
                        'description' => 'Detailed information about Izmir\'s rich history and cultural heritage dating back to 3000 BC.',
                        'image' => null,
                    ],
                ],
            ],
            [
                'thumbnail' => null,
                'sort_order' => 2,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'Smyrna\'dan İzmir\'e',
                        'description' => 'Antik Smyrna kentinden modern İzmir\'e uzanan tarihi yolculuk ve şehrin gelişim süreci.',
                        'image' => null,
                    ],
                    'en' => [
                        'title' => 'From Smyrna to Izmir',
                        'description' => 'The historical journey from ancient Smyrna to modern Izmir and the city\'s development process.',
                        'image' => null,
                    ],
                ],
            ],
            [
                'thumbnail' => null,
                'sort_order' => 3,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'Kültürel Mirasımız',
                        'description' => 'İzmir\'in UNESCO Dünya Mirası Listesi\'ndeki yerleri ve korunması gereken kültürel değerler.',
                        'image' => null,
                    ],
                    'en' => [
                        'title' => 'Our Cultural Heritage',
                        'description' => 'Izmir\'s UNESCO World Heritage sites and cultural values that need to be preserved.',
                        'image' => null,
                    ],
                ],
            ],
        ];

        foreach ($stories as $storyData) {
            $translations = $storyData['translations'];
            unset($storyData['translations']);
            
            $story = Story::create($storyData);
            
            foreach ($translations as $locale => $translationData) {
                $story->translateOrNew($locale)->fill($translationData);
            }
            
            $story->save();
        }
    }
}
