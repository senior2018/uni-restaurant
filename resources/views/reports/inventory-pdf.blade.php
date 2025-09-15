<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
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
            grid-template-columns: repeat(4, 1fr);
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

        .status-available {
            color: #28a745;
            font-weight: bold;
        }

        .status-unavailable {
            color: #dc3545;
            font-weight: bold;
        }

        .category-section {
            margin-bottom: 20px;
        }

        .category-title {
            font-size: 14px;
            font-weight: bold;
            color: #2D5A27;
            margin-bottom: 10px;
            background: #e8f5e8;
            padding: 8px 12px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="restaurant-name">{{ $restaurantName }}</div>
        <div class="report-title">INVENTORY & AVAILABILITY REPORT</div>
        <div class="report-period">CURRENT INVENTORY STATUS</div>
        <div class="generated-info">Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">INVENTORY SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $data['summary']['total_meals'] ?? 0 }}</div>
                <div class="summary-label">Total Meals</div>
            </div>
            <div class="summary-item">
                <div class="summary-value status-available">{{ $data['summary']['available_meals'] ?? 0 }}</div>
                <div class="summary-label">Available Meals</div>
            </div>
            <div class="summary-item">
                <div class="summary-value status-unavailable">{{ $data['summary']['unavailable_meals'] ?? 0 }}</div>
                <div class="summary-label">Unavailable Meals</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $data['summary']['availability_percentage'] ?? 0 }}%</div>
                <div class="summary-label">Availability Rate</div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if(isset($data['category_breakdown']) && count($data['category_breakdown']) > 0)
    <div class="data-section">
        <div class="section-title">CATEGORY BREAKDOWN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Meals</th>
                    <th>Available</th>
                    <th>Unavailable</th>
                    <th>Availability %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['category_breakdown'] as $category)
                <tr>
                    <td>{{ $category['category'] ?? 'Uncategorized' }}</td>
                    <td>{{ $category['total_meals'] ?? 0 }}</td>
                    <td class="status-available">{{ $category['available_meals'] ?? 0 }}</td>
                    <td class="status-unavailable">{{ $category['unavailable_meals'] ?? 0 }}</td>
                    <td>{{ $category['total_meals'] > 0 ? round(($category['available_meals'] / $category['total_meals']) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Popular Meals -->
    @if(isset($data['popular_meals']) && count($data['popular_meals']) > 0)
    <div class="data-section">
        <div class="section-title">POPULAR MEALS (TOP 20)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Meal Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['popular_meals'] as $index => $meal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $meal['name'] ?? 'N/A' }}</td>
                    <td>{{ $meal['category'] ?? 'Uncategorized' }}</td>
                    <td class="tzs">{{ number_format($meal['price'] ?? 0, 0) }} TZS</td>
                    <td>
                        @if($meal['is_available'])
                            <span class="status-available">Available</span>
                        @else
                            <span class="status-unavailable">Unavailable</span>
                        @endif
                    </td>
                    <td>{{ $meal['order_count'] ?? 0 }}</td>
                    <td class="tzs">{{ number_format($meal['revenue'] ?? 0, 0) }} TZS</td>
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
