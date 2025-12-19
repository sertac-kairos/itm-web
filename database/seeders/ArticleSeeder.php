<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'author' => 'Dr. Ahmet Yılmaz',
                'sort_order' => 1,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir\'in Antik Limanları: Smyrna\'dan Günümüze',
                        'content' => 'İzmir, tarih boyunca önemli bir liman kenti olarak varlığını sürdürmüştür. Antik çağlarda Smyrna olarak bilinen kent, Ege Denizi\'nin doğu kıyısında stratejik bir konumda yer almaktaydı. Bu yazıda, İzmir\'in antik limanlarının gelişimini ve günümüze kadar olan değişimini inceleyeceğiz.

Smyrna\'nın ilk limanı, M.Ö. 1000 yıllarında kurulmuş ve ticaret yollarının kesişim noktasında bulunuyordu. Helenistik dönemde, liman genişletilmiş ve modernleştirilmiştir. Roma İmparatorluğu döneminde ise, liman daha da büyütülmüş ve uluslararası ticaretin merkezi haline gelmiştir.

Bizans döneminde, liman önemini korumuş ancak savaşlar ve doğal afetler nedeniyle zarar görmüştür. Osmanlı döneminde ise, liman yeniden canlandırılmış ve modern ticaret merkezi haline getirilmiştir.

Günümüzde İzmir Limanı, Türkiye\'nin en büyük limanlarından biri olarak hizmet vermektedir. Antik Smyrna\'nın ticaret geleneği, modern İzmir\'de devam etmektedir.',
                    ],
                    'en' => [
                        'title' => 'Ancient Ports of Izmir: From Smyrna to Present',
                        'content' => 'Izmir has maintained its existence as an important port city throughout history. The city, known as Smyrna in ancient times, was located at a strategic point on the eastern coast of the Aegean Sea. In this article, we will examine the development of Izmir\'s ancient ports and their changes until today.

The first port of Smyrna was established around 1000 BC and was located at the intersection of trade routes. During the Hellenistic period, the port was expanded and modernized. In the Roman Empire period, the port was further enlarged and became the center of international trade.

During the Byzantine period, the port maintained its importance but was damaged due to wars and natural disasters. During the Ottoman period, the port was revitalized and turned into a modern commercial center.

Today, Izmir Port serves as one of Turkey\'s largest ports. The trading tradition of ancient Smyrna continues in modern Izmir.',
                    ],
                ],
            ],
            [
                'author' => 'Prof. Fatma Demir',
                'sort_order' => 2,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'Kemeraltı Çarşısı: Tarihi Ticaret Merkezi',
                        'content' => 'Kemeraltı Çarşısı, İzmir\'in en eski ve en önemli ticaret merkezlerinden biridir. 16. yüzyılda Osmanlı döneminde kurulan çarşı, günümüzde hala canlılığını korumaktadır.

Çarşının tarihi, İzmir\'in ticaret geçmişiyle iç içedir. Osmanlı döneminde, Kemeraltı sadece bir pazar yeri değil, aynı zamanda sosyal ve kültürel bir merkezdi. Burada farklı milletlerden insanlar bir araya gelir, ticaret yapar ve kültür alışverişinde bulunurdu.

Kemeraltı\'nın mimarisi, geleneksel Osmanlı çarşı mimarisinin güzel bir örneğidir. Dar sokaklar, kemerli geçitler ve geleneksel dükkanlar, tarihi atmosferi korumaktadır. Çarşıda hala geleneksel el sanatları, antika eşyalar ve yerel ürünler bulunabilir.

Günümüzde Kemeraltı, hem yerli hem de yabancı turistlerin ilgisini çeken önemli bir destinasyondur. Tarihi dokusu korunurken, modern ticaret anlayışıyla da uyumlu hale getirilmiştir.',
                    ],
                    'en' => [
                        'title' => 'Kemeraltı Bazaar: Historic Trading Center',
                        'content' => 'Kemeraltı Bazaar is one of the oldest and most important trading centers in Izmir. Established during the Ottoman period in the 16th century, the bazaar still maintains its vibrancy today.

The history of the bazaar is intertwined with Izmir\'s trading history. During the Ottoman period, Kemeraltı was not just a marketplace, but also a social and cultural center. Here, people from different nations would come together, trade, and exchange cultures.

The architecture of Kemeraltı is a beautiful example of traditional Ottoman bazaar architecture. Narrow streets, arched passages, and traditional shops preserve the historic atmosphere. Traditional handicrafts, antiques, and local products can still be found in the bazaar.

Today, Kemeraltı is an important destination that attracts both domestic and foreign tourists. While preserving its historic texture, it has been adapted to modern trading understanding.',
                    ],
                ],
            ],
            [
                'author' => 'Arkeolog Mehmet Kaya',
                'sort_order' => 3,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'Agora Antik Kenti: Roma Döneminin İzleri',
                        'content' => 'İzmir Agora Antik Kenti, Roma döneminin en önemli arkeolojik alanlarından biridir. M.S. 2. yüzyılda inşa edilen Agora, antik Smyrna\'nın ticaret ve sosyal yaşamının merkeziydi.

Agora, antik Yunan ve Roma kentlerinde pazar yeri, toplantı yeri ve sosyal etkinliklerin düzenlendiği açık alanlardı. İzmir Agorası, bu işlevleri yerine getiren önemli bir yapıydı. Geniş avlusu, sütunlu galerileri ve çeşitli dükkanları ile kapsamlı bir ticaret merkeziydi.

Kazılar sırasında ortaya çıkarılan eserler, Agora\'nın zengin tarihini göstermektedir. Roma dönemine ait sikkeler, seramik parçaları, heykeller ve yazıtlar, bu dönemin ticaret ve kültür hayatı hakkında önemli bilgiler vermektedir.

Günümüzde Agora, İzmir\'in en önemli turistik yerlerinden biridir. Ziyaretçiler, antik Roma döneminin atmosferini yaşayabilir ve tarihi yapıları inceleyebilir.',
                    ],
                    'en' => [
                        'title' => 'Agora Ancient City: Traces of the Roman Period',
                        'content' => 'Izmir Agora Ancient City is one of the most important archaeological sites from the Roman period. Built in the 2nd century AD, the Agora was the center of trade and social life of ancient Smyrna.

Agoras were open areas in ancient Greek and Roman cities where markets, meetings, and social events were held. Izmir Agora was an important structure that served these functions. With its large courtyard, columned galleries, and various shops, it was a comprehensive trading center.

The artifacts unearthed during excavations reveal the rich history of the Agora. Coins, ceramic pieces, sculptures, and inscriptions from the Roman period provide important information about the trade and cultural life of this period.

Today, the Agora is one of the most important tourist destinations in Izmir. Visitors can experience the atmosphere of the ancient Roman period and examine the historic structures.',
                    ],
                ],
            ],
        ];

        foreach ($articles as $articleData) {
            $translations = $articleData['translations'];
            unset($articleData['translations']);
            
            $article = Article::create($articleData);
            
            foreach ($translations as $locale => $translationData) {
                $article->translateOrNew($locale)->fill($translationData);
            }
            
            $article->save();
        }
    }
}
