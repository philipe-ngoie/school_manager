# School Manager - Parent App

Flutter mobile application for parents to view their children's information, invoices, and make payments.

## Features

- **Authentication**: Login/Register with Laravel Sanctum API tokens
- **Student Information**: View child's academic information
- **Invoice Viewing**: View pending and paid invoices
- **Payment Processing**: Pay invoices via CinetPay
- **Notifications**: Receive push notifications via FCM
- **Receipt Downloads**: Download payment receipts as PDF

## Prerequisites

- Flutter SDK 3.x or higher
- Dart SDK 3.x or higher
- Android Studio / Xcode (for mobile development)

## Setup

### 1. Install Flutter Dependencies

```bash
flutter pub get
```

### 2. Configure API Endpoint

Edit `lib/config/api_config.dart` and set your API base URL:

```dart
static const String baseUrl = 'http://your-api-domain.com/api';
```

### 3. Firebase Cloud Messaging (FCM) Setup

#### Android
1. Download `google-services.json` from Firebase Console
2. Place it in `android/app/`
3. Update `android/app/build.gradle`:

```gradle
dependencies {
    implementation 'com.google.firebase:firebase-messaging:23.4.0'
    implementation platform('com.google.firebase:firebase-bom:32.7.0')
}

apply plugin: 'com.google.gms.google-services'
```

#### iOS
1. Download `GoogleService-Info.plist` from Firebase Console
2. Place it in `ios/Runner/`
3. Enable Push Notifications in Xcode capabilities
4. Run `cd ios && pod install`

### 4. CinetPay Integration

The app uses WebView to handle CinetPay payments. No additional SDK setup required.

## Running the App

### Android
```bash
flutter run -d android
```

### iOS
```bash
flutter run -d ios
```

## Building for Production

### Android APK
```bash
flutter build apk --release
```

### Android App Bundle (for Play Store)
```bash
flutter build appbundle --release
```

### iOS (for App Store)
```bash
flutter build ios --release
```

## Code Signing (Production)

### Android
1. Create a keystore:
```bash
keytool -genkey -v -keystore school-manager-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias school-manager
```

2. Create `android/key.properties`:
```
storePassword=your_store_password
keyPassword=your_key_password
keyAlias=school-manager
storeFile=/path/to/school-manager-key.jks
```

3. Update `android/app/build.gradle` to reference the keystore

### iOS
1. Create App ID in Apple Developer Console
2. Create Provisioning Profile
3. Configure signing in Xcode

## Project Structure

```
lib/
├── config/          # Configuration files
│   └── api_config.dart
├── models/          # Data models
│   ├── student.dart
│   ├── invoice.dart
│   └── payment.dart
├── providers/       # State management
│   ├── auth_provider.dart
│   └── invoice_provider.dart
├── screens/         # UI screens
│   ├── auth/       # Login/Register
│   ├── home/       # Home screen
│   ├── invoices/   # Invoice list & details
│   └── payments/   # Payment WebView
├── services/        # API services
│   ├── api_service.dart
│   └── notification_service.dart
├── widgets/         # Reusable widgets
└── main.dart       # App entry point
```

## Dependencies

Main dependencies used:
- `http` / `dio`: API communication
- `provider`: State management
- `shared_preferences`: Local data storage
- `firebase_messaging`: Push notifications
- `webview_flutter`: CinetPay payment integration
- `intl`: Date/number formatting

## API Integration

### Authentication Flow
1. User enters email and password
2. POST `/api/login` (or `/api/register` for new users)
3. Receive authentication token
4. Store token in `shared_preferences`
5. Include token in all subsequent API requests

### Payment Flow
1. User selects invoice to pay
2. App calls POST `/api/payments/initiate`
3. Backend returns payment URL
4. App opens WebView with CinetPay payment page
5. User completes payment
6. CinetPay calls webhook (backend handles)
7. App polls GET `/api/payments/verify` to confirm
8. Display success/failure message

## Push Notifications

The app receives push notifications for:
- New invoices
- Payment confirmations
- Payment reminders
- School announcements

Configure FCM in `lib/services/notification_service.dart`.

## Environment Variables

Create `.env` file:
```
API_BASE_URL=https://your-domain.com/api
FCM_SERVER_KEY=your_fcm_server_key
```

## Troubleshooting

### Build Issues
```bash
flutter clean
flutter pub get
flutter run
```

### Android Build Fails
- Check minimum SDK version (API 21+)
- Verify Google Services plugin is applied
- Run `flutter doctor` to check setup

### iOS Build Fails
- Run `cd ios && pod install`
- Check deployment target (iOS 12.0+)
- Verify signing certificates

### FCM Not Working
- Verify `google-services.json` (Android) or `GoogleService-Info.plist` (iOS) is present
- Check Firebase Console for correct app configuration
- Test with Firebase Console test message

## Testing

Run unit tests:
```bash
flutter test
```

Run integration tests:
```bash
flutter test integration_test
```

## License

This project is part of the School Manager system.
