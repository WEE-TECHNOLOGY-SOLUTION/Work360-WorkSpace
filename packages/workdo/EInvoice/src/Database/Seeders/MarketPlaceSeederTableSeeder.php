<?php

namespace Workdo\EInvoice\Database\Seeders;

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
        $module = 'EInvoice';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'EInvoice';
        $data['product_main_description'] = '<p>European Compliant Invoicing is an innovative module designed to streamline and optimize the invoicing process within the European framework. With a strong focus on compliance and adherence to European e-invoicing standards, this module empowers businesses to generate and manage invoices efficiently, ensuring seamless transactions and regulatory compliance.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'E-Invoice European Compliant Invoicing';
        $data['dedicated_theme_description'] = '<p>Right now this module supports EN 16931 Compliant Invoices, with CIUS – PEPPOL BIS.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"All Invoice exports are in XML.","dedicated_theme_section_description":"<p>Simply connect. When connected you will be able to send e-invoices on a global scale and become compliant with the newest regulations in Europe, Asia, North America and more. With over 50 countries already connected.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"E-Invoicing - European Compliant Invoicing","dedicated_theme_section_description":"<p>Do you still work with old systems that cannot connect to other networks yet? Our simple API allows you to connect to many e-invoicing networks and open exchange networks (i.e PEPPOL, BPC, SDI, Finvoice, FACeB2B, SAP, Tungsten).<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"EInvoice"},{"screenshots":"","screenshots_heading":"EInvoice"},{"screenshots":"","screenshots_heading":"EInvoice"},{"screenshots":"","screenshots_heading":"EInvoice"},{"screenshots":"","screenshots_heading":"EInvoice"}]';
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
