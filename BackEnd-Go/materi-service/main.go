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
	db.AutoMigrate(&Material{})
	DB = db

	r := gin.Default()
	api := r.Group("/api")
	{
		api.POST("/materials/sync", SyncMaterial)
		api.Use(AuthMiddleware())
		api.GET("/materials/:class_id", GetMaterials)
	}
	r.Run(":" + os.Getenv("PORT"))
}