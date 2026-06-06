import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:fl_chart/fl_chart.dart'; 
import '../services/auth_service.dart';

class ReportPage extends StatefulWidget {
  final String token;
  final Map userData; 
  final VoidCallback onGoToHome;

  const ReportPage({
    super.key, 
    required this.token,
    required this.userData,
    required this.onGoToHome,
  });

  @override
  State<ReportPage> createState() => _ReportPageState();
}

class _ReportPageState extends State<ReportPage> {
  bool isLoading = true;
  
  List announcements = [];
  List<FlSpot> chartData = []; 
  List<String> tryoutTitles = []; 

  @override
  void initState() {
    super.initState();
    _fetchReportData();
  }

  String _fixImageUrl(String path) {
    if (path.isEmpty) return '';
    if (path.startsWith('http')) return path;
    
    if (!path.startsWith('/')) {
      path = '/$path';
    }
    return 'http://10.0.2.2:8000/storage$path';
  }

  Future<void> _fetchReportData() async {
    try {
      final annRes = await AuthService.getAnnouncements(widget.token).catchError((_) => http.Response('[]', 500));
      
      // ✨ PERBAIKAN: Ambil ID User dari profile
      int currentUserId = 0;
      if (widget.userData['id'] != null) {
        currentUserId = int.parse(widget.userData['id'].toString());
      } else if (widget.userData['user'] != null && widget.userData['user']['id'] != null) {
        currentUserId = int.parse(widget.userData['user']['id'].toString());
      }

      // ✨ PERBAIKAN: Memanggil Tryout History ke Port 9002 (Kirim userId)
      final reportRes = await AuthService.getTryoutHistory(widget.token, currentUserId);

      if (mounted) {
        setState(() {
          // Parsing Pengumuman
          if (annRes.statusCode == 200) {
            final annDecoded = jsonDecode(annRes.body);
            announcements = annDecoded is List ? annDecoded : (annDecoded['data'] ?? []);
          }

          // Parsing Riwayat Nilai
          if (reportRes.statusCode == 200) {
            final decoded = jsonDecode(reportRes.body);
            List history = decoded is List ? decoded : (decoded['data'] ?? []);
            
            chartData.clear();
            tryoutTitles.clear();
            
            if (history.isNotEmpty) {
              history = history.reversed.toList();
              int count = history.length > 7 ? 7 : history.length;
              
              for (var i = 0; i < count; i++) {
                double score = double.parse((history[i]['score'] ?? 0).toString());
                chartData.add(FlSpot(i.toDouble(), score));
                
                String title = history[i]['title'] ?? history[i]['tryout_name'] ?? 'TO ${i+1}';
                tryoutTitles.add(title.length > 6 ? '${title.substring(0,6)}...' : title);
              }
            }
          }
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("❌ Error Fetching Report: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Learning Report", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? const Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            onRefresh: _fetchReportData,
            color: spektaRed,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: const [
                      Icon(Icons.campaign_rounded, color: spektaRed),
                      SizedBox(width: 10),
                      Text("Pengumuman Terbaru", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 15),
                  
                  if (announcements.isNotEmpty)
                    ClipRRect(
                      borderRadius: BorderRadius.circular(15),
                      child: Image.network(
                        _fixImageUrl(announcements[0]['image_url'] ?? announcements[0]['image'] ?? ''),
                        height: 180,
                        width: double.infinity,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Container(
                          height: 180, width: double.infinity,
                          color: Colors.grey[200],
                          child: const Center(child: Icon(Icons.broken_image, color: Colors.grey, size: 40)),
                        ),
                      ),
                    )
                  else 
                    Container(
                      height: 180, width: double.infinity,
                      decoration: BoxDecoration(color: Colors.grey[200], borderRadius: BorderRadius.circular(15)),
                      child: const Center(child: Text("Tidak ada pengumuman", style: TextStyle(color: Colors.grey))),
                    ),

                  const SizedBox(height: 40),

                  Row(
                    children: const [
                      Icon(Icons.auto_graph_rounded, color: spektaRed),
                      SizedBox(width: 10),
                      Text("Statistik Belajar (7 Terakhir)", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 5),
                  const Text("Grafik hasil perkembangan nilai kamu", style: TextStyle(color: Colors.grey, fontSize: 12)),
                  const SizedBox(height: 20),

                  Container(
                    height: 250,
                    width: double.infinity,
                    padding: const EdgeInsets.fromLTRB(15, 30, 20, 10),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15, offset: const Offset(0, 5))]
                    ),
                    child: chartData.isEmpty
                      ? const Center(child: Text("Belum ada data nilai.", style: TextStyle(color: Colors.grey)))
                      : LineChart(
                          LineChartData(
                            gridData: FlGridData(
                              show: true, 
                              drawVerticalLine: false, 
                              getDrawingHorizontalLine: (value) => FlLine(color: Colors.grey[200]!, strokeWidth: 1)
                            ),
                            titlesData: FlTitlesData(
                              show: true,
                              rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                              topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                              bottomTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 30,
                                  getTitlesWidget: (value, meta) {
                                    int index = value.toInt();
                                    if (index >= 0 && index < tryoutTitles.length) {
                                      return Padding(
                                        padding: const EdgeInsets.only(top: 8.0),
                                        child: Text(tryoutTitles[index], style: const TextStyle(fontSize: 10, color: Colors.grey)),
                                      );
                                    }
                                    return const Text('');
                                  },
                                ),
                              ),
                              leftTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 40,
                                  getTitlesWidget: (value, meta) => Text("${value.toInt()}", style: const TextStyle(fontSize: 10, color: Colors.grey)),
                                ),
                              ),
                            ),
                            borderData: FlBorderData(show: false),
                            minX: 0,
                            maxX: chartData.isEmpty ? 0 : (chartData.length - 1).toDouble(),
                            minY: 0,
                            maxY: 100, 
                            lineBarsData: [
                              LineChartBarData(
                                spots: chartData,
                                isCurved: true,
                                color: spektaRed,
                                barWidth: 3,
                                isStrokeCapRound: true,
                                dotData: const FlDotData(show: true),
                                belowBarData: BarAreaData(
                                  show: true,
                                  color: spektaRed.withOpacity(0.15),
                                ),
                              ),
                            ],
                          ),
                        ),
                  ),
                  
                  const SizedBox(height: 30),
                  
                  if (chartData.isEmpty)
                    Center(
                      child: TextButton.icon(
                        onPressed: widget.onGoToHome, 
                        icon: const Icon(Icons.arrow_back, color: spektaRed), 
                        label: const Text("Kembali ke Beranda", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold))
                      ),
                    ),
                    
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }
}