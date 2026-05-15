// practice-service/main.go
package main
import (
    "github.com/gin-gonic/gin"
    "github.com/joho/godotenv"
)

func main() {
    godotenv.Load()
    InitDB() // Fungsi koneksi ke specta_practice_db

    r := gin.Default()
    r.Use(CORSMiddleware())
    
    api := r.Group("/api") {
        // Flutter: getPracticeData()
        api.POST("/practice/questions", GetPracticeByClass) 
    }
    r.Run(":9003")
}