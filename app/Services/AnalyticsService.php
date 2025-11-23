<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get affiliate metrics for a given date range.
     *
     * @param User $affiliate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAffiliateMetrics(User $affiliate, Carbon $startDate, Carbon $endDate): array
    {
        // Cache affiliate analytics for 15 minutes
        $cacheKey = "affiliate_metrics_{$affiliate->id}_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($affiliate, $startDate, $endDate) {
            $totalVisits = $this->getTotalVisits($affiliate, $startDate, $endDate);
            $totalLeads = $this->getTotalLeads($affiliate, $startDate, $endDate);
            
            return [
                'total_visits' => $totalVisits,
                'total_leads' => $totalLeads,
                'conversion_rate' => $this->getConversionRate($totalVisits, $totalLeads),
                'device_breakdown' => $this->getDeviceBreakdown($affiliate, $startDate, $endDate),
                'top_properties' => $this->getTopProperties($affiliate, $startDate, $endDate),
            ];
        });
    }

    /**
     * Get total visits for an affiliate within a date range.
     *
     * @param User $affiliate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getTotalVisits(User $affiliate, Carbon $startDate, Carbon $endDate): int
    {
        return $affiliate->visits()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get total leads for an affiliate within a date range.
     *
     * @param User $affiliate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getTotalLeads(User $affiliate, Carbon $startDate, Carbon $endDate): int
    {
        return $affiliate->leads()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Calculate conversion rate.
     *
     * @param int $visits
     * @param int $leads
     * @return float
     */
    private function getConversionRate(int $visits, int $leads): float
    {
        if ($visits === 0) {
            return 0.0;
        }

        return round(($leads / $visits) * 100, 2);
    }

    /**
     * Get device breakdown (mobile vs desktop) for an affiliate.
     *
     * @param User $affiliate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getDeviceBreakdown(User $affiliate, Carbon $startDate, Carbon $endDate): array
    {
        $breakdown = $affiliate->visits()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->pluck('count', 'device')
            ->toArray();

        return [
            'mobile' => $breakdown['mobile'] ?? 0,
            'desktop' => $breakdown['desktop'] ?? 0,
        ];
    }

    /**
     * Get top performing properties by visit count for an affiliate.
     *
     * @param User $affiliate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    private function getTopProperties(User $affiliate, Carbon $startDate, Carbon $endDate): Collection
    {
        // Optimized query using aggregation and join to prevent N+1
        return DB::table('visits')
            ->join('properties', 'visits.property_id', '=', 'properties.id')
            ->where('visits.affiliate_id', $affiliate->id)
            ->whereBetween('visits.created_at', [$startDate, $endDate])
            ->whereNotNull('visits.property_id')
            ->select(
                'properties.id as property_id',
                'properties.title as property_title',
                'properties.slug as property_slug',
                DB::raw('COUNT(*) as visit_count')
            )
            ->groupBy('properties.id', 'properties.title', 'properties.slug')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'property_id' => $row->property_id,
                    'property_title' => $row->property_title,
                    'property_slug' => $row->property_slug,
                    'visit_count' => $row->visit_count,
                ];
            });
    }

    /**
     * Get global metrics for Super Admin dashboard.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getGlobalMetrics(Carbon $startDate, Carbon $endDate): array
    {
        // Cache global analytics for 15 minutes
        $cacheKey = "global_metrics_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($startDate, $endDate) {
            $totalVisits = $this->getGlobalTotalVisits($startDate, $endDate);
            $totalLeads = $this->getGlobalTotalLeads($startDate, $endDate);
            
            return [
                'total_traffic' => $totalVisits,
                'total_leads' => $totalLeads,
                'active_affiliates' => $this->getActiveAffiliatesCount($startDate, $endDate),
                'conversion_rate' => $this->getConversionRate($totalVisits, $totalLeads),
                'top_affiliates' => $this->getTopAffiliates($startDate, $endDate),
                'recent_leads' => $this->getRecentLeads(),
                'recent_property_views' => $this->getRecentPropertyViews(),
            ];
        });
    }

    /**
     * Get total visits across all affiliates.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getGlobalTotalVisits(Carbon $startDate, Carbon $endDate): int
    {
        return DB::table('visits')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get total leads across all affiliates.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getGlobalTotalLeads(Carbon $startDate, Carbon $endDate): int
    {
        return DB::table('leads')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get count of active affiliates (affiliates with at least one visit or lead).
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    private function getActiveAffiliatesCount(Carbon $startDate, Carbon $endDate): int
    {
        return User::whereNotNull('affiliate_code')
            ->where('status', 'active')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereHas('visits', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })->orWhereHas('leads', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            })
            ->count();
    }

    /**
     * Get top performing affiliates by lead count.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    private function getTopAffiliates(Carbon $startDate, Carbon $endDate): Collection
    {
        return User::whereNotNull('affiliate_code')
            ->withCount([
                'leads' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'visits' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->having('leads_count', '>', 0)
            ->orderByDesc('leads_count')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'affiliate_code' => $user->affiliate_code,
                    'leads_count' => $user->leads_count,
                    'visits_count' => $user->visits_count,
                    'conversion_rate' => $user->visits_count > 0 
                        ? round(($user->leads_count / $user->visits_count) * 100, 2) 
                        : 0,
                ];
            });
    }

    /**
     * Get recent leads across all affiliates.
     *
     * @param int $limit
     * @return Collection
     */
    private function getRecentLeads(int $limit = 10): Collection
    {
        return DB::table('leads')
            ->join('properties', 'leads.property_id', '=', 'properties.id')
            ->leftJoin('users', 'leads.affiliate_id', '=', 'users.id')
            ->select(
                'leads.id',
                'leads.name as visitor_name',
                'leads.whatsapp',
                'leads.status',
                'leads.created_at',
                'properties.title as property_title',
                'properties.slug as property_slug',
                'users.name as affiliate_name',
                'users.affiliate_code'
            )
            ->orderByDesc('leads.created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent property views across all affiliates.
     *
     * @param int $limit
     * @return Collection
     */
    private function getRecentPropertyViews(int $limit = 10): Collection
    {
        return DB::table('visits')
            ->join('users', 'visits.affiliate_id', '=', 'users.id')
            ->leftJoin('properties', 'visits.property_id', '=', 'properties.id')
            ->select(
                'visits.id',
                'visits.created_at',
                'visits.device',
                'visits.browser',
                'users.name as affiliate_name',
                'users.affiliate_code',
                'properties.title as property_title',
                'properties.slug as property_slug'
            )
            ->whereNotNull('visits.property_id')
            ->orderByDesc('visits.created_at')
            ->limit($limit)
            ->get();
    }
}
