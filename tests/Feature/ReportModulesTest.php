<?php

namespace Tests\Feature;

use App\Models\ApInvoice;
use App\Models\ArInvoice;
use App\Models\Deposit;
use App\Models\IncomingPayment;
use App\Models\OutgoingPayment;
use App\Models\PurchaseOrder;
use App\Models\SalesPurchaseOrder;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test guests are redirected to login when trying to access any report module.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $routes = [
            'reports.purchase.purchase-orders.index',
            'reports.purchase.ap-invoices.index',
            'reports.purchase.outgoing-payments.index',
            'reports.sales.purchase-orders.index',
            'reports.sales.ar-invoices.index',
            'reports.sales.incoming-payments.index',
            'reports.sales.deposits.index',
            'reports.financial.balance-sheet.index',
            'reports.financial.profit-loss.index',
            'reports.financial.cash-flow.index',
            'reports.financial.trial-balance.index',
            'reports.financial.general-ledger.index',
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Test a user without report permissions gets 403 Forbidden.
     */
    public function test_user_without_permissions_is_forbidden(): void
    {
        $unauthorizedUser = User::factory()->create([
            'status' => 'active',
        ]);

        $routes = [
            'reports.purchase.purchase-orders.index',
            'reports.purchase.ap-invoices.index',
            'reports.purchase.outgoing-payments.index',
            'reports.sales.purchase-orders.index',
            'reports.sales.ar-invoices.index',
            'reports.sales.incoming-payments.index',
            'reports.sales.deposits.index',
            'reports.financial.balance-sheet.index',
            'reports.financial.profit-loss.index',
            'reports.financial.cash-flow.index',
            'reports.financial.trial-balance.index',
            'reports.financial.general-ledger.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($unauthorizedUser)->get(route($route));
            $response->assertForbidden();
        }
    }

    /**
     * Test Super Admin and Admin can access all report index pages.
     */
    public function test_authorized_user_can_access_all_report_views(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $routes = [
            'reports.purchase.purchase-orders.index',
            'reports.purchase.ap-invoices.index',
            'reports.purchase.outgoing-payments.index',
            'reports.sales.purchase-orders.index',
            'reports.sales.ar-invoices.index',
            'reports.sales.incoming-payments.index',
            'reports.sales.deposits.index',
            'reports.financial.balance-sheet.index',
            'reports.financial.profit-loss.index',
            'reports.financial.cash-flow.index',
            'reports.financial.trial-balance.index',
            'reports.financial.general-ledger.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get(route($route));
            $response->assertOk();
        }
    }

    /**
     * Test navigation displays Report, Purchase, Sales, and Financial menus for authorized users.
     */
    public function test_navigation_displays_report_hierarchy(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Report');
        $response->assertSee('Purchase');
        $response->assertSee('Purchase Order');
        $response->assertSee('AP Invoice');
        $response->assertSee('Outgoing Payment');
        $response->assertSee('Sales');
        $response->assertSee('AR Invoice');
        $response->assertSee('Incoming Payment');
        $response->assertSee('Deposit');
        $response->assertSee('Financial');
        $response->assertSee('Balance Sheet');
        $response->assertSee('Profit &amp; Loss', false);
        $response->assertSee('Cash Flow');
        $response->assertSee('Trial Balance');
        $response->assertSee('General Ledger');
    }

    /**
     * Test DataTables AJAX requests return records for Purchase Order report.
     */
    public function test_purchase_order_datatables_ajax_returns_records(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        PurchaseOrder::create([
            'doc_num' => 'PO-TEST-001',
            'vendor_code' => 'VEND-001',
            'vendor_name' => 'Chip Mong Insee Cement Corp',
            'doc_date' => '2026-05-10',
            'delivery_date' => '2026-05-20',
            'currency' => 'USD',
            'subtotal' => 1000,
            'tax_amount' => 100,
            'discount_amount' => 0,
            'doc_total' => 1100,
            'status' => 'Open',
            'buyer_name' => 'Sokha Mean',
            'payment_terms' => 'Net 30',
        ]);

        $response = $this->actingAs($admin)->getJson(route('reports.purchase.purchase-orders.index').'?draw=1');

        $response->assertOk();
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $response->assertJsonFragment([
            'doc_num' => 'PO-TEST-001',
        ]);
    }

    /**
     * Test Purchase Order show endpoint returns json details.
     */
    public function test_purchase_order_show_endpoint_returns_json(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $po = PurchaseOrder::create([
            'doc_num' => 'PO-TEST-002',
            'vendor_code' => 'VEND-002',
            'vendor_name' => 'Soma Construction & Development',
            'doc_date' => '2026-05-12',
            'delivery_date' => '2026-05-25',
            'currency' => 'USD',
            'subtotal' => 5000,
            'tax_amount' => 500,
            'discount_amount' => 0,
            'doc_total' => 5500,
            'status' => 'Pending',
            'buyer_name' => 'Vireak Chan',
            'payment_terms' => 'Net 60',
        ]);

        $response = $this->actingAs($admin)->getJson(route('reports.purchase.purchase-orders.show', $po));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'doc_num' => 'PO-TEST-002',
                'vendor_name' => 'Soma Construction & Development',
            ],
        ]);
    }

    /**
     * Test AP Invoice DataTables AJAX and show endpoint.
     */
    public function test_ap_invoice_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $inv = ApInvoice::create([
            'invoice_num' => 'INV-TEST-001',
            'po_reference' => 'PO-TEST-001',
            'vendor_code' => 'VEND-001',
            'vendor_name' => 'Chip Mong Insee Cement Corp',
            'doc_date' => '2026-05-15',
            'due_date' => '2026-06-15',
            'currency' => 'USD',
            'subtotal' => 1000,
            'tax_amount' => 100,
            'doc_total' => 1100,
            'paid_amount' => 0,
            'balance_due' => 1100,
            'status' => 'Unpaid',
            'payment_terms' => 'Net 30',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.purchase.ap-invoices.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['invoice_num' => 'INV-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.purchase.ap-invoices.show', $inv));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['invoice_num' => 'INV-TEST-001'],
            ]);
    }

    /**
     * Test Outgoing Payment DataTables AJAX and show endpoint.
     */
    public function test_outgoing_payment_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $pay = OutgoingPayment::create([
            'payment_num' => 'PAY-TEST-001',
            'doc_date' => '2026-05-18',
            'vendor_code' => 'VEND-001',
            'vendor_name' => 'Chip Mong Insee Cement Corp',
            'payment_method' => 'Bank Transfer',
            'reference_num' => 'FT-99881',
            'currency' => 'USD',
            'amount_paid' => 1100,
            'bank_account' => 'ABA Bank - 000111222',
            'status' => 'Completed',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.purchase.outgoing-payments.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['payment_num' => 'PAY-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.purchase.outgoing-payments.show', $pay));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['payment_num' => 'PAY-TEST-001'],
            ]);
    }

    /**
     * Test Sales Purchase Order DataTables AJAX and show endpoint.
     */
    public function test_sales_purchase_order_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $spo = SalesPurchaseOrder::create([
            'doc_num' => 'SO-TEST-001',
            'customer_code' => 'CUST-001',
            'customer_name' => 'Canadia Bank Plc',
            'customer_ref_num' => 'PO-CB-2026',
            'doc_date' => '2026-05-10',
            'delivery_date' => '2026-05-25',
            'currency' => 'USD',
            'subtotal' => 2000,
            'tax_amount' => 200,
            'discount_amount' => 50,
            'doc_total' => 2150,
            'status' => 'Open',
            'sales_employee' => 'Dara Sam',
            'payment_terms' => 'Net 30',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.sales.purchase-orders.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['doc_num' => 'SO-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.sales.purchase-orders.show', $spo));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['doc_num' => 'SO-TEST-001'],
            ]);
    }

    /**
     * Test AR Invoice DataTables AJAX and show endpoint.
     */
    public function test_ar_invoice_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $inv = ArInvoice::create([
            'invoice_num' => 'AR-INV-TEST-001',
            'order_reference' => 'SO-TEST-001',
            'customer_code' => 'CUST-001',
            'customer_name' => 'Canadia Bank Plc',
            'doc_date' => '2026-05-15',
            'due_date' => '2026-06-15',
            'currency' => 'USD',
            'subtotal' => 2000,
            'tax_amount' => 200,
            'doc_total' => 2200,
            'paid_amount' => 2200,
            'balance_due' => 0,
            'status' => 'Paid',
            'sales_employee' => 'Dara Sam',
            'payment_terms' => 'Net 30',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.sales.ar-invoices.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['invoice_num' => 'AR-INV-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.sales.ar-invoices.show', $inv));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['invoice_num' => 'AR-INV-TEST-001'],
            ]);
    }

    /**
     * Test Incoming Payment DataTables AJAX and show endpoint.
     */
    public function test_incoming_payment_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $pay = IncomingPayment::create([
            'payment_num' => 'IN-PAY-TEST-001',
            'doc_date' => '2026-05-20',
            'customer_code' => 'CUST-001',
            'customer_name' => 'Canadia Bank Plc',
            'payment_method' => 'Bank Transfer',
            'reference_num' => 'CAN-REF-001',
            'currency' => 'USD',
            'amount_received' => 2200,
            'bank_account' => 'Canadia Bank - 100200300',
            'status' => 'Cleared',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.sales.incoming-payments.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['payment_num' => 'IN-PAY-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.sales.incoming-payments.show', $pay));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['payment_num' => 'IN-PAY-TEST-001'],
            ]);
    }

    /**
     * Test Deposit DataTables AJAX and show endpoint.
     */
    public function test_deposit_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $dep = Deposit::create([
            'deposit_num' => 'DEP-TEST-001',
            'deposit_date' => '2026-05-22',
            'deposit_type' => 'Cash',
            'bank_name' => 'ABA Bank',
            'bank_account' => 'ABA - 001234567',
            'currency' => 'USD',
            'total_amount' => 5000,
            'reconciliation_status' => 'Reconciled',
            'status' => 'Posted',
            'journal_ref' => 'JRN-2026-001',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.sales.deposits.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonFragment(['deposit_num' => 'DEP-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.sales.deposits.show', $dep));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['deposit_num' => 'DEP-TEST-001'],
            ]);
    }
}
