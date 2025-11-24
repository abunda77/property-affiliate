# Property Affiliate API Documentation

This project uses [Scramble](https://scramble.dedoc.co/) (Dedoc) for automatic API documentation generation.

## 📚 Accessing Documentation

### Web Interface
- **Interactive Documentation**: [http://localhost/docs/api](http://localhost/docs/api)
- **Admin Panel**: [http://localhost/admin/api-documentation](http://localhost/admin/api-documentation)
- **OpenAPI Spec**: [http://localhost/docs/api.json](http://localhost/docs/api.json)

### From Filament Admin
1. Login to admin panel at `/admin`
2. Navigate to **Developer** → **API Documentation**
3. Browse interactive documentation or download OpenAPI spec

## 🔑 Authentication

The API uses **Laravel Sanctum** for authentication with Bearer tokens.

### Getting a Token

```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "mobile_app"
}
```

**Response:**
```json
{
  "access_token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "affiliate_code": "AFF123"
  }
}
```

### Using the Token

Include the token in the Authorization header:

```bash
Authorization: Bearer 1|abc123...
```

## 📋 Available Endpoints

### Authentication
- `POST /api/login` - Login and get access token
- `POST /api/logout` - Logout and revoke token (requires auth)
- `GET /api/user` - Get authenticated user info (requires auth)

### Properties
- `GET /api/properties` - List all properties (with pagination and filters)
- `GET /api/properties/featured` - Get featured properties
- `GET /api/properties/{slug}` - Get property details
- `POST /api/properties/track-click` - Track affiliate click (requires auth)

## 🔍 Example Requests

### List Properties with Filters

```bash
GET /api/properties?type=house&location=Jakarta&min_price=500000000&max_price=2000000000&page=1&per_page=15
```

### Get Property Details

```bash
GET /api/properties/luxury-villa-in-bali
```

### Track Affiliate Click

```bash
POST /api/properties/track-click
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "property_id": 1,
  "affiliate_code": "AFF123",
  "source": "facebook"
}
```

## 🛠️ Configuration

Scramble configuration is located at `config/scramble.php`:

```php
return [
    'api_path' => 'api',
    'api_domain' => null,
    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => 'Property Affiliate API...',
    ],
    'ui' => [
        'title' => 'Property Affiliate API Documentation',
        'theme' => 'system', // light, dark, or system
    ],
];
```

## 📝 Adding Documentation to Controllers

Scramble automatically generates documentation from your code. Enhance it with PHPDoc:

```php
/**
 * Display a listing of properties.
 * 
 * Returns a paginated list of all active properties.
 *
 * @queryParam page integer Page number. Example: 1
 * @queryParam type string Filter by type. Example: house
 * 
 * @response 200 {
 *   "data": [...]
 * }
 */
public function index(Request $request): JsonResponse
{
    // ...
}
```

## 🚀 Generating OpenAPI Spec

Export the OpenAPI specification:

```bash
php artisan scramble:export > openapi.json
```

## 🔒 Security

- API documentation is protected by middleware (see `config/scramble.php`)
- By default, only accessible in local environment
- For production, configure proper access control in `scramble.middleware`

## 📦 Updating Documentation

Documentation is generated automatically from:
- Route definitions in `routes/api.php`
- Controller methods and PHPDoc comments
- Form Request validation rules
- API Resource transformations

No manual updates needed! Just write good code and PHPDoc comments.

## 🎨 Customization

### Change Theme
Edit `config/scramble.php`:
```php
'ui' => [
    'theme' => 'dark', // or 'light', 'system'
],
```

### Add Custom Servers
```php
'servers' => [
    'Local' => 'http://localhost/api',
    'Staging' => 'https://staging.example.com/api',
    'Production' => 'https://api.example.com/api',
],
```

## 📖 Resources

- [Scramble Documentation](https://scramble.dedoc.co/)
- [OpenAPI Specification](https://swagger.io/specification/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

## 🐛 Troubleshooting

### Documentation not showing?
1. Clear cache: `php artisan cache:clear`
2. Check routes: `php artisan route:list --path=api`
3. Verify middleware in `config/scramble.php`

### Missing endpoints?
- Ensure routes are in `routes/api.php`
- Check route prefix matches `scramble.api_path`
- Verify controllers are in correct namespace

---

**Generated with ❤️ by Scramble (Dedoc)**
