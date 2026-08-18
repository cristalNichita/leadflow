<?php

namespace App\Data\Dashboard;

final readonly class DashboardMetricsData
{
    public function __construct(
        public int $totalCustomers,
        public int $activeLeads,
        public int $openDeals,
        public float $wonRevenue,
    ) {}

    /**
     * @return array{
     *     total_customers: int,
     *     active_leads: int,
     *     open_deals: int,
     *     won_revenue: float
     * }
     */
    public function toArray(): array
    {
        return [
            'total_customers' => $this->totalCustomers,
            'active_leads' => $this->activeLeads,
            'open_deals' => $this->openDeals,
            'won_revenue' => $this->wonRevenue,
        ];
    }
}
