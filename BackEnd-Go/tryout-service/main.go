package main

import (
	"fmt"
	"log"
	"os"
	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
)

var DB *gorm.DB

func main() {
	godotenv.Load()
	dsn := fmt.Sprintf("host=%s user=%s password=%s dbname=%s port=%s sslmode=disable",
		os.Getenv("DB_HOST"), os.Getenv("DB_USER"), os.Getenv("DB_PASSWORD"), os.Getenv("DB_NAME"), os.Getenv("DB_PORT"))
	
	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})
	if err != nil { log.Fatal(err) }
	db.AutoMigrate(&Tryout{}, &Question{}, &TryoutResult{})
	DB = db

	r := gin.Default()
	api := r.Group("/api")
	{
		api.POST("/tryouts/sync", SyncTryoutData)
		api.Use(AuthMiddleware())
		api.GET("/tryouts", GetTryouts)
		api.GET("/tryouts/:id/questions", GetQuestions)
		api.POST("/tryouts/submit", SubmitResult)
	}
	r.Run(":" + os.Getenv("PORT"))
}