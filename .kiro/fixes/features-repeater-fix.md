# Fix: Features Repeater Form Issue

## Masalah
Data features tersimpan di database, tetapi tidak tampil dalam form Edit dan InfoList. Screenshot menunjukkan `[object Object]` di form.

## Root Cause
Terjadi **mismatch struktur data** antara:
1. **Database**: Menyimpan array sederhana `["Swimming Pool", "Garden", ...]`
2. **Form (PropertyForm.php)**: Menggunakan `Repeater::simple()` yang mengharapkan array sederhana
3. **InfoList (PropertyInfolist.php)**: Menggunakan `RepeatableEntry` dengan schema `['feature']` yang mengharapkan array asosiatif `[['feature' => 'Swimming Pool'], ...]`

## Solusi

### 1. Update Form Schema (PropertyForm.php)
**File**: `app/Filament/Resources/Properties/Schemas/PropertyForm.php`

**Perubahan**: Ganti `simple()` dengan `schema()` pada Repeater features
```php
// SEBELUM
Repeater::make('features')
    ->simple(
        TextInput::make('feature')
            ->label('Feature')
            ->required()
            ->maxLength(255)
    )

// SESUDAH
Repeater::make('features')
    ->schema([
        TextInput::make('feature')
            ->label('Feature')
            ->required()
            ->maxLength(255)
    ])
```

### 2. Migrasi Data Existing
**File**: `fix_features.php` (temporary script)

Script untuk mengkonversi data lama:
```php
foreach ($properties as $property) {
    $features = $property->features;
    
    if (is_array($features) && !empty($features)) {
        $firstItem = reset($features);
        
        if (is_string($firstItem)) {
            $newFeatures = array_map(function ($item) {
                return ['feature' => $item];
            }, $features);
            
            $property->features = $newFeatures;
            $property->save();
        }
    }
}
```

**Hasil**: Fixed 20 properties

### 3. Update Seeder (PropertySeeder.php)
**File**: `database/seeders/PropertySeeder.php`

**Perubahan**: Update semua array features ke format baru
```php
// SEBELUM
'features' => ['Swimming Pool', 'Garden', 'Carport 3 Mobil']

// SESUDAH
'features' => [
    ['feature' => 'Swimming Pool'],
    ['feature' => 'Garden'],
    ['feature' => 'Carport 3 Mobil'],
]
```

### 4. Update Factory (PropertyFactory.php)
**File**: `database/factories/PropertyFactory.php`

**Perubahan**: Update features array ke format baru
```php
// SEBELUM
'features' => [
    'Swimming Pool',
    'Garden',
    'Parking',
    'Security 24/7',
]

// SESUDAH
'features' => [
    ['feature' => 'Swimming Pool'],
    ['feature' => 'Garden'],
    ['feature' => 'Parking'],
    ['feature' => 'Security 24/7'],
]
```

## Struktur Data Final

### Database (JSON)
```json
[
  {"feature": "Swimming Pool"},
  {"feature": "Garden"},
  {"feature": "Carport 3 Mobil"}
]
```

### Model Cast
```php
protected function casts(): array
{
    return [
        'features' => 'array', // Tetap array
        // ...
    ];
}
```

### Form Component
```php
Repeater::make('features')
    ->schema([
        TextInput::make('feature')
            ->label('Feature')
            ->required()
            ->maxLength(255)
    ])
```

### InfoList Component
```php
RepeatableEntry::make('features')
    ->schema([
        TextEntry::make('feature'),
    ])
    ->grid(2)
```

## Testing
1. ✅ Data tersimpan dengan benar
2. ✅ Data tampil di form Edit
3. ✅ Data tampil di InfoList
4. ✅ Seeder menggunakan format yang benar
5. ✅ Factory menggunakan format yang benar

## Catatan
- `Repeater::simple()` hanya cocok untuk array sederhana (string/number)
- `Repeater::schema()` diperlukan untuk array asosiatif
- InfoList `RepeatableEntry` selalu membutuhkan schema dengan key yang spesifik
- Perubahan ini **backward incompatible** - data lama perlu dimigrasi

## Tanggal
2025-11-24
