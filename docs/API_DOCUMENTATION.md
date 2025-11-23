# PAMS API Documentation

## Overview

The Property Affiliate Management System (PAMS) provides a RESTful API for authentication and user management. The API uses Laravel Sanctum for token-based authentication.

**Base URL**: `http://your-domain.com/api`

**Authentication**: Bearer Token (Sanctum)

**Content Type**: `application/json`

## Authentication Flow

1. **Login**: Send credentials to `/api/login` to receive an access token
2. **Use Token**: Include the token in the `Authorization` header for protected endpoints
3. **Logout**: Revoke the token by calling `/api/logout`

### Token Expiration

- Tokens expire after **60 days** (86,400 minutes) by default
- Configure expiration in `.env` using `SANCTUM_EXPIRATION`
- Set to `null` for tokens that never expire

## Endpoints

### 1. Login

Authenticate a user and receive an access token.

**Endpoint**: `POST /api/login`

**Authentication**: None (Public)

**Request Headers**:
```
Content-Type: application/json
Accept: application/json
```

**Request Body**:
```json
{
  "email": "affiliate@example.com",
  "password": "password123",
  "device_name": "iPhone 14 Pro" // Optional
}
```

**Parameters**:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| email | string | Yes | User's email address |
| password | string | Yes | User's password |
| device_name | string | No | Device identifier for token management |

**Success Response** (200 OK):
```json
{
  "access_token": "1|abc123def456...",
  "token_type": "Bearer",
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "affiliate@example.com",
    "whatsapp": "628123456789",
    "affiliate_code": "ABC12345",
    "status": "active"
  }
}
```

**Error Responses**:

**422 Unprocessable Entity** (Invalid credentials):
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

**403 Forbidden** (Inactive account):
```json
{
  "message": "Your account is not active. Please contact the administrator."
}
```

**Example Request (cURL)**:
```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "affiliate@example.com",
    "password": "password123",
    "device_name": "My Device"
  }'
```

**Example Request (JavaScript)**:
```javascript
const response = await fetch('http://your-domain.com/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'affiliate@example.com',
    password: 'password123',
    device_name: 'My Device'
  })
});

const data = await response.json();
console.log(data.access_token);
```

---

### 2. Logout

Revoke the current access token.

**Endpoint**: `POST /api/logout`

**Authentication**: Required (Bearer Token)

**Request Headers**:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {access_token}
```

**Request Body**: None

**Success Response** (200 OK):
```json
{
  "message": "Successfully logged out."
}
```

**Error Responses**:

**401 Unauthorized** (Missing or invalid token):
```json
{
  "message": "Unauthenticated."
}
```

**Example Request (cURL)**:
```bash
curl -X POST http://your-domain.com/api/logout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123def456..."
```

**Example Request (JavaScript)**:
```javascript
const response = await fetch('http://your-domain.com/api/logout', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${accessToken}`
  }
});

const data = await response.json();
console.log(data.message);
```

---

### 3. Get Authenticated User

Retrieve information about the currently authenticated user.

**Endpoint**: `GET /api/user`

**Authentication**: Required (Bearer Token)

**Request Headers**:
```
Accept: application/json
Authorization: Bearer {access_token}
```

**Request Body**: None

**Success Response** (200 OK):
```json
{
  "user": {
    "id": 5,
    "name": "John Doe",
    "email": "affiliate@example.com",
    "whatsapp": "628123456789",
    "affiliate_code": "ABC12345",
    "status": "active",
    "profile_photo": "/storage/profile-photos/photo.jpg",
    "created_at": "2025-11-20T10:30:00.000000Z"
  }
}
```

**Error Responses**:

**401 Unauthorized** (Missing or invalid token):
```json
{
  "message": "Unauthenticated."
}
```

**Example Request (cURL)**:
```bash
curl -X GET http://your-domain.com/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123def456..."
```

**Example Request (JavaScript)**:
```javascript
const response = await fetch('http://your-domain.com/api/user', {
  method: 'GET',
  headers: {
    'Accept': 'application/json',
    'Authorization': `Bearer ${accessToken}`
  }
});

const data = await response.json();
console.log(data.user);
```

---

## Error Handling

All API endpoints follow consistent error response formats:

### Validation Errors (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message for this field"
    ]
  }
}
```

### Authentication Errors (401)
```json
{
  "message": "Unauthenticated."
}
```

### Authorization Errors (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Server Errors (500)
```json
{
  "message": "Server Error"
}
```

---

## Rate Limiting

API endpoints are rate-limited to prevent abuse:

- **Default**: 60 requests per minute per IP address
- **Authenticated**: 100 requests per minute per user

When rate limit is exceeded:

**Response** (429 Too Many Requests):
```json
{
  "message": "Too Many Attempts."
}
```

**Headers**:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
Retry-After: 45
```

---

## Token Management

### Multiple Devices

Users can have multiple active tokens for different devices. Each token is identified by the `device_name` parameter during login.

### Revoking Tokens

**Revoke Current Token**:
```bash
POST /api/logout
```

**Revoke All Tokens** (programmatically):
```php
$user->tokens()->delete();
```

**Revoke Specific Token** (programmatically):
```php
$user->tokens()->where('id', $tokenId)->delete();
```

---

## Security Best Practices

1. **HTTPS Only**: Always use HTTPS in production to protect tokens in transit
2. **Token Storage**: Store tokens securely (e.g., secure storage on mobile, httpOnly cookies on web)
3. **Token Rotation**: Implement token refresh mechanism for long-lived applications
4. **Logout on Sensitive Actions**: Require re-authentication for sensitive operations
5. **Monitor Token Usage**: Track and alert on suspicious token activity

---

## Testing the API

### Using Postman

1. **Import Collection**: Create a new collection in Postman
2. **Set Base URL**: Configure `{{base_url}}` variable to `http://your-domain.com/api`
3. **Login**: Send POST request to `/login` and save the `access_token`
4. **Set Token**: Add `Authorization: Bearer {{access_token}}` to collection headers
5. **Test Endpoints**: Call protected endpoints with the token

### Using cURL

**Complete Flow Example**:
```bash
# 1. Login and save token
TOKEN=$(curl -s -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"affiliate@example.com","password":"password123"}' \
  | jq -r '.access_token')

# 2. Get user info
curl -X GET http://your-domain.com/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# 3. Logout
curl -X POST http://your-domain.com/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

---

## Environment Configuration

Add these variables to your `.env` file:

```env
# Sanctum Configuration
SANCTUM_EXPIRATION=86400              # Token expiration in minutes (60 days)
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1
```

---

## Future Endpoints (Planned)

The following endpoints are planned for future releases:

- `GET /api/properties` - List all published properties
- `GET /api/properties/{slug}` - Get property details
- `GET /api/leads` - List affiliate's leads
- `POST /api/leads` - Create a new lead
- `PUT /api/leads/{id}` - Update lead status
- `GET /api/analytics` - Get affiliate performance metrics
- `GET /api/visits` - List affiliate's visit history

---

## Support

For API support or questions:

- **Documentation**: Check this file for detailed endpoint information
- **Issues**: Report bugs or request features through your project management system
- **Technical Support**: Contact the development team

---

## Changelog

### Version 1.0.0 (2025-11-22)

- Initial API release
- Authentication endpoints (login, logout, user)
- Sanctum token-based authentication
- 60-day token expiration
- Rate limiting implementation
