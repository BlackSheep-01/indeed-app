
# Indeed
A full-stack job listing web application built with **Vanilla PHP** using a custom Laravel-inspired MVC architecture.
This project was created to deeply understand how backend frameworks work internally by building core features from scratch instead of relying on frameworks.


## Live Demo:  [Indeed](https://job-portal.iceiy.com/)


## Features :
- Session based User Authentication (Register/Login/Logout)
- Role-based Route Protection using Middleware
- CRUD Operations for Job Listings
- Search Listings by Keywords and Location
- Dynamic Routing with Route Parameters
- Flash Messages
- Form Validation & Sanitization
- Authorization Checks for Listing Ownership
- Responsive UI with Tailwind CSS
- Custom Error Handling
- PDO-based Database Layer with Prepared Statements
- Entire project is build using Object Oriented Programming


## Tech Stack
- PHP 8
- MySQL
- Composer (PSR-4 Autoloading)
- Apache
- Tailwind CSS
- Hosted using Aeonfree via Filezilla FTP
  


## Architecture & Concepts Used

### MVC Architecture
The project follows the MVC (Model-View-Controller) pattern to separate:
- Business Logic
- Routing
- Database Operations
- Views/UI

### Custom Routing System
A custom router was built from scratch supporting:
- GET / POST / PUT / DELETE routes
- Dynamic Route Parameters
- Route Matching
- Middleware Support
Example:
```php
$router->put("/listings/{id}", "ListingController@update", ["auth"]);
```

### Folder structure
    App/
     ├── Controllers/
     ├── Views/
    Framework/
     ├── Database.php
     ├── Router.php
     ├── Session.php
     ├── Validation.php
     ├── Middleware/
     config/
     public/
     vendor/
     routes.php
     composer.json

<img width="528" height="1080" alt="Screenshot (279)" src="https://github.com/user-attachments/assets/77a047b4-cad6-41b8-be34-0ae354fb2ddf" />





