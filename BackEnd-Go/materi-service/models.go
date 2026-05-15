package main
import "time"

type Material struct {
	MaterialID   uint      `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID      uint      `json:"class_id"`
	UserID       uint      `json:"user_id"`
	Title        string    `json:"title"`
	MaterialName string    `json:"material_name"`
	Week         int       `json:"week"`
	FilePath     string    `json:"file_path"`
	CreatedAt    time.Time `json:"created_at"`
}