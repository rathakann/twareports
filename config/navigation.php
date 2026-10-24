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
        'label' => 'Components',
        'items' => [
            [
                'label' => 'UI Components',
                'icon' => 'blocks',
                'route' => 'theme.components',
            ],
            [
                'label' => 'Example Listing',
                'icon' => 'table-properties',
                'route' => 'theme.listing',
            ],
            [
                'label' => 'Example Form',
                'icon' => 'file-pen-line',
                'route' => 'theme.form',
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
];
