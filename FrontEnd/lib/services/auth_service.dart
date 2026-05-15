import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Laravel Backend (Auth, Payment, User Profile)
  static const String baseUrl = 'http://10.0.2.2:8000/api';
  
  // ✨ MODIFIKASI: Go Microservice (Materials & Tryouts)
  static const String goBaseUrl = 'http://10.0.2.2:9000/api';

  // ============================
  // 🔐 1. AUTHENTICATION (Laravel)
  // ============================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {'Accept': 'application/json'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'otp': otp.trim()},
    );
  }

  static Future<http.Response> resendOtp(String name) async {
    return await http.post(
      Uri.parse('$baseUrl/resend-otp'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim()},
    );
  }

  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'password': password},
    );
  }

  static Future<http.Response> logout(String token) async {
    return await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }

  // ============================
  // 👤 2. USER PROFILE (Laravel)
  // ============================

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/update-profile'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // ============================
  // 📚 3. CLASS & MATERIALS (Microservice Go)
  // ============================

  // ✨ MODIFIKASI: Diarahkan ke Go (GET materials/:id)
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.get(
      Uri.parse('$goBaseUrl/materials/$classId'),
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
    );
  }

  static Future<http.Response> getAllClasses(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/classes'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }

  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'));
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    request.fields['class_id'] = classId.toString();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }

  // ============================
  // 🏷️ 4. PROMO & ANNOUNCEMENTS (Laravel)
  // ============================

  static Future<http.Response> getActivePromos() async {
    return await http.get(
      Uri.parse('$baseUrl/promos'),
      headers: {'Accept': 'application/json'},
    );
  }

  static Future<http.Response> getAnnouncements(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/announcements'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }

  static Future<http.Response> checkPromoCode(String code, int classId, int price, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/promo/check'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: {
        'code': code.trim(),
        'class_id': classId.toString(),
        'price': price.toString()
      },
    );
  }

  static Future<http.StreamedResponse> joinClassPromo({
    required int classId,
    required String promoCode,
    required String filePath,
    required String token,
  }) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join-promo'));
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    request.fields['class_id'] = classId.toString();
    request.fields['promo_code'] = promoCode.trim();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }

  // ============================
  // 💳 5. PAYMENT & REPORTS (Laravel)
  // ============================

  static Future<http.Response> getSnapToken({
    required int courseId,
    required String name,
    required String email,
    required String token,
    String? promoCode,
  }) async {
    return await http.post(
      Uri.parse('$baseUrl/payment/snap-token'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: {
        'class_id': courseId.toString(),
        'name': name,
        'email': email,
        'promo_code': promoCode ?? '',
      },
    );
  }

  static Future<http.Response> getLearningReport(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/learning-report'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }

  // ============================
  // 📝 6. TRYOUT & TUTOR (Microservice Go)
  // ============================

  static Future<http.Response> getSiswaSchedule(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/schedules'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }

  // ✨ MODIFIKASI: Diarahkan ke Go (GET tryouts/:id/questions)
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.get(
      Uri.parse('$goBaseUrl/tryouts/$tryoutId/questions'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }

  // ✨ MODIFIKASI: Diarahkan ke Go (POST tryouts/submit)
  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<int, String> answers,
    required String token
  }) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(
      Uri.parse('$goBaseUrl/tryouts/submit'), // Diubah ke goBaseUrl
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({
        'tryout_id': tryoutId, 
        'answers': stringAnswers
      }),
    );
  }

  // --- TUTOR DATA (Laravel) ---
  static Future<http.Response> getTutorData(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/form-data'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  static Future<http.Response> getTutorHistory(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/history'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  static Future<http.Response> submitTutor(Map data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/tutor/submit'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // ============================
  // 🔑 7. FORGOT PASSWORD (Laravel)
  // ============================

  static Future<http.Response> forgotPassword(String email) async {
    return await http.post(
      Uri.parse('$baseUrl/forgot-password'),
      headers: {'Accept': 'application/json'},
      body: {'email': email.trim()},
    );
  }

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/reset-password'),
      headers: {'Accept': 'application/json'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }
}