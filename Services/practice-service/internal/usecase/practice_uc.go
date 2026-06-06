package usecase

import (
	"practice-service/internal/models"
	"practice-service/internal/repository"
)

type PracticeUsecase interface {
	SyncQuestions(questions []models.PracticeQuestion) error
	GetListByClass(classID uint) ([]models.PracticeQuestion, error)
	GetList(classID uint, subject string, week int) ([]models.PracticeQuestion, error)
	RemoveWeek(classID uint, subject string, week int) error
	
	// ✨ FUNGSI BARU UNTUK LOGIC 2X PERCOBAAN
	SubmitAnswer(userID uint, questionID uint, answer string) (*models.SubmitAnswerResponse, error)
}

type practiceUC struct {
	repo repository.PracticeRepository
}

func NewPracticeUsecase(repo repository.PracticeRepository) PracticeUsecase {
	return &practiceUC{repo}
}

func (uc *practiceUC) SyncQuestions(questions []models.PracticeQuestion) error {
	return uc.repo.BulkInsert(questions)
}

func (uc *practiceUC) GetListByClass(classID uint) ([]models.PracticeQuestion, error) {
	return uc.repo.GetByClass(classID)
}

func (uc *practiceUC) GetList(classID uint, subject string, week int) ([]models.PracticeQuestion, error) {
	return uc.repo.GetByWeek(classID, subject, week)
}

func (uc *practiceUC) RemoveWeek(classID uint, subject string, week int) error {
	return uc.repo.DeleteByWeek(classID, subject, week)
}

// ✨ IMPLEMENTASI FUNGSI BARU UNTUK CEK JAWABAN
func (uc *practiceUC) SubmitAnswer(userID uint, questionID uint, answer string) (*models.SubmitAnswerResponse, error) {
	// 1. Ambil soal dari DB
	question, err := uc.repo.GetQuestionByID(questionID)
	if err != nil {
		return nil, err
	}

	// 2. Cek apakah user sudah pernah mencoba
	attempt, err := uc.repo.GetUserAttempt(userID, questionID)
	if err != nil {
		// Jika error (berarti belum pernah mencoba), buat object percobaan baru
		attempt = &models.PracticeAttempt{
			UserID:             userID,
			PracticeQuestionID: questionID,
			AttemptsCount:      0,
			IsCorrect:          false,
		}
	}

	response := &models.SubmitAnswerResponse{
		IsCorrect:    attempt.IsCorrect,
		AttemptsLeft: 2 - attempt.AttemptsCount,
	}

	// 3. Cek apakah kesempatan sudah habis (>= 2) atau sudah terlanjur benar sebelumnya
	if attempt.AttemptsCount >= 2 || attempt.IsCorrect {
		response.AttemptsLeft = 0
		response.Explanation = question.Explanation
		response.CorrectAnswer = question.CorrectAnswer
		return response, nil
	}

	// 4. Proses jawaban baru
	attempt.AttemptsCount++
	response.AttemptsLeft = 2 - attempt.AttemptsCount

	isCorrect := (answer == question.CorrectAnswer)

	if isCorrect {
		attempt.IsCorrect = true
		response.IsCorrect = true
		response.Explanation = question.Explanation // Benar -> Dapat Penjelasan
	} else {
		attempt.IsCorrect = false
		response.IsCorrect = false

		if attempt.AttemptsCount == 1 {
			response.Hint = question.Hint // Salah ke-1 -> Dapat Hint
		} else if attempt.AttemptsCount >= 2 {
			response.Explanation = question.Explanation       // Salah ke-2 -> Dapat Penjelasan
			response.CorrectAnswer = question.CorrectAnswer // Dan diberi tahu jawaban aslinya
		}
	}

	// 5. Simpan/Update riwayat ke Database
	err = uc.repo.SaveUserAttempt(attempt)
	if err != nil {
		return nil, err
	}

	return response, nil
}