package repository

import (
	"strconv" 
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(t *models.Tryout, qs []models.Question) error
	SyncSubmissions(s *models.TryoutSubmission) error
	GetByClass(classID string, userID string) ([]map[string]interface{}, error) 
	GetQuestions(tryoutID string) ([]models.Question, error)
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

func (r *tryoutRepository) SyncFullPackage(t *models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(t).Error; err != nil { return err }
		tx.Where("tryout_id = ?", t.TryoutID).Delete(&models.Question{})
		if len(qs) > 0 {
			for i := range qs { qs[i].TryoutID = t.TryoutID }
			if err := tx.Create(&qs).Error; err != nil { return err }
		}
		return nil
	})
}

func (r *tryoutRepository) SyncSubmissions(s *models.TryoutSubmission) error {
	return r.db.Create(s).Error
}

func (r *tryoutRepository) GetByClass(classID string, userID string) ([]map[string]interface{}, error) {
	var tryouts []models.Tryout
	
	if err := r.db.Where("class_id = ?", classID).Find(&tryouts).Error; err != nil {
		return nil, err
	}

	var results []map[string]interface{}

	for _, t := range tryouts {
		var submission models.TryoutSubmission
		
		err := r.db.Where("tryout_id = ? AND user_id = ?", t.TryoutID, userID).First(&submission).Error

		isDone := false
		score := "-" 

		if err == nil {
			isDone = true
			score = strconv.FormatFloat(submission.Score, 'f', 0, 64) 
		}

		item := map[string]interface{}{
			"tryout_id":       t.TryoutID,
			"class_id":        t.ClassID,
			"title":           t.Title,
			"duration":        t.Duration,
			"total_questions": t.TotalQuestions,
			"is_done":         isDone, 
			"score":           score, 
		}
		results = append(results, item)
	}

	return results, nil
}

func (r *tryoutRepository) GetQuestions(tryoutID string) ([]models.Question, error) {
	var data []models.Question
	err := r.db.Where("tryout_id = ?", tryoutID).Order("question_id asc").Find(&data).Error
	return data, err
}