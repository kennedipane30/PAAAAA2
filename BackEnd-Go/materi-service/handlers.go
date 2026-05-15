package main

import (
	"net/http"
	"github.com/gin-gonic/gin"
)

func GetClassContent(c *gin.Context) {
	var input struct { ClassID string `json:"class_id"` }
	c.ShouldBindJSON(&input)

	var materials []Material
	var practices []PracticeQuestion

	DB.Where("class_id = ?", input.ClassID).Order("week asc").Find(&materials)
	DB.Table("practice_questions").Where("class_id = ?", input.ClassID).Find(&practices)

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"materi": materials,
		"practice_questions": practices,
		"enroll_status": "active",
	})
}