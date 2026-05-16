package repository

import (
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(tryout models.Tryout, questions []models.Question) error
	SyncSubmissions(subs []models.TryoutSubmission) error // ✨ 1. Tambahkan ini di interface
}

type tryoutRepo struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepo{db}
}

func (r *tryoutRepo) SyncFullPackage(t models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(&t).Error; err != nil { return err }
		tx.Where("tryout_id = ?", t.TryoutID).Delete(&models.Question{})
		if len(qs) > 0 {
			if err := tx.Create(&qs).Error; err != nil { return err }
		}
		return nil
	})
}

// ✨ 2. Tambahkan implementasi fungsi ini di paling bawah
func (r *tryoutRepo) SyncSubmissions(subs []models.TryoutSubmission) error {
	return r.db.Save(&subs).Error
}