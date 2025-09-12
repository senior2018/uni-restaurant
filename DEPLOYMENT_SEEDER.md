# Deployment Seeder Documentation

## Overview

The Deployment Seeder is a comprehensive database seeding solution designed specifically for production deployments. It creates realistic sample data for the Our Restaurant management system, optimized for Render's free tier database limitations (1GB).

## Features

### **Restaurant Data**
- **8 Meal Categories**: Appetizers, Main Courses, Desserts, Beverages, Salads, Soups, Pasta, Pizza
- **25+ Meals**: Realistic menu items with descriptions, prices, and preparation times
- **Varied Pricing**: $2.99 - $32.99 range for different meal types

### **User Management**
- **1 Super Admin**: Full system access
- **1 Admin**: Restaurant management access
- **5 Staff Members**: Kitchen and service staff
- **20 Customers**: Diverse customer base with realistic profiles

### **Order System**
- **50 Orders**: Historical order data with realistic statuses
- **1-4 Items per Order**: Varied order complexity
- **Multiple Statuses**: pending, confirmed, preparing, ready, delivered, cancelled
- **Realistic Timestamps**: Orders spread over the last 30 days

### **Reviews & Feedback**
- **Customer Ratings**: 3-5 star ratings with realistic comments
- **70% Rating Rate**: Based on delivered orders
- **Contextual Comments**: Rating-appropriate feedback

### **Support System**
- **15 Alerts**: Various customer issues and complaints
- **20 Support Tickets**: Different priority levels and statuses
- **Realistic Scenarios**: Common restaurant management issues

### **Security**
- **OTP Verifications**: Email verification records
- **Secure Passwords**: All users have 'password' as default (change in production)

## Database Usage

### Estimated Space Usage
- **Users**: ~50 records × 1KB = ~50KB
- **Meal Categories**: 8 records × 0.5KB = ~4KB
- **Meals**: 25 records × 2KB = ~50KB
- **Orders**: 50 records × 1KB = ~50KB
- **Order Items**: ~150 records × 0.5KB = ~75KB
- **Ratings**: ~35 records × 1KB = ~35KB
- **Alerts**: 15 records × 0.5KB = ~8KB
- **Support Tickets**: 20 records × 1KB = ~20KB
- **OTP Verifications**: 10 records × 0.5KB = ~5KB

**Total Estimated Usage**: ~300KB (0.03% of 1GB limit)

## Usage

### Automatic Deployment
The seeder runs automatically during deployment via the Dockerfile:

```bash
php artisan db:seed --class=DeploymentSeeder --force
```

### Manual Execution
Run the seeder manually using the custom command:

```bash
# In development
php artisan seed:deployment

# In production (with force flag)
php artisan seed:deployment --force
```

### Standard Laravel Seeding
```bash
# Run all seeders (uses DeploymentSeeder in production)
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DeploymentSeeder
```

## Default Credentials

### Super Admin
- **Email**: superadmin@unirestaurant.com
- **Password**: password
- **Role**: super_admin

### Admin
- **Email**: admin@unirestaurant.com
- **Password**: password
- **Role**: admin

### Staff Members
- **Email**: [firstname].[lastname]@unirestaurant.com
- **Password**: password
- **Role**: staff

### Customers
- **Email**: [firstname].[lastname]@example.com
- **Password**: password
- **Role**: customer

## Data Characteristics

### Realistic Order Patterns
- Orders distributed over 30 days
- Varied order sizes (1-4 items)
- Realistic status progression
- Different delivery addresses

### Authentic Reviews
- 5-star: "Excellent food and service!"
- 4-star: "Very good food, minor issues with timing."
- 3-star: "Average experience, room for improvement."

### Common Support Issues
- Food quality issues
- Late deliveries
- Missing items
- Payment problems
- General inquiries

## Customization

### Adding More Data
To add more records, modify the `DeploymentSeeder.php` file:

```php
// Increase order count
for ($i = 0; $i < 100; $i++) { // Changed from 50 to 100

// Add more customers
$customerNames = [
    // Add more names here
];
```

### Modifying Meal Data
Update the `$meals` array in the `createMeals()` method:

```php
$meals = [
    [
        'name' => 'Your Custom Meal',
        'description' => 'Description here',
        'price' => 15.99,
        'category_id' => $mainCourses->id,
        'is_available' => true,
        'preparation_time' => 20,
    ],
    // Add more meals...
];
```

## Environment Detection

The seeder automatically detects the environment:

- **Production**: Uses `DeploymentSeeder` only
- **Development**: Uses individual seeders for testing

## Best Practices

### Security
1. **Change Default Passwords**: Update all default passwords in production
2. **Email Verification**: Ensure email verification is working
3. **Role Permissions**: Verify role-based access controls

### Performance
1. **Database Indexing**: Ensure proper indexes are in place
2. **Query Optimization**: Monitor database performance
3. **Caching**: Enable appropriate caching strategies

### Maintenance
1. **Regular Backups**: Backup database before major changes
2. **Data Cleanup**: Remove old test data periodically
3. **Monitoring**: Monitor database size and performance

## Troubleshooting

### Common Issues

#### Seeding Fails
```bash
# Check database connection
php artisan migrate:status

# Clear cache and try again
php artisan cache:clear
php artisan config:clear
php artisan db:seed --class=DeploymentSeeder
```

#### Memory Issues
```bash
# Increase memory limit
php -d memory_limit=512M artisan db:seed --class=DeploymentSeeder
```

#### Permission Issues
```bash
# Ensure proper file permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Support

For issues or questions about the deployment seeder:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify database connectivity
3. Ensure all migrations are up to date
4. Check environment configuration

## Version History

- **v1.0**: Initial deployment seeder with comprehensive data
- **v1.1**: Added responsive design enhancements
- **v1.2**: Optimized for Render free tier limitations
