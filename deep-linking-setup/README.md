# Yoola Deep Linking Setup Guide

## Overview

This guide will help you set up **Android App Links** and **iOS Universal Links** for the Yoola app.

**What happens when a user clicks `https://yoola.ug/product/samsung-tv-xyz`:**
- **If App is Installed:** Opens directly in the Yoola Flutter app
- **If App is NOT Installed:** Opens in the mobile web browser

---

## Table of Contents

1. [Server Setup](#1-server-setup)
2. [Flutter App Setup](#2-flutter-app-setup)
3. [Testing](#3-testing)
4. [Troubleshooting](#4-troubleshooting)

---

## 1. Server Setup

### Step 1.1: Get Your App Credentials

#### For Android:
Get your SHA256 fingerprint from your release keystore:
```bash
keytool -list -v -keystore /path/to/your-release-key.keystore -alias your-key-alias
```

Look for the line that says `SHA256:` and copy the entire fingerprint.

#### For iOS:
1. Go to [Apple Developer Portal](https://developer.apple.com/account)
2. Navigate to **Membership** → Find your **Team ID** (10 characters)
3. Your **Bundle ID** is in your Xcode project (e.g., `com.yoola.app`)

### Step 1.2: Update Environment Variables

Add these to your `.env` file on the server:

```env
# Deep Linking Configuration
ANDROID_PACKAGE_NAME=com.yoola.app
ANDROID_SHA256_FINGERPRINT=14:6D:E9:83:C5:C2:... (your full SHA256)

APPLE_TEAM_ID=ABC123XYZ  (your 10-character Team ID)
IOS_BUNDLE_ID=com.yoola.app
```

### Step 1.3: Upload Server Files

The Laravel backend files are already configured. Just ensure these routes are accessible:
- `https://yoola.ug/.well-known/assetlinks.json`
- `https://yoola.ug/.well-known/apple-app-site-association`

### Step 1.4: Verify Server Setup

Test the endpoints:
```bash
curl https://yoola.ug/.well-known/assetlinks.json
curl https://yoola.ug/.well-known/apple-app-site-association
```

---

## 2. Flutter App Setup

### Step 2.1: Add Dependencies

Add to `pubspec.yaml`:
```yaml
dependencies:
  go_router: ^13.0.0
  uni_links: ^0.5.1
```

Then run:
```bash
flutter pub get
```

### Step 2.2: Android Configuration

Copy the contents from `flutter/android/` folder to your Flutter project.

**File: `android/app/src/main/AndroidManifest.xml`**

Add this intent filter inside your main `<activity>` tag:

```xml
<intent-filter android:autoVerify="true">
    <action android:name="android.intent.action.VIEW" />
    <category android:name="android.intent.category.DEFAULT" />
    <category android:name="android.intent.category.BROWSABLE" />
    <data android:scheme="https" android:host="yoola.ug" />
</intent-filter>
```

### Step 2.3: iOS Configuration

**File: `ios/Runner/Runner.entitlements`**

Add Associated Domains:
```xml
<key>com.apple.developer.associated-domains</key>
<array>
    <string>applinks:yoola.ug</string>
</array>
```

**File: `ios/Runner/Info.plist`**

Add URL Types (optional, for custom scheme):
```xml
<key>CFBundleURLTypes</key>
<array>
    <dict>
        <key>CFBundleURLSchemes</key>
        <array>
            <string>yoola</string>
        </array>
    </dict>
</array>
```

### Step 2.4: Add Deep Link Handler

Copy `flutter/lib/deep_link_handler.dart` to your project and integrate it.

---

## 3. Testing

### Android Testing

```bash
# Test deep link from command line
adb shell am start -a android.intent.action.VIEW \
  -d "https://yoola.ug/product/test-product-123" \
  com.yoola.app
```

### iOS Testing

```bash
# Test deep link from command line
xcrun simctl openurl booted "https://yoola.ug/product/test-product-123"
```

### Verify App Links

**Android:**
```bash
adb shell pm get-app-links com.yoola.app
```

**iOS:**
Use Apple's [App Search API Validation Tool](https://search.developer.apple.com/appsearch-validation-tool/)

---

## 4. Troubleshooting

### Android Issues

1. **Links not opening in app:**
   - Clear app defaults: Settings → Apps → Yoola → Open by default → Clear defaults
   - Reinstall the app
   - Verify SHA256 fingerprint matches exactly

2. **Verification failed:**
   - Ensure `assetlinks.json` is accessible via HTTPS
   - Check for redirects (Google doesn't follow redirects)

### iOS Issues

1. **Links not opening in app:**
   - Delete and reinstall the app
   - Ensure Associated Domains is enabled in Apple Developer Portal
   - Check entitlements file is properly signed

2. **Verification failed:**
   - Ensure `apple-app-site-association` has no `.json` extension
   - Content-Type must be `application/json`
   - No redirects allowed

### Debug URLs

- Android: `https://digitalassetlinks.googleapis.com/v1/statements:list?source.web.site=https://yoola.ug&relation=delegate_permission/common.handle_all_urls`
- iOS: `https://app-site-association.cdn-apple.com/a/v1/yoola.ug`

---

## File Structure

```
deep-linking-setup/
├── README.md                    # This file
├── server/
│   ├── .env.example            # Environment variables to add
│   ├── assetlinks.json         # Android verification (reference)
│   └── apple-app-site-association  # iOS verification (reference)
└── flutter/
    ├── android/
    │   └── AndroidManifest.xml.snippet
    ├── ios/
    │   ├── Runner.entitlements.snippet
    │   └── Info.plist.snippet
    └── lib/
        └── deep_link_handler.dart
```

---

## Support

If you encounter issues, check:
1. Server logs for 404 errors on verification files
2. Flutter debug console for deep link parsing errors
3. Android Logcat / iOS Console for system-level errors
