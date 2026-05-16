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
	// 1. Inisialisasi Database
	db := config.InitDB()
	
	// 2. Migrasi tabel otomatis
	db.AutoMigrate(&models.PracticeQuestion{}) 

	// 3. Dependency Injection
	repo := repository.NewPracticeRepository(db)
	uc := usecase.NewPracticeUsecase(repo)
	handler := http.NewPracticeHandler(uc)

	// 4. Setup Router
	r := gin.Default()

	// 5. API Group (Menyesuaikan GO_PRACTICE_URL=.../api di .env Laravel)
	api := r.Group("/api")
	{
		api.POST("/practice/sync", handler.Sync)
		api.DELETE("/practice/:class_id/:week", handler.DeleteWeek)
	}

	// 6. Ambil Port dari .env (Sesuai Laravel: 9003)
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9003" 
	}
	
	r.Run(":" + port)
}