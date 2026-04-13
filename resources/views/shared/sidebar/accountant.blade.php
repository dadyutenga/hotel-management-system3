<div class="flex-1 overflow-y-auto p-4 space-y-2">
    <a href="{{ route('accountant.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.dashboard') }}</span>
    </a>

    <a href="{{ route('accountant.overview') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.overview') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.financial_overview') }}</span>
    </a>

    <a href="{{ route('accountant.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.transactions') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.transactions') }}</span>
    </a>

    <a href="{{ route('accountant.journal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.journal.*') || request()->routeIs('accounting.journal.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.journal_entries') }}</span>
    </a>

    <a href="{{ route('accountant.accounts-payable') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.accounts-payable') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.accounts_payable') }}</span>
    </a>

    <a href="{{ route('accountant.accounts-receivable') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.accounts-receivable') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.accounts_receivable') }}</span>
    </a>

    <a href="{{ route('accountant.expenses') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.expenses') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.expense_management') }}</span>
    </a>

    <a href="{{ route('accountant.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.reports') || request()->routeIs('accounting.reports.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.reports') }}</span>
    </a>

    <a href="{{ route('accountant.audit-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.audit-logs') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <span>{{ __('accountant.sidebar.audit_logs') }}</span>
    </a>
</div>
