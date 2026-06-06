package http

import (
	"net/http"
	"strconv" // ✨ Tambahkan import strconv
	"tryout-service/internal/models"
	"tryout-service/internal/usecase"
	"github.com/gin-gonic/gin"
)

type TryoutHandler struct {
	uc usecase.TryoutUsecase
}

func NewTryoutHandler(uc usecase.TryoutUsecase) *TryoutHandler {
	return &TryoutHandler{uc}
}

// Struct untuk bungkusan data dari Laravel
type SyncRequest struct {
	Tryout    models.Tryout     `json:"tryout"`
	Questions []models.Question `json:"questions"`
}

func (h *TryoutHandler) SyncTryout(c *gin.Context) {
	var req SyncRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format JSON salah"})
		return
	}

	if err := h.uc.SyncTryout(req.Tryout, req.Questions); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal simpan ke database"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Paket Tryout Berhasil Masuk ke Go"})
}

func (h *TryoutHandler) SyncSubmissions(c *gin.Context) {
	var s models.TryoutSubmission
	if err := c.ShouldBindJSON(&s); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format data submission salah"})
		return
	}

	if err := h.uc.SyncSubmissions(s); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal simpan riwayat di Go"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Riwayat berhasil disimpan di Go"})
}

func (h *TryoutHandler) GetTryouts(c *gin.Context) {
	classID := c.Query("class_id")
	userID := c.Query("user_id") 
	
	if userID == "" {
		userID = "0" 
	}

	data, err := h.uc.GetTryouts(classID, userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": "Gagal mengambil daftar tryout"})
		return
	}
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": data})
}

func (h *TryoutHandler) GetQuestions(c *gin.Context) {
	tryoutID := c.Param("id")
	if tryoutID == "" {
		tryoutID = c.Query("tryout_id")
	}

	if tryoutID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "tryout_id is required"})
		return
	}

	data, err := h.uc.GetQuestions(tryoutID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database Error"})
		return
	}

	c.JSON(http.StatusOK, data)
}

/**
 * 5. SIMPAN HASIL UJIAN (SUBMIT) & HITUNG NILAI ASLI
 */
func (h *TryoutHandler) SubmitTryout(c *gin.Context) {
	var req struct {
		TryoutID int               `json:"tryout_id"`
		UserID   int               `json:"user_id"` 
		Answers  map[string]string `json:"answers"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format data jawaban salah"})
		return
	}

	// ✨ MODIFIKASI: Panggil fungsi perhitungan dari Usecase
	tryoutIDStr := strconv.Itoa(req.TryoutID)
	score, correctCount, err := h.uc.CalculateScore(tryoutIDStr, req.Answers)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghitung nilai ujian"})
		return
	}

	// Simpan status ujian ke Database dengan skor yang asli!
	submission := models.TryoutSubmission{
		TryoutID: uint(req.TryoutID),
		UserID:   uint(req.UserID),
		Score:    float64(score), // ✨ Gunakan Skor Dinamis
	}
	h.uc.SyncSubmissions(submission)

	// Kirim response dinamis ke Flutter
	c.JSON(http.StatusOK, gin.H{
		"status":  "success",
		"score":   score,        // ✨ SKOR DINAMIS
		"correct": correctCount, // ✨ JUMLAH BENAR DINAMIS
		"message": "Nilai berhasil disimpan di database",
	})
}