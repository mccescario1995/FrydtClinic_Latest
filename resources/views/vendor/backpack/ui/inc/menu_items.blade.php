{{-- This file is used for menu items by any Backpack v6 theme --}}

{{-- Dashboard --}}
<x-backpack::menu-item title="Dashboard" icon="la la-home" :link="backpack_url('dashboard')" />

{{-- Patient Management Section --}}
<x-backpack::menu-dropdown title="Patient Management" icon="la la-users">
    <x-backpack::menu-dropdown-header title="Patient Records" />
    <x-backpack::menu-dropdown-item title="All Patients" icon="la la-user" :link="backpack_url('patient')" />
    <x-backpack::menu-dropdown-item title="Add New Patient" icon="la la-user-plus" :link="backpack_url('patient/create')" />
    <x-backpack::menu-dropdown-item title="Prenatal Records" icon="la la-heartbeat" :link="backpack_url('prenatal-record')" />
</x-backpack::menu-dropdown>

{{-- Medical Records Section --}}
<x-backpack::menu-dropdown title="Medical Records" icon="la la-file-medical">
    <x-backpack::menu-dropdown-header title="Clinical Data" />
    <x-backpack::menu-dropdown-item title="Laboratory Results" icon="la la-flask" :link="backpack_url('laboratory-result')" />
    <x-backpack::menu-dropdown-item title="Appointments" icon="la la-calendar" :link="backpack_url('appointment')" />
    <x-backpack::menu-dropdown-item title="Book Appointment" icon="la la-calendar-plus" :link="backpack_url('appointment/book')" />
</x-backpack::menu-dropdown>

{{-- Billing & Payments Section --}}
<x-backpack::menu-dropdown title="Billing & Payments" icon="la la-money-bill">
    <x-backpack::menu-dropdown-header title="Financial Management" />
    <x-backpack::menu-dropdown-item title="All Bills" icon="la la-file-invoice-dollar" :link="backpack_url('billing')" />
    <x-backpack::menu-dropdown-item title="Create Bill" icon="la la-plus-circle" :link="backpack_url('billing/create')" />
</x-backpack::menu-dropdown>

{{-- Services & Inventory Section --}}
<x-backpack::menu-dropdown title="Services & Inventory" icon="la la-cogs">
    <x-backpack::menu-dropdown-header title="Operations" />
    <x-backpack::menu-dropdown-item title="Medical Services" icon="la la-stethoscope" :link="backpack_url('service')" />
    <x-backpack::menu-dropdown-item title="Inventory" icon="la la-boxes" :link="backpack_url('inventory')" />
    <x-backpack::menu-dropdown-item title="Categories" icon="la la-tags" :link="backpack_url('inventory-category')" />
    <x-backpack::menu-dropdown-item title="Stock Movements" icon="la la-exchange-alt" :link="backpack_url('inventory-movements')" />
</x-backpack::menu-dropdown>

{{-- Forms & Documents Section --}}
<x-backpack::menu-dropdown title="Forms & Documents" icon="la la-file-alt">
    <x-backpack::menu-dropdown-header title="Documentation" />
    <x-backpack::menu-dropdown-item title="Medical Forms" icon="la la-clipboard-list" :link="backpack_url('form')" />
    <x-backpack::menu-dropdown-item title="Documents" icon="la la-folder-open" :link="backpack_url('document')" />
</x-backpack::menu-dropdown>

{{-- Reports Section --}}
<x-backpack::menu-dropdown title="Reports" icon="la la-chart-bar">
    <x-backpack::menu-dropdown-header title="Analytics" />
    <x-backpack::menu-dropdown-item title="Patient Reports" icon="la la-user-chart" :link="backpack_url('reports/patient')" />
    <x-backpack::menu-dropdown-item title="Financial Reports" icon="la la-chart-line" :link="backpack_url('reports/financial')" />
    <x-backpack::menu-dropdown-item title="Laboratory Reports" icon="la la-chart-pie" :link="backpack_url('reports/laboratory')" />
    <x-backpack::menu-separator />
    <x-backpack::menu-dropdown-item title="Global Search" icon="la la-search" :link="backpack_url('search')" />
</x-backpack::menu-dropdown>

{{-- Settings Section --}}
<x-backpack::menu-dropdown title="Settings" icon="la la-cog">
    <x-backpack::menu-dropdown-header title="System Management" />
    <x-backpack::menu-dropdown-item title="User Management" icon="la la-users-cog" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
    <x-backpack::menu-dropdown-item title="Activity Logs" icon="la la-history" :link="backpack_url('activity-log')" />
    <x-backpack::menu-dropdown-item title="Content Management" icon="la la-file-o" :link="backpack_url('page')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-item title="Activity logs" icon="la la-question" :link="backpack_url('activity-log')" />
