import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../config/app_config.dart'; // 👈 Tambahkan import file konfigurasi terpusat Anda di sini

class ModuleWeekListPage extends StatelessWidget {
  final String subjectName;
  final String token;
  final List allMaterials;

  const ModuleWeekListPage({
    super.key,
    required this.subjectName,
    required this.token,
    required this.allMaterials,
  });

  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);

  // Fungsi untuk membuka PDF di Chrome/browser eksternal
  Future<void> _openPdfInBrowser(String pdfUrl, BuildContext context) async {
    final Uri url = Uri.parse(pdfUrl);
    
    try {
      if (await canLaunchUrl(url)) {
        await launchUrl(
          url,
          mode: LaunchMode.inAppWebView, // Buka di browser eksternal
          webViewConfiguration: const WebViewConfiguration(
            enableJavaScript: true,
            enableDomStorage: true,
          ),
        );
      } else {
        throw 'Tidak dapat membuka URL: $pdfUrl';
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("Gagal membuka PDF: ${e.toString()}"),
            backgroundColor: primaryRed,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: Text(subjectName, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.info_outline),
            onPressed: () {
              showDialog(
                context: context,
                builder: (context) => AlertDialog(
                  title: const Text("Informasi", style: TextStyle(color: textDark)),
                  content: const Text("PDF akan dibuka di browser Chrome.\nTutup browser untuk kembali ke aplikasi.", style: TextStyle(color: textDarkVariant)),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: const Text("OK", style: TextStyle(color: primaryRed)),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, // Tetap menampilkan 20 minggu
        itemBuilder: (context, index) {
          int weekNumber = index + 1;

          // ✨ LOGIKA PENCARIAN: Mencari materi di minggu ini untuk mapel ini
          var materialData = allMaterials.firstWhere(
            (m) {
              final String mSubject = (m['subject_name'] ?? m['material_name'] ?? '').toString().toLowerCase().trim();
              final String mWeek = (m['week'] ?? '0').toString();
              return mWeek == weekNumber.toString() && mSubject == subjectName.toLowerCase().trim();
            },
            orElse: () => null,
          );

          bool isAvailable = materialData != null && (materialData['file_path'] ?? '').toString().isNotEmpty;

          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              color: Colors.white, 
              borderRadius: BorderRadius.circular(15), 
              border: Border.all(color: isAvailable ? primaryRed.withOpacity(0.2) : Colors.transparent),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)]
            ),
            child: ListTile(
              leading: Icon(
                isAvailable ? Icons.picture_as_pdf_rounded : Icons.lock_clock_rounded, 
                color: isAvailable ? primaryRed : neutralGray.withOpacity(0.5)
              ),
              title: Text("Minggu $weekNumber", 
                style: TextStyle(fontWeight: FontWeight.bold, color: isAvailable ? textDark : neutralGray)),
              subtitle: Text(
                isAvailable ? (materialData['title'] ?? "Materi Tersedia") : "Materi belum diunggah",
                style: TextStyle(color: isAvailable ? textDarkVariant : neutralGray),
              ),
              trailing: const Icon(Icons.open_in_browser, size: 16, color: neutralGray),
              
              /*
              onTap: () {
                if (isAvailable) {
                  String path = materialData['file_path'].toString();
                  
                  // ✨ MODIFIKASI: Menggunakan AppConfig.host untuk menyusun URL PDF dari server AWS (Port 80 via Nginx)
                  String pdfUrl;
                  if (path.startsWith('http')) {
                    pdfUrl = path;
                  } else if (path.startsWith('/storage')) {
                    pdfUrl = "http://${AppConfig.host}$path";
                  } else if (path.startsWith('storage')) {
                    pdfUrl = "http://${AppConfig.host}/$path";
                  } else {
                    pdfUrl = "http://${AppConfig.host}/storage/$path";
                  }
                  
                  // Tambahkan token jika diperlukan (opsional)
                  if (token.isNotEmpty) {
                    pdfUrl = "$pdfUrl?token=$token";
                  }
                  
                  // Buka di Chrome/browser eksternal
                  _openPdfInBrowser(pdfUrl, context);
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: const Text("Materi minggu ini belum tersedia"), 
                      backgroundColor: primaryRed.withOpacity(0.8),
                      duration: const Duration(seconds: 2),
                    )
                  );
                }
              },
              */
              onTap: () {
                if (isAvailable) {
                  String path = materialData['file_path'].toString().trim();
                  
                  // ✨ PERBAIKAN UTAMA: Bersihkan localhost lama dan paksa pakai base URL yang valid
                  String pdfUrl;

                  // Ambil base URL bersih (misal: http://10.0.2.2:8000 jika emulator, atau http://127.0.0.1:8000)
                  // Kita bersihkan teks '/api' di ujung AppConfig.baseUrl
                  final String cleanBaseUrl = AppConfig.baseUrl.replaceAll('/api', '');

                  if (path.startsWith('http://127.0.0.1:8000') || path.startsWith('http://localhost:8000') || path.startsWith('http://10.0.2.2:8000')) {
                    // Jika database menyimpan url localhost/emulator lama, ganti kepalanya dengan base URL yang seragam
                    pdfUrl = path
                        .replaceFirst('http://127.0.0.1:8000', cleanBaseUrl)
                        .replaceFirst('http://localhost:8000', cleanBaseUrl)
                        .replaceFirst('http://10.0.2.2:8000', cleanBaseUrl);
                  } else if (path.startsWith('http')) {
                    // Jika sudah berupa link server/hosting online (AWS/Nginx), gunakan langsung
                    pdfUrl = path;
                  } else {
                    // Jika database hanya menyimpan path relatif (misal: 'storage/materi/xxx.pdf')
                    if (path.startsWith('/storage/')) {
                      pdfUrl = '$cleanBaseUrl$path';
                    } else if (path.startsWith('storage/')) {
                      pdfUrl = '$cleanBaseUrl/$path';
                    } else if (path.startsWith('/')) {
                      pdfUrl = '$cleanBaseUrl/storage$path';
                    } else {
                      pdfUrl = '$cleanBaseUrl/storage/$path';
                    }
                  }
                  
                  // Tambahkan token jika diperlukan
                  if (token.isNotEmpty) {
                    // Periksa apakah sudah ada query param '?' di dalam URL
                    pdfUrl = pdfUrl.contains('?') ? "$pdfUrl&token=$token" : "$pdfUrl?token=$token";
                  }

                  debugPrint('📡 MEMBUKA PDF VIA URL: $pdfUrl'); // Memudahkan Anda memantau URL di terminal
                  
                  // Buka di Chrome/browser eksternal
                  _openPdfInBrowser(pdfUrl, context);
                } else {
                  // ... snackbar materi belum tersedia ...
                }
              }
            ),
          );
        },
      ),
    );
  }
}