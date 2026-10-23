<?php

namespace Tests\Feature;

use App\Models\BalanceSheetItem;
use App\Models\CashFlowEntry;
use App\Models\GeneralLedgerEntry;
use App\Models\ProfitLossItem;
use App\Models\TrialBalanceAccount;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test Balance Sheet DataTables AJAX and show endpoint.
     */
    public function test_balance_sheet_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $item = BalanceSheetItem::create([
            'account_code' => '1010',
            'account_name' => 'Cash and Cash Equivalents',
            'category' => 'Current Assets',
            'as_of_date' => '2026-10-20',
            'amount' => 450000.00,
            'previous_amount' => 410000.00,
            'currency' => 'USD',
            'status' => 'Active',
            'notes' => 'Audited balance as of cutoff',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.financial.balance-sheet.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
            ->assertJsonFragment(['account_code' => '1010']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.financial.balance-sheet.show', $item));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'account_code' => '1010',
                    'account_name' => 'Cash and Cash Equivalents',
                    'category' => 'Current Assets',
                ],
            ]);
    }

    /**
     * Test Profit & Loss DataTables AJAX and show endpoint.
     */
    public function test_profit_loss_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $item = ProfitLossItem::create([
            'account_code' => '4010',
            'account_name' => 'Commercial Fuel Sales',
            'category' => 'Operating Revenue',
            'doc_date' => '2026-10-20',
            'amount' => 680000.00,
            'previous_period_amount' => 620000.00,
            'budget_amount' => 650000.00,
            'currency' => 'USD',
            'department' => 'Sales',
            'notes' => 'Commercial sales',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.financial.profit-loss.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
            ->assertJsonFragment(['account_code' => '4010']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.financial.profit-loss.show', $item));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'account_code' => '4010',
                    'category' => 'Operating Revenue',
                ],
            ]);
    }

    /**
     * Test Cash Flow DataTables AJAX and show endpoint.
     */
    public function test_cash_flow_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $entry = CashFlowEntry::create([
            'reference_num' => 'CF-TEST-001',
            'doc_date' => '2026-10-15',
            'activity_type' => 'Operating Activities',
            'flow_type' => 'Inflow',
            'description' => 'Collections from Customer AR Invoices',
            'account_name' => 'Operating Cash Account',
            'inflow_amount' => 450000.00,
            'outflow_amount' => 0.00,
            'net_amount' => 450000.00,
            'payment_method' => 'Wire Transfer',
            'currency' => 'USD',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.financial.cash-flow.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
            ->assertJsonFragment(['reference_num' => 'CF-TEST-001']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.financial.cash-flow.show', $entry));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'reference_num' => 'CF-TEST-001',
                    'activity_type' => 'Operating Activities',
                    'flow_type' => 'Inflow',
                ],
            ]);
    }

    /**
     * Test Trial Balance DataTables AJAX and show endpoint.
     */
    public function test_trial_balance_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $acc = TrialBalanceAccount::create([
            'account_code' => '1010',
            'account_name' => 'Cash and Cash Equivalents',
            'classification' => 'Asset',
            'statement_type' => 'Balance Sheet',
            'doc_date' => '2026-10-23',
            'beginning_balance' => 400000.00,
            'debit_amount' => 250000.00,
            'credit_amount' => 200000.00,
            'ending_balance' => 450000.00,
            'currency' => 'USD',
            'status' => 'Active',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.financial.trial-balance.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
            ->assertJsonFragment(['account_code' => '1010']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.financial.trial-balance.show', $acc));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'account_code' => '1010',
                    'classification' => 'Asset',
                    'ending_balance' => 450000.00,
                ],
            ]);
    }

    /**
     * Test General Ledger DataTables AJAX and show endpoint.
     */
    public function test_general_ledger_endpoints(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $entry = GeneralLedgerEntry::create([
            'entry_number' => 'JE-2026-999',
            'doc_date' => '2026-10-22',
            'account_code' => '1010',
            'account_name' => 'Cash and Cash Equivalents',
            'account_type' => 'Asset',
            'source_doc_type' => 'Incoming Payment',
            'source_doc_num' => 'IN-20261022-001',
            'description' => 'Customer payment received for bulk diesel delivery',
            'debit' => 35000.00,
            'credit' => 0.00,
            'balance' => 450000.00,
            'currency' => 'USD',
            'posted_by' => 'Sokha Meng',
        ]);

        $listResponse = $this->actingAs($admin)->getJson(route('reports.financial.general-ledger.index').'?draw=1');
        $listResponse->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
            ->assertJsonFragment(['entry_number' => 'JE-2026-999']);

        $showResponse = $this->actingAs($admin)->getJson(route('reports.financial.general-ledger.show', $entry));
        $showResponse->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'entry_number' => 'JE-2026-999',
                    'source_doc_type' => 'Incoming Payment',
                ],
            ]);
    }

    /**
     * Test date range filter on General Ledger.
     */
    public function test_general_ledger_date_range_filter(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        GeneralLedgerEntry::create([
            'entry_number' => 'JE-MAY-001',
            'doc_date' => '2026-05-01',
            'account_code' => '1010',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'source_doc_type' => 'Journal Voucher',
            'source_doc_num' => 'JV-MAY-01',
            'description' => 'May transaction',
            'debit' => 1000.00,
            'credit' => 0.00,
            'balance' => 1000.00,
            'currency' => 'USD',
            'posted_by' => 'System',
        ]);

        GeneralLedgerEntry::create([
            'entry_number' => 'JE-OCT-001',
            'doc_date' => '2026-10-15',
            'account_code' => '1010',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'source_doc_type' => 'Journal Voucher',
            'source_doc_num' => 'JV-OCT-01',
            'description' => 'October transaction',
            'debit' => 2000.00,
            'credit' => 0.00,
            'balance' => 3000.00,
            'currency' => 'USD',
            'posted_by' => 'System',
        ]);

        // Filter for October only
        $response = $this->actingAs($admin)->getJson(route('reports.financial.general-ledger.index', [
            'draw' => 1,
            'created_date' => '2026-10-01 to 2026-10-31',
        ]));

        $response->assertOk();
        $response->assertJsonFragment(['entry_number' => 'JE-OCT-001']);
        $response->assertJsonMissing(['entry_number' => 'JE-MAY-001']);
    }

    /**
     * Test that financial report pages render with definition and filter toggle buttons.
     */
    public function test_financial_report_views_render_definition_and_filter_toggles(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $routes = [
            'reports.financial.balance-sheet.index' => ['#bs-definition-card', '#bs-filter-card'],
            'reports.financial.profit-loss.index' => ['#pl-definition-card', '#pl-filter-card'],
            'reports.financial.cash-flow.index' => ['#cf-definition-card', '#cf-filter-card'],
            'reports.financial.trial-balance.index' => ['#tb-definition-card', '#tb-filter-card'],
            'reports.financial.general-ledger.index' => ['#gl-definition-card', '#gl-filter-card'],
        ];

        foreach ($routes as $routeName => [$defTarget, $filterTarget]) {
            $response = $this->actingAs($admin)->get(route($routeName));
            $response->assertOk();
            $response->assertSee('btn-definition-toggle');
            $response->assertSee('btn-filter-toggle');
            $response->assertSee('data-target="'.$defTarget.'"', false);
            $response->assertSee('data-target="'.$filterTarget.'"', false);
            $response->assertSee(ltrim($defTarget, '#'));
            $response->assertSee('btn-close-definition');

            $html = $response->getContent();
            $filterPos = strpos($html, 'btn-filter-toggle');
            $defPos = strpos($html, 'btn-definition-toggle');
            $this->assertNotFalse($filterPos);
            $this->assertNotFalse($defPos);
            $this->assertLessThan($defPos, $filterPos, "On {$routeName}, Filter button should appear before Definition button so Definition is on the rightest.");
        }
    }

    /**
     * Test that the horizontal navigation renders the Financial reports dropdown menu.
     */
    public function test_horizontal_nav_renders_financial_reports_menu(): void
    {
        $admin = User::where('email', 'admin@twareports.test')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertOk();

        // Horizontal navigation element
        $response->assertSee('class="horizontal-nav"', false);
        $response->assertSee('id="horizontal-report-dropdown"', false);

        // All 3 report categories should be present
        $response->assertSee('Purchase');
        $response->assertSee('Sales');
        $response->assertSee('Financial');

        // All 5 financial report routes should be present in horizontal nav
        $response->assertSee(route('reports.financial.balance-sheet.index'));
        $response->assertSee(route('reports.financial.profit-loss.index'));
        $response->assertSee(route('reports.financial.cash-flow.index'));
        $response->assertSee(route('reports.financial.trial-balance.index'));
        $response->assertSee(route('reports.financial.general-ledger.index'));
    }
}
