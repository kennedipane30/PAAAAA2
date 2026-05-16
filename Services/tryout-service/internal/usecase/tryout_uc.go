package usecase

import (
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
)

type TryoutUsecase interface {
	ProcessSync(tryout models.Tryout, questions []models.Question) error
	ProcessSubmissionsSync(subs []models.TryoutSubmission) error // ✨ 3. Tambahkan ini di interface
}

type tryoutUC struct {
	repo repository.TryoutRepository
}

func NewTryoutUsecase(repo repository.TryoutRepository) TryoutUsecase {
	return &tryoutUC{repo}
}

func (uc *tryoutUC) ProcessSync(t models.Tryout, qs []models.Question) error {
	return uc.repo.SyncFullPackage(t, qs)
}

// ✨ 4. Tambahkan implementasi fungsi ini
func (uc *tryoutUC) ProcessSubmissionsSync(subs []models.TryoutSubmission) error {
	return uc.repo.SyncSubmissions(subs)
}