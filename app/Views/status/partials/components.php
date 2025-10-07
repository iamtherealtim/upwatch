<h3 class="text-xl font-semibold mb-4">Services</h3>

<?php if (!empty($components)): ?>
    <?php foreach ($components as $groupName => $groupComponents): ?>
    <div class="mb-6 last:mb-0">
        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3"><?= esc($groupName) ?></h4>
        
        <?php foreach ($groupComponents as $component): ?>
        <div class="py-4 border-b last:border-b-0">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h5 class="font-medium text-white"><?= esc($component['name']) ?></h5>
                    <?php if ($component['description']): ?>
                    <p class="text-sm text-gray-400"><?= esc($component['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-4">
                    <?php if (isset($component['uptime_percentage'])): ?>
                    <span class="text-sm font-semibold text-gray-300">
                        <?= number_format($component['uptime_percentage'], 2) ?>% uptime
                    </span>
                    <?php endif; ?>
                    <?php
                    $statusLabel = match($component['status']) {
                        'operational' => 'Operational',
                        'degraded' => 'Degraded',
                        'partial_outage' => 'Partial Outage',
                        'major_outage' => 'Major Outage',
                        'maintenance' => 'Maintenance',
                        default => 'Unknown'
                    };
                    $statusColor = match($component['status']) {
                        'operational' => 'bg-green-900/50 text-green-300 border-2 border-green-700',
                        'degraded' => 'bg-yellow-900/50 text-yellow-300 border-2 border-yellow-700',
                        'partial_outage' => 'bg-orange-900/50 text-orange-300 border-2 border-orange-700',
                        'major_outage' => 'bg-red-900/50 text-red-300 border-2 border-red-700',
                        'maintenance' => 'bg-blue-900/50 text-blue-300 border-2 border-blue-700',
                        default => 'bg-gray-800 text-gray-300 border-2 border-gray-700'
                    };
                    ?>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusColor ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>
            </div>
            
            <!-- Uptime Visualization -->
            <?php if (isset($component['uptime_data']) && !empty($component['uptime_data'])): ?>
            <?php 
            $periodValue = $period ?? 90;
            $periodLabel = in_array($periodValue, [6, 12, 24]) ? $periodValue . ' hours ago' : $periodValue . ' days ago';
            $periodTitle = in_array($periodValue, [6, 12, 24]) ? $periodValue . ' hour' : $periodValue . '-day';
            ?>
            <div class="mt-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-400"><?= $periodLabel ?></span>
                    <span class="text-xs text-gray-400">Now</span>
                </div>
                <div class="grid gap-[2px]" style="grid-template-columns: repeat(60, 1fr);" title="<?= $periodTitle ?> uptime history (60 bars)">
                    <?php foreach ($component['uptime_data'] as $day): ?>
                    <div class="h-10 rounded-sm <?= $day['status_color'] ?> hover:opacity-80 transition-opacity cursor-pointer" 
                         title="<?= esc($day['date']) ?>: <?= $day['uptime'] ?>% uptime"></div>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-green-600 rounded-sm"></div>
                        <span>Operational (99.5%+)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-yellow-600 rounded-sm"></div>
                        <span>Degraded (95-99%)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-orange-600 rounded-sm"></div>
                        <span>Partial Outage (80-95%)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-red-600 rounded-sm"></div>
                        <span>Major Outage (&lt;80%)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                        <span>No data</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-gray-400">No components configured yet.</p>
<?php endif; ?>
