<?php
// Debug script to check environment variables
echo "<h1>Environment Debug - Restaurant App</h1>";
echo "<h2>Current Environment Variables:</h2>";
echo "<p><strong>APP_URL:</strong> " . (getenv('APP_URL') ?: 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_CLIENT_ID:</strong> " . (getenv('GOOGLE_CLIENT_ID') ? 'SET' : 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_CLIENT_SECRET:</strong> " . (getenv('GOOGLE_CLIENT_SECRET') ? 'SET' : 'NOT SET') . "</p>";
echo "<p><strong>GOOGLE_REDIRECT_URI:</strong> " . (getenv('GOOGLE_REDIRECT_URI') ?: 'NOT SET') . "</p>";

echo "<h2>Current Domain:</h2>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";

echo "<h2>Expected vs Actual:</h2>";
echo "<p><strong>Expected APP_URL:</strong> https://the-restaurant-b5fz.onrender.com</p>";
echo "<p><strong>Actual APP_URL:</strong> " . (getenv('APP_URL') ?: 'NOT SET') . "</p>";
echo "<p><strong>Expected GOOGLE_REDIRECT_URI:</strong> https://the-restaurant-b5fz.onrender.com/auth/google/callback</p>";
echo "<p><strong>Actual GOOGLE_REDIRECT_URI:</strong> " . (getenv('GOOGLE_REDIRECT_URI') ?: 'NOT SET') . "</p>";

echo "<h2>All Environment Variables:</h2>";
echo "<pre>";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'APP_') === 0 || strpos($key, 'GOOGLE_') === 0) {
        echo "$key = $value\n";
    }
}
echo "</pre>";

echo "<h2>Status:</h2>";
if (getenv('APP_URL') === 'https://the-restaurant-b5fz.onrender.com') {
    echo "<p style='color: green;'>✅ APP_URL is correct!</p>";
} else {
    echo "<p style='color: red;'>❌ APP_URL is wrong or not set!</p>";
}

if (getenv('GOOGLE_REDIRECT_URI') === 'https://the-restaurant-b5fz.onrender.com/auth/google/callback') {
    echo "<p style='color: green;'>✅ GOOGLE_REDIRECT_URI is correct!</p>";
} else {
    echo "<p style='color: red;'>❌ GOOGLE_REDIRECT_URI is wrong or not set!</p>";
}

echo "<h2>🚨 CRITICAL ISSUE:</h2>";
if (getenv('GOOGLE_CLIENT_ID') === false) {
    echo "<p style='color: red; font-size: 18px;'>❌ GOOGLE_CLIENT_ID is NOT SET in Render!</p>";
    echo "<p>You need to set this in your Render dashboard Environment tab.</p>";
}

if (getenv('GOOGLE_CLIENT_SECRET') === false) {
    echo "<p style='color: red; font-size: 18px;'>❌ GOOGLE_CLIENT_SECRET is NOT SET in Render!</p>";
    echo "<p>You need to set this in your Render dashboard Environment tab.</p>";
}

echo "<h2>📋 What You Need to Do:</h2>";
echo "<ol>";
echo "<li>Go to <a href='https://render.com' target='_blank'>render.com</a> → Your app → Environment tab</li>";
echo "<li>Add these environment variables:</li>";
echo "<ul>";
echo "<li><strong>APP_URL</strong> = https://the-restaurant-b5fz.onrender.com</li>";
echo "<li><strong>GOOGLE_CLIENT_ID</strong> = your_google_client_id</li>";
echo "<li><strong>GOOGLE_CLIENT_SECRET</strong> = your_google_client_secret</li>";
echo "<li><strong>GOOGLE_REDIRECT_URI</strong> = https://the-restaurant-b5fz.onrender.com/auth/google/callback</li>";
echo "</ul>";
echo "<li>Click 'Save Changes'</li>";
echo "<li>Go to 'Manual Deploy' → 'Deploy latest commit'</li>";
echo "</ol>";
?>
