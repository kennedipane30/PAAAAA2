package main

import (
	"materi-service/config"
	"materi-service/internal/delivery/http"
	"materi-service/internal/models"
	"materi-service/internal/repository"
	"materi-service/internal/usecase"
	"os"

	"github.com/gin-gonic/gin"
)

func main() {
	db := config.InitDB()
	
	// MIGRASI: Membuat tabel materials
	db.AutoMigrate(&models.Material{})

	repo := repository.NewMaterialRepository(db)
	uc := usecase.NewMaterialUsecase(repo)
	handler := http.NewMaterialHandler(uc)

	r := gin.Default()
	api := r.Group("/api")
	{
		api.POST("/materials/sync", handler.SyncMaterial)
		api.GET("/materials", handler.GetMaterials)
	}

	port := os.Getenv("PORT")
	if port == "" { port = "9001" }
	r.Run(":" + port)
}