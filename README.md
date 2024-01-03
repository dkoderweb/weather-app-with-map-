# Weather App with Map

This web application, built with Laravel, provides weather information based on the user's current location and allows searching for weather details by clicking on the map.

## Getting Started

Follow these steps to set up and run the project locally:

1. **Clone the repository:**
   ```bash
   git clone [repository_url]
   ```

2. **Navigate to the project directory:**
   ```bash
   cd weather-app-with-map-
   ```

3. **Copy the example environment file:**
   ```bash
   cp example.env .env
   ```

4. **Configure your environment variables:**
   - Edit the `.env` file and provide the required variables, including your OpenWeatherMap API key.

5. **Install dependencies:**
   ```bash
   composer install
   ```

6. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

7. **Run the development server:**
   ```bash
   php artisan serve
   ```

8. **Visit [http://localhost:8000](http://localhost:8000) in your browser.**

## Usage

Upon loading the page, the weather for the current location will be displayed. You can filter the temperature unit and search for weather information in different locations by clicking on the map.

## Contributing

Thank you for considering contributing to the Weather App with Map! Please review the [contribution guide](CONTRIBUTING.md) for more details.

## Code of Conduct

To ensure a welcoming community, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

If you discover any security vulnerabilities, please report them to Taylor Otwell via taylor@laravel.com.

## License

This Laravel application is open-source software licensed under the MIT license.

Make sure to replace `[repository_url]` with the actual URL of your Git repository. Feel free to adjust the formatting and details based on your specific project requirements.

---

**Note:** Ensure that you have PHP, Composer, and Laravel installed on your system before following these steps. If not, please refer to the official documentation for installation instructions:

- [PHP](https://www.php.net/manual/en/install.php)
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [Laravel](https://laravel.com/docs/8.x/installation)