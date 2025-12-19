<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsImage;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsItems = [
            [
                'slug' => 'izmir-antik-kentlerinde-yeni-kesifler',
                'news_date' => '2024-01-15',
                'sort_order' => 1,
                'is_active' => true,
                'featured_image' => 'news/featured-1.jpg',
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir Antik Kentlerinde Yeni Keşifler',
                        'content' => 'İzmir\'in antik kentlerinde yapılan arkeolojik kazılarda önemli bulgular ortaya çıktı. Efes, Pergamon ve Smyrna antik kentlerinde yapılan çalışmalar sonucunda yeni tapınak kalıntıları ve mozaikler keşfedildi. Bu keşifler, bölgenin tarihi önemini bir kez daha gözler önüne seriyor.',
                    ],
                    'en' => [
                        'title' => 'New Discoveries in Ancient Cities of Izmir',
                        'content' => 'Important findings have been discovered in archaeological excavations in the ancient cities of Izmir. New temple remains and mosaics were discovered as a result of studies carried out in the ancient cities of Ephesus, Pergamon and Smyrna. These discoveries once again reveal the historical importance of the region.',
                    ],
                ],
            ],
            [
                'slug' => 'efes-muzesi-yeni-sergi-aciliyor',
                'news_date' => '2024-01-20',
                'sort_order' => 2,
                'is_active' => true,
                'featured_image' => 'news/featured-2.jpg',
                'translations' => [
                    'tr' => [
                        'title' => 'Efes Müzesi Yeni Sergi Açıyor',
                        'content' => 'Efes Müzesi, "Antik Dünyanın İhtişamı" adlı yeni sergisini ziyaretçilerle buluşturuyor. Sergide, Efes antik kentinden çıkarılan en değerli eserler sergileniyor. Sergi, 3 ay boyunca ziyaretçilere açık olacak.',
                    ],
                    'en' => [
                        'title' => 'Ephesus Museum Opens New Exhibition',
                        'content' => 'The Ephesus Museum is bringing together its new exhibition titled "The Splendor of the Ancient World" with visitors. The exhibition features the most valuable artifacts unearthed from the ancient city of Ephesus. The exhibition will be open to visitors for 3 months.',
                    ],
                ],
            ],
            [
                'slug' => 'pergamon-kazilarinda-roma-donemi-bulgulari',
                'news_date' => '2024-01-25',
                'sort_order' => 3,
                'is_active' => true,
                'featured_image' => 'news/featured-3.jpg',
                'translations' => [
                    'tr' => [
                        'title' => 'Pergamon Kazılarında Roma Dönemi Bulguları',
                        'content' => 'Pergamon antik kentinde yapılan kazılarda Roma dönemine ait önemli bulgular ortaya çıktı. Kazı ekibi, Roma dönemine ait hamam kalıntıları ve su sistemleri keşfetti. Bu bulgular, Pergamon\'un Roma dönemindeki önemini gösteriyor.',
                    ],
                    'en' => [
                        'title' => 'Roman Period Findings in Pergamon Excavations',
                        'content' => 'Important findings from the Roman period were discovered in excavations in the ancient city of Pergamon. The excavation team discovered Roman period bath remains and water systems. These findings show the importance of Pergamon in the Roman period.',
                    ],
                ],
            ],
            [
                'slug' => 'izmir-tarihi-kent-merkezi-restorasyonu',
                'news_date' => '2024-02-01',
                'sort_order' => 4,
                'is_active' => true,
                'featured_image' => 'news/featured-4.jpg',
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir Tarihi Kent Merkezi Restorasyonu',
                        'content' => 'İzmir\'in tarihi kent merkezinde büyük bir restorasyon projesi başlatıldı. Proje kapsamında, Kemeraltı Çarşısı ve çevresindeki tarihi yapılar restore edilecek. Restorasyon çalışmaları 2 yıl sürecek.',
                    ],
                    'en' => [
                        'title' => 'Izmir Historical City Center Restoration',
                        'content' => 'A major restoration project has been launched in the historical city center of Izmir. Within the scope of the project, Kemeraltı Bazaar and surrounding historical buildings will be restored. Restoration works will last 2 years.',
                    ],
                ],
            ],
            [
                'slug' => 'antik-tiyatro-konserleri-basliyor',
                'news_date' => '2024-02-05',
                'sort_order' => 5,
                'is_active' => true,
                'featured_image' => 'news/featured-5.jpg',
                'translations' => [
                    'tr' => [
                        'title' => 'Antik Tiyatro Konserleri Başlıyor',
                        'content' => 'İzmir\'deki antik tiyatrolarda yaz konserleri başlıyor. Efes ve Pergamon antik tiyatrolarında düzenlenecek konserlerde, dünyaca ünlü sanatçılar sahne alacak. Konser programı ve bilet satışları hakkında detaylı bilgi için web sitemizi ziyaret edebilirsiniz.',
                    ],
                    'en' => [
                        'title' => 'Ancient Theater Concerts Begin',
                        'content' => 'Summer concerts are starting in the ancient theaters in Izmir. World-famous artists will take the stage in concerts to be held in the ancient theaters of Ephesus and Pergamon. You can visit our website for detailed information about the concert program and ticket sales.',
                    ],
                ],
            ],
        ];

        foreach ($newsItems as $newsData) {
            $translations = $newsData['translations'];
            unset($newsData['translations']);
            
            $news = News::create($newsData);
            
            foreach ($translations as $locale => $translationData) {
                $news->translateOrNew($locale)->fill($translationData);
            }
            
            $news->save();

            // Her haber için örnek görseller ekle
            NewsImage::create([
                'news_id' => $news->id,
                'image_path' => $newsData['featured_image'],
                'alt_text' => $news->title,
                'sort_order' => 1,
            ]);
        }
    }
}
