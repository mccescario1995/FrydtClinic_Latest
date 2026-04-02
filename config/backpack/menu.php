<?php

return [
    [
        'label' => 'Dashboard',
        'route' => 'backpack.dashboard',
        'icon' => 'la la-home',
    ],

    // Patient Management Section
    [
        'label' => 'Patient Management',
        'icon' => 'la la-users',
        'submenu' => [
            [
                'label' => 'All Patients',
                'route' => 'backpack.patient.index',
                'icon' => 'la la-user',
            ],
            [
                'label' => 'Add New Patient',
                'route' => 'backpack.patient.create',
                'icon' => 'la la-user-plus',
            ],
            [
                'label' => 'Prenatal Records',
                'route' => 'backpack.prenatal-record.index',
                'icon' => 'la la-heartbeat',
            ],
        ],
    ],

    // Medical Records Section
    [
        'label' => 'Medical Records',
        'icon' => 'la la-file-medical',
        'submenu' => [
            [
                'label' => 'Laboratory Results',
                'route' => 'backpack.laboratory-result.index',
                'icon' => 'la la-flask',
            ],
            [
                'label' => 'Appointments',
                'route' => 'backpack.appointment.index',
                'icon' => 'la la-calendar',
            ],
            [
                'label' => 'Book Appointment',
                'route' => 'backpack.appointment.booking.form',
                'icon' => 'la la-calendar-plus',
            ],
        ],
    ],

    // Billing & Payments Section
    [
        'label' => 'Billing & Payments',
        'icon' => 'la la-money-bill',
        'submenu' => [
            [
                'label' => 'All Bills',
                'route' => 'backpack.billing.index',
                'icon' => 'la la-file-invoice-dollar',
            ],
            [
                'label' => 'Create Bill',
                'route' => 'backpack.billing.create',
                'icon' => 'la la-plus-circle',
            ],
            [
                'label' => 'Payment Methods',
                'route' => 'backpack.payment.index',
                'icon' => 'la la-credit-card',
            ],
        ],
    ],

    // Services & Inventory Section
    [
        'label' => 'Services & Inventory',
        'icon' => 'la la-cogs',
        'submenu' => [
            [
                'label' => 'Medical Services',
                'route' => 'backpack.service.index',
                'icon' => 'la la-stethoscope',
            ],
            [
                'label' => 'Inventory',
                'route' => 'backpack.inventory.index',
                'icon' => 'la la-boxes',
            ],
            [
                'label' => 'Inventory Categories',
                'route' => 'backpack.inventory-category.index',
                'icon' => 'la la-tags',
            ],
            [
                'label' => 'Stock Movements',
                'route' => 'backpack.inventory-movements.index',
                'icon' => 'la la-exchange-alt',
            ],
        ],
    ],

    // Forms & Documents Section
    [
        'label' => 'Forms & Documents',
        'icon' => 'la la-file-alt',
        'submenu' => [
            [
                'label' => 'Medical Forms',
                'route' => 'backpack.form.index',
                'icon' => 'la la-clipboard-list',
            ],
            [
                'label' => 'Documents',
                'route' => 'backpack.document.index',
                'icon' => 'la la-folder-open',
            ],
        ],
    ],

    // Reports Section
    [
        'label' => 'Reports',
        'icon' => 'la la-chart-bar',
        'submenu' => [
            [
                'label' => 'Patient Reports',
                'route' => 'backpack.report.patient',
                'icon' => 'la la-user-chart',
            ],
            [
                'label' => 'Financial Reports',
                'route' => 'backpack.report.financial',
                'icon' => 'la la-chart-line',
            ],
            [
                'label' => 'Laboratory Reports',
                'route' => 'backpack.report.laboratory',
                'icon' => 'la la-chart-pie',
            ],
        ],
    ],

    // Settings Section
    [
        'label' => 'Settings',
        'icon' => 'la la-cog',
        'submenu' => [
            [
                'label' => 'User Management',
                'route' => 'backpack.user.index',
                'icon' => 'la la-users-cog',
            ],
            [
                'label' => 'System Settings',
                'route' => 'backpack.setting.index',
                'icon' => 'la la-sliders-h',
            ],
        ],
    ],
];
