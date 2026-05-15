package main

import (
	"net/http"
	"github.com/gin-gonic/gin"
)

func GetMaterials(c *gin.Context) {
	classID := c.Param("class_id")
	var materials []Material
	DB.Where("class_id = ?", classID).Order("week asc").Find(&materials)
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": materials})
}

func SyncMaterial(c *gin.Context) {
	var m Material
	if err := c.ShouldBindJSON(&m); err != nil {
		c.JSON(400, gin.H{"error": err.Error()})
		return
	}
	DB.Save(&m)
	c.JSON(200, gin.H{"message": "Materi Synced Successfully"})
}