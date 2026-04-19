<?php

return [
    'index' => [
        'title' => 'Customers',
        'page_title' => 'Customers',
        'page_subtitle' => 'Customer master data with receivable visibility and quick account lookup',
        'directory_title' => 'Customer Directory',
        'directory_text' => 'Search by customer name or phone number and open account details instantly.',
    ],
    'summary' => [
        'total_customers' => 'Total Customers',
        'total_customers_text' => 'Active customer records',
        'customers_with_balance' => 'Customers With Balance',
        'customers_with_balance_text' => 'Accounts with receivables',
        'outstanding_balance' => 'Outstanding Balance',
        'outstanding_balance_text' => 'Visible on this page',
    ],
    'filters' => [
        'customer_name' => 'Customer Name',
        'search_name' => 'Search by full name',
        'phone_number' => 'Phone Number',
        'search_phone' => 'Search by phone',
        'sort' => 'Sort',
        'latest' => 'Latest',
        'oldest' => 'Oldest',
    ],
    'table' => [
        'customer' => 'Customer',
        'phone' => 'Phone',
        'address' => 'Address',
        'sales' => 'Sales',
        'outstanding' => 'Outstanding',
        'actions' => 'Actions',
    ],
    'actions' => [
        'new' => 'New Customer',
        'apply_filters' => 'Apply Filters',
        'reset' => 'Reset',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
    'messages' => [
        'number' => 'Customer #:id',
        'no_address' => 'No address added',
        'delete_confirm' => 'Delete :name? This is only allowed when no sales history exists.',
        'empty' => 'No customers matched your current search.',
    ],
];
