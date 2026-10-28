<?php

return [
    [
        'label' => 'Workspace',
        'items' => [
            [
                'label' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'route' => 'dashboard',
            ],
        ],
    ],
    [
        'label' => 'Master Data',
        'items' => [
            [
                'label' => 'SAP',
                'icon' => 'database',
                'children' => [
                    [
                        'label' => 'Branches',
                        'icon' => 'building-2',
                        'route' => 'master-data.branches.index',
                        'permission' => 'master-data.branches.view',
                    ],
                    [
                        'label' => 'Warehouses',
                        'icon' => 'warehouse',
                        'route' => 'master-data.warehouses.index',
                        'permission' => 'master-data.warehouses.view',
                    ],
                    [
                        'label' => 'Bins',
                        'icon' => 'boxes',
                        'route' => 'master-data.bins.index',
                        'permission' => 'master-data.bins.view',
                    ],
                    [
                        'label' => 'Business Partner',
                        'icon' => 'users-round',
                        'route' => 'master-data.business-partners.index',
                        'permission' => 'master-data.business-partners.view',
                    ],
                    [
                        'label' => 'Items',
                        'icon' => 'package',
                        'route' => 'master-data.items.index',
                        'permission' => 'master-data.items.view',
                    ],
                    [
                        'label' => 'UoM',
                        'icon' => 'scale',
                        'route' => 'master-data.uoms.index',
                        'permission' => 'master-data.uoms.view',
                    ],
                    [
                        'label' => 'UoM Group',
                        'icon' => 'shapes',
                        'route' => 'master-data.uom-groups.index',
                        'permission' => 'master-data.uom-groups.view',
                    ],
                    [
                        'label' => 'Item Group',
                        'icon' => 'tags',
                        'route' => 'master-data.item-groups.index',
                        'permission' => 'master-data.item-groups.view',
                    ],
                    [
                        'label' => 'Item Class',
                        'icon' => 'layers',
                        'route' => 'master-data.item-classes.index',
                        'permission' => 'master-data.item-classes.view',
                    ],
                    [
                        'label' => 'Barcode',
                        'icon' => 'barcode',
                        'route' => 'master-data.barcodes.index',
                        'permission' => 'master-data.barcodes.view',
                    ],
                    [
                        'label' => 'Bom',
                        'icon' => 'git-fork',
                        'route' => 'master-data.boms.index',
                        'permission' => 'master-data.boms.view',
                    ],
                    [
                        'label' => 'Documents',
                        'icon' => 'files',
                        'route' => 'master-data.documents.index',
                        'permission' => 'master-data.documents.view',
                    ],
                    [
                        'label' => 'Document Series',
                        'icon' => 'file-spreadsheet',
                        'route' => 'master-data.document-series.index',
                        'permission' => 'master-data.document-series.view',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Components',
        'is_active' => false,
        'items' => [
            [
                'label' => 'UI Components',
                'icon' => 'blocks',
                'route' => 'theme.components',
                'is_active' => false,
            ],
            [
                'label' => 'Example Listing',
                'icon' => 'table-properties',
                'route' => 'theme.listing',
                'is_active' => false,
            ],
            [
                'label' => 'Example Form',
                'icon' => 'file-pen-line',
                'route' => 'theme.form',
                'is_active' => false,
            ],
        ],
    ],
    [
        'label' => 'Report',
        'items' => [
            [
                'label' => 'Purchase',
                'icon' => 'shopping-cart',
                'children' => [
                    [
                        'label' => 'Purchase Order',
                        'icon' => 'file-text',
                        'route' => 'reports.purchase.purchase-orders.index',
                        'permission' => 'reports.purchase.purchase-orders.view',
                    ],
                    [
                        'label' => 'AP Invoice',
                        'icon' => 'receipt',
                        'route' => 'reports.purchase.ap-invoices.index',
                        'permission' => 'reports.purchase.ap-invoices.view',
                    ],
                    [
                        'label' => 'Outgoing Payment',
                        'icon' => 'credit-card',
                        'route' => 'reports.purchase.outgoing-payments.index',
                        'permission' => 'reports.purchase.outgoing-payments.view',
                    ],
                ],
            ],
            [
                'label' => 'Sales',
                'icon' => 'trending-up',
                'children' => [
                    [
                        'label' => 'Purchase Order',
                        'icon' => 'file-text',
                        'route' => 'reports.sales.purchase-orders.index',
                        'permission' => 'reports.sales.purchase-orders.view',
                    ],
                    [
                        'label' => 'AR Invoice',
                        'icon' => 'receipt',
                        'route' => 'reports.sales.ar-invoices.index',
                        'permission' => 'reports.sales.ar-invoices.view',
                    ],
                    [
                        'label' => 'Incoming Payment',
                        'icon' => 'wallet',
                        'route' => 'reports.sales.incoming-payments.index',
                        'permission' => 'reports.sales.incoming-payments.view',
                    ],
                    [
                        'label' => 'Deposit',
                        'icon' => 'piggy-bank',
                        'route' => 'reports.sales.deposits.index',
                        'permission' => 'reports.sales.deposits.view',
                    ],
                ],
            ],
            [
                'label' => 'Financial',
                'icon' => 'landmark',
                'children' => [
                    [
                        'label' => 'Balance Sheet',
                        'icon' => 'scale',
                        'route' => 'reports.financial.balance-sheet.index',
                        'permission' => 'reports.financial.balance-sheet.view',
                    ],
                    [
                        'label' => 'Profit & Loss',
                        'icon' => 'chart-line',
                        'route' => 'reports.financial.profit-loss.index',
                        'permission' => 'reports.financial.profit-loss.view',
                    ],
                    [
                        'label' => 'Cash Flow Statement',
                        'icon' => 'activity',
                        'route' => 'reports.financial.cash-flow.index',
                        'permission' => 'reports.financial.cash-flow.view',
                    ],
                    [
                        'label' => 'Trial Balance',
                        'icon' => 'table-2',
                        'route' => 'reports.financial.trial-balance.index',
                        'permission' => 'reports.financial.trial-balance.view',
                    ],
                    [
                        'label' => 'General Ledger',
                        'icon' => 'book-open',
                        'route' => 'reports.financial.general-ledger.index',
                        'permission' => 'reports.financial.general-ledger.view',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Access Control',
        'items' => [
            [
                'label' => 'User Management',
                'icon' => 'users',
                'route' => 'users.index',
                'permission' => 'users.view',
            ],
            [
                'label' => 'Roles',
                'icon' => 'shield',
                'route' => 'roles.index',
                'permission' => 'roles.view',
            ],
            [
                'label' => 'Permissions',
                'icon' => 'key',
                'route' => 'permissions.index',
                'permission' => 'permissions.view|permissions.manage',
            ],
        ],
    ],
    [
        'label' => 'Administrator',
        'items' => [
            [
                'label' => 'Modules',
                'icon' => 'blocks',
                'route' => 'admin.modules.index',
                'permission' => 'admin.modules.view',
            ],
            [
                'label' => 'Navigation Groups',
                'icon' => 'layers',
                'route' => 'admin.navigation-groups.index',
                'permission' => 'admin.modules.view',
            ],
        ],
    ],
];
