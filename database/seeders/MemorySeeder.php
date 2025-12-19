<?php

namespace Database\Seeders;

use App\Models\Memory;
use Illuminate\Database\Seeder;

class MemorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memories = [
            [
                'image' => null,
                'link' => 'https://www.izmir.bel.tr/tr',
                'sort_order' => 1,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir Belediyesi: Şehrin Kalbi',
                        'content' => 'İzmir Büyükşehir Belediyesi, şehrin yönetim merkezi olarak hizmet vermektedir. Modern belediye binası, şehrin merkezinde yer almakta ve vatandaşlara çeşitli hizmetler sunmaktadır.

Belediye binası, modern mimari tasarımı ile dikkat çekmektedir. Çevre dostu özellikleri ve sürdürülebilir yapı tasarımı ile örnek bir kamu binasıdır. İçerisinde belediye başkanlığı, meclis salonu ve çeşitli birimler bulunmaktadır.

Bu bina, İzmir\'in yönetim merkezi olarak şehrin gelişimine katkı sağlamaktadır. Vatandaşlar burada çeşitli işlemlerini gerçekleştirebilir, şikayet ve önerilerini iletebilirler.',
                    ],
                    'en' => [
                        'title' => 'Izmir Municipality: Heart of the City',
                        'content' => 'Izmir Metropolitan Municipality serves as the administrative center of the city. The modern municipality building is located in the center of the city and provides various services to citizens.

The municipality building attracts attention with its modern architectural design. It is an exemplary public building with its environmentally friendly features and sustainable building design. It contains the mayor\'s office, council hall, and various units.

This building contributes to the development of the city as the administrative center of Izmir. Citizens can carry out various transactions here and submit complaints and suggestions.',
                    ],
                ],
            ],
            [
                'image' => null,
                'link' => 'https://www.kulturportali.gov.tr/izmir',
                'sort_order' => 2,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'Kültür Portalı: İzmir\'in Kültürel Mirası',
                        'content' => 'Kültür Portalı, İzmir\'in zengin kültürel mirasını dijital ortamda sunan kapsamlı bir platformdur. Bu portal, şehrin tarihi, kültürü ve sanatı hakkında detaylı bilgiler içermektedir.

Portalda İzmir\'in antik çağlardan günümüze kadar olan tarihi, müzeleri, tarihi mekanları, kültür sanat etkinlikleri ve yerel gelenekleri yer almaktadır. Kullanıcılar bu platform üzerinden şehrin kültürel zenginliklerini keşfedebilir.

Ayrıca portal, İzmir\'de düzenlenen festivaller, konserler, sergiler ve diğer kültür sanat etkinlikleri hakkında güncel bilgiler sunmaktadır. Bu sayede hem yerli hem de yabancı ziyaretçiler şehrin kültürel hayatına katılabilir.',
                    ],
                    'en' => [
                        'title' => 'Culture Portal: Cultural Heritage of Izmir',
                        'content' => 'Culture Portal is a comprehensive platform that presents Izmir\'s rich cultural heritage in digital environment. This portal contains detailed information about the city\'s history, culture, and art.

The portal includes Izmir\'s history from ancient times to the present, museums, historical sites, cultural and artistic events, and local traditions. Users can discover the city\'s cultural richness through this platform.

Additionally, the portal provides up-to-date information about festivals, concerts, exhibitions, and other cultural and artistic events held in Izmir. This way, both domestic and foreign visitors can participate in the city\'s cultural life.',
                    ],
                ],
            ],
            [
                'image' => null,
                'link' => null,
                'sort_order' => 3,
                'is_active' => true,
                'translations' => [
                    'tr' => [
                        'title' => 'İzmir\'in Unutulmaz Anıları',
                        'content' => 'İzmir, tarih boyunca birçok önemli olaya tanıklık etmiştir. Bu şehir, her dönemde farklı kültürlerin buluşma noktası olmuş ve zengin bir tarihi miras bırakmıştır.

Şehrin sokaklarında yürürken, her köşede tarihi bir hikaye bulabilirsiniz. Antik Smyrna\'dan Osmanlı dönemine, Cumhuriyet\'in ilk yıllarından günümüze kadar İzmir, sürekli gelişen ve değişen bir şehir olmuştur.

Bu şehirde yaşayan insanlar, her gün yeni anılar biriktirmekte ve şehrin tarihine katkı sağlamaktadır. İzmir\'in hafızası, sadece geçmişte değil, bugün de canlı ve dinamik bir şekilde yaşamaya devam etmektedir.',
                    ],
                    'en' => [
                        'title' => 'Unforgettable Memories of Izmir',
                        'content' => 'Izmir has witnessed many important events throughout history. This city has been a meeting point of different cultures in every period and has left a rich historical heritage.

While walking in the streets of the city, you can find a historical story at every corner. From ancient Smyrna to the Ottoman period, from the early years of the Republic to the present, Izmir has been a city that constantly develops and changes.

People living in this city collect new memories every day and contribute to the city\'s history. The memory of Izmir continues to live not only in the past but also today in a lively and dynamic way.',
                    ],
                ],
            ],
        ];

        foreach ($memories as $memoryData) {
            $translations = $memoryData['translations'];
            unset($memoryData['translations']);
            
            $memory = Memory::create($memoryData);
            
            foreach ($translations as $locale => $translationData) {
                $memory->translateOrNew($locale)->fill($translationData);
            }
            
            $memory->save();
        }
    }
}
