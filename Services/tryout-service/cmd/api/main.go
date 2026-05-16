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
	// 1. Inisialisasi Database
	db := config.InitDB()
	
	// 2. Auto Migrate Tabel Tryout
	// ✨ TAMBAHKAN &models.TryoutSubmission{} di baris ini
	db.AutoMigrate(&models.Tryout{}, &models.Question{}, &models.TryoutResult{}, &models.TryoutSubmission{})

	// 3. Dependency Injection
	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	// 4. Setup Router
	r := gin.Default()

	// 5. API Group
	api := r.Group("/api")
	{
		api.POST("/tryouts/sync", handler.SyncTryout)
		api.POST("/tryouts/submissions/sync", handler.SyncSubmissions) // ✨ Endpoint Baru
	}

	// 6. Port 9002
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9002" 
	}
	
	r.Run(":" + port)
}