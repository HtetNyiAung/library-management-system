# Library Management System

## Description

A comprehensive library management system built with Laravel and Docker. This project includes features for managing books, users, borrowings, and reservations.

## Features

- **Admin:** Create, update, and delete books, manage categories, and book copies.
- **User:** Browse and borrow books, make reservations.

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/HtetNyiAung/library-management-system.git
    ```

2. Navigate to the project directory:
    ```bash
    cd library-management-system
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Set up environment variables:
    ```bash
    cp .env.example .env
    ```

5. Generate the application key:
    ```bash
    php artisan key:generate
    ```

6. Run migrations:
    ```bash
    php artisan migrate
    ```

7. Start the application:
    ```bash
    php artisan serve
    ```

## Usage

- **Admin Access:** Use admin credentials to access admin functionalities.
- **User Access:** Use user credentials to access user functionalities.

## Contributing

Feel free to submit issues or pull requests. For detailed contribution guidelines, please check our [CONTRIBUTING.md](CONTRIBUTING.md) file.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
