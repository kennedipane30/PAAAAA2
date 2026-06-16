// lib/app_config.dart

class AppConfig {
  // ============================================================
  // 🌐 CENTRAL CONFIGURATION
  // ============================================================
  // Cukup ubah baris ini saja jika IP server berubah atau ganti ke domain publik
  static const String host = '3.107.184.92'; 

  // URL Dasar untuk Laravel Backend
  static const String baseUrl = 'http://$host/api';
  static const String storageUrl = 'http://$host/storage';

  // URL Dasar untuk Microservices Golang (Sudah di-handle Nginx Reverse Proxy)
  static const String materiUrl   = 'http://$host/api/materi';
  static const String tryoutUrl   = 'http://$host/api/tryout';
  static const String practiceUrl = 'http://$host/api/practice';
}