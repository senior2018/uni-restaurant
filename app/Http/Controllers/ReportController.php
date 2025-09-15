<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Meal;
use App\Models\Rating;
use App\Models\OrderItem;
use App\Exports\SalesReportExport;
use App\Exports\InventoryReportExport;
use App\Models\RestaurantConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get the restaurant configuration
     */
    private function getRestaurantConfig()
    {
        return RestaurantConfig::current();
    }

    /**
     * Get the currency symbol
     */
    private function getCurrencySymbol()
    {
        return $this->getRestaurantConfig()->currency_symbol ?? 'TSh';
    }
    /**
     * Display the reports index page
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Admin/Reports/Index');
    }

    /**
     * Generate sales report
     */
    public function salesReport(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $week = $request->get('week');
        $day = $request->get('day');
        $format = $request->get('format', 'json'); // json, csv, pdf

        $data = $this->getSalesData($period, $startDate, $endDate, $year, $month, $week, $day);

        if ($format === 'csv') {
            return $this->exportSalesCSV($data, $period);
        } elseif ($format === 'excel') {
            return $this->exportSalesExcel($data, $period, $year);
        } elseif ($format === 'pdf') {
            return $this->exportSalesPDF($data, $period, $year);
        }

        // For JSON format (preview), return Inertia response
        return inertia('Admin/Reports/Preview', [
            'reportData' => $data,
            'reportType' => 'sales',
            'period' => $period,
            'year' => $year,
        ]);
    }

    /**
     * Generate customer feedback analysis report
     */
    public function feedbackReport(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $format = $request->get('format', 'json');

        $data = $this->getFeedbackData($period, $startDate, $endDate);

        if ($format === 'csv') {
            return $this->exportFeedbackCSV($data, $period);
        } elseif ($format === 'pdf') {
            return $this->exportFeedbackPDF($data, $period);
        }

        // For JSON format (preview), return Inertia response
        return inertia('Admin/Reports/Preview', [
            'reportData' => $data,
            'reportType' => 'feedback',
            'period' => $period,
        ]);
    }

    /**
     * Generate inventory and availability report
     */
    public function inventoryReport(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $format = $request->get('format', 'json');

        $data = $this->getInventoryData();

        if ($format === 'csv') {
            return $this->exportInventoryCSV($data);
        } elseif ($format === 'excel') {
            return $this->exportInventoryExcel($data);
        } elseif ($format === 'pdf') {
            return $this->exportInventoryPDF($data);
        }

        // For JSON format (preview), return Inertia response
        return inertia('Admin/Reports/Preview', [
            'reportData' => $data,
            'reportType' => 'inventory',
            'period' => 'current',
        ]);
    }

    /**
     * Get comprehensive analytics data
     */
    public function analyticsReport(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $format = $request->get('format', 'json');

        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $week = $request->get('week');
        $day = $request->get('day');

        $data = [
            'sales' => $this->getSalesData($period, $startDate, $endDate, $year, $month, $week, $day),
            'feedback' => $this->getFeedbackData($period, $startDate, $endDate),
            'inventory' => $this->getInventoryData(),
            'users' => $this->getUserData($period, $startDate, $endDate),
            'orders' => $this->getOrderData($period, $startDate, $endDate),
        ];

        if ($format === 'csv') {
            return $this->exportAnalyticsCSV($data, $period);
        } elseif ($format === 'pdf') {
            return $this->exportAnalyticsPDF($data, $period);
        }

        // For JSON format (preview), return Inertia response
        return inertia('Admin/Reports/Preview', [
            'reportData' => $data,
            'reportType' => 'analytics',
            'period' => $period,
            'year' => $year,
        ]);
    }

    /**
     * Get sales data for the specified period
     */
    private function getSalesData($period, $startDate = null, $endDate = null, $year = null, $month = null, $week = null, $day = null)
    {
        $query = Order::where('status', 'delivered');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query = $this->applyPeriodFilter($query, $period, $year, $month, $week, $day);
        }

        $orders = $query->with(['items.meal', 'items.meal.category', 'user'])->get();

        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Calculate total items sold
        $totalItemsSold = $orders->sum(function ($order) {
            return $order->items->sum('quantity');
        });

        // Calculate cancelled/refunded orders
        $cancelledOrders = $orders->where('status', 'cancelled')->count();
        $refundedAmount = $orders->where('status', 'cancelled')->sum('total_price');

        // Daily breakdown with enhanced data
        $dailyBreakdown = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            $dayRevenue = $dayOrders->sum('total_price');
            $dayOrderCount = $dayOrders->count();
            $dayAverageOrderValue = $dayOrderCount > 0 ? $dayRevenue / $dayOrderCount : 0;
            $dayItemsSold = $dayOrders->sum(function ($order) {
                return $order->items->sum('quantity');
            });

            // Get top meal for the day
            $mealCounts = [];
            foreach ($dayOrders as $order) {
                foreach ($order->items as $item) {
                    $mealName = $item->meal->name ?? 'Unknown';
                    $mealCounts[$mealName] = ($mealCounts[$mealName] ?? 0) + $item->quantity;
                }
            }
            $topMeal = !empty($mealCounts) ? array_keys($mealCounts, max($mealCounts))[0] : 'N/A';

            // Payment methods for the day
            $paymentMethods = $dayOrders->groupBy('payment_method')->map(function ($orders) {
                return [
                    'count' => $orders->count(),
                    'amount' => $orders->sum('total_price'),
                ];
            });

            return [
                'date' => $dayOrders->first()->created_at->format('Y-m-d'),
                'total_revenue' => $dayRevenue,
                'total_orders' => $dayOrderCount,
                'average_order_value' => $dayAverageOrderValue,
                'total_items_sold' => $dayItemsSold,
                'top_meal' => $topMeal,
                'payment_methods' => $paymentMethods,
                'cancelled_orders' => $dayOrders->where('status', 'cancelled')->count(),
            ];
        })->values();

        // Top selling meals with enhanced data
        $mealSales = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $mealName = $item->meal->name ?? 'Unknown';
                $mealCategory = $item->meal->category->name ?? 'Uncategorized';
                if (!isset($mealSales[$mealName])) {
                    $mealSales[$mealName] = [
                        'name' => $mealName,
                        'category' => $mealCategory,
                        'quantity' => 0,
                        'revenue' => 0,
                        'price' => $item->meal->price ?? 0,
                    ];
                }
                $mealSales[$mealName]['quantity'] += $item->quantity;
                $mealSales[$mealName]['revenue'] += $item->price * $item->quantity;
            }
        }

        $topMeals = collect($mealSales)->sortByDesc('revenue')->take(10)->values();

        // Payment methods breakdown
        $paymentMethods = $orders->groupBy('payment_method')->map(function ($orders) {
            return [
                'method' => $orders->first()->payment_method ?? 'Unknown',
                'count' => $orders->count(),
                'amount' => $orders->sum('total_price'),
                'percentage' => 0, // Will be calculated below
            ];
        })->values();

        // Calculate payment method percentages
        $totalPaymentAmount = $paymentMethods->sum('amount');
        $paymentMethods = $paymentMethods->map(function ($method) use ($totalPaymentAmount) {
            $method['percentage'] = $totalPaymentAmount > 0 ? round(($method['amount'] / $totalPaymentAmount) * 100, 1) : 0;
            return $method;
        });

        // Staff performance (if staff_id is available)
        $staffPerformance = $orders->whereNotNull('staff_id')->groupBy('staff_id')->map(function ($orders) {
            $staff = $orders->first()->staff ?? null;
            return [
                'staff_name' => $staff ? $staff->name : 'Unknown Staff',
                'orders_processed' => $orders->count(),
                'total_sales' => $orders->sum('total_price'),
            ];
        })->sortByDesc('total_sales')->take(5)->values();

        // Category breakdown
        $categoryBreakdown = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $categoryName = $item->meal->category->name ?? 'Uncategorized';
                if (!isset($categoryBreakdown[$categoryName])) {
                    $categoryBreakdown[$categoryName] = [
                        'category' => $categoryName,
                        'orders' => 0,
                        'items_sold' => 0,
                        'revenue' => 0,
                    ];
                }
                $categoryBreakdown[$categoryName]['items_sold'] += $item->quantity;
                $categoryBreakdown[$categoryName]['revenue'] += $item->quantity * $item->price;
            }
        }

        // Count unique orders per category
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $categoryName = $item->meal->category->name ?? 'Uncategorized';
                $categoryBreakdown[$categoryName]['orders']++;
                break; // Count each order only once per category
            }
        }

        $categoryBreakdown = collect($categoryBreakdown)->sortByDesc('revenue')->values();

        // Peak hours analysis
        $peakHours = $orders->groupBy(function ($order) {
            return $order->created_at->format('H');
        })->map(function ($hourOrders) {
            return [
                'hour' => $hourOrders->first()->created_at->format('H:00'),
                'orders' => $hourOrders->count(),
                'revenue' => $hourOrders->sum('total_price'),
            ];
        })->sortByDesc('revenue')->take(5)->values();

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'year' => $year,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'average_order_value' => round($averageOrderValue, 2),
                'total_items_sold' => $totalItemsSold,
                'cancelled_orders' => $cancelledOrders,
                'refunded_amount' => $refundedAmount,
            ],
            'daily_breakdown' => $dailyBreakdown,
            'top_meals' => $topMeals,
            'payment_methods' => $paymentMethods,
            'staff_performance' => $staffPerformance,
            'category_breakdown' => $categoryBreakdown,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get feedback data for the specified period
     */
    private function getFeedbackData($period, $startDate = null, $endDate = null)
    {
        $query = Rating::with(['order', 'order.user']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query = $this->applyPeriodFilter($query, $period);
        }

        $ratings = $query->get();

        $totalRatings = $ratings->count();
        $averageRating = $totalRatings > 0 ? $ratings->avg('rating') : 0;

        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $ratings->where('rating', $i)->count();
            $distribution[] = [
                'rating' => $i,
                'count' => $count,
                'percentage' => $totalRatings > 0 ? round(($count / $totalRatings) * 100, 2) : 0,
            ];
        }

        // Comments analysis
        $comments = $ratings->whereNotNull('comment')->pluck('comment');
        $positiveComments = $ratings->where('rating', '>=', 4)->whereNotNull('comment')->count();
        $negativeComments = $ratings->where('rating', '<=', 2)->whereNotNull('comment')->count();

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'summary' => [
                'total_ratings' => $totalRatings,
                'average_rating' => round($averageRating, 2),
                'positive_feedback' => $positiveComments,
                'negative_feedback' => $negativeComments,
            ],
            'distribution' => $distribution,
            'recent_comments' => $ratings->whereNotNull('comment')->take(20)->map(function ($rating) {
                return [
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'user' => $rating->order->user->name ?? 'Anonymous',
                    'date' => $rating->created_at->format('Y-m-d H:i:s'),
                ];
            })->values(),
        ];
    }

    /**
     * Get inventory and availability data
     */
    private function getInventoryData()
    {
        $meals = Meal::with(['category', 'orderItems'])->get();

        $totalMeals = $meals->count();
        $availableMeals = $meals->where('is_available', true)->count();
        $unavailableMeals = $meals->where('is_available', false)->count();

        // Popular meals (based on order count)
        $popularMeals = $meals->map(function ($meal) {
            $orderCount = $meal->orderItems->sum('quantity');
            return [
                'id' => $meal->id,
                'name' => $meal->name,
                'category' => $meal->category->name ?? 'Uncategorized',
                'price' => $meal->price,
                'is_available' => $meal->is_available,
                'order_count' => $orderCount,
                'revenue' => $meal->orderItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }),
            ];
        })->sortByDesc('order_count')->take(20)->values();

        // Category breakdown
        $categoryBreakdown = $meals->groupBy('category.name')->map(function ($categoryMeals, $categoryName) {
            return [
                'category' => $categoryName ?: 'Uncategorized',
                'total_meals' => $categoryMeals->count(),
                'available_meals' => $categoryMeals->where('is_available', true)->count(),
                'unavailable_meals' => $categoryMeals->where('is_available', false)->count(),
            ];
        })->values();

        return [
            'summary' => [
                'total_meals' => $totalMeals,
                'available_meals' => $availableMeals,
                'unavailable_meals' => $unavailableMeals,
                'availability_percentage' => $totalMeals > 0 ? round(($availableMeals / $totalMeals) * 100, 2) : 0,
            ],
            'popular_meals' => $popularMeals,
            'category_breakdown' => $categoryBreakdown,
        ];
    }

    /**
     * Get user data for the specified period
     */
    private function getUserData($period, $startDate = null, $endDate = null)
    {
        $query = User::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query = $this->applyPeriodFilter($query, $period);
        }

        $users = $query->get();

        $totalUsers = $users->count();
        $customers = $users->where('role', 'customer')->count();
        $staff = $users->where('role', 'staff')->count();
        $admins = $users->where('role', 'admin')->count();

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'summary' => [
                'total_users' => $totalUsers,
                'customers' => $customers,
                'staff' => $staff,
                'admins' => $admins,
            ],
        ];
    }

    /**
     * Get order data for the specified period
     */
    private function getOrderData($period, $startDate = null, $endDate = null)
    {
        $query = Order::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query = $this->applyPeriodFilter($query, $period);
        }

        $orders = $query->get();

        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $processingOrders = $orders->where('status', 'processing')->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        $cancelledOrders = $orders->where('status', 'cancelled')->count();

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'summary' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'processing_orders' => $processingOrders,
                'delivered_orders' => $deliveredOrders,
                'cancelled_orders' => $cancelledOrders,
            ],
        ];
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, $period, $year = null, $month = null, $week = null, $day = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        $week = $week ?? now()->week;
        $day = $day ?? now()->day;

        switch ($period) {
            case 'daily':
                if ($year && $month && $day) {
                    return $query->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->whereDay('created_at', $day);
                }
                return $query->whereDate('created_at', today());

            case 'weekly':
                if ($year && $week) {
                    // Calculate the start and end of the specified week
                    $startOfWeek = now()->setYear($year)->setISODate($year, $week, 1)->startOfDay();
                    $endOfWeek = now()->setYear($year)->setISODate($year, $week, 7)->endOfDay();
                    return $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                }
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);

            case 'monthly':
                if ($year && $month) {
                    return $query->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month);
                }
                return $query->whereMonth('created_at', now()->month)->whereYear('created_at', $year);

            case 'yearly':
                if ($year) {
                    return $query->whereYear('created_at', $year);
                }
                return $query->whereYear('created_at', $year);

            default:
                return $query->whereMonth('created_at', now()->month)->whereYear('created_at', $year);
        }
    }

    /**
     * Export sales data as CSV
     */
    private function exportSalesCSV($data, $period)
    {
        $filename = "sales_report_{$period}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Summary row
            fputcsv($file, ['Report Type', 'Period', 'Total Revenue', 'Total Orders', 'Average Order Value']);
            fputcsv($file, [
                'Sales Report',
                $data['period'],
                $data['summary']['total_revenue'],
                $data['summary']['total_orders'],
                $data['summary']['average_order_value']
            ]);

            fputcsv($file, []); // Empty row

            // Daily breakdown
            fputcsv($file, ['Date', 'Orders', 'Revenue']);
            foreach ($data['daily_breakdown'] as $day) {
                fputcsv($file, [$day['date'], $day['orders'], $day['revenue']]);
            }

            fputcsv($file, []); // Empty row

            // Top meals
            fputcsv($file, ['Meal Name', 'Quantity Sold', 'Revenue']);
            foreach ($data['top_meals'] as $meal) {
                fputcsv($file, [$meal['name'], $meal['quantity'], $meal['revenue']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export feedback data as CSV
     */
    private function exportFeedbackCSV($data, $period)
    {
        $filename = "feedback_report_{$period}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Summary
            fputcsv($file, ['Report Type', 'Period', 'Total Ratings', 'Average Rating', 'Positive Feedback', 'Negative Feedback']);
            fputcsv($file, [
                'Feedback Report',
                $data['period'],
                $data['summary']['total_ratings'],
                $data['summary']['average_rating'],
                $data['summary']['positive_feedback'],
                $data['summary']['negative_feedback']
            ]);

            fputcsv($file, []); // Empty row

            // Rating distribution
            fputcsv($file, ['Rating', 'Count', 'Percentage']);
            foreach ($data['distribution'] as $dist) {
                fputcsv($file, [$dist['rating'], $dist['count'], $dist['percentage']]);
            }

            fputcsv($file, []); // Empty row

            // Recent comments
            fputcsv($file, ['Rating', 'Comment', 'User', 'Date']);
            foreach ($data['recent_comments'] as $comment) {
                fputcsv($file, [$comment['rating'], $comment['comment'], $comment['user'], $comment['date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export inventory data as CSV
     */
    private function exportInventoryCSV($data)
    {
        $filename = "inventory_report_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Summary
            fputcsv($file, ['Total Meals', 'Available Meals', 'Unavailable Meals', 'Availability Percentage']);
            fputcsv($file, [
                $data['summary']['total_meals'],
                $data['summary']['available_meals'],
                $data['summary']['unavailable_meals'],
                $data['summary']['availability_percentage']
            ]);

            fputcsv($file, []); // Empty row

            // Popular meals
            fputcsv($file, ['Meal Name', 'Category', 'Price', 'Available', 'Order Count', 'Revenue']);
            foreach ($data['popular_meals'] as $meal) {
                fputcsv($file, [
                    $meal['name'],
                    $meal['category'],
                    $meal['price'],
                    $meal['is_available'] ? 'Yes' : 'No',
                    $meal['order_count'],
                    $meal['revenue']
                ]);
            }

            fputcsv($file, []); // Empty row

            // Category breakdown
            fputcsv($file, ['Category', 'Total Meals', 'Available Meals', 'Unavailable Meals']);
            foreach ($data['category_breakdown'] as $category) {
                fputcsv($file, [
                    $category['category'],
                    $category['total_meals'],
                    $category['available_meals'],
                    $category['unavailable_meals']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export comprehensive analytics as CSV
     */
    private function exportAnalyticsCSV($data, $period)
    {
        $filename = "analytics_report_{$period}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Sales summary
            fputcsv($file, ['SALES SUMMARY']);
            fputcsv($file, ['Total Revenue', 'Total Orders', 'Average Order Value']);
            fputcsv($file, [
                $data['sales']['summary']['total_revenue'],
                $data['sales']['summary']['total_orders'],
                $data['sales']['summary']['average_order_value']
            ]);

            fputcsv($file, []); // Empty row

            // Feedback summary
            fputcsv($file, ['FEEDBACK SUMMARY']);
            fputcsv($file, ['Total Ratings', 'Average Rating', 'Positive Feedback', 'Negative Feedback']);
            fputcsv($file, [
                $data['feedback']['summary']['total_ratings'],
                $data['feedback']['summary']['average_rating'],
                $data['feedback']['summary']['positive_feedback'],
                $data['feedback']['summary']['negative_feedback']
            ]);

            fputcsv($file, []); // Empty row

            // Inventory summary
            fputcsv($file, ['INVENTORY SUMMARY']);
            fputcsv($file, ['Total Meals', 'Available Meals', 'Unavailable Meals', 'Availability Percentage']);
            fputcsv($file, [
                $data['inventory']['summary']['total_meals'],
                $data['inventory']['summary']['available_meals'],
                $data['inventory']['summary']['unavailable_meals'],
                $data['inventory']['summary']['availability_percentage']
            ]);

            fputcsv($file, []); // Empty row

            // User summary
            fputcsv($file, ['USER SUMMARY']);
            fputcsv($file, ['Total Users', 'Customers', 'Staff', 'Admins']);
            fputcsv($file, [
                $data['users']['summary']['total_users'],
                $data['users']['summary']['customers'],
                $data['users']['summary']['staff'],
                $data['users']['summary']['admins']
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sales data as Excel
     */
    private function exportSalesExcel($data, $period, $year = null)
    {
        $filename = 'sales_report_' . $period . '_' . ($year ?? now()->year) . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new SalesReportExport($data, $period, $year), $filename);
    }

    /**
     * Export inventory data as Excel
     */
    private function exportInventoryExcel($data)
    {
        $filename = 'inventory_report_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new InventoryReportExport($data), $filename);
    }

    /**
     * Export sales data as PDF
     */
    private function exportSalesPDF($data, $period, $year = null)
    {
        $filename = 'sales_report_' . $period . '_' . ($year ?? now()->year) . '_' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'data' => $data,
            'period' => $period,
            'year' => $year,
            'generatedAt' => now(),
            'restaurantName' => 'OUR RESTAURANT'
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Export feedback data as PDF (placeholder)
     */
    private function exportFeedbackPDF($data, $period)
    {
        return response()->json([
            'message' => 'PDF export is not yet implemented. Please use CSV export.',
            'data' => $data
        ]);
    }

    /**
     * Export inventory data as PDF
     */
    private function exportInventoryPDF($data)
    {
        $filename = 'inventory_report_' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('reports.inventory-pdf', [
            'data' => $data,
            'generatedAt' => now(),
            'restaurantName' => 'OUR RESTAURANT'
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Export comprehensive analytics as PDF (placeholder)
     */
    private function exportAnalyticsPDF($data, $period)
    {
        return response()->json([
            'message' => 'PDF export is not yet implemented. Please use CSV export.',
            'data' => $data
        ]);
    }
}
