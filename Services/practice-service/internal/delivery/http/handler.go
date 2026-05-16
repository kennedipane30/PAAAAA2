package http

import (
	"net/http"
	"practice-service/internal/models"
	"practice-service/internal/usecase"
	"strconv"

	"github.com/gin-gonic/gin"
)

type PracticeHandler struct {
	uc usecase.PracticeUsecase
}

func NewPracticeHandler(uc usecase.PracticeUsecase) *PracticeHandler {
	return &PracticeHandler{uc}
}

func (h *PracticeHandler) Sync(c *gin.Context) {
	var req []models.PracticeQuestion
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.SyncQuestions(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Sync Success"})
}

func (h *PracticeHandler) DeleteWeek(c *gin.Context) {
	classID, _ := strconv.Atoi(c.Param("class_id"))
	week, _ := strconv.Atoi(c.Param("week"))
	subject := c.Query("subject")

	if err := h.uc.RemoveWeek(uint(classID), subject, week); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Deleted successfully"})
}