# Laravel OUI Lookup API

The Laravel OUI Lookup API is a RESTful JSON API that allows you to look up the vendor's Organizationally Unique Identifier (OUI) based on MAC addresses. The API fetches the latest OUI data from the IEEE OUI database and provides real-time vendor information for the provided MAC addresses.

## Features

- Supports both single MAC lookup (GET request) and multiple MAC lookup (POST request).
- Handles randomised MAC addresses with the second character '2', '6', 'A', or 'E.'
- Parses OUI data from the IEEE OUI database and imports it into the database using a scheduled command.
- Provides vendor information (organization name and address) for the given MAC addresses.

## Installation

1. Clone the repository:

```bash
git clone https://github.com/your-username/laravel-oui-lookup-api.git
```

2. Install dependencies:

```bash
cd laravel-oui-lookup-api
composer install
```

3. Set up the database:

- Create a new database for the application.
- Update the database configuration in the `.env` file with your database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

4. Run the migration:

```bash
php artisan migrate
```

5. Run the scheduler (for automatic OUI data updates):

```bash
cd laravel-oui-lookup-api && php artisan schedule:run >> /dev/null 2>&1
```

## Usage

### Single MAC Lookup (GET Request)

To look up the vendor's OUI information for a single MAC address, send a GET request to the following endpoint:

```
GET /api/lookup/{mac}
```

Replace `{mac}` with the MAC address you want to look up. The MAC address can be in any of the following formats:

- `00-11-22-33-44-55`
- `00:11:22:33:44:55`
- `0001.1122.3344`
- `061122334455`

The response will be in JSON format and include the MAC address and vendor information, if available.

### Multiple MAC Lookup (POST Request)

To look up the vendor's OUI information for multiple MAC addresses, send a POST request to the following endpoint:

```
POST /api/lookup
```

Include the MAC addresses in the request body as an array with the key `mac_addresses`, like this:

```json
{
  "mac_addresses": [
    "00:11:22:33:44:55",
    "1C-1B-0D-EA-C0-00",
    "74D4357A5441"
  ]
}
```

The response will be in JSON format and include an array of MAC addresses along with their respective vendor information.

### Handling Randomised MAC Addresses

The API can handle randomised MAC addresses with the second character '2', '6', 'A', or 'E.' When a randomised MAC address is encountered, the response will indicate that it is a "Randomised MAC."
