<?php

namespace Workdo\Mollie\Database\Seeders;

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
        $module = 'Mollie';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Mollie';
        $data['product_main_description'] = '<p>Your Mollie.com account allows you to select multiple Payment methods. However the Mollie Payment Gateway integrates with the iDeal payment method. This account has the "iDeal" and "Bank transfer" payment methods active, however only iDeal will be available to your users for payments.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Mollie Payment Gateway';
        $data['dedicated_theme_description'] = '<p>Your attendees can pay for their event registrations with a credit card or using services like IDEAL, Bancontact, SEPA Direct debit, Giropay, PayPal, Sofort Banking, and others.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"What is Mollie payment gateway?","dedicated_theme_section_description":"<p>designed for growth. An advanced solution to accept payments, optimise conversion, and access funding. Sign up. Powering growth for over 130,000 businesses – from startups to enterprises.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"What does Mollie payments do?","dedicated_theme_section_description":"<p>Mollie is a leading European payment provider that specializes in processing online payments on behalf of merchants such as webshop owners. A pioneer in the payment industry, Mollie helps companies of all sizes accept different payment methods from customers with their robust yet simple payments API.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Mollie"},{"screenshots":"","screenshots_heading":"Mollie"},{"screenshots":"","screenshots_heading":"Mollie"},{"screenshots":"","screenshots_heading":"Mollie"},{"screenshots":"","screenshots_heading":"Mollie"}]';
        $data['addon_heading'] = 'Why choose dedicated modulesfor Your Business?';
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
