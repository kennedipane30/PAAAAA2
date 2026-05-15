package main

import (
	"net/http"
	"os"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/golang-jwt/jwt/v5"
)

func AuthMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		// 1. Ambil Header Authorization
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "Token diperlukan"})
			return
		}

		// 2. Bersihkan string "Bearer "
		tokenString := strings.Replace(authHeader, "Bearer ", "", 1)
		
		// 3. Parse dan Validasi Token menggunakan Secret Key Laravel
		token, err := jwt.Parse(tokenString, func(token *jwt.Token) (interface{}, error) {
			// Mengambil JWT_SECRET dari file .env milik tryout-service
			return []byte(os.Getenv("JWT_SECRET")), nil
		})

		// 4. Cek apakah token valid dan tidak error
		if err != nil || token == nil || !token.Valid {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "Token tidak valid atau kadaluarsa"})
			return
		}

		// 5. Jika valid, ambil data User ID (sub) dari claims
		if claims, ok := token.Claims.(jwt.MapClaims); ok {
			// Simpan userID ke dalam context Gin agar bisa dipakai di handler jika perlu
			c.Set("userID", claims["sub"])
			c.Next() // Lanjut ke handler (GetTryouts/GetQuestions)
		} else {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "Gagal membaca data dari token"})
		}
	}
}