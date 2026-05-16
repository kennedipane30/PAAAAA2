package http

import (
	"net/http"
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

func (h *TryoutHandler) SyncTryout(c *gin.Context) {
	var payload struct {
		Tryout    models.Tryout     `json:"tryout"`
		Questions []models.Question `json:"questions"`
	}

	if err := c.ShouldBindJSON(&payload); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := h.uc.ProcessSync(payload.Tryout, payload.Questions); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Tryout and Questions synced successfully"})
}

// ✨ TAMBAHKAN INI: Handler untuk Sync data dari Pengajar
func (h *TryoutHandler) SyncSubmissions(c *gin.Context) {
	var req []models.TryoutSubmission
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := h.uc.ProcessSubmissionsSync(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Submissions synced successfully"})
}