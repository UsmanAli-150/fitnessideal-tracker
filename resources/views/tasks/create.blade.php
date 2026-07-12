<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Task</h2>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100" x-data="{ recurrence: '{{ old('recurrence', 'Daily') }}' }">

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" maxlength="255" required
                           class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                            @foreach (['Health', 'Work', 'Learning', 'Chores', 'Other'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                        <input type="time" name="scheduled_time" value="{{ old('scheduled_time') }}" required
                               class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                            @foreach (['High', 'Medium', 'Low'] as $p)
                                <option value="{{ $p }}" @selected(old('priority', 'Medium') === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (min)</label>
                        <input type="number" name="estimated_duration" value="{{ old('estimated_duration') }}" min="1"
                               class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recurrence</label>
                    <select name="recurrence" x-model="recurrence" class="w-full border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                        @foreach (['Daily', 'Weekdays', 'Weekends', 'Custom'] as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="recurrence === 'Custom'" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <label class="flex items-center gap-2 text-sm capitalize">
                            <input type="checkbox" name="custom_days[]" value="{{ $day }}" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            {{ $day }}
                        </label>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-brand-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-brand-700 active:scale-95 shadow-sm transition-all duration-150">
                        Save Task
                    </button>
                    <a href="{{ route('tasks.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>