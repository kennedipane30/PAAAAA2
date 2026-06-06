package repository

import (
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(t *models.Tryout, qs []models.Question) error
	SyncSubmissions(s *models.TryoutSubmission) error
	GetByClass(classID string, userID string) ([]map[string]interface{}, error) // Diubah return valuenya
	GetQuestions(tryoutID string) ([]models.Question, error)
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

// 1. Simpan Paket TO lengkap
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

// 2. SIMPAN RIWAYAT
func (r *tryoutRepository) SyncSubmissions(s *models.TryoutSubmission) error {
	return r.db.Create(s).Error
}

// 3. ✨ PERBAIKAN: Ambil Daftar TO + Status "is_done"
func (r *tryoutRepository) GetByClass(classID string, userID string) ([]map[string]interface{}, error) {
	var tryouts []models.Tryout
	// Ambil daftar tryout
	if err := r.db.Where("class_id = ?", classID).Find(&tryouts).Error; err != nil {
		return nil, err
	}

	var results []map[string]interface{}

	// Looping tiap tryout, cek apakah userID ini sudah pernah submit
	for _, t := range tryouts {
		var count int64
		// Cek di tabel submission
		r.db.Model(&models.TryoutSubmission{}).
			Where("tryout_id = ? AND user_id = ?", t.TryoutID, userID).
			Count(&count)

		isDone := false
		if count > 0 {
			isDone = true
		}

		// Bungkus ulang data menjadi map JSON
		item := map[string]interface{}{
			"tryout_id":       t.TryoutID,
			"class_id":        t.ClassID,
			"title":           t.Title,
			"duration":        t.Duration,
			"total_questions": t.TotalQuestions,
			"is_done":         isDone, // 🔥 FLAG PENTING UNTUK FLUTTER
		}
		results = append(results, item)
	}

	return results, nil
}

// 4. Ambil Daftar Soal
func (r *tryoutRepository) GetQuestions(tryoutID string) ([]models.Question, error) {
	var data []models.Question
	err := r.db.Where("tryout_id = ?", tryoutID).Order("question_id asc").Find(&data).Error
	return data, err
}