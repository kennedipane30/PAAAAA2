package main

import (
	"log" // ✨ Perbaikan: Tambahkan log
	"os"
	"practice-service/config"
	"practice-service/internal/delivery/http"
	"practice-service/internal/models"
	"practice-service/internal/repository"
	"practice-service/internal/usecase"

	"github.com/gin-gonic/gin"
)

// Middleware CORS agar bisa diakses oleh Flutter
func CORSMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Writer.Header().Set("Access-Control-Allow-Origin", "*")
		c.Writer.Header().Set("Access-Control-Allow-Credentials", "true")
		c.Writer.Header().Set("Access-Control-Allow-Headers", "Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization, accept, origin, Cache-Control, X-Requested-With")
		c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS, GET, PUT, DELETE")

		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}
		c.Next()
	}
}

func main() {
	// 1. Inisialisasi Database
	db := config.InitDB()
	
	// 2. Migrasi Tabel Otomatis
	// ✨ MODIFIKASI: Tambahkan PracticeAttempt agar Golang membuat tabel riwayat di Database
	db.AutoMigrate(&models.PracticeQuestion{}, &models.PracticeAttempt{}) 

	// 3. Dependency Injection
	repo := repository.NewPracticeRepository(db)
	uc := usecase.NewPracticeUsecase(repo)
	handler := http.NewPracticeHandler(uc)

	// 4. Inisialisasi Gin
	r := gin.Default()
	r.Use(CORSMiddleware()) 

	// 5. API Routes
	api := r.Group("/api")
	{
		// SINKRON: Gunakan rute /tryouts karena Flutter memanggil ini ke Port 9003
		api.GET("/tryouts", handler.GetPractice) 
		api.GET("/practice", handler.GetPractice) // Backup rute
		api.POST("/practice/sync", handler.Sync)
		
		// ✨ MODIFIKASI: Tambahkan rute untuk Flutter mengirim/mengecek jawaban
		api.POST("/practice/submit", handler.SubmitAnswer) 
	}

	// 6. Penentuan Port (Gunakan os agar tidak error 'not used')
	port := os.Getenv("PORT")
	if port == "" {
		port = "9003"
	}

	// 7. Jalankan Server
	log.Printf("Practice Service is starting on port %s", port)
	err := r.Run(":" + port)
	if err != nil {
		log.Fatal("Failed to start server: ", err)
	}
}