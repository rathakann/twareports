<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\NavigationGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // 1. Workspace
            [
                'group_label' => 'Workspace',
                'group_order' => 10,
                'label' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'route' => 'dashboard',
                'permission' => null,
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'System dashboard and overview analytics',
            ],

            // 2. Master Data -> SAP (Parent)
            [
                'group_label' => 'Master Data',
                'group_order' => 20,
                'label' => 'SAP',
                'icon' => 'database',
                'route' => null,
                'permission' => null,
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'SAP Business One Master Data Integration',
                'children' => [
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Branches',
                        'icon' => 'building-2',
                        'route' => 'master-data.branches.index',
                        'permission' => 'master-data.branches.view',
                        'sort_order' => 10,
                        'is_active' => true,
                        'description' => 'SAP Business Places and branches (OBPL)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Warehouses',
                        'icon' => 'warehouse',
                        'route' => 'master-data.warehouses.index',
                        'permission' => 'master-data.warehouses.view',
                        'sort_order' => 20,
                        'is_active' => true,
                        'description' => 'Physical warehouses and bulk depots (OWHS)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Bins',
                        'icon' => 'boxes',
                        'route' => 'master-data.bins.index',
                        'permission' => 'master-data.bins.view',
                        'sort_order' => 30,
                        'is_active' => true,
                        'description' => 'Warehouse bin locations and shelf racks (OBIN)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Business Partner',
                        'icon' => 'users-round',
                        'route' => 'master-data.business-partners.index',
                        'permission' => 'master-data.business-partners.view',
                        'sort_order' => 40,
                        'is_active' => true,
                        'description' => 'Customers, vendors and lead accounts (OCRD)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Items',
                        'icon' => 'package',
                        'route' => 'master-data.items.index',
                        'permission' => 'master-data.items.view',
                        'sort_order' => 50,
                        'is_active' => true,
                        'description' => 'SAP Item Master Data (OITM)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'UoM',
                        'icon' => 'scale',
                        'route' => 'master-data.uoms.index',
                        'permission' => 'master-data.uoms.view',
                        'sort_order' => 60,
                        'is_active' => true,
                        'description' => 'Units of measure definitions (OUOM)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'UoM Group',
                        'icon' => 'shapes',
                        'route' => 'master-data.uom-groups.index',
                        'permission' => 'master-data.uom-groups.view',
                        'sort_order' => 70,
                        'is_active' => true,
                        'description' => 'UoM Conversion matrix and group hierarchies (OUGP)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Item Group',
                        'icon' => 'tags',
                        'route' => 'master-data.item-groups.index',
                        'permission' => 'master-data.item-groups.view',
                        'sort_order' => 80,
                        'is_active' => true,
                        'description' => 'Item group categories and valuation methods (OITB)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Item Class',
                        'icon' => 'layers',
                        'route' => 'master-data.item-classes.index',
                        'permission' => 'master-data.item-classes.view',
                        'sort_order' => 90,
                        'is_active' => true,
                        'description' => 'Item material classifications and types',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Barcode',
                        'icon' => 'barcode',
                        'route' => 'master-data.barcodes.index',
                        'permission' => 'master-data.barcodes.view',
                        'sort_order' => 100,
                        'is_active' => true,
                        'description' => 'Product barcodes and packaging levels (OBCD)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Bom',
                        'icon' => 'git-fork',
                        'route' => 'master-data.boms.index',
                        'permission' => 'master-data.boms.view',
                        'sort_order' => 110,
                        'is_active' => true,
                        'description' => 'Bill of Materials trees and recipes (OITT)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Documents',
                        'icon' => 'files',
                        'route' => 'master-data.documents.index',
                        'permission' => 'master-data.documents.view',
                        'sort_order' => 120,
                        'is_active' => true,
                        'description' => 'Document types and object definitions (ONNM)',
                    ],
                    [
                        'group_label' => 'Master Data',
                        'group_order' => 20,
                        'label' => 'Document Series',
                        'icon' => 'file-spreadsheet',
                        'route' => 'master-data.document-series.index',
                        'permission' => 'master-data.document-series.view',
                        'sort_order' => 130,
                        'is_active' => true,
                        'description' => 'Document numbering series and periods (NNM1)',
                    ],
                ],
            ],

            // 3. Reports: Purchase Category (Parent)
            [
                'group_label' => 'Report',
                'group_order' => 30,
                'label' => 'Purchase',
                'icon' => 'shopping-cart',
                'route' => null,
                'permission' => null,
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'Purchasing and procurement reports',
                'children' => [
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Purchase Order',
                        'icon' => 'file-text',
                        'route' => 'reports.purchase.purchase-orders.index',
                        'permission' => 'reports.purchase.purchase-orders.view',
                        'sort_order' => 10,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'AP Invoice',
                        'icon' => 'receipt',
                        'route' => 'reports.purchase.ap-invoices.index',
                        'permission' => 'reports.purchase.ap-invoices.view',
                        'sort_order' => 20,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Outgoing Payment',
                        'icon' => 'credit-card',
                        'route' => 'reports.purchase.outgoing-payments.index',
                        'permission' => 'reports.purchase.outgoing-payments.view',
                        'sort_order' => 30,
                        'is_active' => true,
                    ],
                ],
            ],

            // 3. Reports: Sales Category (Parent)
            [
                'group_label' => 'Report',
                'group_order' => 30,
                'label' => 'Sales',
                'icon' => 'badge-dollar-sign',
                'route' => null,
                'permission' => null,
                'sort_order' => 20,
                'is_active' => true,
                'description' => 'Sales and revenue reports',
                'children' => [
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Purchase Order',
                        'icon' => 'file-text',
                        'route' => 'reports.sales.purchase-orders.index',
                        'permission' => 'reports.sales.purchase-orders.view',
                        'sort_order' => 10,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'AR Invoice',
                        'icon' => 'receipt',
                        'route' => 'reports.sales.ar-invoices.index',
                        'permission' => 'reports.sales.ar-invoices.view',
                        'sort_order' => 20,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Incoming Payment',
                        'icon' => 'wallet',
                        'route' => 'reports.sales.incoming-payments.index',
                        'permission' => 'reports.sales.incoming-payments.view',
                        'sort_order' => 30,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Deposit',
                        'icon' => 'piggy-bank',
                        'route' => 'reports.sales.deposits.index',
                        'permission' => 'reports.sales.deposits.view',
                        'sort_order' => 40,
                        'is_active' => true,
                    ],
                ],
            ],

            // 3. Reports: Financial Category (Parent)
            [
                'group_label' => 'Report',
                'group_order' => 30,
                'label' => 'Financial',
                'icon' => 'landmark',
                'route' => null,
                'permission' => null,
                'sort_order' => 30,
                'is_active' => true,
                'description' => 'Financial and accounting ledger statements',
                'children' => [
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Balance Sheet',
                        'icon' => 'scale',
                        'route' => 'reports.financial.balance-sheet.index',
                        'permission' => 'reports.financial.balance-sheet.view',
                        'sort_order' => 10,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Profit & Loss',
                        'icon' => 'chart-line',
                        'route' => 'reports.financial.profit-loss.index',
                        'permission' => 'reports.financial.profit-loss.view',
                        'sort_order' => 20,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Cash Flow Statement',
                        'icon' => 'activity',
                        'route' => 'reports.financial.cash-flow.index',
                        'permission' => 'reports.financial.cash-flow.view',
                        'sort_order' => 30,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'Trial Balance',
                        'icon' => 'table-2',
                        'route' => 'reports.financial.trial-balance.index',
                        'permission' => 'reports.financial.trial-balance.view',
                        'sort_order' => 40,
                        'is_active' => true,
                    ],
                    [
                        'group_label' => 'Report',
                        'group_order' => 30,
                        'label' => 'General Ledger',
                        'icon' => 'book-open',
                        'route' => 'reports.financial.general-ledger.index',
                        'permission' => 'reports.financial.general-ledger.view',
                        'sort_order' => 50,
                        'is_active' => true,
                    ],
                ],
            ],

            // 4. Access Control
            [
                'group_label' => 'Access Control',
                'group_order' => 40,
                'label' => 'User Management',
                'icon' => 'users',
                'route' => 'users.index',
                'permission' => 'users.view',
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'System users, account statuses and profiles',
            ],
            [
                'group_label' => 'Access Control',
                'group_order' => 40,
                'label' => 'Roles',
                'icon' => 'shield',
                'route' => 'roles.index',
                'permission' => 'roles.view',
                'sort_order' => 20,
                'is_active' => true,
                'description' => 'Security roles and authorization levels',
            ],
            [
                'group_label' => 'Access Control',
                'group_order' => 40,
                'label' => 'Permissions',
                'icon' => 'key',
                'route' => 'permissions.index',
                'permission' => 'permissions.view|permissions.manage',
                'sort_order' => 30,
                'is_active' => true,
                'description' => 'Granular permission assignments',
            ],

            // 5. Administrator (New Group)
            [
                'group_label' => 'Administrator',
                'group_order' => 50,
                'label' => 'Modules',
                'icon' => 'blocks',
                'route' => 'admin.modules.index',
                'permission' => 'admin.modules.view',
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'Dynamic navigation menu and module management',
            ],
            [
                'group_label' => 'Administrator',
                'group_order' => 50,
                'label' => 'Navigation Groups',
                'icon' => 'layers',
                'route' => 'admin.navigation-groups.index',
                'permission' => 'admin.modules.view',
                'sort_order' => 20,
                'is_active' => true,
                'description' => 'Dynamic navigation groups and categories',
            ],

            // 6. Components
            [
                'group_label' => 'Components',
                'group_order' => 60,
                'label' => 'UI Components',
                'icon' => 'blocks',
                'route' => 'theme.components',
                'permission' => null,
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'Theme component demonstration library',
            ],
            [
                'group_label' => 'Components',
                'group_order' => 60,
                'label' => 'Example Listing',
                'icon' => 'table-properties',
                'route' => 'theme.listing',
                'permission' => null,
                'sort_order' => 20,
                'is_active' => true,
                'description' => 'Theme listing layout example',
            ],
            [
                'group_label' => 'Components',
                'group_order' => 60,
                'label' => 'Example Form',
                'icon' => 'file-pen-line',
                'route' => 'theme.form',
                'permission' => null,
                'sort_order' => 30,
                'is_active' => true,
                'description' => 'Theme input form layout example',
            ],
        ];

        $groupIcons = [
            'Workspace' => 'layout-dashboard',
            'Master Data' => 'database',
            'Report' => 'file-bar-chart',
            'Access Control' => 'shield',
            'Administrator' => 'shield-check',
            'Components' => 'blocks',
        ];

        $groupsByName = [];
        foreach ($modules as $data) {
            $gLabel = $data['group_label'];
            if (! isset($groupsByName[$gLabel])) {
                $groupsByName[$gLabel] = NavigationGroup::firstOrCreate(
                    ['label' => $gLabel],
                    [
                        'uuid' => (string) Str::uuid(),
                        'icon' => $groupIcons[$gLabel] ?? 'folder',
                        'sort_order' => $data['group_order'] ?? 50,
                        'is_active' => true,
                    ]
                );
            }
        }

        foreach ($modules as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $navGroup = $groupsByName[$data['group_label']] ?? null;
            if ($navGroup) {
                $data['navigation_group_id'] = $navGroup->id;
            }

            $module = Module::firstOrCreate(
                [
                    'group_label' => $data['group_label'],
                    'label' => $data['label'],
                    'route' => $data['route'],
                ],
                array_merge($data, ['uuid' => (string) Str::uuid()])
            );

            foreach ($children as $childData) {
                if ($navGroup) {
                    $childData['navigation_group_id'] = $navGroup->id;
                }

                Module::firstOrCreate(
                    [
                        'parent_id' => $module->id,
                        'label' => $childData['label'],
                        'route' => $childData['route'],
                    ],
                    array_merge($childData, [
                        'uuid' => (string) Str::uuid(),
                        'parent_id' => $module->id,
                    ])
                );
            }
        }
    }
}
