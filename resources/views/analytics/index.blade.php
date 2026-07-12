<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Analytics</h2>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top stat cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Today's Rate</div>
                <div class="text-3xl font-bold text-brand-600 mt-1">{{ $todayRate }}%</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Productivity Score</div>
                <div class="text-3xl font-bold text-brand-600 mt-1">{{ $productivityScore }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Longest Streak</div>
                <div class="text-3xl font-bold text-orange-500 mt-1">🔥 {{ $longestStreakOverall }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Active Streaks</div>
                <div class="text-3xl font-bold text-gray-800 mt-1">{{ $streaks->where('current_streak', '>', 0)->count() }}</div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 7-day bar chart -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-semibold text-gray-800 mb-4">Last 7 Days Completion</h3>
                <canvas id="weeklyChart" height="220"></canvas>
            </div>

            <!-- Category pie chart -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-semibold text-gray-800 mb-4">Category Breakdown</h3>
                @if ($categoryBreakdown->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-16">No completed tasks yet — mark some tasks done to see this chart.</p>
                @else
                    <canvas id="categoryChart" height="220"></canvas>
                @endif
            </div>
        </div>

        <!-- Streaks list -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Task Streaks</h3>
            @if ($streaks->isEmpty())
                <p class="text-gray-500 text-sm">No streaks yet — complete a task to start one.</p>
            @else
                <div class="space-y-2">
                    @foreach ($streaks as $streak)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-gray-700 font-medium">{{ $streak->task->name ?? 'Deleted task' }}</span>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-orange-600">🔥 {{ $streak->current_streak }} current</span>
                                <span class="text-gray-400">Best: {{ $streak->longest_streak }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Monthly calendar -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ $monthName }}</h3>
            <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                @foreach ($calendar as $day)
                    <div @class([
                        'aspect-square rounded-lg flex items-center justify-center text-xs sm:text-sm font-medium',
                        'bg-gray-50 text-gray-300' => $day['isFuture'] || $day['rate'] === null,
                        'bg-green-500 text-white' => !$day['isFuture'] && $day['rate'] !== null && $day['rate'] >= 80,
                        'bg-yellow-400 text-white' => !$day['isFuture'] && $day['rate'] !== null && $day['rate'] >= 40 && $day['rate'] < 80,
                        'bg-red-400 text-white' => !$day['isFuture'] && $day['rate'] !== null && $day['rate'] < 40,
                    ])>
                        {{ $day['day'] }}
                    </div>
                @endforeach
            </div>
            <div class="flex gap-4 mt-4 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span> 80%+</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span> 40-79%</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-400 inline-block"></span> Below 40%</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-50 border border-gray-200 inline-block"></span> No tasks / future</span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Chart(document.getElementById('weeklyChart'), {
                type: 'bar',
                data: {
                    labels: @json($last7Days->pluck('label')),
                    datasets: [{
                        label: 'Completion %',
                        data: @json($last7Days->pluck('rate')),
                        backgroundColor: '#f97316',
                        borderRadius: 6,
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true, max: 100 } },
                    plugins: { legend: { display: false } }
                }
            });

            @if ($categoryBreakdown->isNotEmpty())
            new Chart(document.getElementById('categoryChart'), {
                type: 'pie',
                data: {
                    labels: @json($categoryBreakdown->keys()),
                    datasets: [{
                        data: @json($categoryBreakdown->values()),
                        backgroundColor: ['#f97316', '#3b82f6', '#22c55e', '#eab308', '#a855f7'],
                    }]
                },
                options: {
                    plugins: { legend: { position: 'bottom' } }
                }
            });
            @endif
        });
    </script>
    @endpush
</x-app-layout>