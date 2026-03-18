<?php

return [
    // Show the "Near Expiry Alert" when there are batches expiring within this many days.
    // Used by App\Http\Middleware\NearExpiryAlert.
    'near_expiry_days' => env('NEAR_EXPIRY_DAYS', 7),
];
