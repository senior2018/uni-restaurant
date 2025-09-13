<?php
// Test script to verify account locking functionality
// This script can be run to test if account locking works after 3 failed attempts

echo "<h1>Account Locking Test</h1>";

// Instructions for testing
echo "<h2>How to Test Account Locking:</h2>";
echo "<ol>";
echo "<li><strong>Create a test user account</strong> (if you don't have one)</li>";
echo "<li><strong>Try to login with wrong password 3 times</strong></li>";
echo "<li><strong>On the 3rd attempt, the account should be locked</strong></li>";
echo "<li><strong>Try to login again - should redirect to locked account page</strong></li>";
echo "</ol>";

echo "<h2>Expected Behavior:</h2>";
echo "<ul>";
echo "<li>✅ <strong>1st wrong attempt:</strong> 'The password you entered is incorrect. Please try again.'</li>";
echo "<li>✅ <strong>2nd wrong attempt:</strong> 'The password you entered is incorrect. Please try again.'</li>";
echo "<li>✅ <strong>3rd wrong attempt:</strong> Account gets locked, redirects to locked account page</li>";
echo "<li>✅ <strong>4th attempt:</strong> Redirects to locked account page (account is locked)</li>";
echo "</ul>";

echo "<h2>Test User Doesn't Exist:</h2>";
echo "<ul>";
echo "<li>✅ <strong>Try login with non-existent email:</strong> 'No account found with this email address. Please create an account first.'</li>";
echo "<li>✅ <strong>Blue info box appears:</strong> With link to create account</li>";
echo "</ul>";

echo "<h2>Database Check:</h2>";
echo "<p>You can check the database to see:</p>";
echo "<ul>";
echo "<li><code>failed_login_attempts</code> - Should increment with each failed attempt</li>";
echo "<li><code>locked_at</code> - Should be set to current timestamp after 3rd attempt</li>";
echo "<li><code>last_failed_attempt</code> - Should be updated with each failed attempt</li>";
echo "</ul>";

echo "<h2>To Unlock Account:</h2>";
echo "<p>Use the locked account page to unlock the account via OTP.</p>";

echo "<hr>";
echo "<p><strong>Note:</strong> This is a test guide. The actual functionality is implemented in the AuthenticatedSessionController.</p>";
?>
