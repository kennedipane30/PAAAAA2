package main

type PracticeQuestion struct {
	ID            uint   `gorm:"primaryKey;column:practice_question_id" json:"id"`
	ClassID       uint   `json:"class_id"`
	Subject       string `json:"subject"`
	Week          int    `json:"week"`
	Question      string `json:"question"`
	OptionA       string `json:"option_a"`
	OptionB       string `json:"option_b"`
	OptionC       string `json:"option_c"`
	OptionD       string `json:"option_d"`
	CorrectAnswer string `json:"correct_answer"`
}