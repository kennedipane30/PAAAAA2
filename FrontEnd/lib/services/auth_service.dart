import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // ============================
  // 🌐 ENDPOINT CONFIGURATION
  // ============================
  // 10.0.2.2 adalah IP khusus Emulator Android untuk akses localhost laptop
  static const String baseUrl = 'http://10.0.2.2:8000/api';        // Laravel (Auth & Payment)
  static const String materiBaseUrl = 'http://10.0.2.2:9001/api';  // Go (Materi Service)
  static const String tryoutBaseUrl = 'http://10.0.2.2:9002/api';  // Go (Tryout Service)
  static const String practiceBaseUrl = 'http://10.0.2.2:9003/api';// Go (Practice Service)

  // ============================
  // 🔐 1. AUTHENTICATION (Laravel - Port 8000)
  // ============================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'),
      headers: {'Accept': 'application/json'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // ✨ FIX: Menambahkan kembali verifyRegistration yang hilang di screenshot
  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'otp': otp.trim()},
    );
  }

  // ✨ FIX: Menambahkan kembali resendOtp yang hilang di screenshot
  static Future<http.Response> resendOtp(String name) async {
    return await http.post(
      Uri.parse('$baseUrl/resend-otp'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim()},
    );
  }

  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'password': password},
    );
  }

  static Future<http.Response> logout(String token) async {
    return await http.post(Uri.parse('$baseUrl/logout'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ============================
  // 👤 2. USER PROFILE (Laravel - Port 8000)
  // ============================

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(Uri.parse('$baseUrl/user'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  // ✨ FIX: Menambahkan kembali updateProfile yang hilang di screenshot
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

  static Future<http.Response> getAllClasses(String token) async {
    return await http.get(Uri.parse('$baseUrl/classes'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ============================
  // 📚 3. MATERI SERVICE (Port 9001 - Go)
  // ============================

  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(
      Uri.parse('$materiBaseUrl/class/content'), 
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'class_id': classId.toString()}),
    );
  }

  // ============================
  // 📖 4. PRACTICE SERVICE (Port 9003 - Go)
  // ============================

  static Future<http.Response> getPracticeData(int classId, String token) async {
    return await http.post(
      Uri.parse('$practiceBaseUrl/practice/questions'), 
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'class_id': classId.toString()}),
    );
  }

  // ============================
  // 📝 5. TRYOUT SERVICE (Port 9002 - Go)
  // ============================

  static Future<http.Response> getTryoutList(int classId, String token) async {
    return await http.get(
      Uri.parse('$tryoutBaseUrl/tryouts?class_id=$classId'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.post(
      Uri.parse('$tryoutBaseUrl/tryout/questions'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'tryout_id': tryoutId.toString()}),
    );
  }

  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<int, String> answers,
    required String token
  }) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(Uri.parse('$tryoutBaseUrl/tryout/submit'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}),
    );
  }

  static Future<http.Response> getLearningReport(String token) async {
    return await http.get(
      Uri.parse('$tryoutBaseUrl/learning-report'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ============================
  // 🏷️ 6. OTHER UTILITIES (Laravel - Port 8000)
  // ============================

  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'));
    request.headers.addAll({'Accept': 'application/json', 'Authorization': 'Bearer $token'});
    request.fields['class_id'] = classId.toString();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }

  static Future<http.Response> getAnnouncements(String token) async => 
    await http.get(Uri.parse('$baseUrl/announcements'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> checkPromoCode(String code, int classId, int price, String token) async => 
    await http.post(Uri.parse('$baseUrl/promo/check'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: {'code': code.trim(), 'class_id': classId.toString(), 'price': price.toString()});

  static Future<http.Response> getTutorData(String token) async => 
    await http.get(Uri.parse('$baseUrl/tutor/form-data'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> getTutorHistory(String token) async => 
    await http.get(Uri.parse('$baseUrl/tutor/history'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> submitTutor(Map data, String token) async => 
    await http.post(Uri.parse('$baseUrl/tutor/submit'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: data.map((key, value) => MapEntry(key, value.toString())));

  static Future<http.Response> forgotPassword(String email) async => 
    await http.post(Uri.parse('$baseUrl/forgot-password'), headers: {'Accept': 'application/json'}, body: {'email': email.trim()});

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async => 
    await http.post(Uri.parse('$baseUrl/reset-password'), headers: {'Accept': 'application/json'}, body: data.map((key, value) => MapEntry(key, value.toString())));
}