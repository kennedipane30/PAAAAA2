import 'package:flutter/material.dart';
import 'dart:convert';
import '../../services/auth_service.dart'; // ✨ Perbaikan path import otomatis

class PracticeQuizPage extends StatefulWidget {
  final List questions;
  final String token;
  final int userId;

  const PracticeQuizPage({super.key, required this.questions, required this.token, required this.userId});

  @override
  State<PracticeQuizPage> createState() => _PracticeQuizPageState();
}

class _PracticeQuizPageState extends State<PracticeQuizPage> {
  int currentIndex = 0;
  String? selectedAnswer;
  
  bool isChecked = false;
  bool isLoading = false; 

  bool isCorrect = false;
  int attemptsLeft = 2;
  String? hintText;
  String? explanationText;
  String? trueAnswerFromApi;

  void _next() {
    if (currentIndex < widget.questions.length - 1) {
      setState(() { 
        currentIndex++; 
        selectedAnswer = null; 
        isChecked = false; 
        hintText = null;
        explanationText = null;
        trueAnswerFromApi = null;
        attemptsLeft = 2;
      });
    } else {
      Navigator.pop(context);
    }
  }

 // ✨ MODIFIKASI: Perbaikan fungsi _checkAnswer
  Future<void> _checkAnswer() async {
    if (selectedAnswer == null) return;
    
    setState(() { isLoading = true; });

    try {
      // 1. Ambil ID mentah dari JSON
      var rawQid = widget.questions[currentIndex]['practice_question_id']; 
      
      // 2. PAKSA konversi menjadi Integer (Int) yang aman
      int qId = 0;
      if (rawQid != null) {
        qId = int.tryParse(rawQid.toString()) ?? 0;
      }

      // [DEBUG] Hapus atau biarkan print ini untuk melihat apa yang dikirim ke terminal VS Code Anda
      print("🚀 MENGIRIM KE GO -> UserID: ${widget.userId}, QuestionID: $qId, Answer: $selectedAnswer");

      var response = await AuthService.submitPracticeAnswer(
        widget.token, 
        widget.userId, 
        qId, 
        selectedAnswer!
      );

      // [DEBUG] Lihat balasan asli dari Golang di terminal VS Code Anda
      print("📥 RESPON GO -> Status: ${response.statusCode}, Body: ${response.body}");

      if (response.statusCode == 200) {
        var data = jsonDecode(response.body);
        setState(() {
          isCorrect = data['is_correct'] ?? false;
          attemptsLeft = data['attempts_left'] ?? 0;
          hintText = data['hint']; 
          explanationText = data['explanation']; 
          trueAnswerFromApi = data['correct_answer']; 
          
          isChecked = isCorrect || attemptsLeft <= 0;
        });
      } else {
        // ✨ Menampilkan error asli dari Golang ke layar HP agar kita tahu masalahnya
        var errorData = jsonDecode(response.body);
        String errorMsg = errorData['details'] ?? errorData['error'] ?? 'Gagal mengirim jawaban';
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("Error API: $errorMsg")));
      }
    } catch (e) {
      print("❌ ERROR EXCEPTION: $e");
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Terjadi kesalahan sistem.")));
    } finally {
      setState(() { isLoading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[currentIndex];
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: Text("Question ${currentIndex + 1} / ${widget.questions.length}", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.white)), backgroundColor: const Color(0xFF990000), foregroundColor: Colors.white, elevation: 0),
      body: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(q['question'] ?? "Question not found", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black87)),
            const SizedBox(height: 30),
            
            _option("A", q['option_a'] ?? ""),
            _option("B", q['option_b'] ?? ""),
            _option("C", q['option_c'] ?? ""),
            _option("D", q['option_d'] ?? ""),
            
            const Spacer(),

            if (hintText != null && !isChecked)
              Container(width: double.infinity, padding: const EdgeInsets.all(15), margin: const EdgeInsets.only(bottom: 10), decoration: BoxDecoration(color: Colors.orange.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.orange)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text("JAWABAN SALAH (Sisa $attemptsLeft x Percobaan)", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Colors.orange)),
                    const SizedBox(height: 5),
                    Text("Kata Kunci: $hintText", style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic, color: Colors.black87)),
                  ])),

            if (isChecked && explanationText != null)
              Container(width: double.infinity, padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: isCorrect ? Colors.blue.shade50 : Colors.red.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: isCorrect ? Colors.blue.shade100 : Colors.red.shade100)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(isCorrect ? "JAWABAN BENAR!" : "KESEMPATAN HABIS!", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: isCorrect ? Colors.blue : Colors.red)),
                    const SizedBox(height: 8),
                    Text("Penjelasan:\n$explanationText", style: const TextStyle(fontSize: 12, color: Colors.black87)),
                  ])),

            const SizedBox(height: 20),
            
            SizedBox(width: double.infinity, height: 55, child: ElevatedButton(
                onPressed: isLoading 
                    ? null 
                    : (isChecked ? _next : (selectedAnswer != null ? _checkAnswer : null)),
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), disabledBackgroundColor: Colors.grey.shade300, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), elevation: 0),
                child: isLoading 
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : Text(isChecked ? "NEXT QUESTION" : "CHECK ANSWER", style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              )),
          ],
        ),
      ),
    );
  }

  Widget _option(String code, String text) {
    bool isSelected = selectedAnswer == code;
    Color borderCol = Colors.grey.shade200;
    
    if (isChecked) {
      if (trueAnswerFromApi == code || (isCorrect && isSelected)) {
        borderCol = Colors.green; 
      } else if (isSelected && !isCorrect) {
        borderCol = Colors.red; 
      }
    } else if (isSelected) {
      borderCol = const Color(0xFF990000); 
    }

    return GestureDetector(
      onTap: isChecked ? null : () => setState(() => selectedAnswer = code),
      child: Container(margin: const EdgeInsets.only(bottom: 15), padding: const EdgeInsets.all(15), decoration: BoxDecoration(color: isSelected ? borderCol.withOpacity(0.05) : Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: borderCol, width: 2)),
        child: Row(children: [
            Text("$code.", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
            const SizedBox(width: 12),
            Expanded(child: Text(text, style: const TextStyle(fontSize: 14, color: Colors.black87))),
            if (isChecked && (trueAnswerFromApi == code || (isCorrect && isSelected))) 
              const Icon(Icons.check_circle, color: Colors.green, size: 20),
            if (isChecked && isSelected && !isCorrect) 
              const Icon(Icons.cancel, color: Colors.red, size: 20),
          ])),
    );
  }
}