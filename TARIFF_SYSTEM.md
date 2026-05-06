# Automatic Parking Tariff Calculation System

## Overview
This parking system automatically calculates and applies tariffs based on vehicle type when vehicles enter and exit the parking area.

## Tariff Rules

### Base Tariff by Vehicle Type
- **Motor (Motorcycle)**: Rp 2,000
- **Mobil (Car)**: Rp 4,000

## Implementation Details

### Helper Function
The `TarifHelper` class provides reusable tariff calculation methods:

```php
// Get tariff by vehicle type
TarifHelper::getTarifByJenis('motor')  // Returns: 2000
TarifHelper::getTarifByJenis('mobil')  // Returns: 4000

// Calculate exit tariff
TarifHelper::calculateExitTarif($parkir)
```

### When Tariff is Applied

#### 1. **Vehicle Entry (Store)**
- Tariff is automatically set based on vehicle type
- Triggered when user submits the "Masuk" form
- Stored immediately in the `tarif` column

```php
$tarif = TarifHelper::getTarifByJenis($kendaraan->jenis_kendaraan);
$parkir = Parkir::create([
    'tarif' => $tarif,
    // ... other fields
]);
```

#### 2. **Vehicle Exit (Keluar)**
- Tariff remains the same (set during entry)
- Status changed to 'keluar'
- Waktu_keluar timestamp recorded
- Success message displays the final tariff

```php
$tarif = $parkir->calculateTarif();
$parkir->update([
    'status' => 'keluar',
    'waktu_keluar' => now(),
    'tarif' => $tarif,
]);
```

### Model Methods

#### Parkir Model
- `calculateTarif()`: Returns the calculated tariff based on vehicle type
- `getTarifByVehicleType()`: Gets tariff directly from vehicle relationship

### Files Modified/Created

1. **App/Helpers/TarifHelper.php** (NEW)
   - Central location for tariff calculations
   - Methods: `getTarifByJenis()`, `calculateExitTarif()`

2. **App/Models/Parkir.php**
   - Updated `calculateTarif()` method
   - Added `getTarifByVehicleType()` method
   - Imported TarifHelper

3. **App/Http/Controllers/ParkirController.php**
   - Updated `store()` method to set tariff on entry
   - Updated `keluar()` method to calculate tariff on exit
   - Imported TarifHelper

4. **resources/views/parkir/index.blade.php**
   - Added Tarif column to active parking table
   - Displays tariff in Rupiah format

5. **resources/views/parkir/history.blade.php**
   - Added Tarif column to history table
   - Shows final tariff for completed transactions
   - Added empty state message

## Usage Example

### Scenario 1: Motor Masuk
1. User inputs: Nomor Plat: "H 1234 ABC", Jenis: "Motor"
2. System automatically sets: Tarif = Rp 2,000
3. Record created with tarif value

### Scenario 2: Mobil Masuk then Keluar
1. User inputs: Nomor Plat: "B 5678 XYZ", Jenis: "Mobil"
2. System sets: Tarif = Rp 4,000
3. When user clicks "Keluar", tariff remains Rp 4,000
4. Success message: "Kendaraan keluar berhasil. Tarif: Rp 4,000"

## Code Quality Features

✓ **No Hardcoding**: Tariffs defined in TarifHelper class
✓ **Reusable**: Helper methods used in multiple places
✓ **Clean Code**: Well-documented methods with clear naming
✓ **DRY Principle**: Single source of truth for tariff values
✓ **Easy Maintenance**: Change tariffs in one place
✓ **Error Handling**: Graceful handling of missing vehicle data

## Future Enhancements

Possible improvements for future versions:
- Hourly rate calculations for long-term parking
- Special rates for subscribers/members
- Discounts based on parking duration
- Peak hour surcharges
- Admin interface to configure tariffs
