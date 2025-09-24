<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License


## API Documentation

### Authentication
- `POST /users/register` — Register a new user
- `POST /users/login` — Login user
- `POST /users/logout` — Logout user (requires authentication)
- `GET /users/me` — Get current user profile (requires authentication)

### Products
- `GET /products` — List products
- `POST /products` — Create product
- `GET /products/{id}` — Get product details
- `PUT /products/{id}` — Update product
- `DELETE /products/{id}` — Delete product
- `POST /products/{product}/produce` — Mark product as produced
- `POST /products/pay` — Pay for products (requires authentication)

### Cart
- `GET /cart` — Get cart items (requires authentication)
- `POST /cart` — Add item to cart (requires authentication)
- `PUT /cart/{id}` — Update cart item (requires authentication)
- `DELETE /cart/{id}` — Remove item from cart (requires authentication)
- `DELETE /cart` — Clear cart (requires authentication)
- `POST /cart/buy-with-points` — Buy with points (requires authentication)

### Materials
- `GET /materials` — List available materials (requires authentication)

### Addresses & Phones
- `GET /addresses` — List user addresses (requires authentication, user role)
- `POST /addresses` — Add address (requires authentication, user role)
- `GET /phone-numbers` — List user phone numbers (requires authentication, user role)
- `POST /phone-numbers` — Add phone number (requires authentication, user role)
- `GET /phones` — List phones (requires authentication)
- `POST /phones` — Add phone (requires authentication)

### Requests
- `POST /requests` — Create request (requires authentication, user role)
- `GET /requests/{id}` — Get request details (requires authentication, user role)
- `GET /user/dashboard` — Get user dashboard (requires authentication, user role)

#### Admin Requests
- `GET /admin/requests` — List all requests (requires authentication, admin role)
- `POST /admin/requests/{id}/status` — Update request status (requires authentication, admin role)

#### Collector Requests
- `GET /collector/assignments` — List collector assignments (requires authentication, collector role)
- `POST /collector/requests/{id}/status` — Update request status (requires authentication, collector role)

### AI
- `POST /ai/diy-helper` — Get DIY helper suggestions

### Admin APIs
- `POST /admin/login` — Admin login
- `GET /admin/me` — Get authenticated admin info
- `GET /admin/users` — List users
- `POST /admin/users` — Create user
- `GET /admin/users/{id}` — Get user details
- `PUT /admin/users/{id}` — Update user
- `DELETE /admin/users/{id}` — Delete user
- `GET /admin/products` — List products
- `POST /admin/products` — Create product
- `GET /admin/products/{id}` — Get product details
- `PUT /admin/products/{id}` — Update product
- `DELETE /admin/products/{id}` — Delete product
- `GET /admin/materials` — List materials
- `POST /admin/materials` — Create material
- `GET /admin/materials/{id}` — Get material details
- `PUT /admin/materials/{id}` — Update material
- `DELETE /admin/materials/{id}` — Delete material
- `GET /admin/categories` — List categories
- `POST /admin/categories` — Create category
- `GET /admin/categories/{id}` — Get category details
- `PUT /admin/categories/{id}` — Update category
- `DELETE /admin/categories/{id}` — Delete category
- `GET /admin/dashboard/stats` — Get dashboard statistics
- `GET /admin/dashboard/activities` — Get dashboard activities

> **Note:** Most endpoints require authentication and/or specific user roles. Refer to your backend code for request/response details and validation rules.

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
