package repository

import (
	"practice-service/internal/models"

	"gorm.io/gorm"
)

type PracticeRepository interface {
	BulkInsert(questions []models.PracticeQuestion) error
	GetByClass(classID uint) ([]models.PracticeQuestion, error)
	GetByWeek(classID uint, subject string, week int) ([]models.PracticeQuestion, error)
	DeleteByWeek(classID uint, subject string, week int) error
	
	// ✨ FUNGSI BARU UNTUK LOGIC 2X PERCOBAAN
	GetQuestionByID(id uint) (*models.PracticeQuestion, error)
	GetUserAttempt(userID uint, questionID uint) (*models.PracticeAttempt, error)
	SaveUserAttempt(attempt *models.PracticeAttempt) error
}

type practiceRepo struct {
	db *gorm.DB
}

func NewPracticeRepository(db *gorm.DB) PracticeRepository {
	return &practiceRepo{db}
}

func (r *practiceRepo) BulkInsert(questions []models.PracticeQuestion) error {
	return r.db.Save(&questions).Error
}

func (r *practiceRepo) GetByClass(classID uint) ([]models.PracticeQuestion, error) {
	var results []models.PracticeQuestion
	err := r.db.Where("class_id = ?", classID).Find(&results).Error
	return results, err
}

func (r *practiceRepo) GetByWeek(classID uint, subject string, week int) ([]models.PracticeQuestion, error) {
	var results []models.PracticeQuestion
	err := r.db.Where("class_id = ? AND subject = ? AND week = ?", classID, subject, week).Find(&results).Error
	return results, err
}

func (r *practiceRepo) DeleteByWeek(classID uint, subject string, week int) error {
	return r.db.Where("class_id = ? AND subject = ? AND week = ?", classID, subject, week).Delete(&models.PracticeQuestion{}).Error
}

// ✨ IMPLEMENTASI FUNGSI BARU
func (r *practiceRepo) GetQuestionByID(id uint) (*models.PracticeQuestion, error) {
	var q models.PracticeQuestion
	err := r.db.First(&q, id).Error
	return &q, err
}

func (r *practiceRepo) GetUserAttempt(userID uint, questionID uint) (*models.PracticeAttempt, error) {
	var attempt models.PracticeAttempt
	err := r.db.Where("user_id = ? AND practice_question_id = ?", userID, questionID).First(&attempt).Error
	return &attempt, err
}

func (r *practiceRepo) SaveUserAttempt(attempt *models.PracticeAttempt) error {
	return r.db.Save(attempt).Error
}