// 1. Test: Buat user baru dan kirim email verifikasi
echo "=== Test 1: Membuat user baru dan mengirim email verifikasi ===\n";

$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'whatsapp' => '+628123456789',
    'status' => \App\Enums\UserStatus::PENDING,
]);

echo "User created: {$user->name} ({$user->email})\n";

// Dispatch Registered event untuk trigger email verifikasi
event(new \Illuminate\Auth\Events\Registered($user));

echo "Email verifikasi telah dikirim ke: {$user->email}\n";
echo "Cek mailbox Anda (MailHog/Mailpit di http://localhost:8025)\n\n";

// 2. Test: Kirim ulang email verifikasi ke user yang sudah ada
echo "=== Test 2: Kirim ulang email verifikasi ===\n";

// Ambil user yang belum verified
$unverifiedUser = \App\Models\User::whereNull('email_verified_at')->first();

if ($unverifiedUser) {
    echo "Mengirim ulang email verifikasi ke: {$unverifiedUser->email}\n";
    $unverifiedUser->sendEmailVerificationNotification();
    echo "Email verifikasi telah dikirim ulang!\n\n";
} else {
    echo "Tidak ada user yang belum verified.\n\n";
}

// 3. Test: Cek status verifikasi
echo "=== Test 3: Cek status verifikasi user ===\n";

$allUsers = \App\Models\User::all();
foreach ($allUsers as $u) {
    $verified = $u->hasVerifiedEmail() ? 'VERIFIED' : 'NOT VERIFIED';
    echo "- {$u->name} ({$u->email}): {$verified}\n";
}

echo "\n=== Selesai ===\n";
echo "Untuk verify email secara manual:\n";
echo "\$user = \App\Models\User::where('email', 'test@example.com')->first();\n";
echo "\$user->markEmailAsVerified();\n";
