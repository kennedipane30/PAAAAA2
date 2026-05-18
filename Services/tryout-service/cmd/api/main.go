package main

import (
	"tryout-service/config"
	"tryout-service/internal/delivery/http"
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
	"tryout-service/internal/usecase"
	"os"

	"github.com/gin-gonic/gin"
)

func main() {
	db := config.InitDB()
	
	// MIGRASI LENGKAP: Agar tabel persis seperti di screenshot pgAdmin Anda
	db.AutoMigrate(
		&models.Tryout{}, 
		&models.Question{}, 
		&models.TryoutResult{}, 
		&models.TryoutSubmission{},
	)

	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	r := gin.Default()
	api := r.Group("/api")
	{
		api.GET("/tryouts", handler.GetTryouts)
		// Tambahkan route lain sesuai kebutuhan Anda
	}

	port := os.Getenv("PORT")
	if port == "" { port = "9002" }
	r.Run(":" + port)
}