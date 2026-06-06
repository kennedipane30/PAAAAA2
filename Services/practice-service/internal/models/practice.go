package models

import "time"

type PracticeQuestion struct {
	PracticeQuestionID uint      `gorm:"primaryKey;column:practice_question_id" json:"practice_question_id"`
	ClassID            uint      `gorm:"column:class_id" json:"class_id"`
	Subject            string    `gorm:"column:subject" json:"subject"` 
	Week               int       `gorm:"column:week" json:"week"`       
	Question           string    `gorm:"column:question" json:"question"`
	OptionA            string    `gorm:"column:option_a" json:"option_a"`
	OptionB            string    `gorm:"column:option_b" json:"option_b"`
	OptionC            string    `gorm:"column:option_c" json:"option_c"`
	OptionD            string    `gorm:"column:option_d" json:"option_d"`
	CorrectAnswer      string    `gorm:"column:correct_answer" json:"correct_answer"`
	
	// ✨ MODIFIKASI: Tambah Hint untuk Kata Kunci
	Hint               string    `gorm:"column:hint" json:"hint"` 
	
	Explanation        string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt          time.Time `json:"created_at"`
	UpdatedAt          time.Time `json:"updated_at"`
}

// ✨ MODIFIKASI: Tambah Struct Model untuk mencatat riwayat user ke Database
type PracticeAttempt struct {
	ID                 uint `gorm:"primaryKey" json:"id"`
	UserID             uint `gorm:"column:user_id" json:"user_id"`
	PracticeQuestionID uint `gorm:"column:practice_question_id" json:"practice_question_id"`
	AttemptsCount      int  `gorm:"column:attempts_count" json:"attempts_count"`
	IsCorrect          bool `gorm:"column:is_correct" json:"is_correct"`
}

// ✨ MODIFIKASI: Tambah Struct untuk format balasan (Response) ke aplikasi Flutter
type SubmitAnswerResponse struct {
	IsCorrect     bool   `json:"is_correct"`
	AttemptsLeft  int    `json:"attempts_left"`
	Hint          string `json:"hint,omitempty"`           // Dikirim jika salah 1x
	Explanation   string `json:"explanation,omitempty"`    // Dikirim jika benar atau salah 2x
	CorrectAnswer string `json:"correct_answer,omitempty"` // Dikirim jika kesempatan habis
}