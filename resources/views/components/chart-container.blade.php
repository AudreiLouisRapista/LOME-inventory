@props(['title', 'subtitle' => null, 'height' => 300, 'id'])

@php
    $chartId = $id ?? 'chart-' . uniqid();
    $displayHeight = $height . 'px';
@endphp

<style>
    .chart-container-component {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 28px;
        height: 100%;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
    }

    .chart-header {
        margin-bottom: 25px;
        flex-shrink: 0;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-top: 4px;
        font-weight: 500;
    }

    .chart-body {
        flex: 1;
        position: relative;
        min-height: {{ $displayHeight }};
    }

    .chart-canvas {
        width: 100% !important;
        height: {{ $displayHeight }} !important;
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .chart-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #64748b;
        font-size: 14px;
    }

    .chart-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .chart-actions {
        display: flex;
        gap: 10px;
    }

    .chart-action-btn {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 6px;
        font-size: 12px;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .chart-action-btn:hover {
        border-color: #6366f1;
        color: #6366f1;
        background: #f8fafc;
    }

    .chart-metric {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #6366f1;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
    }

    .metric-item {
        text-align: center;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .metric-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="chart-container-component">
    <div class="chart-header">
        <div class="chart-toolbar">
            <div>
                <h3 class="chart-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    {{ $title }}
                </h3>
                @if ($subtitle)
                    <p class="chart-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="chart-actions">
                <button class="chart-action-btn" onclick="exportChart('{{ $chartId }}')">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="chart-action-btn" onclick="toggleFullscreen('{{ $chartId }}')">
                    <i class="fas fa-expand"></i> Fullscreen
                </button>
            </div>
        </div>
    </div>

    <div class="chart-body">
        <div class="chart-loading" id="loading-{{ $chartId }}">
            <i class="fas fa-spinner fa-spin"></i> Loading chart data...
        </div>
        <canvas id="{{ $chartId }}" class="chart-canvas"></canvas>
    </div>
</div>

<script>
    function exportChart(chartId) {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const link = document.createElement('a');
            link.download = chartId + '.png';
            link.href = canvas.toDataURL();
            link.click();
        }
    }

    function toggleFullscreen(chartId) {
        const container = document.getElementById(chartId).closest('.chart-container-component');
        if (container) {
            container.classList.toggle('fullscreen');
            const icon = container.querySelector('.fa-expand, .fa-compress');
            if (icon) {
                icon.classList.toggle('fa-expand');
                icon.classList.toggle('fa-compress');
            }
        }
    }

    // Hide loading indicator when chart is ready
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const loading = document.getElementById('loading-{{ $chartId }}');
            if (loading) {
                loading.style.display = 'none';
            }
        }, 500);
    });
</script>
