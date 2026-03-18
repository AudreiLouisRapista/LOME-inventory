<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 14px; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; }
        .meta { color: #6b7280; font-size: 11px; margin: 0; }
        .stats { width: 100%; border-collapse: collapse; margin: 10px 0 16px 0; }
        .stats td { border: 1px solid #e5e7eb; padding: 10px; vertical-align: top; }
        .stat-label { color: #6b7280; font-size: 11px; margin: 0 0 4px 0; }
        .stat-value { font-size: 16px; font-weight: 700; margin: 0; }
        .section-title { font-size: 13px; font-weight: 700; margin: 14px 0 8px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #e5e7eb; padding: 8px; }
        table.data th { background: #f3f4f6; text-align: left; font-size: 11px; color: #374151; }
        .right { text-align: right; }
        .muted { color: #6b7280; }
        .footer { margin-top: 16px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Financial Report</p>
        <p class="meta">
            Period: <strong>{{ ($period ?? 'this_month') === 'last_quarter' ? 'Last Quarter' : 'This Month' }}</strong>
            &nbsp;|&nbsp;
            Category: <strong>
                @php
                    $catName = 'All Categories';
                    if (($categoryId ?? 'all') !== 'all' && !empty($categoryId) && isset($categories)) {
                        $match = collect($categories)->firstWhere('category_ID', (int)$categoryId);
                        if ($match && isset($match->category_name)) {
                            $catName = (string) $match->category_name;
                        }
                    }
                @endphp
                {{ $catName }}
            </strong>
            &nbsp;|&nbsp;
            Generated: <strong>{{ now()->format('Y-m-d H:i') }}</strong>
        </p>
    </div>

    <table class="stats">
        <tr>
            @foreach(($stats ?? []) as $stat)
                <td>
                    <p class="stat-label">{{ $stat['label'] ?? '' }}</p>
                    <p class="stat-value">{{ $stat['value'] ?? '' }}</p>
                    <p class="meta">{{ $stat['badge'] ?? '—' }}</p>
                </td>
            @endforeach
        </tr>
    </table>

    <div>
        <div class="section-title">Top Products by Profit</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="right">Revenue</th>
                    <th class="right">Profit</th>
                    <th class="right">Margin</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($topProductsByProfit ?? []) as $row)
                    @php
                        $revenue = (float) ($row['revenue'] ?? 0);
                        $profit = (float) ($row['profit'] ?? 0);
                        $margin = (float) ($row['margin'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $row['name'] ?? '—' }}</td>
                        <td class="right">₱{{ number_format($revenue, 2) }}</td>
                        <td class="right">₱{{ number_format($profit, 2) }}</td>
                        <td class="right">{{ number_format($margin, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No data for selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        <div class="section-title">Expense Breakdown</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Label</th>
                    <th class="right">Percent</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($expenseBreakdown ?? []) as $row)
                    <tr>
                        <td>{{ $row['label'] ?? '—' }}</td>
                        <td class="right">{{ (int)($row['percent'] ?? 0) }}%</td>
                        <td class="right">₱{{ number_format((float)($row['total'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">No expense data.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="2" class="right"><strong>Total Expenses</strong></td>
                    <td class="right"><strong>₱{{ number_format((float)($expenseTotal ?? 0), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Note: Charts are not included in the PDF export.
    </div>
</body>
</html>
