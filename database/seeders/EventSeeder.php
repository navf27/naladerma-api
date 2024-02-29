<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('events')->insert([
            "category_id" => "1",
            "name" => "Mengenal Sejarah Wayang Beber",
            "description" => "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sapiente mollitia atque culpa dolores natus quasi facere, exercitationem beatae a eum accusamus! Repudiandae eius odio incidunt magni enim unde iure eaque beatae alias quia odit laudantium, mollitia quam sequi, nisi consequatur. Illo enim doloribus, pariatur quas est, reprehenderit nisi, qui dicta fugit nihil magni doloremque eligendi fuga quod at. Ipsa iure rerum provident quidem perferendis corporis veniam id ab fugiat magni obcaecati nulla vitae culpa, animi voluptatem quo iusto, voluptatibus non. Quae culpa consectetur tenetur magni obcaecati est, fugit, omnis maxime error molestiae rem, ipsa inventore repudiandae. Quam veniam, sed illum accusantium, optio omnis molestias blanditiis quos corporis ipsa illo placeat assumenda tempora ipsum reiciendis, nobis nihil perferendis necessitatibus corrupti atque. Vero magni, incidunt a voluptates, nesciunt impedit quidem quisquam porro quas aperiam voluptate omnis consequuntur ad facere? Sed, impedit rem iure deleniti deserunt repudiandae ipsa inventore odio voluptatem quod perspiciatis, quisquam consectetur. Mollitia odio natus voluptas quae excepturi adipisci aperiam laboriosam placeat hic, nisi accusamus corporis quasi ab doloremque nobis quam suscipit commodi praesentium quod cupiditate veritatis odit. Deleniti, illo praesentium repudiandae eaque aperiam est dolor qui nesciunt odio. Excepturi natus atque sint nihil corrupti? Nisi similique libero possimus dolorem.",
            "location" => "Benteng Vastenburg",
            "price" => "10000",
            "file_link" => "",
            "img_link" => "https://linkgambar",
            "start_time" => "2024-01-25 15:27:13",
            "time_ends" => "2024-01-25 15:27:13",
        ]);
    }
}
