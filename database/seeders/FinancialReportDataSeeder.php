<?php

namespace Database\Seeders;

use App\Models\BalanceSheetItem;
use App\Models\CashFlowEntry;
use App\Models\GeneralLedgerEntry;
use App\Models\ProfitLossItem;
use App\Models\TrialBalanceAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialReportDataSeeder extends Seeder
{
    /**
     * Run the database seeds for all 5 financial modules.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $thisWeek = Carbon::today()->startOfWeek()->addDays(2);
        $thisMonth = Carbon::today()->startOfMonth()->addDays(5);
        $lastMonth = Carbon::today()->subMonth()->startOfMonth()->addDays(10);
        $twoMonthsAgo = Carbon::today()->subMonths(2)->startOfMonth()->addDays(12);

        $dates = [
            $today->format('Y-m-d'),
            $thisWeek->format('Y-m-d'),
            $thisMonth->format('Y-m-d'),
            $lastMonth->format('Y-m-d'),
            $twoMonthsAgo->format('Y-m-d'),
        ];

        // 1. Seed Balance Sheet Items
        $balanceSheetAccounts = [
            ['code' => '1010', 'name' => 'Cash and Cash Equivalents', 'category' => 'Current Assets', 'amount' => 450000.00, 'prev' => 410000.00],
            ['code' => '1020', 'name' => 'Petty Cash - Main Office', 'category' => 'Current Assets', 'amount' => 5000.00, 'prev' => 5000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable - Trade', 'category' => 'Current Assets', 'amount' => 320000.00, 'prev' => 290000.00],
            ['code' => '1200', 'name' => 'Merchandise Inventory', 'category' => 'Current Assets', 'amount' => 580000.00, 'prev' => 530000.00],
            ['code' => '1300', 'name' => 'Prepaid Insurance & Expenses', 'category' => 'Current Assets', 'amount' => 24000.00, 'prev' => 28000.00],
            ['code' => '1510', 'name' => 'Land and Buildings', 'category' => 'Non-Current Assets', 'amount' => 1250000.00, 'prev' => 1250000.00],
            ['code' => '1520', 'name' => 'Machinery & Storage Tanks', 'category' => 'Non-Current Assets', 'amount' => 840000.00, 'prev' => 810000.00],
            ['code' => '1530', 'name' => 'Fleet Logistics Vehicles', 'category' => 'Non-Current Assets', 'amount' => 420000.00, 'prev' => 460000.00],
            ['code' => '1590', 'name' => 'Accumulated Depreciation', 'category' => 'Non-Current Assets', 'amount' => -380000.00, 'prev' => -340000.00],
            ['code' => '2010', 'name' => 'Accounts Payable - Vendors', 'category' => 'Current Liabilities', 'amount' => 285000.00, 'prev' => 260000.00],
            ['code' => '2020', 'name' => 'Accrued Payroll & Taxes', 'category' => 'Current Liabilities', 'amount' => 64000.00, 'prev' => 58000.00],
            ['code' => '2030', 'name' => 'Short-Term Bank Borrowings', 'category' => 'Current Liabilities', 'amount' => 120000.00, 'prev' => 150000.00],
            ['code' => '2510', 'name' => 'Long-Term Facility Loan', 'category' => 'Long-Term Liabilities', 'amount' => 650000.00, 'prev' => 700000.00],
            ['code' => '3010', 'name' => 'Common Capital Stock', 'category' => 'Equity', 'amount' => 1500000.00, 'prev' => 1500000.00],
            ['code' => '3020', 'name' => 'Retained Earnings', 'category' => 'Equity', 'amount' => 890000.00, 'prev' => 775000.00],
        ];

        DB::table('balance_sheet_items')->truncate();
        $bsData = [];
        foreach ($dates as $dateIdx => $d) {
            foreach ($balanceSheetAccounts as $acc) {
                $multiplier = 1 + ($dateIdx * 0.02);
                $bsData[] = [
                    'account_code' => $acc['code'],
                    'account_name' => $acc['name'],
                    'category' => $acc['category'],
                    'as_of_date' => $d,
                    'amount' => round($acc['amount'] * $multiplier, 2),
                    'previous_amount' => $acc['prev'],
                    'currency' => 'USD',
                    'status' => 'Active',
                    'notes' => 'Audited balance as of statement cutoff',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        BalanceSheetItem::insert($bsData);

        // 2. Seed Profit & Loss Items
        $plAccounts = [
            ['code' => '4010', 'name' => 'Commercial Fuel Sales', 'category' => 'Operating Revenue', 'amount' => 680000.00, 'prev' => 620000.00, 'budget' => 650000.00, 'dept' => 'Sales'],
            ['code' => '4020', 'name' => 'Lubricants & Chemical Sales', 'category' => 'Operating Revenue', 'amount' => 195000.00, 'prev' => 180000.00, 'budget' => 190000.00, 'dept' => 'Sales'],
            ['code' => '4030', 'name' => 'Logistics & Delivery Freight', 'category' => 'Operating Revenue', 'amount' => 85000.00, 'prev' => 78000.00, 'budget' => 80000.00, 'dept' => 'Logistics'],
            ['code' => '5010', 'name' => 'Cost of Petroleum Goods Sold', 'category' => 'Cost of Goods Sold', 'amount' => 540000.00, 'prev' => 495000.00, 'budget' => 520000.00, 'dept' => 'Operations'],
            ['code' => '5020', 'name' => 'Direct Storage & Terminal Cost', 'category' => 'Cost of Goods Sold', 'amount' => 42000.00, 'prev' => 39000.00, 'budget' => 40000.00, 'dept' => 'Terminal'],
            ['code' => '6010', 'name' => 'Salaries, Allowances & Benefits', 'category' => 'Operating Expense', 'amount' => 78000.00, 'prev' => 74000.00, 'budget' => 76000.00, 'dept' => 'HR & Admin'],
            ['code' => '6020', 'name' => 'Depot Maintenance & Inspection', 'category' => 'Operating Expense', 'amount' => 22000.00, 'prev' => 19000.00, 'budget' => 20000.00, 'dept' => 'Maintenance'],
            ['code' => '6030', 'name' => 'Vehicle Fleet Fuel & Repairs', 'category' => 'Operating Expense', 'amount' => 31000.00, 'prev' => 28000.00, 'budget' => 30000.00, 'dept' => 'Fleet'],
            ['code' => '6040', 'name' => 'Marketing & Client Relations', 'category' => 'Operating Expense', 'amount' => 14000.00, 'prev' => 12000.00, 'budget' => 15000.00, 'dept' => 'Marketing'],
            ['code' => '6050', 'name' => 'Office Rent & Utilities', 'category' => 'Operating Expense', 'amount' => 16500.00, 'prev' => 16500.00, 'budget' => 16500.00, 'dept' => 'Administration'],
            ['code' => '7010', 'name' => 'Bank Interest & Financing Charges', 'category' => 'Non-Operating', 'amount' => 8500.00, 'prev' => 9200.00, 'budget' => 9000.00, 'dept' => 'Finance'],
            ['code' => '7020', 'name' => 'Foreign Exchange Gain / (Loss)', 'category' => 'Non-Operating', 'amount' => 4200.00, 'prev' => 2100.00, 'budget' => 3000.00, 'dept' => 'Finance'],
            ['code' => '8010', 'name' => 'Corporate Income Tax Provision', 'category' => 'Tax Expense', 'amount' => 36000.00, 'prev' => 31000.00, 'budget' => 34000.00, 'dept' => 'Finance'],
        ];

        DB::table('profit_loss_items')->truncate();
        $plData = [];
        foreach ($dates as $dateIdx => $d) {
            foreach ($plAccounts as $acc) {
                $multiplier = 1 + ($dateIdx * 0.015);
                $plData[] = [
                    'account_code' => $acc['code'],
                    'account_name' => $acc['name'],
                    'category' => $acc['category'],
                    'doc_date' => $d,
                    'amount' => round($acc['amount'] * $multiplier, 2),
                    'previous_period_amount' => $acc['prev'],
                    'budget_amount' => $acc['budget'],
                    'currency' => 'USD',
                    'department' => $acc['dept'],
                    'notes' => 'Monthly profit & loss entry',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        ProfitLossItem::insert($plData);

        // 3. Seed Cash Flow Entries
        DB::table('cash_flow_entries')->truncate();
        $cfActivities = [
            ['act' => 'Operating Activities', 'flow' => 'Inflow', 'desc' => 'Collections from Customer AR Invoices', 'acc' => 'Operating Cash Account', 'in' => 450000.00, 'out' => 0.00, 'method' => 'Wire Transfer'],
            ['act' => 'Operating Activities', 'flow' => 'Outflow', 'desc' => 'Settlement of Vendor Petroleum Purchases', 'acc' => 'Trade AP Outgoing', 'in' => 0.00, 'out' => 320000.00, 'method' => 'Bank Transfer'],
            ['act' => 'Operating Activities', 'flow' => 'Outflow', 'desc' => 'Payroll & Staff Benefit Disbursement', 'acc' => 'Payroll Cash Account', 'in' => 0.00, 'out' => 78000.00, 'method' => 'ACH Direct Deposit'],
            ['act' => 'Operating Activities', 'flow' => 'Outflow', 'desc' => 'Payment of Taxes & Statutory Duties', 'acc' => 'Tax Escrow Account', 'in' => 0.00, 'out' => 36000.00, 'method' => 'Bank Transfer'],
            ['act' => 'Investing Activities', 'flow' => 'Outflow', 'desc' => 'Capital Acquisition of 2 Heavy Tanker Trucks', 'acc' => 'Fleet Asset Account', 'in' => 0.00, 'out' => 140000.00, 'method' => 'Bank Check'],
            ['act' => 'Investing Activities', 'flow' => 'Inflow', 'desc' => 'Disposal of Retired Logistics Equipment', 'acc' => 'Capital Reserve Account', 'in' => 25000.00, 'out' => 0.00, 'method' => 'Wire Transfer'],
            ['act' => 'Investing Activities', 'flow' => 'Outflow', 'desc' => 'Depot Safety Valve & Automation Upgrade', 'acc' => 'Facility Improvement', 'in' => 0.00, 'out' => 45000.00, 'method' => 'Bank Transfer'],
            ['act' => 'Financing Activities', 'flow' => 'Inflow', 'desc' => 'Short-Term Working Capital Facility Drawdown', 'acc' => 'Credit Facility Account', 'in' => 100000.00, 'out' => 0.00, 'method' => 'Wire Transfer'],
            ['act' => 'Financing Activities', 'flow' => 'Outflow', 'desc' => 'Scheduled Amortization of Bank Term Loan', 'acc' => 'Debt Servicing Account', 'in' => 0.00, 'out' => 50000.00, 'method' => 'Wire Transfer'],
            ['act' => 'Financing Activities', 'flow' => 'Outflow', 'desc' => 'Interim Dividend Distribution to Shareholders', 'acc' => 'Dividend Disbursement', 'in' => 0.00, 'out' => 60000.00, 'method' => 'Check'],
        ];

        $cfData = [];
        $seq = 1;
        foreach ($dates as $d) {
            foreach ($cfActivities as $cf) {
                $ref = sprintf('CF-%s-%04d', str_replace('-', '', $d), $seq++);
                $net = $cf['in'] > 0 ? $cf['in'] : (-1 * $cf['out']);
                $cfData[] = [
                    'reference_num' => $ref,
                    'doc_date' => $d,
                    'activity_type' => $cf['act'],
                    'flow_type' => $cf['flow'],
                    'description' => $cf['desc'],
                    'account_name' => $cf['acc'],
                    'inflow_amount' => $cf['in'],
                    'outflow_amount' => $cf['out'],
                    'net_amount' => $net,
                    'payment_method' => $cf['method'],
                    'currency' => 'USD',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        CashFlowEntry::insert($cfData);

        // 4. Seed Trial Balance Accounts
        DB::table('trial_balance_accounts')->truncate();
        $tbAccounts = [
            ['code' => '1010', 'name' => 'Cash and Cash Equivalents', 'class' => 'Asset', 'stmt' => 'Balance Sheet', 'begin' => 400000.00, 'debit' => 250000.00, 'credit' => 200000.00, 'end' => 450000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable - Trade', 'class' => 'Asset', 'stmt' => 'Balance Sheet', 'begin' => 290000.00, 'debit' => 380000.00, 'credit' => 350000.00, 'end' => 320000.00],
            ['code' => '1200', 'name' => 'Merchandise Inventory', 'class' => 'Asset', 'stmt' => 'Balance Sheet', 'begin' => 530000.00, 'debit' => 590000.00, 'credit' => 540000.00, 'end' => 580000.00],
            ['code' => '1500', 'name' => 'Property, Plant & Equipment', 'class' => 'Asset', 'stmt' => 'Balance Sheet', 'begin' => 2520000.00, 'debit' => 70000.00, 'credit' => 80000.00, 'end' => 2510000.00],
            ['code' => '2010', 'name' => 'Accounts Payable - Vendors', 'class' => 'Liability', 'stmt' => 'Balance Sheet', 'begin' => 260000.00, 'debit' => 320000.00, 'credit' => 345000.00, 'end' => 285000.00],
            ['code' => '2510', 'name' => 'Bank Term Loans Payable', 'class' => 'Liability', 'stmt' => 'Balance Sheet', 'begin' => 850000.00, 'debit' => 50000.00, 'credit' => 0.00, 'end' => 800000.00],
            ['code' => '3010', 'name' => 'Common Capital Stock', 'class' => 'Equity', 'stmt' => 'Balance Sheet', 'begin' => 1500000.00, 'debit' => 0.00, 'credit' => 0.00, 'end' => 1500000.00],
            ['code' => '3020', 'name' => 'Retained Earnings', 'class' => 'Equity', 'stmt' => 'Balance Sheet', 'begin' => 775000.00, 'debit' => 60000.00, 'credit' => 175000.00, 'end' => 890000.00],
            ['code' => '4010', 'name' => 'Sales Revenue - Wholesale Fuel', 'class' => 'Revenue', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 0.00, 'credit' => 960000.00, 'end' => 960000.00],
            ['code' => '5010', 'name' => 'Cost of Goods Sold - Fuel', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 582000.00, 'credit' => 0.00, 'end' => 582000.00],
            ['code' => '6010', 'name' => 'Operating Payroll & Benefits', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 78000.00, 'credit' => 0.00, 'end' => 78000.00],
            ['code' => '6020', 'name' => 'Logistics Freight & Delivery', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 31000.00, 'credit' => 0.00, 'end' => 31000.00],
            ['code' => '6030', 'name' => 'General Administration & Rent', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 38500.00, 'credit' => 0.00, 'end' => 38500.00],
            ['code' => '7010', 'name' => 'Finance & Interest Charges', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 8500.00, 'credit' => 0.00, 'end' => 8500.00],
            ['code' => '8010', 'name' => 'Income Tax Expense', 'class' => 'Expense', 'stmt' => 'Profit & Loss', 'begin' => 0.00, 'debit' => 36000.00, 'credit' => 0.00, 'end' => 36000.00],
        ];

        $tbData = [];
        foreach ($dates as $d) {
            foreach ($tbAccounts as $tb) {
                $tbData[] = [
                    'account_code' => $tb['code'],
                    'account_name' => $tb['name'],
                    'classification' => $tb['class'],
                    'statement_type' => $tb['stmt'],
                    'doc_date' => $d,
                    'beginning_balance' => $tb['begin'],
                    'debit_amount' => $tb['debit'],
                    'credit_amount' => $tb['credit'],
                    'ending_balance' => $tb['end'],
                    'currency' => 'USD',
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        TrialBalanceAccount::insert($tbData);

        // 5. Seed General Ledger Entries
        DB::table('general_ledger_entries')->truncate();
        $journalDescriptions = [
            ['code' => '1010', 'name' => 'Cash and Cash Equivalents', 'type' => 'Asset', 'doc_type' => 'Incoming Payment', 'desc' => 'Customer payment received for bulk diesel delivery', 'dr' => 35000.00, 'cr' => 0.00, 'bal' => 450000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable - Trade', 'type' => 'Asset', 'doc_type' => 'Incoming Payment', 'desc' => 'Offset AR for customer invoice payment', 'dr' => 0.00, 'cr' => 35000.00, 'bal' => 320000.00],
            ['code' => '5010', 'name' => 'Cost of Petroleum Goods Sold', 'type' => 'Expense', 'doc_type' => 'AP Invoice', 'desc' => 'Recorded petroleum stock intake from Chevron Asia', 'dr' => 125000.00, 'cr' => 0.00, 'bal' => 540000.00],
            ['code' => '2010', 'name' => 'Accounts Payable - Vendors', 'type' => 'Liability', 'doc_type' => 'AP Invoice', 'desc' => 'Recognized trade liability to Chevron Asia', 'dr' => 0.00, 'cr' => 125000.00, 'bal' => 285000.00],
            ['code' => '2010', 'name' => 'Accounts Payable - Vendors', 'type' => 'Liability', 'doc_type' => 'Outgoing Payment', 'desc' => 'Bank wire payment to TotalEnergies Marketing', 'dr' => 88000.00, 'cr' => 0.00, 'bal' => 197000.00],
            ['code' => '1010', 'name' => 'Cash and Cash Equivalents', 'type' => 'Asset', 'doc_type' => 'Outgoing Payment', 'desc' => 'Settled vendor invoice via ABA Bank wire', 'dr' => 0.00, 'cr' => 88000.00, 'bal' => 362000.00],
            ['code' => '6010', 'name' => 'Salaries, Allowances & Benefits', 'type' => 'Expense', 'doc_type' => 'Journal Voucher', 'desc' => 'Bi-weekly operations payroll accrual', 'dr' => 39000.00, 'cr' => 0.00, 'bal' => 78000.00],
            ['code' => '2020', 'name' => 'Accrued Payroll & Taxes', 'type' => 'Liability', 'doc_type' => 'Journal Voucher', 'desc' => 'Accrued payroll liability to operations staff', 'dr' => 0.00, 'cr' => 39000.00, 'bal' => 64000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable - Trade', 'type' => 'Asset', 'doc_type' => 'AR Invoice', 'desc' => 'Billed Phnom Penh Logistics Hub for heavy fuel shipment', 'dr' => 48500.00, 'cr' => 0.00, 'bal' => 368500.00],
            ['code' => '4010', 'name' => 'Commercial Fuel Sales', 'type' => 'Revenue', 'doc_type' => 'AR Invoice', 'desc' => 'Recognized commercial fuel revenue', 'dr' => 0.00, 'cr' => 48500.00, 'bal' => 680000.00],
            ['code' => '6030', 'name' => 'Vehicle Fleet Fuel & Repairs', 'type' => 'Expense', 'doc_type' => 'Outgoing Payment', 'desc' => 'Fleet tire replacement and routine maintenance service', 'dr' => 7400.00, 'cr' => 0.00, 'bal' => 31000.00],
            ['code' => '1010', 'name' => 'Cash and Cash Equivalents', 'type' => 'Asset', 'doc_type' => 'Outgoing Payment', 'desc' => 'Disbursed check for maintenance services', 'dr' => 0.00, 'cr' => 7400.00, 'bal' => 354600.00],
        ];

        $glData = [];
        $glSeq = 1;
        $users = ['Sokha Meng', 'Vireak Roth', 'Borey Chan', 'Dara Som', 'Kallyan Keo'];
        foreach ($dates as $d) {
            foreach ($journalDescriptions as $idx => $jd) {
                $jeNum = sprintf('JE-%s-%04d', str_replace('-', '', $d), $glSeq++);
                $sourceDoc = sprintf('%s-%s-%03d', strtoupper(substr($jd['doc_type'], 0, 3)), str_replace('-', '', $d), $idx + 1);
                $glData[] = [
                    'entry_number' => $jeNum,
                    'doc_date' => $d,
                    'account_code' => $jd['code'],
                    'account_name' => $jd['name'],
                    'account_type' => $jd['type'],
                    'source_doc_type' => $jd['doc_type'],
                    'source_doc_num' => $sourceDoc,
                    'description' => $jd['desc'],
                    'debit' => $jd['dr'],
                    'credit' => $jd['cr'],
                    'balance' => $jd['bal'],
                    'currency' => 'USD',
                    'posted_by' => $users[$idx % count($users)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        GeneralLedgerEntry::insert($glData);
    }
}
