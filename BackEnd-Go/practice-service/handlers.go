package main

import (
	"net/http"
	"github.com/gin-gonic/gin"
)

func GetPracticeByClass(c *gin.Context) {
	var input struct { ClassID string `json:"class_id"` }
	c.ShouldBindJSON(&input)

	var practices []PracticeQuestion
	DB.Where("class_id = ?", input.ClassID).Find(&practices)

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"practice_questions": practices,
	})
}