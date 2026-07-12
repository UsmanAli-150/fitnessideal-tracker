<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trash</h2>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm shadow-sm">{{ session('success') }}</div>
        @endif

        <a href="{{ route('tasks.index') }}" class="text-sm text-brand-600 hover:text-brand-800 underline">&larr; Back to Tasks</a>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b border-gray-200">
                    <tr>
                        <th class="p-3">Name</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Deleted At</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-gray-50/70 transition-colors duration-150">
                            <td class="p-3 font-medium text-gray-800">{{ $task->name }}</td>
                            <td class="p-3 text-gray-600">{{ $task->category }}</td>
                            <td class="p-3 text-gray-500">{{ $task->deleted_at->format('M d, Y g:i A') }}</td>
                            <td class="p-3 space-x-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('tasks.restore', $task->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 font-medium hover:underline">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('tasks.forceDelete', $task->id) }}" class="inline"
                                      onsubmit="return confirm('This will permanently delete the task. This cannot be undone. Continue?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium hover:underline">Delete Permanently</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-gray-500">Trash is empty.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>