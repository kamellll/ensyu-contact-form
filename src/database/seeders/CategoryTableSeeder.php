<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'content' => '商品のお届けについて',
        ];
        //DB::table('categories')->insert($param);
        Category::create($param);
        $param = [
            'content' => '商品の交換について',
        ];
        Category::create($param);
        $param = [
            'content' => '商品トラブル',
        ];
        Category::create($param);
        $param = [
            'content' => 'ショップへのお問い合わせ',
        ];
        Category::create($param);
        $param = [
            'content' => 'その他',
        ];
        Category::create($param);
    }
}
