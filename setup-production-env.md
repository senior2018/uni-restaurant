# Production Environment Setup for Google OAuth

## 1. Render Environment Variables

Set these in your Render dashboard under "Environment":

```bash
APP_URL=https://your-app-name.onrender.com
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://your-app-name.onrender.com/auth/google/callback
```

## 2. Google Cloud Console Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Navigate to **APIs & Services** → **Credentials**
3. Edit your OAuth 2.0 Client ID
4. Add these **Authorized redirect URIs**:
   - `https://your-app-name.onrender.com/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (for local development)

## 3. Test the Setup

After setting up:
1. Deploy your app
2. Try Google login
3. Check that it redirects to your Render domain, not localhost

## 4. Common Issues

- **localhost redirects**: APP_URL not set correctly
- **Invalid redirect URI**: Google Console not updated
- **CORS errors**: Domain mismatch between config and Google Console
