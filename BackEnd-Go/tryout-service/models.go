package main

import "time"

type Tryout struct {
	TryoutID  uint      `gorm:"primaryKey;column:tryout_id" json:"tryout_id"`
	ClassID   uint      `json:"class_id"`
	Title     string    `json:"title"`
	Duration  int       `json:"duration"`
	CreatedAt time.Time `json:"created_at"`
}

type Question struct {
	QuestionID    uint   `gorm:"primaryKey;column:question_id" json:"question_id"`
	TryoutID      uint   `json:"tryout_id"`
	ClassID       uint   `json:"class_id"`
	Question      string `json:"question"`
	QuestionImage string `json:"question_image"`
	OptionA       string `json:"option_a"`
	OptionB       string `json:"option_b"`
	OptionC       string `json:"option_c"`
	OptionD       string `json:"option_d"`
	CorrectAnswer string `json:"correct_answer"`
	Explanation   string `json:"explanation"`
}

type TryoutResult struct {
	ID           uint `gorm:"primaryKey" json:"id"`
	UserID       uint `json:"user_id"`
	TryoutID     uint `json:"tryout_id"`
	Score        int  `json:"score"`
	TotalCorrect int  `json:"total_correct"`
}