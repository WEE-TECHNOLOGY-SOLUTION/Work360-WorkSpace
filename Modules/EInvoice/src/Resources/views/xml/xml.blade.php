<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cbc:CustomizationID>urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0
    </cbc:CustomizationID>
    <cbc:ProfileID>urn:fdc:peppol.eu:2017:poacc:billing:01:1.0</cbc:ProfileID>
    <cbc:ID>{{ App\Models\Invoice::invoiceNumberFormat($invoice->invoice_id) }}</cbc:ID>
    <cbc:IssueDate>{{ $invoice->issue_date }}</cbc:IssueDate>
    <cbc:DueDate>{{ $invoice->due_date }}</cbc:DueDate>
    <cbc:InvoiceTypeCode>380</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>{{ company_setting('defult_currancy') }}</cbc:DocumentCurrencyCode>
    <cbc:TaxCurrencyCode>{{ company_setting('defult_currancy') }}</cbc:TaxCurrencyCode>
    <cbc:BuyerReference>{{ App\Models\Invoice::invoiceNumberFormat($invoice->invoice_id) }}</cbc:BuyerReference>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cbc:EndpointID schemeID="{{ company_setting('electronic_address_schema') }}">{{  company_setting('electronic_address')  }}</cbc:EndpointID>
            <cac:PartyName>
                <cbc:Name>{{ $workspace->name }}</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:StreetName>{{ $customer->billing_address }}</cbc:StreetName>
                <cbc:CityName>{{ $customer->billing_city }}</cbc:CityName>
                <cac:Country>
                    <cbc:IdentificationCode>{{ strtoupper(substr($customer->billing_country , 0 ,2)) }}</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>{{ company_setting('vat_number') }}</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>{{ company_setting('tax_type') }}</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>{{ $customer->name }}</cbc:RegistrationName>
                <cbc:CompanyID schemeID="{{ company_setting('company_id_schema') }}">{{ company_setting('company_id') }}</cbc:CompanyID>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cbc:EndpointID schemeID="{{ $customer->electronic_address_scheme }}">{{ $customer->electronic_address }}</cbc:EndpointID>
            <cac:PostalAddress>
                <cac:Country>
                    <cbc:IdentificationCode />
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>{{ company_setting('company_name') }}</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $totalTaxPrice }}</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getTotal() }}</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $totalTaxPrice }}</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>{{ $totalTaxRate }}</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>{{ company_setting('tax_type') }}</cbc:ID>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getTotal() }}</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getTotal() }}</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getSubTotal() }}</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getSubTotal() }}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:InvoicedQuantity unitCode="C62">1</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getTotal() }}</cbc:LineExtensionAmount>
        <cac:Item>
            <cbc:Name>{{ $productname }}</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>{{ $totalTaxRate }}</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>{{ company_setting('tax_type') }}</cbc:ID>
                </cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="{{ company_setting('defult_currancy') }}">{{ $invoice->getSubTotal() }}</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>
</Invoice>
