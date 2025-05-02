# University Restaurant Food Ordering Platform

## Project Overview

This web application is designed for a university restaurant, targeting students, lecturers, and staff, to facilitate online food ordering and delivery. The platform allows users to browse the menu, place orders, rate meals and deliveries, and report any issues with their orders. The backend is built using **Laravel 12**, and the frontend leverages **Tailwind CSS** for styling. The application uses **PostgreSQL** for database management.

## Features

- **User Authentication**: Users can register, log in, and manage their accounts.
- **Role-Based Access Control**: Users have different roles (`customer`, `staff`, `admin`), with each role having different permissions.
- **Meal Management**: Admins and staff can manage meal categories and meals (CRUD functionality).
- **Order System**: Customers can place orders, select payment methods, and specify delivery locations. Admins and staff can manage orders, update their status (pending, preparing, delivered).
- **Rating & Reviews**: Customers can rate food and delivery services. Staff can respond to ratings.
- **Alerts & Notifications**: Customers can raise alerts for issues with orders. Staff/admins are notified and can resolve the alerts.
- **Payment Integration**: Support for multiple payment methods (cash, mobile money, card).
- **Order History**: Customers can view past orders and reorder meals.

## Technologies Used

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS, Vue.js
- **Database**: PostgreSQL
- **Authentication**: Laravel Breeze (for simple authentication system)
- **Payment Gateway**: Mobile money, card, and cash (integration in progress)

## Project Setup

### Prerequisites

Before getting started, ensure you have the following installed on your machine:

- PHP (8.3.17 or higher)
- Composer
- Laravel 12
- PostgreSQL
- Apache2 (if using for deployment)

### Installation Steps

1. **Clone the Repository**:
    ```bash
    git clone https://github.com/yourusername/restaurant-ordering-system.git
    cd restaurant-ordering-system
    ```

2. **Install Dependencies**:
    ```bash
    composer install
    npm install
    ```

3. **Set Up Environment**:
    - Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```
    - Configure your `.env` file with your database and other environment-specific settings.

4. **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```

5. **Run Migrations**:
    - Run the migrations to set up the database:
    ```bash
    php artisan migrate
    ```

6. **Seed the Database**:
    - Seed the database with sample data using factories:
    ```bash
    php artisan db:seed
    ```

7. **Serve the Application**:
    - Start the development server:
    ```bash
    php artisan serve
    ```

    Alternatively, you can configure Apache2 to serve the app if using a production environment.

8. **Frontend Development**:
    - Compile assets for development:
    ```bash
    npm run dev
    ```
    - For production, use:
    ```bash
    npm run prod
    ```

## Directory Structure
/app /Models # Eloquent models /Http /Controllers # Controllers to handle business logic /Middleware # Middleware for handling requests /database /migrations # Database migrations /factories # Database factories for testing and seeding /resources /views # Blade templates for the frontend /js # JavaScript files (Vue.js components) /css # Tailwind CSS files /routes web.php # Routes for the application



## Features to Implement

1. **Authentication & Authorization**:
   - Implement Laravel Breeze or Jetstream for user authentication.
   - Add role-based access control for users (customers, staff, and admins).

2. **Admin Panel**:
   - Develop a comprehensive admin dashboard to manage meals, users, orders, and ratings.

3. **Order Management**:
   - Build the backend logic to manage order status and update order progress.

4. **Rating & Review System**:
   - Complete the functionality for customers to rate meals and delivery, and for staff to respond.

5. **Payment Integration**:
   - Finalize integration with payment gateways for mobile money, cash, and card payments.

6. **Alerts and Notifications**:
   - Develop a notification system for alerts (e.g., delayed orders, incorrect meals, etc.).

7. **Testing & Bug Fixing**:
   - Write unit and integration tests to ensure the functionality is robust and bug-free.

8. **Deployment**:
   - Prepare the application for deployment on a production server.
   - Set up environment variables for production (e.g., database credentials, payment API keys).

9. **Post-Deployment Monitoring & Updates**:
   - Monitor application performance and resolve any issues post-launch.
   - Implement any necessary updates based on user feedback.

## Contributing

Feel free to fork the repository, submit issues, and send pull requests. Please ensure your contributions follow the coding standards of this project.

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-xyz`).
3. Commit your changes (`git commit -am 'Add feature xyz'`).
4. Push to the branch (`git push origin feature-xyz`).
5. Open a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

This README will be updated as the project evolves and new features are added.





## Factories:
Factories are classes that define how data should be generated for models. They typically use a library like Faker to generate realistic, fake data. Factories are often used during testing to quickly generate multiple model instances with different attributes, allowing you to test different scenarios. They can also be used to generate data related to model relationships. 

## Seeders:
Seeders are classes that contain instructions for inserting data into your database. They are commonly used to insert initial data that your application needs to function, such as roles, categories, or system settings. Seeders ensure that every developer on a team has the same initial data, which can help prevent bugs and inconsistencies. 

## Key Porpose:
Factories can generate data related to model relationships, while seeders typically insert data directly into the database without managing relationships. 
