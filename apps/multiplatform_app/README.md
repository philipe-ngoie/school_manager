# School Manager - Multiplatform App

Flutter application for school administrators and staff to manage school operations on multiple platforms (Android, iOS, Windows, macOS, Linux, Web).

## Features

- **Authentication**: Login/Register with Laravel Sanctum API tokens
- **Student Management**: View and manage students
- **Invoice Management**: Create, view, and manage invoices
- **Payment Processing**: Initiate payments via CinetPay
- **Reports**: View financial and attendance reports
- **Multi-platform**: Works on Android, iOS, Windows, macOS, Linux, and Web

## Prerequisites

- Flutter SDK 3.x or higher
- Dart SDK 3.x or higher
- Android Studio / Xcode (for mobile development)
- Visual Studio (for Windows development)

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
3. Follow the Firebase setup guide for Android

#### iOS
1. Download `GoogleService-Info.plist` from Firebase Console
2. Place it in `ios/Runner/`
3. Follow the Firebase setup guide for iOS

## Running the App

### Android
```bash
flutter run -d android
```

### iOS
```bash
flutter run -d ios
```

### Windows
```bash
flutter run -d windows
```

### Web
```bash
flutter run -d chrome
```

## Building for Production

### Android APK
```bash
flutter build apk --release
```

### Android App Bundle
```bash
flutter build appbundle --release
```

### iOS
```bash
flutter build ios --release
```

### Windows
```bash
flutter build windows --release
```

### Web
```bash
flutter build web --release
```

## Project Structure

```
lib/
├── config/          # Configuration files
├── models/          # Data models
├── providers/       # State management (Provider pattern)
├── screens/         # UI screens
│   ├── auth/       # Authentication screens
│   ├── dashboard/  # Dashboard screen
│   ├── invoices/   # Invoice management
│   ├── payments/   # Payment screens
│   └── students/   # Student management
├── services/        # API services
├── widgets/         # Reusable widgets
└── main.dart       # App entry point
```

## Dependencies

- `http`: API communication
- `provider`: State management
- `shared_preferences`: Local storage
- `firebase_messaging`: Push notifications
- `webview_flutter`: Payment gateway integration
- `pdf`: PDF generation
- `intl`: Internationalization

## Environment Variables

Create a `.env` file in the project root:

```
API_BASE_URL=http://your-api-domain.com/api
FCM_SERVER_KEY=your_fcm_server_key
```

## API Authentication

The app uses Laravel Sanctum for API authentication:

1. User logs in with email/password
2. API returns an authentication token
3. Token is stored locally using `shared_preferences`
4. All subsequent API requests include the token in headers

## Payment Integration

CinetPay payment flow:
1. User selects invoice to pay
2. App calls `/api/payments/initiate` endpoint
3. Backend returns payment URL
4. App opens WebView with payment URL
5. User completes payment on CinetPay
6. CinetPay redirects back with payment status
7. App verifies payment via `/api/payments/verify`

## Troubleshooting

### Android Build Issues
- Ensure minimum SDK version is 21 or higher in `android/app/build.gradle`
- Run `flutter clean` and `flutter pub get`

### iOS Build Issues
- Run `cd ios && pod install`
- Ensure deployment target is iOS 12.0 or higher

### Windows Build Issues
- Ensure Visual Studio 2019 or higher is installed
- Enable Windows Desktop development workload

## License

This project is part of the School Manager system.
