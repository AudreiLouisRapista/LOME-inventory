<?php

namespace App\Services;

use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get dashboard statistics data
     */
    public function getStatistics(): array
    {
        return [
            [
                'icon' => 'fas fa-box',
                'value' => number_format($this->getTotalProducts()),
                'label' => 'Total Products',
                'color' => 'green'
            ],
            [
                'icon' => 'fas fa-shopping-cart',
                'value' => number_format($this->getTotalOrders()),
                'label' => 'Orders',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-chart-line',
                'value' => number_format($this->getTotalStock()),
                'label' => 'Total Stock',
                'color' => 'purple'
            ],
            [
                'icon' => 'fas fa-exclamation-triangle',
                'value' => $this->getOutOfStockCount(),
                'label' => 'Out of Stock',
                'color' => 'orange'
            ],
        ];
    }

    /**
     * Get top products data
     */
    public function getTopProducts(): array
    {
        return [
            ['Wireless Mouse', 92],
            ['USB-C Cable', 85],
            ['Laptop Stand', 78],
            ['Mechanical Keyboard', 71],
            ['Desk Lamp', 65],
        ];
    }

    /**
     * Calculate dashboard metrics
     */
    public function getDashboardMetrics(): array
    {
        $totalProducts = $this->getTotalProducts();
        $outOfStock = $this->getOutOfStockCount();

        return [
            'stockUtilization' => round(($totalProducts - $outOfStock) / $totalProducts * 100, 1),
            'orderFulfillmentRate' => round($this->getTotalOrders() / ($this->getTotalOrders() + $outOfStock) * 100, 1),
            'expiryRiskLevel' => $this->calculateExpiryRiskLevel(),
            'availableStock' => number_format($totalProducts - $this->getTotalStock()),
        ];
    }

    /**
     * Get chart data for expense vs profit
     */
    public function getExpenseProfitChartData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [[
                'label' => 'Profit',
                'data' => [35, 42, 38, 55, 48, 62],
                'borderColor' => '#6366f1',
                'backgroundColor' => 'rgba(99, 102, 241, 0.05)',
                'fill' => true,
                'tension' => 0.4,
                'pointRadius' => 5,
                'pointBackgroundColor' => '#fff',
                'pointBorderWidth' => 2
            ]]
        ];
    }

    /**
     * Get chart data for inventory vs sales
     */
    public function getInventorySalesChartData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Items Sold',
                    'data' => [440, 510, 470, 590, 630, 680],
                    'backgroundColor' => '#6366f1',
                    'borderRadius' => 8,
                    'barThickness' => 22
                ],
                [
                    'type' => 'line',
                    'label' => 'Stock Level',
                    'data' => [700, 850, 720, 950, 880, 1020],
                    'borderColor' => '#10b981',
                    'borderWidth' => 3,
                    'tension' => 0,
                    'fill' => false,
                    'pointStyle' => 'circle'
                ]
            ]
        ];
    }

    /**
     * Get expiration center data
     */
    public function getExpirationData(): array
    {
        return [
            'itemsNearExpiry' => $this->getItemsNearExpiry(),
            'criticalItems' => $this->getCriticalItems(),
            'warningItems' => $this->getWarningItems(),
            'daysUntilNextExpiry' => 7,
        ];
    }

    /**
     * Get critical expiry items
     */
    public function getCriticalExpiryItems(): array
    {
        return [
            ['Sample Item A', '#12345', 'Mar 14, 2026'],
            ['Sample Item B', '#12346', 'Mar 15, 2026'],
            ['Sample Item C', '#12347', 'Mar 16, 2026'],
        ];
    }

    /**
     * Get warning expiry items by category
     */
    public function getWarningItemsByCategory(): array
    {
        return [
            'Electronics' => rand(10, 40),
            'Food' => rand(10, 40),
            'Medicine' => rand(10, 40),
            'Chemicals' => rand(10, 40),
        ];
    }

    /**
     * Calculate expiry risk level
     */
    private function calculateExpiryRiskLevel(): string
    {
        $criticalItems = $this->getCriticalItems();
        if ($criticalItems > 50) {
            return 'high';
        } elseif ($criticalItems > 20) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get greeting based on time
     */
    public function getGreeting(): string
    {
        $currentHour = date('H');
        return $currentHour < 12 ? 'Good Morning' : ($currentHour < 18 ? 'Good Afternoon' : 'Good Evening');
    }

    // Mock data methods - in real app these would query database
    private function getTotalProducts(): int { return 5483; }
    private function getTotalOrders(): int { return 2859; }
    private function getTotalStock(): int { return 5483; }
    private function getOutOfStockCount(): int { return 38; }
    private function getItemsNearExpiry(): int { return 127; }
    private function getCriticalItems(): int { return 23; }
    private function getWarningItems(): int { return 104; }
}