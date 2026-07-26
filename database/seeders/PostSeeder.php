<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('DELETE FROM social_shares');
        \DB::statement('DELETE FROM posts');

        $posts = [
            [
                'title' => 'Marvel Filmleri İzleme Sırası 2026: Kronolojik ve Çıkış Sırasıyla',
                'category' => 'Rehber',
                'read_time' => 10,
                'image_url' => 'https://image.tmdb.org/t/p/w780/rjQ0GbYsLX6WDWefPrxGMCqPqx6.jpg',
                'excerpt' => "Marvel Sinematik Evreni'ni baştan sona doğru sırayla izlemek isteyenler için kapsamlı rehber.",
                'body' => "Marvel Sinematik Evreni (MCU), 2008'de **[Iron Man](/film/1726-iron-man)** ile başladı ve bugüne kadar 30'dan fazla film, onlarca dizi ile dev bir evrene dönüştü.

## Çıkış Sırasıyla İzleme

### Faz 1: Başlangıç (2008-2012)

1. **[Iron Man](/film/1726-iron-man)** (2008)
2. **[The Incredible Hulk](/film/1724-the-incredible-hulk)** (2008)
3. **[Iron Man 2](/film/10138-iron-man-2)** (2010)
4. **[Thor](/film/10195-thor)** (2011)
5. **[Captain America: The First Avenger](/film/1771-captain-america-the-first-avenger)** (2011)
6. **[The Avengers](/film/24428-the-avengers)** (2012)

### Faz 2: Genişleme (2013-2015)

7. **[Iron Man 3](/film/68721-iron-man-3)** (2013)
8. **[Thor: The Dark World](/film/76338-thor-the-dark-world)** (2013)
9. **[Captain America: The Winter Soldier](/film/100402-captain-america-the-winter-soldier)** (2014)
10. **[Guardians of the Galaxy](/film/118340-guardians-of-the-galaxy)** (2014)
11. **[Avengers: Age of Ultron](/film/99861-avengers-age-of-ultron)** (2015)
12. **[Ant-Man](/film/102899-ant-man)** (2015)

### Faz 3: Sonsuzluk Destanı (2016-2019)

13. **[Captain America: Civil War](/film/271110-captain-america-civil-war)** (2016)
14. **[Doctor Strange](/film/284052-doctor-strange)** (2016)
15. **[Guardians of the Galaxy Vol. 2](/film/283995-guardians-of-the-galaxy-vol-2)** (2017)
16. **[Spider-Man: Homecoming](/film/315635-spider-man-homecoming)** (2017)
17. **[Thor: Ragnarok](/film/284053-thor-ragnarok)** (2017)
18. **[Black Panther](/film/284054-black-panther)** (2018)
19. **[Avengers: Infinity War](/film/299536-avengers-infinity-war)** (2018)
20. **[Avengers: Endgame](/film/299534-avengers-endgame)** (2019)

## Püf Noktaları

- End-credits sahnelerini asla atlamayın!
- İlk kez izliyorsanız **kesinlikle çıkış sırasıyla** başlayın
- Daha fazla Marvel filmi için **[aksiyon](/mod/aksiyon)** ve **[bilim kurgu](/mod/bilimkurgu)** modlarımıza göz atın
- **[DC filmleri izleme sırası](/blog/dc-filmleri-izleme-sirasi-2026-yeni-dc-evreniyle-bastan-sona)** yazımıza da bakın

Bu rehberi favorilere ekleyin, Marvel yolculuğunuzda yanınızda olsun!",
            ],
            [
                'title' => 'IMDb Puanı 8.0 Üzeri En İyi 50 Film (2026 Güncel Liste)',
                'category' => 'Liste',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/kJAJNNBYlbqAcpTDxBNnaILSMTy.jpg',
                'excerpt' => 'IMDb puanı 8 ve üzeri olan, mutlaka izlenmesi gereken en iyi 50 filmi sizin için listeledik.',
                'body' => "Sinema tarihinin en yüksek puanlı filmlerini merak ediyorsanız doğru yerdesiniz.

## IMDb'nin Zirvesi (8.5+)

**[The Shawshank Redemption](/film/278-esaretin-bedeli)** — IMDb'nin değişmez 1 numarası.

**[The Godfather](/film/238-baba)** — **[Francis Ford Coppola](/kisi/1776-francis-ford-coppola)**'nın mafya destanı. **[Marlon Brando](/kisi/3084-marlon-brando)** ve **[Al Pacino](/kisi/1158-al-pacino)** efsane.

**[The Dark Knight](/film/155-kara-sovalye)** — **[Christopher Nolan](/kisi/525-christopher-nolan)**'ın başyapıtı. **[Heath Ledger](/kisi/1810-heath-ledger)**'ın Joker performansı.

**[12 Angry Men](/film/389-12-ofkeli-adam)** — Tek bir odada geçen en gerilimli mahkeme draması.

**[Schindler's List](/film/424-schindlerin-listesi)** — **[Steven Spielberg](/kisi/488-steven-spielberg)**'in Holokost başyapıtı.

## IMDb 8.0-8.5 Arası Modern Klasikler

**[Pulp Fiction](/film/680-ucuz-roman)** — **[Quentin Tarantino](/kisi/138-quentin-tarantino)**'nun başyapıtı.

**[Fight Club](/film/550-dovus-kulubu)** — **[David Fincher](/kisi/7467-david-fincher)** yönetiyor. **[Brad Pitt](/kisi/287-brad-pitt)** başrolde.

**[Inception](/film/27205-baslangic)** — Rüya içinde rüya. **[Leonardo DiCaprio](/kisi/6193-leonardo-dicaprio)** başrolde.

**[The Matrix](/film/603-the-matrix)** — **[Keanu Reeves](/kisi/6384-keanu-reeves)** başrolde.

**[Interstellar](/film/157336-yildizlararasi)** — Nolan'dan bir uzay destanı.

**[Parasite](/film/496243-parazit)** — Oscar kazanan ilk yabancı dilde film.

**[The Prestige](/film/1124-prestij)** — **[Christian Bale](/kisi/3894-christian-bale)** ve **[Hugh Jackman](/kisi/6968-hugh-jackman)** başrolde.

**[Goodfellas](/film/769-siki-dostlar)** — **[Martin Scorsese](/kisi/1032-martin-scorsese)**'nin mafya başyapıtı.

Bu filmlerin çoğunu [Netflix](/platform/8/netflix) ve [Amazon Prime](/platform/119/amazon-prime-video)'da bulabilirsiniz.",
            ],
            [
                'title' => 'Hafta Sonu İçin Netflix Film Önerileri: Haziran 2026',
                'category' => 'Liste',
                'read_time' => 6,
                'image_url' => 'https://image.tmdb.org/t/p/w780/vmlJvz6qVzYgei2V74GvnmcuQfW.jpg',
                'excerpt' => "Bu hafta sonu Netflix'te ne izlesem diyenler için en güncel ve en iyi film önerileri.",
                'body' => "Hafta sonu geldi, [Netflix](/platform/8/netflix) karşınızda ama ne izleyeceğinize karar veremiyorsunuz. Merak etmeyin, sizin için en iyileri seçtik.

## Gerilim Severlere

**[Prisoners](/film/146233-tutsak)** — Kaçırılan iki kız ve çaresiz bir baba. **[Denis Villeneuve](/kisi/137427-denis-villeneuve)** yönetiyor, **[Hugh Jackman](/kisi/6968-hugh-jackman)** başrolde.

**[Gone Girl](/film/210577-kayip-kiz)** — **[David Fincher](/kisi/7467-david-fincher)**'dan evliliğe karanlık bir bakış.

## Bilim Kurgu ve Fantastik

**[Dune: Part Two](/film/693134-dune-part-two)** — 2024'ün en çok konuşulan filmi. Villeneuve'den görsel şölen.

**[The Adam Project](/film/696806-the-adam-project)** — Ryan Reynolds'lı eğlenceli zaman yolculuğu.

## Ailecek İzlemelik

**[Klaus](/film/508965-klaus)** — Modern bir Noel klasiği.

## Romantik / Dram

**[Marriage Story](/film/492188-marriage-story)** — Scarlett Johansson ve Adam Driver'dan oyunculuk dersi.

Daha fazlası için [Netflix sayfamızı](/platform/8/netflix) ziyaret edin!",
            ],
            [
                'title' => "2026'nın En Çok Beklenen 20 Filmi",
                'category' => 'Liste',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/eCg1FNCwiUIkzyuiTtMMfjmx4Za.jpg',
                'excerpt' => '2026 yılında vizyona girecek ve şimdiden heyecan yaratan en büyük yapımları sıraladık.',
                'body' => "2026 yılı, dev bütçeli yapımlar ve merakla beklenen devam filmleriyle sinema severler için heyecan verici bir yıl olacak.

## Süper Kahraman Filmleri

**Avengers: The Kang Dynasty** — Marvel hayranlarının yıllardır beklediği büyük buluşma.

**The Batman: Part II** — **[Robert Pattinson](/kisi/11288-robert-pattinson)** pelerinle geri dönüyor.

## Bilim Kurgu ve Fantastik

**Dune: Messiah** — **[Denis Villeneuve](/kisi/137427-denis-villeneuve)**'den yeni başyapıt.

**Avatar 4** — **[James Cameron](/kisi/2710-james-cameron)** Pandora'ya geri dönüyor.

## Korku ve Gerilim

**A Quiet Place: Part III** — Sessizlik destanı devam ediyor.

## Yerli Yapımlar

**[Cem Yılmaz](/kisi/17304-cem-yilmaz)**'ın yeni filmi ve genç yönetmenlerin projeleri dikkat çekiyor.

Takviminizi **[yakında](/yakinda)** sayfamızdan takip edin!",
            ],
            [
                'title' => 'En İyi Gerilim Filmleri: Nefesinizi Tutarak İzleyeceğiniz 25 Film',
                'category' => 'Liste',
                'read_time' => 9,
                'image_url' => 'https://image.tmdb.org/t/p/w780/twJgioJt2pe3yUmmth5obRxRgnM.jpg',
                'excerpt' => 'Gerilim türünün zirvesindeki 25 filmi sizin için listeledik.',
                'body' => "Gerilim türü, sinemanın en güçlü silahlarından biridir.

## Psikolojik Gerilim

**[Se7en](/film/807-yedi)** — **[David Fincher](/kisi/7467-david-fincher)**'in başyapıtı. Yedi ölümcül günah. **[Brad Pitt](/kisi/287-brad-pitt)** ve **[Morgan Freeman](/kisi/192-morgan-freeman)** başrolde.

**[The Silence of the Lambs](/film/274-kuzularin-sessizligi)** — **[Anthony Hopkins](/kisi/4173-anthony-hopkins)**'in Hannibal Lecter'ı unutulmaz.

**[Gone Girl](/film/210577-kayip-kiz)** — Fincher'dan evliliğe karanlık bir bakış.

**[Shutter Island](/film/11324-zindan-adasi)** — **[Martin Scorsese](/kisi/1032-martin-scorsese)** ve **[Leonardo DiCaprio](/kisi/6193-leonardo-dicaprio)** işbirliği.

**[No Country for Old Men](/film/69778-ihtiyarlara-yer-yok)** — **[Javier Bardem](/kisi/3810-javier-bardem)**'in unutulmaz performansı.

**[Prisoners](/film/146233-tutsak)** — **[Denis Villeneuve](/kisi/137427-denis-villeneuve)**'den insanın karanlık yüzü.

## Aksiyon Gerilim

**[The Departed](/film/1422-kostebek)** — Scorsese'nin Oscar kazanan mafya gerilimi.

**[Heat](/film/949-buyuk-hesaplasma)** — **[Al Pacino](/kisi/1158-al-pacino)** ve **[Robert De Niro](/kisi/380-robert-de-niro)**'yu aynı sahnede buluşturan efsane.

Daha fazla gerilim için **[gerilim modumuza](/mod/gerilim)** göz atın!",
            ],
            [
                'title' => 'Christopher Nolan Filmleri: En Kötüden En İyiye Sıralama',
                'category' => 'Liste',
                'read_time' => 10,
                'image_url' => 'https://image.tmdb.org/t/p/w780/3YMd9Ogae4rDKLWuAZFuse9xhc5.jpg',
                'excerpt' => "Modern sinemanın en büyük yönetmenlerinden Christopher Nolan'ın tüm filmlerini en kötüden en iyiye sıraladık.",
                'body' => "**[Christopher Nolan](/kisi/525-christopher-nolan)**, 21. yüzyıl sinemasının tartışmasız en önemli yönetmenlerinden biri. Zaman, hafıza ve gerçeklik kavramlarıyla dans eden 12 filmini sıraladık.

## En Kötüden En İyiye

**12. Tenet (2020)** — Nolan'ın en karmaşık filmi.

**9. [The Dark Knight Rises](/film/49026-kara-sovalye-yukseliyor)** (2012) — **[Tom Hardy](/kisi/2524-tom-hardy)**'nin Bane performansı.

**8. [Batman Begins](/film/272-batman-begins)** (2005) — **[Christian Bale](/kisi/3894-christian-bale)** ilk kez pelerin giyiyor.

**7. [Dunkirk](/film/374720-dunkirk)** (2017) — Üç zaman çizelgesinde bir savaş filmi.

**6. [Oppenheimer](/film/872585-oppenheimer)** (2023) — **[Cillian Murphy](/kisi/2037-cillian-murphy)**'nin Oscar'lı performansı.

**5. [The Prestige](/film/1124-prestij)** (2006) — İki sihirbazın amansız rekabeti.

**4. [Interstellar](/film/157336-yildizlararasi)** (2014) — **[Matthew McConaughey](/kisi/10297-matthew-mcconaughey)** başrolde.

**3. [Memento](/film/77-akil-defteri)** (2000) — Tersten ilerleyen kurgusuyla başyapıt.

**2. [Inception](/film/27205-baslangic)** (2010) — **[Leonardo DiCaprio](/kisi/6193-leonardo-dicaprio)** liderliğindeki muhteşem kadro.

**1. [The Dark Knight](/film/155-kara-sovalye)** (2008) — Tüm zamanların en iyi filmlerinden. **[Heath Ledger](/kisi/1810-heath-ledger)**'ın Joker'i efsane.

Sıralamaya katılıyor musunuz? **[Nolan profiline](/kisi/525-christopher-nolan)** giderek tüm filmlerini keşfedin!",
            ],
            [
                'title' => 'Hangi Film Hangi Platformda? Türkiye Dijital Platform Rehberi',
                'category' => 'Rehber',
                'read_time' => 7,
                'image_url' => 'https://image.tmdb.org/t/p/w780/hoA55UeOCYnO2EkX93EFa2e6g1i.jpg',
                'excerpt' => 'Netflix, Amazon Prime, Disney+, BluTV... Hangi platformda ne var?',
                'body' => "Artık film izlemek için sinemaya gitmek zorunda değiliz, ama bu sefer de başka bir sorun var: Hangi platformda ne var?

## [Netflix](/platform/8/netflix)

Orijinal içerikleriyle sektöre yön veriyor. En geniş katalog.

**Netflix'te izleyin:** [Marriage Story](/film/492188-marriage-story), [Roma](/film/426426-roma), [The Irishman](/film/398978-the-irishman)

## [Amazon Prime Video](/platform/119/amazon-prime-video)

Fiyat/performans oranı en yüksek platform.

## [Disney+](/platform/337/disney-plus)

Marvel, Star Wars, Pixar ve National Geographic'in resmi evi.

**Disney+'ta izleyin:** [Soul](/film/508442-soul), The Mandalorian

## BluTV

Türkiye'nin en eski yerli platformu. Yerli yapımlarda çok güçlü.

Filmincele'de film sayfalarındaki **Nereden İzlenir?** bölümünde istediğiniz filmin hangi platformda olduğunu anında görebilirsiniz! Her **[film sayfasında](/kesfet)** sağ sütunda platform bilgisi yer alır.",
            ],
            [
                'title' => 'Yeni Başlayanlar İçin Studio Ghibli Rehberi',
                'category' => 'Rehber',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/nmytAEE5zg17Y1J3ygbjbsa5t6q.jpg',
                'excerpt' => 'Japon animasyon stüdyosu Studio Ghibli dünyasına adım atmak için başlangıç rehberi.',
                'body' => "Studio Ghibli, **[Hayao Miyazaki](/kisi/608-hayao-miyazaki)** tarafından 1985'te kurulan, animasyon tarihinin en sevilen stüdyosudur.

## Başlangıç İçin En İyi 5 Film

**[Spirited Away](/film/129-ruhlarin-kacisi)** (2001) — Oscar kazanan tek anime filmi. Ghibli'ye başlamak için en iyi yer.

**[My Neighbor Totoro](/film/8392-komsum-totoro)** (1988) — İki kız kardeşin orman ruhu Totoro ile dostluğu.

**[Princess Mononoke](/film/128-prenses-mononoke)** (1997) — İnsan ve doğa arasındaki savaş.

**[Howl's Moving Castle](/film/4935-yuruyen-sato)** (2004) — Büyü, aşk ve savaş karşıtı mesajlarla dolu.

**[Kiki's Delivery Service](/film/16859-kucuk-cadi-kiki)** (1989) — Küçük cadının büyüme hikayesi.

## Nereden İzlenir?

Tüm Ghibli filmleri şu anda **[Netflix'te](/platform/8/netflix)** mevcut!

Daha fazla animasyon için **[aile modumuza](/mod/aile)** göz atabilirsiniz.",
            ],
            [
                'title' => 'En İyi Bilim Kurgu Filmleri: 30 Unutulmaz Yapım',
                'category' => 'Liste',
                'read_time' => 10,
                'image_url' => 'https://image.tmdb.org/t/p/w780/uIrFdMWlJFdc1jPBP9bxeaISCDj.jpg',
                'excerpt' => 'Uzaydan yapay zekaya, zaman yolculuğundan distopyalara 30 bilim kurgu filmi.',
                'body' => "Bilim kurgu, sinemanın en yaratıcı türlerinden biri.

## Türün Zirvesi

**[Interstellar](/film/157336-yildizlararasi)** — **[Christopher Nolan](/kisi/525-christopher-nolan)**'dan zaman genleşmesi ve baba-kız ilişkisi.

**[The Matrix](/film/603-the-matrix)** — **[Keanu Reeves](/kisi/6384-keanu-reeves)** başrolde.

**[Inception](/film/27205-baslangic)** — **[Leonardo DiCaprio](/kisi/6193-leonardo-dicaprio)** başrolde.

**[Blade Runner 2049](/film/335984-blade-runner-2049)** — **[Denis Villeneuve](/kisi/137427-denis-villeneuve)**'den görsel şiir. **[Ryan Gosling](/kisi/30614-ryan-gosling)** ve **[Harrison Ford](/kisi/3-harrison-ford)**.

**[Arrival](/film/329865-gelis)** — Uzaylılarla iletişim kuran bir dilbilimci.

**[Children of Men](/film/9693-son-umut)** — Distopik bir gelecek.

**[Her](/film/152601-her)** — **[Joaquin Phoenix](/kisi/73421-joaquin-phoenix)**'ten yapay zeka aşkı.

## Saklı Hazineler

**[Ex Machina](/film/264660-ex-machina)** — Turing testi.

**[Gattaca](/film/782-gattaca)** — Genetik distopya.

**[Moon](/film/17436-moon)** — **[Sam Rockwell](/kisi/6807-sam-rockwell)**'in tek kişilik şovu.

**[The Martian](/film/286565-marsli)** — **[Matt Damon](/kisi/1895-matt-damon)** Mars'ta.

Daha fazlası için **[bilim kurgu modumuza](/mod/bilimkurgu)** göz atın!",
            ],
            [
                'title' => 'En İyi Korku Filmleri: 20 Tüyler Ürpertici Klasik',
                'category' => 'Liste',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/dQgIcW6Th08kMRf2HBoYWoFE6OD.jpg',
                'excerpt' => 'Işıkları kapatın, sesi açın. Sinema tarihinin en korkunç filmleri.',
                'body' => "Korku sineması, bizi en temel duygularımızla yüzleştirir.

## Modern Korkunun Zirvesi

**[Get Out](/film/419430-kapan)** — Jordan Peele'in devrim niteliğindeki filmi.

**[Hereditary](/film/493922-ayin)** — Ari Aster'in ilk filmi. Toni Collette unutulmaz.

**[Midsommar](/film/530385-midsommar)** — Gündüz vakti geçen bir kabus.

**[The Witch](/film/310131-the-witch)** — 1630'lar New England'ı.

## Klasik Korkular

**[The Shining](/film/694-cinnet)** — Stanley Kubrick'in otel kabusu.

**[Psycho](/film/539-sapik)** — Hitchcock'un başyapıtı.

**[Alien](/film/348-yaratik)** — Uzayda kimse çığlığını duyamaz.

**[The Exorcist](/film/9552-seytan)** — 50 yıl sonra hala korkutan ender film.

## Asya Korkusu

**[The Ring](/film/565-halka)** — Video kaset laneti.

**[Train to Busan](/film/396535-busana-yolculuk)** — Zombi türünün zirvesi.

Daha fazlası için **[gerilim modumuza](/mod/gerilim)** göz atın!",
            ],
            [
                'title' => 'Oscar 2026: Kazananlar, Sürprizler ve Unutulmaz Anlar',
                'category' => 'Haber',
                'read_time' => 7,
                'image_url' => 'https://image.tmdb.org/t/p/w780/1HRUTqEVDmJC4L6tp6zd85MI6uH.jpg',
                'excerpt' => "98. Akademi Ödülleri sahiplerini buldu. Kazananlar ve geceden unutulmaz anlar.",
                'body' => "Hollywood'un en büyük gecesi 98. kez gerçekleşti.

## Büyük Ödüller

En İyi Film, En İyi Yönetmen ve oyunculuk kategorilerinde bu yıl beklenmedik sonuçlar çıktı.

Geçen yıl **[Oppenheimer](/film/872585-oppenheimer)**'ın 7 Oscar ile damga vurduğu geceden sonra, bu yıl daha çeşitli bir dağılım gördük.

## Önceki Yılların Oscar Kazananları

**[Parasite](/film/496243-parazit)** — **[Bong Joon-ho](/kisi/21684-bong-joon-ho)** imzası. En İyi Film kazanan ilk yabancı dilde film.

**[Everything Everywhere All at Once](/film/545611-everything-everywhere-all-at-once)** — 7 Oscar kazanan bağımsız başyapıt.

Oscar dönemi boyunca **[blog yazılarımızdan](/blog)** takip edebilirsiniz.",
            ],
            [
                'title' => 'En İyi Romantik Komedi Filmleri: İçinizi Isıtacak 25 Film',
                'category' => 'Liste',
                'read_time' => 7,
                'image_url' => 'https://image.tmdb.org/t/p/w780/suFNqtGOi0BsBI3uJPKkDXCwgVI.jpg',
                'excerpt' => 'Romantik komedi deyip geçmeyin. Türün en iyi 25 filmi.',
                'body' => "Romantik komedi, sizi hem güldürür hem duygulandırır.

## Modern Klasikler

**[When Harry Met Sally](/film/639-when-harry-met-sally)** — Gelmiş geçmiş en iyi romantik komedi.

**[Notting Hill](/film/509-notting-hill)** — Kitapçı ve Hollywood yıldızı.

**[Amélie](/film/194-amelie)** — Paris'te küçük mutluluklar.

**[Love Actually](/film/508-love-actually)** — 10 aşk hikayesi bir arada.

**[Eternal Sunshine of the Spotless Mind](/film/38-sil-bastan)** — **[Jim Carrey](/kisi/206-jim-carrey)** ve **[Kate Winslet](/kisi/204-kate-winslet)** başrolde.

## Son Yılların En İyileri

**[Silver Linings Playbook](/film/82693-umut-isigim)** — **[Jennifer Lawrence](/kisi/72129-jennifer-lawrence)** Oscar kazandı.

**[About Time](/film/122906-about-time)** — Zaman yolculuğu ve aşk.

Daha fazlası için **[romantik modumuza](/mod/romantik)** bakın!",
            ],
            [
                'title' => 'DC Filmleri İzleme Sırası 2026: Yeni DC Evreniyle Baştan Sona',
                'category' => 'Rehber',
                'read_time' => 9,
                'image_url' => 'https://image.tmdb.org/t/p/w780/u4pdyUcuIIdglSQqKZiQ8PkNdSm.jpg',
                'excerpt' => 'DC Comics uyarlamalarını doğru sırayla izlemek için eksiksiz kılavuz.',
                'body' => "DC filmleri için en iyi izleme sırası.

## Kara Şövalye Üçlemesi (Kendi Evreninde)

**[Christopher Nolan](/kisi/525-christopher-nolan)**'ın efsanevi Batman üçlemesi:

1. **[Batman Begins](/film/272-batman-begins)** (2005)
2. **[The Dark Knight](/film/155-kara-sovalye)** (2008) — **[Heath Ledger](/kisi/1810-heath-ledger)**'ın Joker'i
3. **[The Dark Knight Rises](/film/49026-kara-sovalye-yukseliyor)** (2012)

## Öne Çıkan DC Filmleri

**[Man of Steel](/film/49521-man-of-steel)** (2013)

**[Wonder Woman](/film/297762-wonder-woman)** (2017) — **[Gal Gadot](/kisi/90633-gal-gadot)** başrolde

**[Aquaman](/film/297802-aquaman)** (2018) — **[Jason Momoa](/kisi/117642-jason-momoa)** başrolde

**[Joker](/film/475557-joker)** (2019) — **[Joaquin Phoenix](/kisi/73421-joaquin-phoenix)** Oscar kazandı

**[The Batman](/film/414906-the-batman)** (2022) — **[Robert Pattinson](/kisi/11288-robert-pattinson)** başrolde

Daha fazla aksiyon için **[aksiyon modumuza](/mod/aksiyon)** bakın!",
            ],
            [
                'title' => 'Sinema Tarihinin En İyi Açılış Sahneleri',
                'category' => 'Liste',
                'read_time' => 7,
                'image_url' => 'https://image.tmdb.org/t/p/w780/iGpMm603GUKH2SiXB2S5m4sZ17t.jpg',
                'excerpt' => 'İlk 5 dakikadan sizi yakalayan 10 unutulmaz açılış sahnesi.',
                'body' => "Bir filmin ilk 5 dakikası her şeydir.

**[The Dark Knight](/film/155-kara-sovalye)** — Joker'in banka soygunu. **[Heath Ledger](/kisi/1810-heath-ledger)**'ı ilk gördüğümüz an.

**[Saving Private Ryan](/film/857-er-ryani-kurtarmak)** — Omaha Sahili. Sinema tarihinin en gerçekçi savaş sahnesi.

**[The Godfather](/film/238-baba)** — Bonasera'nın monoloğu. **[Marlon Brando](/kisi/3084-marlon-brando)**'nun ikonik silüeti.

**[Inglourious Basterds](/film/16869-soysuzlar-cetesi)** — Christoph Waltz'ın Hans Landa olarak ilk göründüğü sahne.

**[Up](/film/14160-yukari-bak)** — 4 dakikada bir ömür.

**[Pulp Fiction](/film/680-ucuz-roman)** — Dondurulmuş kare ve Misirlou.

**[Jaws](/film/578-denizin-disleri)** — Köpekbalığını göstermeden korku yaratma sanatı.

**[The Social Network](/film/37799-sosyal-ag)** — Aaron Sorkin yazımının zirvesi.",
            ],
            [
                'title' => 'En İyi Türk Filmleri: Yeşilçamdan Günümüze 25 Unutulmaz Yapım',
                'category' => 'Liste',
                'read_time' => 10,
                'image_url' => 'https://image.tmdb.org/t/p/w780/6orkLLEy9odOEYNWfcH5oedF7yg.jpg',
                'excerpt' => 'Yeşilçam klasiklerinden günümüzün ödüllü yapımlarına Türk sinemasının en iyileri.',
                'body' => "Türk sineması yüz yılı aşkın tarihiyle dünyanın en köklü sinema geleneklerinden birine sahip.

## Yeşilçam Klasikleri

**Hababam Sınıfı** serisi — **[Kemal Sunal](/kisi/17529-kemal-sunal)** ve **[Şener Şen](/kisi/17306-sener-sen)**'li efsane seri.

**Selvi Boylum Al Yazmalım** (1978) — Sevgi emekti, sevgi özveriydi...

**Eşkıya** (1996) — Yavuz Turgut'un unutulmaz filmi.

## Yeni Dönem

**Kış Uykusu** (2014) — Cannes'da Altın Palmiye kazandı.

## Günümüz

**Mustang** (2015) — Oscar adayı.

**[Cem Yılmaz](/kisi/17304-cem-yilmaz)** filmleri — Modern Türk komedi sineması.

Daha fazla yerli içerik için sitemizi keşfedin!",
            ],
            [
                'title' => 'Filmlerde Easter Egg Avı: Kaçırdığınız 15 Gizli Detay',
                'category' => 'Eğlence',
                'read_time' => 7,
                'image_url' => 'https://image.tmdb.org/t/p/w780/4bR3qWlf4PhSSmiXmYEuI40j9vd.jpg',
                'excerpt' => "En sevdiğiniz filmlerde saklanmış easter egg'leri biliyor musunuz?",
                'body' => "Easter egg, yönetmenlerin dikkatli izleyiciler için sakladığı gizli detaylardır.

## Pixar Paylaşımlı Evreni

**A113** — Pixar'ın tüm filmlerinde geçen gizemli kod. [Toy Story](/film/862-oyuncak-hikayesi)'den [Coco](/film/354912-coco)'ya her yerde.

**Pizza Planet Kamyonu** — İlk kez [Toy Story](/film/862-oyuncak-hikayesi)'de gördüğümüz sarı kamyon.

## Tarantino Evreni

**[Quentin Tarantino](/kisi/138-quentin-tarantino)**'nun tüm filmlerinde Red Apple sigarası ve Big Kahuna Burger bulunur.

## Marvel

Stan Lee, vefatına kadar neredeyse her Marvel filminde cameo yaptı.

**[Fight Club](/film/550-dovus-kulubu)** — **[David Fincher](/kisi/7467-david-fincher)**, her sahnede bir Starbucks bardağı sakladı.

**Back to the Future** — Twin Pines Mall tabelası, bir kazadan sonra Lone Pine Mall'a dönüşür.

**The Truman Show** — İkinci izleyişte her sahnede kamera görürsünüz.",
            ],
            [
                'title' => 'En İyi Savaş Filmleri: Tarihin En Çarpıcı 20 Yapımı',
                'category' => 'Liste',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/qOGVmgnIO71nIffkGmGtGmhHLdN.jpg',
                'excerpt' => 'Savaşın anlamsızlığını, kahramanlığını ve trajedisini en iyi anlatan 20 film.',
                'body' => "Savaş filmleri, insanlığın en karanlık yüzünü perdeye taşır.

## II. Dünya Savaşı

**[Saving Private Ryan](/film/857-er-ryani-kurtarmak)** — **[Steven Spielberg](/kisi/488-steven-spielberg)**'den Omaha Sahili.

**[Schindler's List](/film/424-schindlerin-listesi)** — Holokost'un en güçlü anlatısı.

**[Dunkirk](/film/374720-dunkirk)** — **[Christopher Nolan](/kisi/525-christopher-nolan)**'dan farklı bir savaş filmi.

**[The Pianist](/film/423-piyanist)** — Varşova gettosunda hayatta kalma.

## Vietnam Savaşı

**[Apocalypse Now](/film/28-apocalypse-now)** — **[Francis Ford Coppola](/kisi/1776-francis-ford-coppola)**'nın başyapıtı.

**[Full Metal Jacket](/film/600-full-metal-jacket)** — Kubrick'ten savaşın deliliği.

## I. Dünya Savaşı

**[1917](/film/530915-1917)** — Tek plan illüzyonuyla çekilmiş başyapıt.

**[All Quiet on the Western Front](/film/49046-bati-cephesinde-yeni-bir-sey-yok)** — Savaşın anlamsızlığı.

## Modern Savaş

**[The Hurt Locker](/film/12162-olumcul-tuzak)** — Irak savaşında bomba imha ekibi.

**[Black Hawk Down](/film/855-kara-sahin-dustu)** — **[Ridley Scott](/kisi/578-ridley-scott)**'tan Somali operasyonu.",
            ],
            [
                'title' => 'Yönetmen Sineması Rehberi: Tarz Sahibi 10 Usta',
                'category' => 'Rehber',
                'read_time' => 9,
                'image_url' => 'https://image.tmdb.org/t/p/w780/9cgwiPHZ57NjI0RKb2yIu8mUnNs.jpg',
                'excerpt' => 'Kadrajından tanıyabileceğiniz, kendine özgü stili olan 10 usta yönetmen.',
                'body' => "Bazı yönetmenler vardır ki, birkaç kareden kimin çektiğini anlarsınız.

**[Wes Anderson](/kisi/5655-wes-anderson)** — Simetri, pastel renkler, üst açılar.

**[Quentin Tarantino](/kisi/138-quentin-tarantino)** — Uzun diyaloglar, 70'ler müzikleri. [Pulp Fiction](/film/680-ucuz-roman).

**[Christopher Nolan](/kisi/525-christopher-nolan)** — Zaman, IMAX, Hans Zimmer. [Inception](/film/27205-baslangic), [Interstellar](/film/157336-yildizlararasi).

**[David Fincher](/kisi/7467-david-fincher)** — Soğuk renkler, dijital. [Fight Club](/film/550-dovus-kulubu), [Se7en](/film/807-yedi).

**[Denis Villeneuve](/kisi/137427-denis-villeneuve)** — Devasa ölçekler. [Arrival](/film/329865-gelis), [Dune](/film/438631-dune).

**[Martin Scorsese](/kisi/1032-martin-scorsese)** — Mafya, suç, kefaret. [Goodfellas](/film/769-siki-dostlar).

**[Greta Gerwig](/kisi/56512-greta-gerwig)** — Samimiyet ve enerji. [Lady Bird](/film/391713-lady-bird).

**[Bong Joon-ho](/kisi/21684-bong-joon-ho)** — Türlerin karışımı. [Parasite](/film/496243-parazit).

**[Hayao Miyazaki](/kisi/608-hayao-miyazaki)** — El çizimi animasyon. [Spirited Away](/film/129-ruhlarin-kacisi).

**[Jordan Peele](/kisi/72557-jordan-peele)** — Sosyal hiciv + korku. [Get Out](/film/419430-kapan).

Her yönetmenin profilini kişi sayfalarından inceleyebilirsiniz!",
            ],
            [
                'title' => 'En İyi Polisiye Filmleri: Dedektiflikten Suç Dramasına 20 Film',
                'category' => 'Liste',
                'read_time' => 8,
                'image_url' => 'https://image.tmdb.org/t/p/w780/hsJG5r6etrMNwW00BVp4NC7zi67.jpg',
                'excerpt' => 'Cinayet, gizem ve adalet arayışı... Polisiye türünün en iyi 20 filmi.',
                'body' => "Polisiye türü, insan zihninin karanlık köşelerine yolculuk yapar.

## Neo-Noir Başyapıtları

**[Se7en](/film/807-yedi)** — **[David Fincher](/kisi/7467-david-fincher)**'in karanlık başyapıtı. **[Brad Pitt](/kisi/287-brad-pitt)** ve **Morgan Freeman**.

**[Zodiac](/film/1949-zodiac)** — Gerçek bir seri katilin peşinde. **[Jake Gyllenhaal](/kisi/13242-jake-gyllenhaal)** ve **[Robert Downey Jr.](/kisi/3223-robert-downey-jr)**.

**[L.A. Confidential](/film/2118-l-a-confidential)** — 1950'ler Los Angeles'ı.

**[Chinatown](/film/829-chinatown)** — **[Jack Nicholson](/kisi/514-jack-nicholson)**'lı neo-noir klasiği.

## Dedektif Hikayeleri

**[The Silence of the Lambs](/film/274-kuzularin-sessizligi)** — **[Anthony Hopkins](/kisi/4173-anthony-hopkins)**'in Hannibal Lecter'ı.

**[Prisoners](/film/146233-tutsak)** — **[Hugh Jackman](/kisi/6968-hugh-jackman)** ve **[Jake Gyllenhaal](/kisi/13242-jake-gyllenhaal)**.

**[Memories of Murder](/film/9746-memories-of-murder)** — **[Bong Joon-ho](/kisi/21684-bong-joon-ho)**'dan.

## Suç Dramaları

**[The Departed](/film/1422-kostebek)** — **[Martin Scorsese](/kisi/1032-martin-scorsese)**'nin Oscar'lı filmi.

**[Heat](/film/949-buyuk-hesaplasma)** — **[Al Pacino](/kisi/1158-al-pacino)** ve **[Robert De Niro](/kisi/380-robert-de-niro)** aynı sahnede.

**[The Usual Suspects](/film/629-olagan-supheliler)** — En büyük plot twist. Keyser Söze.

Daha fazlası için **[gizem modumuza](/mod/gizem)** göz atın!",
            ],
        ];

        foreach ($posts as $post) {
            Post::create(array_merge($post, [
                'published_at' => now()->subDays(rand(1, 60)),
                'is_published' => true,
            ]));
        }
    }
}
