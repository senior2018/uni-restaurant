<?php
// Debug script to check environment variables
echo "<h1>Environment Debug</h1>";
echo "<h2>Current Environment Variables:</h2>";
echo "<p><strong>APP_URL:</strong> " . (getenv('APP_URL') ?: 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_CLIENT_ID:</strong> " . (getenv('GOOGLE_CLIENT_ID') ? 'SET' : 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_CLIENT_SECRET:</strong> " . (getenv('GOOGLE_CLIENT_SECRET') ? 'SET' : 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_REDIRECT_URI:</strong> " . (getenv('GOOGLE_REDIRECT_URI') ?: 'NOT SET') . "</p>";

echo "<h2>Laravel Config:</h2>";
echo "<p><strong>app.url:</strong> " . config('app.url') . "</p>";
echo "<p><strong>services.google.redirect:</strong> " . config('services.google.redirect') . "</p>";

echo "<h2>Current Domain:</h2>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";

echo "<h2>Google OAuth URLs:</h2>";
echo "<p><strong>Redirect URL should be:</strong> https://" . ($_SERVER['HTTP_HOST'] ?? 'your-domain') . "/auth/google/callback</p>";
?>
