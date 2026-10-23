<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds to populate 5,000 records for each report module.
     */
    public function run(): void
    {
        $faker = fake();
        $chunkSize = 500;
        $total = 5000;
        $now = now();

        $vendors = [
            ['code' => 'V1001', 'name' => 'TotalEnergies Marketing Ltd'],
            ['code' => 'V1002', 'name' => 'Chevron Asia Pacific Pte'],
            ['code' => 'V1003', 'name' => 'Caltex Lubricants Cambodia'],
            ['code' => 'V1004', 'name' => 'Shell Global Logistics AG'],
            ['code' => 'V1005', 'name' => 'PetroVietnam Trading Corp'],
            ['code' => 'V1006', 'name' => 'Siam Gas & Petrochemicals'],
            ['code' => 'V1007', 'name' => 'ExxonMobil Asia Pacific'],
            ['code' => 'V1008', 'name' => 'PTT Public Company Ltd'],
            ['code' => 'V1009', 'name' => 'Sinopec International Corp'],
            ['code' => 'V1010', 'name' => 'Idemitsu Kosan Trading'],
        ];

        $customers = [
            ['code' => 'C1001', 'name' => 'Phnom Penh Logistics Hub'],
            ['code' => 'C1002', 'name' => 'Angkor Express Transport'],
            ['code' => 'C1003', 'name' => 'Sihanoukville Port Services'],
            ['code' => 'C1004', 'name' => 'Battambang Agrico Ltd'],
            ['code' => 'C1005', 'name' => 'Mekong River Freight Co'],
            ['code' => 'C1006', 'name' => 'Royal Railway Cargo Line'],
            ['code' => 'C1007', 'name' => 'Kampot Logistics Depot'],
            ['code' => 'C1008', 'name' => 'Siem Reap Hospitality Supply'],
            ['code' => 'C1009', 'name' => 'Bavet Border Dry Port'],
            ['code' => 'C1010', 'name' => 'Kandal Industrial Park'],
        ];

        $buyers = ['Borey Chan', 'Sokha Meng', 'Vireak Roth', 'Dara Som', 'Kallyan Keo', 'Sreymom Pich'];
        $salesReps = ['Channary Heng', 'Phalla Bun', 'Rathana Chea', 'Vannak Nguon', 'Sophal Lim', 'Bopha Tep'];
        $banks = [
            ['name' => 'ABA Bank', 'account' => '001 234 567 (USD)'],
            ['name' => 'Canadia Bank', 'account' => '002 987 654 (USD)'],
            ['name' => 'ACLEDA Bank', 'account' => '003 456 789 (USD)'],
            ['name' => 'Sathapana Bank', 'account' => '004 112 233 (USD)'],
            ['name' => 'Foreign Trade Bank (FTB)', 'account' => '005 667 889 (USD)'],
        ];
        $paymentTerms = ['Net 30', 'Net 15', 'COD', 'Net 60', 'Immediate'];

        // 1. Purchase Orders (5,000 records)
        $this->command?->info('Seeding 5,000 Purchase Orders...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $vendor = $vendors[array_rand($vendors)];
                $subtotal = round($faker->randomFloat(2, 500, 75000), 2);
                $tax = round($subtotal * 0.10, 2);
                $discount = round($subtotal * $faker->randomElement([0, 0, 0.02, 0.05]), 2);
                $totalAmt = round($subtotal + $tax - $discount, 2);
                $docDate = $now->copy()->subDays(rand(1, 550));
                $deliveryDate = $docDate->copy()->addDays(rand(5, 30));

                $data[] = [
                    'doc_num' => sprintf('PO-%d-%05d', $docDate->year, $idx),
                    'vendor_code' => $vendor['code'],
                    'vendor_name' => $vendor['name'],
                    'doc_date' => $docDate->format('Y-m-d'),
                    'delivery_date' => $deliveryDate->format('Y-m-d'),
                    'currency' => 'USD',
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount,
                    'doc_total' => $totalAmt,
                    'status' => $faker->randomElement(['Open', 'Closed', 'Pending', 'Cancelled']),
                    'payment_terms' => $faker->randomElement($paymentTerms),
                    'buyer_name' => $faker->randomElement($buyers),
                    'remarks' => $faker->optional(0.4)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('purchase_orders')->insert($data);
        }

        // 2. AP Invoices (5,000 records)
        $this->command?->info('Seeding 5,000 AP Invoices...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $vendor = $vendors[array_rand($vendors)];
                $subtotal = round($faker->randomFloat(2, 600, 85000), 2);
                $tax = round($subtotal * 0.10, 2);
                $totalAmt = round($subtotal + $tax, 2);
                $status = $faker->randomElement(['Paid', 'Partially Paid', 'Unpaid', 'Cancelled']);
                $paid = match ($status) {
                    'Paid' => $totalAmt,
                    'Partially Paid' => round($totalAmt * 0.5, 2),
                    default => 0.00,
                };
                $balance = round($totalAmt - $paid, 2);
                $docDate = $now->copy()->subDays(rand(1, 550));
                $dueDate = $docDate->copy()->addDays(30);

                $data[] = [
                    'invoice_num' => sprintf('API-%d-%05d', $docDate->year, $idx),
                    'po_reference' => sprintf('PO-%d-%05d', $docDate->year, rand(1, 5000)),
                    'vendor_code' => $vendor['code'],
                    'vendor_name' => $vendor['name'],
                    'doc_date' => $docDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'currency' => 'USD',
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'doc_total' => $totalAmt,
                    'paid_amount' => $paid,
                    'balance_due' => $balance,
                    'status' => $status,
                    'payment_terms' => $faker->randomElement($paymentTerms),
                    'remarks' => $faker->optional(0.3)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('ap_invoices')->insert($data);
        }

        // 3. Outgoing Payments (5,000 records)
        $this->command?->info('Seeding 5,000 Outgoing Payments...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $vendor = $vendors[array_rand($vendors)];
                $bank = $banks[array_rand($banks)];
                $docDate = $now->copy()->subDays(rand(1, 550));
                $amount = round($faker->randomFloat(2, 300, 60000), 2);

                $data[] = [
                    'payment_num' => sprintf('OP-%d-%05d', $docDate->year, $idx),
                    'doc_date' => $docDate->format('Y-m-d'),
                    'vendor_code' => $vendor['code'],
                    'vendor_name' => $vendor['name'],
                    'payment_method' => $faker->randomElement(['Bank Transfer', 'Cheque', 'Cash']),
                    'reference_num' => sprintf('REF-%06d', rand(100000, 999999)),
                    'currency' => 'USD',
                    'amount_paid' => $amount,
                    'bank_account' => "{$bank['name']} - {$bank['account']}",
                    'status' => $faker->randomElement(['Completed', 'Pending', 'Voided']),
                    'remarks' => $faker->optional(0.25)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('outgoing_payments')->insert($data);
        }

        // 4. Sales Purchase Orders (5,000 records)
        $this->command?->info('Seeding 5,000 Sales Purchase Orders...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $customer = $customers[array_rand($customers)];
                $subtotal = round($faker->randomFloat(2, 800, 95000), 2);
                $tax = round($subtotal * 0.10, 2);
                $discount = round($subtotal * $faker->randomElement([0, 0, 0.03, 0.05]), 2);
                $totalAmt = round($subtotal + $tax - $discount, 2);
                $docDate = $now->copy()->subDays(rand(1, 550));
                $deliveryDate = $docDate->copy()->addDays(rand(7, 35));

                $data[] = [
                    'doc_num' => sprintf('SPO-%d-%05d', $docDate->year, $idx),
                    'customer_code' => $customer['code'],
                    'customer_name' => $customer['name'],
                    'customer_ref_num' => sprintf('CUST-PO-%05d', rand(1000, 99999)),
                    'doc_date' => $docDate->format('Y-m-d'),
                    'delivery_date' => $deliveryDate->format('Y-m-d'),
                    'currency' => 'USD',
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => $discount,
                    'doc_total' => $totalAmt,
                    'status' => $faker->randomElement(['Open', 'Closed', 'Processing', 'Draft']),
                    'sales_employee' => $faker->randomElement($salesReps),
                    'payment_terms' => $faker->randomElement($paymentTerms),
                    'remarks' => $faker->optional(0.35)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('sales_purchase_orders')->insert($data);
        }

        // 5. AR Invoices (5,000 records)
        $this->command?->info('Seeding 5,000 AR Invoices...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $customer = $customers[array_rand($customers)];
                $subtotal = round($faker->randomFloat(2, 900, 98000), 2);
                $tax = round($subtotal * 0.10, 2);
                $totalAmt = round($subtotal + $tax, 2);
                $status = $faker->randomElement(['Paid', 'Partially Paid', 'Unpaid', 'Overdue']);
                $paid = match ($status) {
                    'Paid' => $totalAmt,
                    'Partially Paid' => round($totalAmt * 0.45, 2),
                    default => 0.00,
                };
                $balance = round($totalAmt - $paid, 2);
                $docDate = $now->copy()->subDays(rand(1, 550));
                $dueDate = $docDate->copy()->addDays(30);

                $data[] = [
                    'invoice_num' => sprintf('ARI-%d-%05d', $docDate->year, $idx),
                    'order_reference' => sprintf('SPO-%d-%05d', $docDate->year, rand(1, 5000)),
                    'customer_code' => $customer['code'],
                    'customer_name' => $customer['name'],
                    'doc_date' => $docDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'currency' => 'USD',
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'doc_total' => $totalAmt,
                    'paid_amount' => $paid,
                    'balance_due' => $balance,
                    'status' => $status,
                    'sales_employee' => $faker->randomElement($salesReps),
                    'payment_terms' => $faker->randomElement($paymentTerms),
                    'remarks' => $faker->optional(0.3)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('ar_invoices')->insert($data);
        }

        // 6. Incoming Payments (5,000 records)
        $this->command?->info('Seeding 5,000 Incoming Payments...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $customer = $customers[array_rand($customers)];
                $bank = $banks[array_rand($banks)];
                $docDate = $now->copy()->subDays(rand(1, 550));
                $amount = round($faker->randomFloat(2, 400, 75000), 2);

                $data[] = [
                    'payment_num' => sprintf('IP-%d-%05d', $docDate->year, $idx),
                    'doc_date' => $docDate->format('Y-m-d'),
                    'customer_code' => $customer['code'],
                    'customer_name' => $customer['name'],
                    'payment_method' => $faker->randomElement(['Bank Transfer', 'Credit Card', 'Cash', 'Cheque']),
                    'reference_num' => sprintf('TXN-%06d', rand(100000, 999999)),
                    'currency' => 'USD',
                    'amount_received' => $amount,
                    'bank_account' => "{$bank['name']} - {$bank['account']}",
                    'status' => $faker->randomElement(['Cleared', 'Deposited', 'Pending', 'Voided']),
                    'remarks' => $faker->optional(0.25)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('incoming_payments')->insert($data);
        }

        // 7. Deposits (5,000 records)
        $this->command?->info('Seeding 5,000 Deposits...');
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j + 1;
                $bank = $banks[array_rand($banks)];
                $docDate = $now->copy()->subDays(rand(1, 550));
                $amount = round($faker->randomFloat(2, 1000, 150000), 2);

                $data[] = [
                    'deposit_num' => sprintf('DEP-%d-%05d', $docDate->year, $idx),
                    'deposit_date' => $docDate->format('Y-m-d'),
                    'deposit_type' => $faker->randomElement(['Cash Deposit', 'Cheque Deposit', 'Credit Card Settlement']),
                    'bank_name' => $bank['name'],
                    'bank_account' => $bank['account'],
                    'currency' => 'USD',
                    'total_amount' => $amount,
                    'reconciliation_status' => $faker->randomElement(['Reconciled', 'Unreconciled']),
                    'status' => $faker->randomElement(['Posted', 'Draft', 'Cancelled']),
                    'journal_ref' => sprintf('JE-%06d', rand(100000, 999999)),
                    'remarks' => $faker->optional(0.2)->sentence(),
                    'created_at' => $docDate,
                    'updated_at' => $docDate,
                ];
            }
            DB::table('deposits')->insert($data);
        }

        $this->command?->info('Successfully seeded 35,000 records across all 7 report modules.');
    }
}
