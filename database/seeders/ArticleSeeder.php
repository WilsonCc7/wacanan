<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Seed ten published articles spread across every category.
     */
    public function run(): void
    {
        $now = now();

        $covers = [
            'https://images.unsplash.com/photo-1493421419110-74f4e85ba126?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1483412033650-1015ddeb83d1?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1553729459-efe14ef6055d?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=1200&q=70',
            'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=1200&q=70',
        ];

        foreach ($this->articles() as $index => $data) {
            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    ...$data,
                    'cover_image' => $covers[$index] ?? $covers[0],
                    'published_at' => $now->clone()->subDays(74 - ($index * 7))->setTime(9, 30),
                    'likes_count' => [128, 42, 17, 263, 9, 87, 54, 31, 146, 63][$index] ?? 0,
                ]
            );
        }
    }

    /**
     * @return array<int, array{title: string, excerpt: string, body: string, category: string, author_name: string}>
     */
    private function articles(): array
    {
        return [
            [
                'title' => 'The Quiet Power of Whitespace in Editorial Layouts',
                'category' => 'Design',
                'author_name' => 'Rina Wijaya',
                'excerpt' => 'Empty space is not a leftover. It is the loudest decision a layout can make.',
                'body' => "Every page starts as a fight for room. Headlines want to be bigger, images want to breathe, and nobody volunteers to be cut. The designer's first job is to arbitrate that fight, and whitespace is the verdict.\n\nIn editorial work, margins do more than frame the text. They set the tempo. A wide outer margin tells the reader to slow down, that this is a long read worth settling into. A tight grid feels like news, urgent and disposable.\n\nThe mistake is treating whitespace as a budget that runs out. When a layout feels crowded, the fix is rarely a bigger image. It is almost always permission to remove something.",
            ],
            [
                'title' => 'Why Local-First Software Is Winning Again',
                'category' => 'Technology',
                'author_name' => 'Adi Nugroho',
                'excerpt' => 'The cloud promised freedom and delivered a subscription. Local-first gives the data back.',
                'body' => "For a decade the default architecture was obvious: put everything on a server, charge monthly, and call the browser a thin client. It worked, and then it stopped feeling like progress.\n\nLocal-first inverts the arrangement. Your device owns the data, the interface never waits for a network round trip, and sync becomes a background convenience instead of a precondition for opening the app.\n\nThe hard part was never the storage. It was conflict resolution when two devices disagree. CRDTs and mature sync engines have made that tractable, which is why the pattern now ships in products people use every day.",
            ],
            [
                'title' => 'Pricing as a Product Decision, Not a Spreadsheet',
                'category' => 'Business',
                'author_name' => 'Sari Pratiwi',
                'excerpt' => 'Your price tells customers what you believe you are. Listen to what they hear.',
                'body' => "Most pricing conversations start in the wrong room. Finance opens a cost model, adds a margin, and publishes a number that nobody ever tested with a real customer.\n\nBut price is the first thing a buyer sees, long before the feature list. It frames the comparison. A tool priced at three times its competitor is not expensive, it is making a claim about who it is for.\n\nTest pricing the way you test onboarding. Watch what people do at the paywall, not what they say in surveys. The number you keep defending is usually the number that is costing you the most growth.",
            ],
            [
                'title' => 'The Last Bookshops of Kota Tua',
                'category' => 'Culture',
                'author_name' => 'Bayu Santoso',
                'excerpt' => 'Behind a faded storefront, an inventory of a century survives on index cards.',
                'body' => "The shopfront is easy to miss. Between a print service and a cell phone repair stall, a hand-painted sign reads simply 'Buku'.\n\nInside, the ceiling has been replaced by a second floor of shelves, reached by a ladder the owner climbs without looking up. Nothing is barcoded. Every title is accounted for on index cards in a tin box that predates the register.\n\nThe shop does not sell many books a week, and that is not the point of it. What it keeps is the habit of browsing, of finding a title you never searched for. Some things a recommender algorithm cannot replace.",
            ],
            [
                'title' => 'Cassette Culture and the Warmth of Imperfection',
                'category' => 'Music',
                'author_name' => 'Maya Lestari',
                'excerpt' => 'A generation that never owned a tape deck is buying them anyway. The flaw is the feature.',
                'body' => "Tape hiss is a measurement error. On paper, a cassette is the worst carrier a recording can have, and the numbers that prove it are not wrong.\n\nBut music is not a measurement. The wow and flutter of a worn tape do something a clean digital file cannot: they remind you a performance happened, once, on a physical object that was somewhere.\n\nIndependent labels noticed before the press did. A limited cassette release still sells out faster than the streaming campaign that promotes it, mostly to buyers who have no deck at home. They are collecting the ritual, not the medium.",
            ],
            [
                'title' => 'Type Scales That Survive a Redesign',
                'category' => 'Design',
                'author_name' => 'Putri Halim',
                'excerpt' => 'A good scale is boring on paper and indestructible in practice.',
                'body' => "Pick a ratio, apply it everywhere, stop arguing. That is the whole advice, and most teams still get it wrong by hand-tuning each heading until nothing matches anything.\n\nA scale works because it removes a decision. When the next redesign comes, and it will, you do not re-litigate 47 font sizes. You change one multiplier and the entire system follows.\n\nThe ratio you choose matters less than the discipline of using it. Even a modest 1.2 progression looks deliberate next to the chaos of individually chosen values.",
            ],
            [
                'title' => 'SQLite Is Enough for Your First 10,000 Users',
                'category' => 'Technology',
                'author_name' => 'Joko Tarigan',
                'excerpt' => 'You probably do not need Postgres yet. You need to measure before you migrate.',
                'body' => "Every pitch deck includes a scaling slide, which is how so many products start with a three-server database for a product with 300 users.\n\nModern SQLite with WAL mode handles concurrent reads comfortably, ships as a single file, and removes an entire class of deployment work. For a read-heavy content product, the ceiling is higher than most engineers assume.\n\nMigrating later is a real cost, but so is maintaining infrastructure you never needed. Spend the weekend on the migration when the numbers demand it, not when the deck suggests it.",
            ],
            [
                'title' => 'Bootstrapping Without a Board Deck',
                'category' => 'Business',
                'author_name' => 'Dewi Kusuma',
                'excerpt' => 'Revenue from customers is slower and far more honest than money from a room.',
                'body' => "Fundraising has a clear script: build the deck, take the meetings, optimize the round. Bootstrapping has no script, which is exactly why so few people talk about how it actually works.\n\nThe trade is simple and unfashionable. You keep total ownership and every strategic option, and in exchange you grow at the speed your customers pay for. Slow quarters are not survivable unless the burn is near zero.\n\nThe quiet advantage is that nothing about the product is performance art for investors. Every feature has to be worth money to someone, and that discipline compounds in ways that funding often washes out.",
            ],
            [
                'title' => 'How Night Markets Feed a City\'s Memory',
                'category' => 'Culture',
                'author_name' => 'Rizky Amalia',
                'excerpt' => 'The same stall, the same cart, the same hour, for four generations.',
                'body' => "A night market is usually described as food, but that undersells it. It is a schedule the city keeps with itself, and the menu is the most stable thing about the neighborhood.\n\nThe cart in the third row has sold one dish since 1968. The recipe changed once, in 1974, and the regulars still mention it. That continuity is the product, and the recipe is almost incidental.\n\nCities rebuild quickly now, and markets are the soft infrastructure that gets valued last. When one closes, what disappears is not a place to eat. It is a hundred small histories that had nowhere else to live.",
            ],
            [
                'title' => 'Field Recordings as an Archive of Place',
                'category' => 'Music',
                'author_name' => 'Arya Salim',
                'excerpt' => 'Sound is the only document that records what nobody thought to photograph.',
                'body' => "Photography chose what to keep. Sound recording, if you leave it running, keeps everything: the traffic, the vendor two stalls away, the room itself.\n\nArchivists have understood this for decades, but cheap portable recorders put the practice in reach of anyone curious enough to stand still for ten minutes.\n\nThe recordings that matter most are usually the ones nobody curated. A market at closing, a train platform in rain, a street before the first commute. They are the closest thing we have to a time machine that cannot lie.",
            ],
        ];
    }
}
