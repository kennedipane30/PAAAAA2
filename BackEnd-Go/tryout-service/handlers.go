package main

import (
	"net/http"
	"github.com/gin-gonic/gin"
)

func GetTryoutList(c *gin.Context) {
	classID := c.Query("class_id")
	var tryouts []Tryout
	DB.Where("class_id = ?", classID).Find(&tryouts)
	c.JSON(200, gin.H{"status": "success", "tryouts": tryouts})
}

func GetQuestions(c *gin.Context) {
	var input struct { TryoutID string `json:"tryout_id"` }
	c.ShouldBindJSON(&input)
	var questions []Question
	DB.Where("tryout_id = ?", input.TryoutID).Find(&questions)
	c.JSON(200, gin.H{"status": "success", "data": questions})
}

func SubmitTryoutResult(c *gin.Context) {
	var res TryoutResult
	if err := c.ShouldBindJSON(&res); err != nil {
		c.JSON(400, gin.H{"error": "Invalid data"})
		return
	}
	DB.Create(&res)
	c.JSON(200, gin.H{"status": "success", "score": res.score})
}