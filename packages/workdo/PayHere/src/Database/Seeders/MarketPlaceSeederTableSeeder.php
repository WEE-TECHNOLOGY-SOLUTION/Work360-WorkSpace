<?php

namespace  Workdo\PayHere\Database\Seeders;

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
        $module = 'PayHere';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'PayHere';
        $data['product_main_description'] = '<p>Welcome to the future of payment processing with PayHere Payment Gateway Integration in Dash SaaS! This innovative integration brings together the trusted payment solutions of PayHere with the powerful capabilities of Dash SaaS, offering users a seamless and secure payment experience. Whether you\'re a small business owner or a large enterprise, PayHere integration in Dash SaaS caters to your payment processing needs with efficiency and reliability.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Simplify Payments with PayHere in Dash SaaS.';
        $data['dedicated_theme_description'] = '<p>Experience secure transactions and diverse payment options with PayHere integration in Dash SaaS. Streamline your payment process effortlessly for enhanced efficiency.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Secure and Reliable Payment Processing","dedicated_theme_section_description":"<p>Say goodbye to worries about payment security with PayHere Payment Gateway Integration. PayHere is renowned for its robust security measures, ensuring that every transaction is encrypted and protected from potential threats. With PayHere, users can trust that their payment information is safe and secure, providing peace of mind for both businesses and customers alike.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Effortless Integration Process","dedicated_theme_section_description":"<p>Integrating PayHere into your Dash SaaS platform is a breeze, thanks to its seamless integration process. With just a few simple steps, you can connect PayHere to your Dash SaaS account and start accepting payments online in no time. Say goodbye to complex setup procedures and hello to streamlined payment processing with PayHere integration.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Diverse Payment Options for Customers","dedicated_theme_section_description":"<p>One of the key benefits of PayHere Payment Gateway Integration is its support for multiple payment options. Whether your customers prefer to pay with credit/debit cards, mobile payments, or other popular payment methods, PayHere has you covered. By offering a variety of payment options, you can enhance flexibility and convenience for your customers, ultimately improving their overall shopping experience.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Real-time Transaction Monitoring and Enhanced User Experience","dedicated_theme_section_description":"<p>With PayHere Payment Gateway Integration, you can keep track of transactions in real-time, allowing you to monitor payment activity and reconcile transactions efficiently. Additionally, by providing customers with a seamless checkout experience, you can improve user satisfaction and drive conversions. Say hello to enhanced payment processing capabilities and a superior user experience with PayHere integration in Dash SaaS!</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"PayHere"},{"screenshots":"","screenshots_heading":"PayHere"},{"screenshots":"","screenshots_heading":"PayHere"},{"screenshots":"","screenshots_heading":"PayHere"},{"screenshots":"","screenshots_heading":"PayHere"}]';
        $data['addon_heading'] = 'What is Lorem Ipsum?';
        $data['addon_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'What is Lorem Ipsum?';
        $data['whychoose_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
        $data['pricing_plan_heading'] = 'What is Lorem Ipsum?';
        $data['pricing_plan_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
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
