package main

type Material struct {
	MaterialID   uint   `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID      uint   `json:"class_id"`
	Title        string `json:"title"`
	MaterialName string `json:"material_name"`
	Week         int    `json:"week"`
	FilePath     string `json:"file_path"`
}

type PracticeQuestion struct {
	ID            uint   `gorm:"primaryKey" json:"id"`
	ClassID       uint   `json:"class_id"`
	Subject       string `json:"subject" gorm:"column:subject"`
	Week          int    `json:"week"`
	Question      string `json:"question"`
	OptionA       string `json:"option_a"`
	OptionB       string `json:"option_b"`
	OptionC       string `json:"option_c"`
	OptionD       string `json:"option_d"`
	CorrectAnswer string `json:"correct_answer"`
}