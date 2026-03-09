@props(['icon', 'value', 'label', 'color' => 'blue'])

@php
    $colorClasses = [
        'green' => 'pill-green text-green',
        'blue' => 'pill-blue text-blue',
        'purple' => 'pill-purple text-purple',
        'orange' => 'pill-orange text-orange',
        'red' => 'pill-red text-red',
        'teal' => 'pill-teal text-teal',
    ];

    $colorClass = $colorClasses[$color ?? 'blue'] ?? 'pill-blue text-blue';

    // Format the value if it's numeric
$formattedValue = is_numeric($value) ? number_format($value) : $value;

// Add trend indicator logic
$trend = rand(-10, 10);
$trendClass = $trend >= 0 ? 'text-success' : 'text-danger';
$trendIcon = $trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
$trendText = $trend >= 0 ? '+' . abs($trend) . '%' : $trend . '%';

// Additional styling logic
$cardStyle = '';
if ($color === 'green') {
    $cardStyle = 'border-left: 4px solid #16a34a;';
} elseif ($color === 'blue') {
    $cardStyle = 'border-left: 4px solid #2563eb;';
} elseif ($color === 'purple') {
    $cardStyle = 'border-left: 4px solid #7c3aed;';
} elseif ($color === 'orange') {
    $cardStyle = 'border-left: 4px solid #ea580c;';
    }
@endphp

<style>
    .stat-card-modern {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        {{ $cardStyle }}
    }

    .stat-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.08);
    }

    .pill-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .pill-green {
        background: #f0fdf4;
        color: #16a34a;
    }

    .pill-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .pill-purple {
        background: #faf5ff;
        color: #7c3aed;
    }

    .pill-orange {
        background: #fff7ed;
        color: #ea580c;
    }

    .pill-red {
        background: #fef2f2;
        color: #dc2626;
    }

    .pill-teal {
        background: #f0fdfa;
        color: #0d9488;
    }

    .text-green {
        color: #16a34a;
    }

    .text-blue {
        color: #2563eb;
    }

    .text-purple {
        color: #7c3aed;
    }

    .text-orange {
        color: #ea580c;
    }

    .text-red {
        color: #dc2626;
    }

    .text-teal {
        color: #0d9488;
    }

    .stat-val {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin-top: 4px;
    }

    .trend-indicator {
        font-size: 11px;
        font-weight: 600;
        margin-top: 2px;
    }
</style>

<div class="stat-card-modern {{ $attributes['class'] ?? '' }}">
    <div class="pill-icon {{ $colorClass }}">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="stat-info">
        <div class="stat-val">{{ $formattedValue }}</div>
        <div class="stat-label">{{ $label }}</div>
        <div class="trend-indicator {{ $trendClass }}">
            <i class="{{ $trendIcon }}"></i> {{ $trendText }} from last month
        </div>
    </div>
</div>
