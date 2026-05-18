package main

import (
	"practice-service/config"
	"practice-service/internal/delivery/http"
	"practice-service/internal/models"
	"practice-service/internal/repository"
	"practice-service/internal/usecase"
	"os"

	"github.com/gin-gonic/gin"
)

func main() {
	db := config.InitDB()
	
	// MIGRASI: Membuat tabel practice_questions
	db.AutoMigrate(&models.PracticeQuestion{})

	repo := repository.NewPracticeRepository(db)
	uc := usecase.NewPracticeUsecase(repo)
	handler := http.NewPracticeHandler(uc)

	r := gin.Default()
	api := r.Group("/api")
	{
		api.GET("/practices", handler.GetPractices)
	}

	port := os.Getenv("PORT")
	if port == "" { port = "9003" }
	r.Run(":" + port)
}