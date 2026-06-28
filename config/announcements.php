<?php

return [
    'master_email' => env('ANNOUNCEMENT_MASTER_EMAIL', 'info@bdiversity.co.mz'),
    'master_password' => env('ANNOUNCEMENT_MASTER_PASSWORD'),
    'master_password_hash' => env('ANNOUNCEMENT_MASTER_PASSWORD_HASH', '$2y$10$VcJwtLUfPq1ozUke/sAA4OXEA06MpyBZdldzT24IPtdcxIC63xfxC'),
    'password_expires_months' => (int) env('ANNOUNCEMENT_PASSWORD_EXPIRES_MONTHS', 6),
    'intranet_url' => env('BD_INTRANET_URL', 'https://bdiversity.co.mz/intranet'),
];
