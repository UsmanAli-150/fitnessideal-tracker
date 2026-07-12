<div class="sm:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 flex justify-around items-center h-16 z-40">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-500' }}">
        <span>🏠</span>
        <span>Home</span>
    </a>
    <a href="{{ route('tasks.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('tasks.*') ? 'text-brand-600' : 'text-gray-500' }}">
        <span>✅</span>
        <span>Tasks</span>
    </a>
    <a href="{{ route('analytics.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('analytics.*') ? 'text-brand-600' : 'text-gray-500' }}">
        <span>📊</span>
        <span>Analytics</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('profile.*') ? 'text-brand-600' : 'text-gray-500' }}">
        <span>👤</span>
        <span>Profile</span>
    </a>
</div>