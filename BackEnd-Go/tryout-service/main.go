// tryout-service/main.go
package main
import (
    "github.com/gin-gonic/gin"
    "github.com/joho/godotenv"
)

func main() {
    godotenv.Load()
    InitDB() // Fungsi koneksi ke specta_tryout_db

    r := gin.Default()
    r.Use(CORSMiddleware())
    
    api := r.Group("/api") {
        // Flutter: getQuestions(), submitTryout(), getLearningReport()
        api.POST("/tryout/questions", GetQuestions)
        api.POST("/tryout/submit", SubmitResult)
        api.GET("/learning-report", GetLearningReport)
    }
    r.Run(":9002")
}