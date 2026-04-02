<?php

namespace Database\Seeders;

use Backpack\PageManager\app\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //



        Page::create([
            'template' => 'contact_us',
            'name' => 'Contact Us',
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'extras' => [
                "address" => "South Centro, Sipocot, Camarines Sur",
                "phone" => "450-65-84",
                "email" => "frydtlyingin@gmail.com",
                "maps" => "https://maps.app.goo.gl/jDFYSbrgsiafxFwg7",
                "facebook" => "https://www.facebook.com/",
                "twitter" => null,
            ],
        ]);

        Page::create([
            'template' => 'about_us',
            'name' => 'About Us',
            'title' => 'About Us',
            'slug' => 'about-us',
            'extras' => [
                "about_us_header" => "Frydt Lying-In Clinic",
                "about_us_content" => "Frydt Lying-In Clinic is a healthcare facility dedicated to providing comprehensive maternity and gynecological services to ",
                "mission_header" => "",
                "mission_content" => "<p>Promotion of basic health services in a safe, healthcare facility to reduce health risk life mortality and ensure that the basic health services for good prenatal, delivery and postnatal care delivered to the clients.<br></p>",
                "vision_header" => "",
                "vision_content" => "<p>To provide effective and efficient safe delivery for mother and render quality newborn care, promotion and reproductive health program through education and counselling and other basic health care services.<br></p>",
                "cta_header" => "Find Out More About Our Services.",
                "cta_content" => ""
            ],
        ]);
    }
}
