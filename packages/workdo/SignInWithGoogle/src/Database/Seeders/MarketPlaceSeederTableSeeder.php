<?php

namespace Workdo\SignInWithGoogle\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\LandingPage\Entities\MarketplacePageSetting;


class MarketPlaceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module = 'SignInWithGoogle';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'SignInWIthGoogle';
        $data['product_main_description'] = '<p>The Sign-In with Google module in Dash SaaS allows users to log in using their existing Google accounts, eliminating the need to remember additional usernames and passwords. This streamlined access enhances user convenience, making it easier for them to engage with your platform. By leveraging Google’s secure authentication system, you can provide a seamless and efficient sign-in experience that encourages more frequent usage and engagement.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Seamless Integration';
        $data['dedicated_theme_description'] = '<p>Effortlessly log in with your Google account using Dash SaaS`s Sign-In with Google module.</p>';
        $data['dedicated_theme_sections'] = '[
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Enhanced Security",
                                                    "dedicated_theme_section_description": "<p>With Google’s robust security measures, the Sign-In with Google module offers a highly secure way to authenticate users. Google employs advanced encryption and security protocols to protect user data, ensuring that their login credentials are safe from unauthorized access. This added layer of security builds trust with your users, reassuring them that their information is safeguarded and encouraging them to interact more confidently with your platform.</p>",
                                                    "dedicated_theme_section_cards": {
                                                        "1": {
                                                        "title": null,
                                                        "description": null
                                                        }
                                                    }
                                                },
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Improved User Experience",
                                                    "dedicated_theme_section_description": "<p>Integrating the Sign-In with Google module simplifies the login process, providing a user-friendly interface that enhances the overall user experience. Users can quickly access their accounts with just a few clicks, reducing friction and potential drop-offs during the sign-in process. This ease of access is particularly beneficial for mobile users, who can log in swiftly without the hassle of typing out long credentials on small screens.</p>",
                                                    "dedicated_theme_section_cards": {
                                                        "1": {
                                                        "title": null,
                                                        "description": null
                                                        }
                                                    }
                                                }
                                            ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"SignInWIthGoogle"},{"screenshots":"","screenshots_heading":"SignInWIthGoogle"},{"screenshots":"","screenshots_heading":"SignInWIthGoogle"},{"screenshots":"","screenshots_heading":"SignInWIthGoogle"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules<b> for Your Business?</b></h2>';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach($data as $key => $value){
            if(!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()){
                MarketplacePageSetting::updateOrCreate(
                [
                    'name' => $key,
                    'module' => $module

                ],
                [
                    'value' => $value
                ]);
            }
        }
    }
}
