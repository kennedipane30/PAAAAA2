// =====================================================================
//  login_page.dart — Spekta Academy (Logo di Tengah)
//  Logo digambar via CustomPainter (tidak butuh asset file)
//  Tampilan mengikuti register_page dengan penyesuaian tata letak tengah
// =====================================================================

import 'dart:convert';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../services/auth_service.dart';
import '../theme/theme_controller.dart';
import 'register_page.dart';
import 'main_screen.dart';
import 'forgot_password_page.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage>
    with SingleTickerProviderStateMixin {

  final _nameCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _obscure  = true;
  bool _loading  = false;

  late AnimationController _ac;
  late Animation<double>   _fade;
  late Animation<Offset>   _slide;

  static const Color mainRed  = Color(0xFFD32F2F);
  static const Color teal1    = Color(0xFF26A69A);
  static const Color teal2    = Color(0xFF00796B);
  static const Color textDark = Color(0xFF1F2028);
  static const Color textMuted = Color(0xFF8C8C95);

  static const double radiusLg = 22;
  static const double radiusMd = 12;
  static const double spacing  = 14;

  // ── Lifecycle ─────────────────────────────────────────────────────
  @override
  void initState() {
    super.initState();
    _ac = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 700));
    _fade  = CurvedAnimation(parent: _ac, curve: Curves.easeOut);
    _slide = Tween<Offset>(begin: const Offset(0, 0.06), end: Offset.zero)
        .animate(CurvedAnimation(parent: _ac, curve: Curves.easeOutCubic));
    _ac.forward();
  }

  @override
  void dispose() {
    _ac.dispose();
    _nameCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }

  // ── Login handler ─────────────────────────────────────────────────
  Future<void> _login() async {
    if (_nameCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      _snack('Nama dan Password wajib diisi!', isError: true);
      return;
    }
    setState(() => _loading = true);
    try {
      final r = await AuthService.login(
          _nameCtrl.text.trim(), _passCtrl.text);
      if (!mounted) return;
      setState(() => _loading = false);

      if (r.statusCode == 200) {
        final d = jsonDecode(r.body);
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (_) => MainScreen(
              userName: d['user']['name'],
              token: d['token'],
              userProfileData: d['user'],
            ),
          ),
          (_) => false,
        );
      } else {
        final e = jsonDecode(r.body);
        _snack(e['message'] ?? 'Nama atau Password salah!', isError: true);
      }
    } catch (_) {
      if (mounted) {
        setState(() => _loading = false);
        _snack('Koneksi gagal. Periksa server.', isError: true);
      }
    }
  }

  void _snack(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg, style: const TextStyle(fontWeight: FontWeight.w600)),
      backgroundColor: isError ? mainRed : const Color(0xFF22C55E),
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      margin: const EdgeInsets.all(16),
    ));
  }

  // ── Build ─────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final theme  = Provider.of<ThemeController>(context);
    final isDark = theme.isDark;
    final topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor:
          isDark ? const Color(0xFF0D0D0D) : const Color(0xFFF0F2F5),
      body: FadeTransition(
        opacity: _fade,
        child: SlideTransition(
          position: _slide,
          child: Column(
            children: [
              _buildHeader(isDark, topPad, theme),
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                  child: Transform.translate(
                    offset: const Offset(0, -22),
                    child: Container(
                      width: double.infinity,
                      padding: const EdgeInsets.fromLTRB(18, 22, 18, 22),
                      decoration: BoxDecoration(
                        color: isDark ? const Color(0xFF1A1A1A) : Colors.white,
                        borderRadius: BorderRadius.circular(radiusLg),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black
                                .withOpacity(isDark ? 0.30 : 0.08),
                            blurRadius: 20,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Full Name
                          _fieldLabel(isDark, 'Full Name'),
                          const SizedBox(height: 6),
                          _buildInput(
                            isDark,
                            ctrl: _nameCtrl,
                            hint: 'Masukkan nama lengkap',
                            icon: Icons.person_outline_rounded,
                            inputType: TextInputType.name,
                          ),
                          const SizedBox(height: spacing),

                          // Password
                          _fieldLabel(isDark, 'Password'),
                          const SizedBox(height: 6),
                          _buildPasswordInput(isDark),

                          // Forgot password
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton(
                              onPressed: () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (_) =>
                                        const ForgotPasswordPage()),
                              ),
                              style: TextButton.styleFrom(
                                padding: const EdgeInsets.only(
                                    top: 4, bottom: 2),
                                tapTargetSize:
                                    MaterialTapTargetSize.shrinkWrap,
                              ),
                              child: Text(
                                'Forgot Password?',
                                style: TextStyle(
                                  color: isDark ? Colors.white60 : mainRed,
                                  fontWeight: FontWeight.w700,
                                  fontSize: 12,
                                ),
                              ),
                            ),
                          ),

                          const SizedBox(height: 8),
                          _buildLoginButton(),
                          const SizedBox(height: 18),

                          Center(
                            child: GestureDetector(
                              onTap: () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (_) => const RegisterPage()),
                              ),
                              child: RichText(
                                text: TextSpan(
                                  text: 'Belum punya akun? ',
                                  style: TextStyle(
                                    color: isDark
                                        ? Colors.white.withOpacity(0.55)
                                        : textMuted,
                                    fontSize: 12.5,
                                  ),
                                  children: [
                                    TextSpan(
                                      text: 'Daftar Sekarang',
                                      style: TextStyle(
                                        color: isDark ? Colors.white : mainRed,
                                        fontWeight: FontWeight.w800,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ── Header dengan logo CustomPainter di Tengah ─────────────────────
  Widget _buildHeader(bool isDark, double topPad, ThemeController theme) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.fromLTRB(18, topPad + 10, 18, 40),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: isDark
              ? const [Color(0xFF7B0000), Color(0xFF1A1A2E)]
              : const [Color(0xFFC62828), Color(0xFF26A69A)],
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center, // Ubah ke center
        mainAxisSize: MainAxisSize.min,
        children: [
          // Toggle dark mode (tetap di kanan)
          Align(
            alignment: Alignment.centerRight,
            child: GestureDetector(
              onTap: theme.toggleTheme,
              child: Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.20),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isDark
                      ? Icons.dark_mode_rounded
                      : Icons.light_mode_rounded,
                  color: Colors.white,
                  size: 18,
                ),
              ),
            ),
          ),
          const SizedBox(height: 4),

          // ── Logo CustomPainter di Tengah ──────────────────────────────
          _SpektaLogoWidget(),
          const SizedBox(height: 14),

          // Teks Judul dan Tagline di Tengah
          const Text(
            'SPEKTA ACADEMY',
            style: TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.w900,
              letterSpacing: 1.2,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 4),
          const Text(
            'Achieve Your Dream of Serving the Nation',
            style: TextStyle(
              color: Colors.white70,
              fontSize: 11,
              fontStyle: FontStyle.italic,
              height: 1.4,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 24),

          // Judul Form Masuk Ke Akun
          const Text(
            'Masuk ke Akun',
            style: TextStyle(
              color: Colors.white,
              fontSize: 22,
              fontWeight: FontWeight.w800,
              letterSpacing: -0.3,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 4),
          const Text(
            'Gunakan nama & password yang terdaftar',
            style: TextStyle(color: Colors.white70, fontSize: 12),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  // ── Helpers field ─────────────────────────────────────────────────
  Widget _fieldLabel(bool isDark, String text) {
    return Text(
      text,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 12.5,
        fontWeight: FontWeight.w700,
      ),
    );
  }

  InputDecoration _baseDecoration(bool isDark, String hint, IconData icon,
      {Widget? suffixIcon}) {
    final fill = isDark
        ? Colors.white.withOpacity(0.06)
        : const Color(0xFFF4F6FB);
    final border = isDark
        ? Colors.white.withOpacity(0.12)
        : const Color(0xFFDDE3EF);

    return InputDecoration(
      filled: true,
      fillColor: fill,
      hintText: hint,
      hintStyle: TextStyle(
        color: isDark ? Colors.white30 : const Color(0xFFB0B8C8),
        fontSize: 13,
      ),
      prefixIcon: Icon(icon,
          color: isDark ? Colors.white38 : const Color(0xFF9CA3AF),
          size: 18),
      suffixIcon: suffixIcon,
      isDense: true,
      contentPadding:
          const EdgeInsets.symmetric(vertical: 13, horizontal: 14),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: BorderSide(color: border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed),
      ),
    );
  }

  Widget _buildInput(bool isDark,
      {required TextEditingController ctrl,
      required String hint,
      required IconData icon,
      TextInputType inputType = TextInputType.text}) {
    return TextField(
      controller: ctrl,
      keyboardType: inputType,
      style: TextStyle(
          color: isDark ? Colors.white : textDark,
          fontSize: 13,
          fontWeight: FontWeight.w500),
      decoration: _baseDecoration(isDark, hint, icon),
    );
  }

  Widget _buildPasswordInput(bool isDark) {
    return TextField(
      controller: _passCtrl,
      obscureText: _obscure,
      style: TextStyle(
          color: isDark ? Colors.white : textDark,
          fontSize: 13,
          fontWeight: FontWeight.w500),
      decoration: _baseDecoration(
        isDark,
        'Masukkan password',
        Icons.lock_outline_rounded,
        suffixIcon: IconButton(
          icon: Icon(
            _obscure
                ? Icons.visibility_off_outlined
                : Icons.visibility_outlined,
            color: isDark ? Colors.white38 : const Color(0xFF9CA3AF),
            size: 18,
          ),
          onPressed: () => setState(() => _obscure = !_obscure),
        ),
      ),
    );
  }

  // ── Tombol MASUK — teal gradient ──────────────────────────────────
  Widget _buildLoginButton() {
    return Container(
      width: double.infinity,
      height: 48,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(radiusMd),
        gradient: const LinearGradient(
          colors: [teal1, teal2],
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
        ),
        boxShadow: [
          BoxShadow(
            color: teal1.withOpacity(0.35),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: _loading ? null : _login,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(radiusMd)),
        ),
        child: _loading
            ? const SizedBox(
                width: 20, height: 20,
                child: CircularProgressIndicator(
                    color: Colors.white, strokeWidth: 2.5))
            : const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text('MASUK',
                      style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 1.5)),
                  SizedBox(width: 6),
                  Icon(Icons.arrow_forward_rounded, size: 16),
                ],
              ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────
//  LOGO SPEKTA — CustomPainter
//  Menggambar logo lingkaran merah dengan gedung + tulisan SPEKTA
// ─────────────────────────────────────────────────────────────────────
class _SpektaLogoWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      width: 64,
      height: 64,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white.withOpacity(0.5), width: 2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.22),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipOval(
        child: CustomPaint(
          size: const Size(64, 64),
          painter: _SpektaLogoPainter(),
        ),
      ),
    );
  }
}

class _SpektaLogoPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final cx = size.width / 2;
    final cy = size.height / 2;
    final r  = size.width / 2;

    // ── Background: merah ke merah gelap ──────────────────────────
    final bgPaint = Paint()
      ..shader = const RadialGradient(
        center: Alignment(-0.3, -0.3),
        radius: 1.0,
        colors: [Color(0xFFE53935), Color(0xFF7B0000)],
      ).createShader(Rect.fromCircle(
          center: Offset(cx, cy), radius: r));
    canvas.drawCircle(Offset(cx, cy), r, bgPaint);

    // ── Ring dalam putih transparan ───────────────────────────────
    canvas.drawCircle(
      Offset(cx, cy), r - 3,
      Paint()
        ..color = Colors.white.withOpacity(0.18)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.2,
    );

    final whiteFill = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.fill;
    final goldPaint = Paint()..color = const Color(0xFFFDD835);

    // ──────────────────────────────────────────────────────────────
    // GEDUNG UTAMA (bangunan tengah tinggi)
    // ──────────────────────────────────────────────────────────────
    final bx = cx; 
    final baseY = cy + r * 0.38; 

    // Badan gedung utama
    final mainBuilding = RRect.fromRectAndRadius(
      Rect.fromLTWH(bx - 10, baseY - 26, 20, 26),
      const Radius.circular(1.5),
    );
    canvas.drawRRect(mainBuilding, whiteFill);

    // Atap segitiga gedung utama
    final roofMain = Path()
      ..moveTo(bx - 13, baseY - 26)
      ..lineTo(bx, baseY - 36)
      ..lineTo(bx + 13, baseY - 26)
      ..close();
    canvas.drawPath(roofMain, whiteFill);

    // Pintu tengah gedung
    final door = RRect.fromRectAndRadius(
      Rect.fromLTWH(bx - 3.5, baseY - 10, 7, 10),
      const Radius.circular(3.5),
    );
    canvas.drawRRect(
        door,
        Paint()
          ..color = const Color(0xFF7B0000)
          ..style = PaintingStyle.fill);

    // Jendela gedung utama (2 baris x 2 kolom)
    final winPaint = Paint()
      ..color = const Color(0xFF7B0000)
      ..style = PaintingStyle.fill;
    for (int row = 0; row < 2; row++) {
      for (int col = 0; col < 2; col++) {
        final wx = bx - 7 + col * 9.0;
        final wy = baseY - 24 + row * 8.0;
        canvas.drawRect(Rect.fromLTWH(wx, wy, 5, 5), winPaint);
      }
    }

    // ── Gedung sayap kiri ─────────────────────────────────────────
    final leftWing = RRect.fromRectAndRadius(
      Rect.fromLTWH(bx - 22, baseY - 18, 13, 18),
      const Radius.circular(1),
    );
    canvas.drawRRect(leftWing, whiteFill);

    // Atap sayap kiri
    final roofLeft = Path()
      ..moveTo(bx - 24, baseY - 18)
      ..lineTo(bx - 15.5, baseY - 25)
      ..lineTo(bx - 9, baseY - 18)
      ..close();
    canvas.drawPath(roofLeft, whiteFill);

    // Jendela sayap kiri
    for (int row = 0; row < 2; row++) {
      canvas.drawRect(
          Rect.fromLTWH(bx - 20, baseY - 16 + row * 7.0, 4, 5), winPaint);
      canvas.drawRect(
          Rect.fromLTWH(bx - 14, baseY - 16 + row * 7.0, 4, 5), winPaint);
    }

    // ── Gedung sayap kanan ────────────────────────────────────────
    final rightWing = RRect.fromRectAndRadius(
      Rect.fromLTWH(bx + 9, baseY - 18, 13, 18),
      const Radius.circular(1),
    );
    canvas.drawRRect(rightWing, whiteFill);

    // Atap sayap kanan
    final roofRight = Path()
      ..moveTo(bx + 9, baseY - 18)
      ..lineTo(bx + 15.5, baseY - 25)
      ..lineTo(bx + 24, baseY - 18)
      ..close();
    canvas.drawPath(roofRight, whiteFill);

    // Jendela sayap kanan
    for (int row = 0; row < 2; row++) {
      canvas.drawRect(
          Rect.fromLTWH(bx + 10, baseY - 16 + row * 7.0, 4, 5), winPaint);
      canvas.drawRect(
          Rect.fromLTWH(bx + 16, baseY - 16 + row * 7.0, 4, 5), winPaint);
    }

    // ── Tiang bendera di atas atap ────────────────────────────────
    canvas.drawLine(
      Offset(bx, baseY - 36),
      Offset(bx, baseY - 46),
      Paint()
        ..color = Colors.white
        ..strokeWidth = 1.5
        ..strokeCap = StrokeCap.round,
    );
    // Bendera
    final flag = Path()
      ..moveTo(bx, baseY - 46)
      ..lineTo(bx + 7, baseY - 43)
      ..lineTo(bx, baseY - 40)
      ..close();
    canvas.drawPath(flag, goldPaint);

    // ── Garis tanah ───────────────────────────────────────────────
    canvas.drawLine(
      Offset(cx - r * 0.62, baseY),
      Offset(cx + r * 0.62, baseY),
      Paint()
        ..color = Colors.white.withOpacity(0.6)
        ..strokeWidth = 1.2,
    );

    // ── Tulisan "SPEKTA" melengkung di bawah ──────────────────────
    _drawArcText(canvas, size, 'SPEKTA', r - 8, math.pi * 0.3);
  }

  void _drawArcText(
      Canvas canvas, Size size, String text, double radius, double startAngle) {
    final cx = size.width / 2;
    final cy = size.height / 2;
    final tp = TextPainter(textDirection: TextDirection.ltr);
    final charCount = text.length;
    const angleStep = 0.26; 
    final totalAngle = angleStep * (charCount - 1);
    final angleStart = math.pi / 2 + startAngle - totalAngle / 2;

    for (int i = 0; i < charCount; i++) {
      final angle = angleStart + i * angleStep;
      tp.text = TextSpan(
        text: text[i],
        style: const TextStyle(
          color: Colors.white,
          fontSize: 7.5,
          fontWeight: FontWeight.w900,
          letterSpacing: 0,
        ),
      );
      tp.layout();
      canvas.save();
      canvas.translate(
        cx + radius * math.cos(angle),
        cy + radius * math.sin(angle),
      );
      canvas.save();
      canvas.rotate(angle + math.pi / 2);
      canvas.translate(-tp.width / 2, -tp.height / 2);
      tp.paint(canvas, Offset.zero);
      canvas.restore();
      canvas.restore();
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter old) => false;
}