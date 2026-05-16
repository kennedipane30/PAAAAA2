package repository

import (
	"practice-service/internal/models"
	"gorm.io/gorm"
)

type PracticeRepository interface {
	BulkInsert(questions []models.PracticeQuestion) error
	GetByWeek(classID uint, subject string, week int) ([]models.PracticeQuestion, error)
	DeleteByWeek(classID uint, subject string, week int) error
}

type practiceRepo struct {
	db *gorm.DB
}

func NewPracticeRepository(db *gorm.DB) PracticeRepository {
	return &practiceRepo{db}
}

func (r *practiceRepo) BulkInsert(questions []models.PracticeQuestion) error {
	// GORM Clause 'OnConflict' memastikan jika ID sudah ada, maka akan diupdate (Upsert)
	return r.db.Save(&questions).Error
}

func (r *practiceRepo) GetByWeek(classID uint, subject string, week int) ([]models.PracticeQuestion, error) {
	var results []models.PracticeQuestion
	err := r.db.Where("class_id = ? AND subject = ? AND week = ?", classID, subject, week).Find(&results).Error
	return results, err
}

func (r *practiceRepo) DeleteByWeek(classID uint, subject string, week int) error {
	return r.db.Where("class_id = ? AND subject = ? AND week = ?", classID, subject, week).Delete(&models.PracticeQuestion{}).Error
}