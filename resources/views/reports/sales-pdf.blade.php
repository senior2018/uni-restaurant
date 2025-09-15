<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - {{ ucfirst($period) }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2D5A27;
            padding-bottom: 20px;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .generated-info {
            font-size: 12px;
            color: #888;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2D5A27;
        }

        .summary-title {
            font-size: 16px;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 5px;
        }

        .summary-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .summary-grid-6 {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
        }

        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background: #2D5A27;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 12px;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .data-table tr:hover {
            background: #e8f5e8;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
        }

        .currency {
            font-weight: bold;
            color: #2D5A27;
        }

        .tzs {
            font-weight: bold;
            color: #2D5A27;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="restaurant-name">{{ $restaurantName }}</div>
        <div class="report-title">SALES REPORT</div>
        <div class="report-period">{{ strtoupper($period) }} REPORT @if($year) - {{ $year }} @endif</div>
        <div class="generated-info">Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">SUMMARY</div>
        <div class="summary-grid-6">
            <div class="summary-item">
                <div class="summary-value tzs">{{ number_format($data['summary']['total_revenue'] ?? 0, 0) }} TZS</div>
                <div class="summary-label">Total Revenue</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $data['summary']['total_orders'] ?? 0 }}</div>
                <div class="summary-label">Total Orders</div>
            </div>
            <div class="summary-item">
                <div class="summary-value tzs">{{ number_format($data['summary']['average_order_value'] ?? 0, 0) }} TZS</div>
                <div class="summary-label">Average Order Value</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $data['summary']['total_items_sold'] ?? 0 }}</div>
                <div class="summary-label">Items Sold</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $data['summary']['cancelled_orders'] ?? 0 }}</div>
                <div class="summary-label">Cancelled Orders</div>
            </div>
            <div class="summary-item">
                <div class="summary-value tzs">{{ number_format($data['summary']['refunded_amount'] ?? 0, 0) }} TZS</div>
                <div class="summary-label">Refunded Amount</div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown -->
    @if(isset($data['daily_breakdown']) && count($data['daily_breakdown']) > 0)
    <div class="data-section">
        <div class="section-title">DAILY BREAKDOWN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Items Sold</th>
                    <th>Avg Order Value</th>
                    <th>Top Meal</th>
                    <th>Cancelled</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['daily_breakdown'] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}</td>
                    <td>{{ $day['total_orders'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($day['total_revenue'] ?? 0, 0) }} TZS</td>
                    <td>{{ $day['total_items_sold'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($day['average_order_value'] ?? 0, 0) }} TZS</td>
                    <td>{{ $day['top_meal'] ?? 'N/A' }}</td>
                    <td>{{ $day['cancelled_orders'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top Selling Meals -->
    @if(isset($data['top_meals']) && count($data['top_meals']) > 0)
    <div class="data-section">
        <div class="section-title">TOP SELLING MEALS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Meal Name</th>
                    <th>Category</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_meals'] as $index => $meal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $meal['name'] ?? 'N/A' }}</td>
                    <td>{{ $meal['category'] ?? 'Uncategorized' }}</td>
                    <td>{{ $meal['quantity'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($meal['revenue'] ?? 0, 0) }} TZS</td>
                    <td class="tzs">{{ number_format($meal['price'] ?? 0, 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Payment Methods Breakdown -->
    @if(isset($data['payment_methods']) && count($data['payment_methods']) > 0)
    <div class="data-section">
        <div class="section-title">PAYMENT METHODS BREAKDOWN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Orders</th>
                    <th>Amount</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['payment_methods'] as $method)
                <tr>
                    <td>{{ ucfirst($method['method'] ?? 'Unknown') }}</td>
                    <td>{{ $method['count'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($method['amount'] ?? 0, 0) }} TZS</td>
                    <td>{{ $method['percentage'] ?? 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Category Breakdown -->
    @if(isset($data['category_breakdown']) && count($data['category_breakdown']) > 0)
    <div class="data-section">
        <div class="section-title">CATEGORY BREAKDOWN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Orders</th>
                    <th>Items Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['category_breakdown'] as $category)
                <tr>
                    <td>{{ $category['category'] ?? 'Uncategorized' }}</td>
                    <td>{{ $category['orders'] ?? 0 }}</td>
                    <td>{{ $category['items_sold'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($category['revenue'] ?? 0, 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Peak Hours -->
    @if(isset($data['peak_hours']) && count($data['peak_hours']) > 0)
    <div class="data-section">
        <div class="section-title">PEAK SALES HOURS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hour</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['peak_hours'] as $hour)
                <tr>
                    <td>{{ $hour['hour'] ?? 'N/A' }}</td>
                    <td>{{ $hour['orders'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($hour['revenue'] ?? 0, 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Staff Performance -->
    @if(isset($data['staff_performance']) && count($data['staff_performance']) > 0)
    <div class="data-section">
        <div class="section-title">STAFF PERFORMANCE</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Staff Member</th>
                    <th>Orders Processed</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['staff_performance'] as $staff)
                <tr>
                    <td>{{ $staff['staff_name'] ?? 'Unknown' }}</td>
                    <td>{{ $staff['orders_processed'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($staff['total_sales'] ?? 0, 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Peak Hours -->
    @if(isset($data['peak_hours']) && count($data['peak_hours']) > 0)
    <div class="data-section">
        <div class="section-title">PEAK SALES HOURS (TOP 5)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hour</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['peak_hours'] as $hour)
                <tr>
                    <td>{{ $hour['hour'] ?? 'N/A' }}</td>
                    <td>{{ $hour['orders'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($hour['revenue'] ?? 0, 0) }} TZS</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by {{ $restaurantName }} Restaurant Management System</p>
        <p>For questions about this report, please contact the restaurant management</p>
    </div>
</body>
</html>
