<?php

namespace Database\Seeders;

use App\Models\DonationCase;
use Illuminate\Database\Seeder;

class DonationCaseSeeder extends Seeder
{
    //To insert seeds in DB: php artisan db:seed --class=DonationCaseSeeder
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $cases = [
            [
                'id' => 1,
                'title' => "Food for Resala",
                "goal_amount" => 500,
                "raised_amount" => 200,
                "image" => "png.png",
                "deadline" => "2022-03-23 16:07:26",
                "description" => "the food will go to the resala comp",
                "organization_id" => 1,
                "category_id" => 1,
                "user_id" => 3
            ],

            [
                'id' => 2,
                'title' => "Clothes for resala",
                "goal_amount" => 5000,
                "raised_amount" => 2000,
                "image" => "png.png",
                "deadline" => "2022-03-23 16:07:26",
                "description" => "the clothes will go to the resala comp",
                "organization_id" => 1,
                "category_id" => 2,
                "user_id" => 3
            ],
            [
                'id' => 3,
                'title' => "money for resala",
                "goal_amount" => 1000,
                "raised_amount" => 2000,
                "image" => "png.png",
                "deadline" => "2022-03-23 16:07:26",
                "description" => "the money will go to the resala comp",
                "organization_id" => 1,
                "category_id" => 2,
                "user_id" => 3
            ],
            [
                'id' => 4,
                'title' => "money for misrelkhier",
                "goal_amount" => 100,
                "raised_amount" => 200,
                "image" => "png.png",
                "deadline" => "2022-03-23 16:07:26",
                "description" => "the money will go to the misrelkhier",
                "organization_id" => 2,
                "category_id" => 1,
                "user_id" => 1
            ],
            [
                'id' => 5,
                'title' => "food for misrelkhier",
                "goal_amount" => 500,
                "raised_amount" => 1000,
                "image" => "png.png",
                "deadline" => "2022-03-23 16:07:26",
                "description" => "the food will go to the misrelkhier",
                "organization_id" => 2,
                "category_id" => 1,
                "user_id" => 1
            ],
        ];

        foreach ($cases as $key => $value) {
            DonationCase::create($value);
        }
    }
}
