// lib/app_config.dart

class AppConfig {
  static const String host = '3.107.184.92'; 

  // URL Dasar untuk Laravel Backend
  static const String baseUrl = 'http://$host/api';
  static const String storageUrl = 'http://$host/storage';

  // 🔥 SINKRONISASI FINAL: Semua mengarah ke port 80 standar IP AWS
  // Nginx yang akan otomatis memilah paket berdasarkan kata 'materials', 'tryouts', atau 'practices'
  static const String materiUrl   = 'http://$host/api'; 
  static const String tryoutUrl   = 'http://$host/api'; 
  static const String practiceUrl = 'http://$host/api'; 
}