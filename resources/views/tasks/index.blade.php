<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>
            <a href="{{ route('tasks.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 active:scale-95 shadow-sm transition-all duration-150">
                + New Task
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ selected: [] }">

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm shadow-sm animate-fade-in-up">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search & Filters -->
        <form method="GET" action="{{ route('tasks.index') }}" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..."
                   class="border-gray-300 rounded-lg text-sm flex-1 min-w-[150px] focus:ring-brand-500 focus:border-brand-500">

            <select name="category" class="border-gray-300 rounded-lg text-sm focus:ring-brand-500 focus:border-brand-500">
                <option value="">All Categories</option>
                @foreach (['Health', 'Work', 'Learning', 'Chores', 'Other'] as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="status" class="border-gray-300 rounded-lg text-sm focus:ring-brand-500 focus:border-brand-500">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-900 active:scale-95 transition-all duration-150">Filter</button>
            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-500 underline hover:text-gray-700">Reset</a>
            <a href="{{ route('tasks.trash') }}" class="text-sm text-red-500 underline hover:text-red-700 ml-auto">View Trash</a>
        </form>

        <!-- Bulk delete -->
        <form method="POST" action="{{ route('tasks.bulkDelete') }}" @submit="if(selected.length===0){ $event.preventDefault(); alert('Select at least one task'); }">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b border-gray-200">
                        <tr>
                            <th class="p-3"><input type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" @click="selected = $event.target.checked ? [{{ $tasks->pluck('id')->implode(',') }}] : []"></th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Time</th>
                            <th class="p-3">Priority</th>
                            <th class="p-3">Recurrence</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tasks as $task)
                            <tr class="hover:bg-gray-50/70 transition-colors duration-150">
                                <td class="p-3">
                                    <input type="checkbox" name="ids[]" value="{{ $task->id }}" x-model="selected" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                </td>
                                <td class="p-3 font-medium text-gray-800">{{ $task->name }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $task->category }}</span>
                                </td>
                                <td class="p-3 text-gray-600">{{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}</td>
                                <td class="p-3">
                                    <span @class([
                                        'px-2.5 py-0.5 rounded-full text-xs font-medium border',
                                        'bg-red-50 text-red-700 border-red-100' => $task->priority === 'High',
                                        'bg-yellow-50 text-yellow-700 border-yellow-100' => $task->priority === 'Medium',
                                        'bg-green-50 text-green-700 border-green-100' => $task->priority === 'Low',
                                    ])>{{ $task->priority }}</span>
                                </td>
                                <td class="p-3 text-gray-600">{{ $task->recurrence }}</td>
                                <td class="p-3">
                                    <form method="POST" action="{{ route('tasks.toggleActive', $task) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors duration-150 {{ $task->is_active ? 'bg-green-50 text-green-700 border border-green-100 hover:bg-green-100' : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200' }}">
                                            {{ $task->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="p-3 space-x-3 whitespace-nowrap">
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-brand-600 hover:text-brand-800 font-medium hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Move this task to trash?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-8 text-center text-gray-500">No tasks found. Create your first one!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tasks->count())
                <button type="submit" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 active:scale-95 shadow-sm transition-all duration-150">
                    Delete Selected
                </button>
            @endif
        </form>

        <div class="mt-6">{{ $tasks->links() }}</div>
    </div>
</x-app-layout>