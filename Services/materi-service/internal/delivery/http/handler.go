package http

import (
	"materi-service/internal/models"
	"materi-service/internal/usecase"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type MaterialHandler struct {
	uc usecase.MaterialUsecase
}

func NewMaterialHandler(uc usecase.MaterialUsecase) *MaterialHandler {
	return &MaterialHandler{uc}
}

// Handler untuk Sync dari Laravel
func (h *MaterialHandler) SyncMaterial(c *gin.Context) {
	var req models.Material
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"message": err.Error()})
		return
	}

	if err := h.uc.Sync(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"message": err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Sync Success"})
}

// Handler untuk Ambil Data Materi
func (h *MaterialHandler) GetMaterials(c *gin.Context) {
	classID, _ := strconv.Atoi(c.Query("class_id"))
	subjectName := c.Query("subject_name")

	data, err := h.uc.FetchMaterials(uint(classID), subjectName)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"message": err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"data": data})
}