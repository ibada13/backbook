<?php

namespace Database\Seeders;
use Faker\Factory  as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Comment;
use App\Models\Type;
use App\Models\Book;
class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
     private function generateSentence($numWords) {
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
        // Get the absolute path to the image folder
        $imageDirectory = public_path("images/{$folder}");
    
        // Get all image files in the directory with the specified extensions
        $images = glob($imageDirectory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    
        // Check if the directory contains any images
        if (!$images || empty($images)) {
            return null; 
        }
    
        // Pick a random image from the array
        $randomImagePath = $images[array_rand($images)];
    
        // Use the asset helper to generate the URL
       
        return basename($randomImagePath);
    }
    

    public function run(): void
    {
        $imageDirectory = 'images/books/';

        $mytypes = [
            'فلسفة',
            "رواية",
            "شعر",
            "فانتازيا",
            "خيال علمي",
            "مغامرة",
            "تاريخ",
            "ديني",
            "تراجيدي",
            "فكاهي",
            "سيرة",
            "رعب",
            "سياسة",
            "حرب"
        ];
        
        Storage::makeDirectory($imageDirectory);
        $faker=Faker::create('ar_SA');
        for ($index = 0; $index <10; $index++) {
            $typestoattach=[];
            $authorname=$faker->name;
            $pages=$faker->numberBetween(150,1200);
            $author=Author::firstOrCreate(
                    ['name'=>$authorname],
                    ['bio'=>$this->generateSentence(rand(45,60)),'author_pfp'=>$this->getRandomImage("authors")],
            );
            $book=Book::create([
                'title'=>$this->generateSentence(rand(1,3)),
                'isbn'=>$faker->isbn13,
                'description'=>$faker->boolean()?$this->generateSentence(rand(45,60)):null,   
                'published_year'=>$faker->year('now'),
                'pages'=>$pages,
                'cover_path'=>$this->getRandomImage("books"),
                'current_page_number'=>$faker->numberBetween(0,$pages),
            ]);
            $book->authors()->attach($author->id);

                    for($i = 0 ;$i<3;$i++){
                        $randomtype = $mytypes[array_rand($mytypes)];
                        $type = Type::firstOrCreate([
                            "name"=>$randomtype,
                        ]);
                        $typestoattach[] =$type->id ;
                      
                    }
                    $book->types()->syncWithoutDetaching($typestoattach);
            // $book->types()->attach($types->pluck('id')->toArray());

            for($commentid=0;$commentid<rand(0,5);$commentid++){
                Comment::create([
                    'book_id'=>$book->id,
                    'comment'=>$this->generateSentence(rand(10,50)),
                ]);
            }
        }
    }
}
