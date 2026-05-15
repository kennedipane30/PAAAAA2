package main

import (
	"net/http"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func GetTryouts(c *gin.Context) {
	classID := c.Query("class_id")
	var tryouts []Tryout
	DB.Where("class_id = ?", classID).Find(&tryouts)
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": tryouts})
}

func GetQuestions(c *gin.Context) {
	tryoutID := c.Param("id")
	var questions []Question
	DB.Where("tryout_id = ?", tryoutID).Find(&questions)
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": questions})
}

func SyncTryoutData(c *gin.Context) {
	var payload struct {
		Tryout    Tryout     `json:"tryout"`
		Questions []Question `json:"questions"`
	}
	if err := c.ShouldBindJSON(&payload); err != nil {
		c.JSON(400, gin.H{"error": err.Error()})
		return
	}
	DB.Transaction(func(tx *gorm.DB) error {
		tx.Save(&payload.Tryout)
		if len(payload.Questions) > 0 { tx.Save(&payload.Questions) }
		return nil
	})
	c.JSON(200, gin.H{"message": "Tryout Data Synced"})
}

func SubmitResult(c *gin.Context) {
	var res TryoutResult
	if err := c.ShouldBindJSON(&res); err != nil {
		c.JSON(400, gin.H{"error": err.Error()})
		return
	}
	DB.Create(&res)
	c.JSON(201, gin.H{"message": "Skor disimpan"})
}