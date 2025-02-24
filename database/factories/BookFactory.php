<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use App\Models\Type;
use App\Models\Comment;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $pages = $this->faker->numberBetween(150, 1200);

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->isbn13,
            'description' => $this->faker->boolean() ? $this->generateSentence(rand(45, 60)) : null,
            'published_year' => $this->faker->year(),
            'pages' => $pages,
            'cover_path' => $this->getRandomImage("books"),
            'status' => $this->faker->boolean() ? Book::STATUS_APPROVED : Book::STATUS_PENDING_APPROVAL,

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Book $book) {
            $authorname = $this->faker->name;
            $author = Author::firstOrCreate(
                ['name' => $authorname],
                ['bio' => $this->generateSentence(rand(45, 60)), 'author_pfp' => $this->getRandomImage("authors"), 'user_id' => User::inRandomOrder()->first()->id]
            );
            $book->authors()->attach($author->id);

            $types = ['فلسفة', 'رواية', 'شعر', 'فانتازيا', 'خيال علمي', 'مغامرة', 'تاريخ', 'ديني', 'تراجيدي', 'فكاهي', 'سيرة', 'رعب', 'سياسة', 'حرب'];
            $typesToAttach = [];
            for ($i = 0; $i < 3; $i++) {
                $randomType = $types[array_rand($types)];
                $type = Type::firstOrCreate(["name" => $randomType]);
                $typesToAttach[] = $type->id;
            }
            $book->types()->syncWithoutDetaching($typesToAttach);

            for ($commentid = 0; $commentid < rand(0, 5); $commentid++) {
                Comment::create([
                    'book_id' => $book->id,
                    'comment' => $this->generateSentence(rand(10, 50)),
                    'user_id' => User::inRandomOrder()->first()->id
                ]);
            }

            $users = \App\Models\User::inRandomOrder()->take(rand(1, 5))->get();
            foreach ($users as $user) {
                $book->readers()->attach($user->id, [
                    'pages' => rand(0, $book->pages),
                ]);
            }

            $book->update([
                'readers_count' => $book->readers()->count(),
                'favorited_count' => $book->favoritedByUsers()->count(),
            ]);
        });
    }

    private function generateSentence($numWords)
    {
        $words = [
            "تفاحة", "موز", "برتقال", "عنب", "سماء", "زرقاء", "شمس", "سعيد", "سريع", "ثعلب",
            "كسول", "كلب", "يقفز", "يجري", "حلم", "نجمة", "مشرق", "نهر", "يرقص", "زهرة",
            "غابة", "كتاب", "قلم", "بحر", "جبل", "طائر", "جميل", "صغير", "كبير", "فرح",
            "ليل", "نهار", "طاولة", "كرسي", "نافذة", "باب", "مدينة", "قرية", "سيارة", "طائرة",
            "حديقة", "شجرة", "وردة", "صديق", "مطر", "ثلج", "ريح", "حقل", "وقت", "ساعة",
            "شارع", "طريق", "مبنى", "مسجد", "كنيسة", "مدرسة", "جامعة", "أستاذ", "طالب", "كتاب",
            "رسم", "لوحة", "عالم", "نجوم", "فضاء", "قمر", "شلال", "نهر", "شاطئ", "أمل",
            "فرحة", "سعادة", "غروب", "شروق", "طعام", "شراب", "خبز", "جبن", "حليب", "لحم",
            "سمك", "فاكهة", "خضروات", "حديقة", "طبيعة", "صحراء", "واحة", "نخيل", "زهور", "رمال",
            "صقر", "نسر", "غيمة", "سماء", "رعد", "برق", "جسر", "صخور", "كهف", "غرفة"
        ];
        shuffle($words);
        return implode(" ", array_slice($words, 0, $numWords)) . ".";
    }

    private function getRandomImage($folder)
    {
        $imageDirectory = public_path("images/{$folder}");
        $images = glob($imageDirectory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        if (!$images || empty($images)) {
            return null;
        }
        return basename($images[array_rand($images)]);
    }
}
