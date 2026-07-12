<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Today's Tasks</h2>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ skipModalFor: null, skipReason: '', skipNotes: '' }">

        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm shadow-sm animate-fade-in-up">
                {{ session('success') }}
            </div>
        @endif

        <!-- Progress card -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="flex justify-between items-baseline mb-3">
                <span class="text-sm text-gray-500">{{ $done }} of {{ $total }} tasks completed</span>
                <span class="text-2xl sm:text-3xl font-bold text-brand-600">{{ $completionRate }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-500 to-brand-600 h-2.5 rounded-full transition-all duration-700 ease-out"
                     style="width: {{ $completionRate }}%"></div>
            </div>
        </div>

        <!-- Task list -->
        <div class="space-y-3">
            @forelse ($tasks as $task)
                @php $log = $logs->get($task->id); @endphp
                <div class="task-card bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 hover:shadow-md hover:border-brand-100 transition-all duration-200"
                     style="animation-delay: {{ $loop->index * 40 }}ms">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800">{{ $task->name }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $task->category }}</span>
                            <span @class([
                                'px-2.5 py-0.5 rounded-full text-xs font-medium border',
                                'bg-red-50 text-red-700 border-red-100' => $task->priority === 'High',
                                'bg-yellow-50 text-yellow-700 border-yellow-100' => $task->priority === 'Medium',
                                'bg-green-50 text-green-700 border-green-100' => $task->priority === 'Low',
                            ])>{{ $task->priority }}</span>
                        </div>
                        <div class="text-sm text-gray-500 mt-1.5">
                            {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                            @if ($task->streak && $task->streak->current_streak > 0)
                                <span class="text-orange-600 font-medium">&middot; 🔥 {{ $task->streak->current_streak }} day streak</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if ($log && $log->status === 'Done')
                            <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200 animate-pop">✓ Done</span>
                        @elseif ($log && $log->status === 'Skipped')
                            <span class="px-3.5 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 border border-gray-200">Skipped ({{ $log->skip_reason }})</span>
                        @else
                            <form method="POST" action="{{ route('tasks.markDone', $task) }}" class="flex-1 sm:flex-none">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-3.5 py-1.5 rounded-lg text-sm font-medium bg-brand-600 text-white hover:bg-brand-700 active:scale-95 shadow-sm transition-all duration-150">
                                    Mark Done
                                </button>
                            </form>
                            <button type="button" @click="skipModalFor = {{ $task->id }}"
                                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 active:scale-95 transition-all duration-150">
                                Skip
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-500">
                    No tasks scheduled for today. <a href="{{ route('tasks.create') }}" class="text-brand-600 underline font-medium">Add one?</a>
                </div>
            @endforelse
        </div>

        <!-- Skip modal -->
        <div x-show="skipModalFor !== null" x-cloak
             class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-xl p-6 w-full max-w-sm animate-fade-in-up" @click.outside="skipModalFor = null">
                <h3 class="font-semibold text-gray-800 mb-4">Why are you skipping this?</h3>

                <template x-for="task in {{ $tasks->toJson() }}" :key="task.id">
                    <form method="POST" :action="`/tasks/${task.id}/skip`" x-show="skipModalFor === task.id" class="space-y-3">
                        @csrf
                        <select name="skip_reason" x-model="skipReason" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="">Select a reason</option>
                            <option value="sick">Sick</option>
                            <option value="holiday">Holiday</option>
                            <option value="overtime">Overtime</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea name="notes" x-model="skipNotes" placeholder="Optional notes..."
                                  class="w-full border-gray-300 rounded-lg text-sm focus:ring-brand-500 focus:border-brand-500" rows="2"></textarea>
                        <div class="flex gap-2 justify-end pt-1">
                            <button type="button" @click="skipModalFor = null" class="px-4 py-2 rounded-lg text-sm border border-gray-300 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg text-sm bg-gray-800 text-white hover:bg-gray-900">
                                Confirm Skip
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>