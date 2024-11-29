<?php

namespace Workdo\Inventory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\ConvertToInvoice;
use App\Events\CreateInvoice;
use App\Events\CreatePurchase;
use App\Events\UpdateInvoice;
use App\Events\UpdatePurchase;
use Workdo\Account\Events\CreateBill;
use Workdo\Account\Events\UpdateBill;
use Workdo\Inventory\Listeners\CompanyMenuListener;
use Workdo\Inventory\Listeners\ConvertToInvoiceLis;
use Workdo\Inventory\Listeners\CreateBillLis;
use Workdo\Inventory\Listeners\CreateInvoiceLis;
use Workdo\Inventory\Listeners\CreatePaymentPosLis;
use Workdo\Inventory\Listeners\CreatePurchaseLis;
use Workdo\Inventory\Listeners\CreateSalesInvoiceItemLis;
use Workdo\Inventory\Listeners\RetainerConvertToInvoiceLis;
use Workdo\Inventory\Listeners\UpdateBillLis;
use Workdo\Inventory\Listeners\UpdateInvoiceLis;
use Workdo\Inventory\Listeners\UpdatePurchaseLis;
use Workdo\Pos\Events\CreatePaymentPos;
use Workdo\Retainer\Events\RetainerConvertToInvoice;
use Workdo\Sales\Events\CreateSalesInvoiceItem;

class EventServiceProvider extends Provider
{
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    protected $listen = [
        CompanyMenuEvent::class => [
            CompanyMenuListener::class,
        ],
        ConvertToInvoice::class => [
            ConvertToInvoiceLis::class,
        ],
        CreateBill::class => [
            CreateBillLis::class,
        ],
        CreateInvoice::class => [
            CreateInvoiceLis::class,
        ],
        CreatePaymentPos::class => [
            CreatePaymentPosLis::class,
        ],
        CreatePurchase::class => [
            CreatePurchaseLis::class,
        ],
        CreateSalesInvoiceItem::class => [
            CreateSalesInvoiceItemLis::class,
        ],
        RetainerConvertToInvoice::class => [
            RetainerConvertToInvoiceLis::class,
        ],
        UpdateBill::class => [
            UpdateBillLis::class,
        ],
        UpdateInvoice::class => [
            UpdateInvoiceLis::class,
        ],
        UpdatePurchase::class => [
            UpdatePurchaseLis::class,
        ]
    ];

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            __DIR__ . '/../Listeners',
        ];
    }
}
