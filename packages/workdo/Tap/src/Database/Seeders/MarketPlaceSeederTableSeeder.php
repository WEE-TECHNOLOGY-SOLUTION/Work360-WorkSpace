<?php

namespace Workdo\Tap\Database\Seeders;

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
        $module = 'Tap';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Tap';
        $data['product_main_description'] = '<p>Tap Payments is a payment gateway and financial technology company based in the Middle East, particularly in the United Arab Emirates (UAE). It provides online payment processing services, enabling businesses to accept payments through various channels, including websites, mobile apps, and other digital platforms.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Tap Payment Gateway';
        $data['dedicated_theme_description'] = '<p>Tap Payments supports a diverse range of payment methods, including major credit cards, debit cards, and popular digital wallets. This ensures that businesses can cater to a wide audience, offering flexibility to customers in how they prefer to make payments.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Why use Tap payment?","dedicated_theme_section_description":"<p>Tap Payments prioritizes security, employing advanced encryption and security measures to safeguard sensitive financial data. This commitment to security builds trust among both merchants and customers.Integrating Tap Payments into your online platforms is straightforward. With developer-friendly APIs and plugins, businesses can quickly implement Tap payment gateway, reducing the time and effort required for setup.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Invoice Payment with Tap","dedicated_theme_section_description":"<p>Simplify invoice payments using Tap`s user-friendly and secure gateway, enabling customers to settle invoices conveniently with various payment options.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Tap"},{"screenshots":"","screenshots_heading":"Tap"},{"screenshots":"","screenshots_heading":"Tap"},{"screenshots":"","screenshots_heading":"Tap"},{"screenshots":"","screenshots_heading":"Tap"}]';
        $data['addon_heading'] = 'Why choose dedicated modules for Your Business?';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modules for Your Business?';
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
