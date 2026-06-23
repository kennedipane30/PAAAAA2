@extends('layouts.spekta')

@section('title', 'Tambah Pengajar')
@section('subtitle', 'Tambah akun pengajar baru Spekta Academy')

@section('content')
<div class="teacher-form-page">

    {{-- ── WELCOME CARD (Container Personal) ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Tambah Pengajar</h1>
            <p>Daftarkan akun pengajar baru agar dapat mengakses portal Spekta Academy.</p>
        </div>
        <div class="welcome-action">
            <a href="{{ route('admin.manajemen-pengajar.index') }}" class="back-btn">
                Kembali
            </a>
        </div>
    </section>

    @if($errors->any())
        <div class="form-alert error">
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── FORM CARD ── --}}
    <section class="form-card">
        <div class="card-heading">
            <div>
                <h2>Form Tambah Pengajar</h2>
                <p>Isi data pengajar dengan benar. Akun akan langsung bisa digunakan sesuai status yang dipilih.</p>
            </div>
        </div>

        <form action="{{ route('admin.manajemen-pengajar.store') }}" method="POST" class="teacher-form" id="teacherForm">
            @csrf

            <div class="input-group">
                <label>Nama Lengkap</label>
                <div class="input-wrap">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Kennedi Pane" required>
                </div>
                <small class="error-message" id="nameError">Nama hanya boleh berisi huruf dan spasi</small>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="teacher@gmail.com" required>
                </div>
            </div>

            <div class="input-group">
                <label>Nomor Telepon</label>
                <div class="input-wrap">
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="081234567890" required>
                </div>
                <small class="error-message" id="phoneError">Nomor telepon hanya boleh berisi angka</small>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required>
                </div>
                <small class="error-message" id="passwordError">Password harus mengandung minimal 1 huruf kapital dan 1 huruf kecil</small>
            </div>

            <div class="input-group">
                <label>Status Akun</label>
                <div class="input-wrap">
                    <select name="is_verified" required>
                        <option value="1" {{ old('is_verified', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_verified') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.manajemen-pengajar.index') }}" class="cancel-btn">
                    Batal
                </a>

                <button type="submit" class="submit-btn" id="submitBtn">
                    Simpan Pengajar
                </button>
            </div>
        </form>
    </section>
</div>

<style>
    .teacher-form-page {
        width: 100%;
        font-family: 'Montserrat', system-ui, sans-serif;
    }

    /* ── WELCOME CARD (Container Personal seperti gambar kedua) ── */
    .welcome-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 5px solid #14b8a6;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }

    .welcome-card::after {
        content: "";
        position: absolute;
        width: 200px;
        height: 200px;
        right: -60px;
        top: -60px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.05) 0%, rgba(20, 184, 166, 0.02) 100%);
        border-radius: 999px;
        pointer-events: none;
    }

    .welcome-text {
        position: relative;
        z-index: 1;
    }

    .welcome-text h1 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
    }

    .welcome-text p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    .welcome-action {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 1px solid #e5e7eb;
        padding-left: 24px;
        min-width: 140px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 18px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #ffffff;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: #f9fafb;
        border-color: #14b8a6;
        color: #14b8a6;
    }

    /* ── ALERT ── */
    .form-alert {
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .form-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .form-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
        font-weight: 600;
    }

    /* ── FORM CARD ── */
    .form-card {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        padding: 28px 32px;
        width: 100%;
    }

    .card-heading {
        margin-bottom: 28px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f3f4f6;
    }

    .card-heading h2 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 18px;
        font-weight: 800;
    }

    .card-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.5;
    }

    /* ── FORM ── */
    .teacher-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .input-group {
        display: flex;
        flex-direction: column;
    }

    .input-group label {
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .input-wrap {
        position: relative;
        flex: 1;
    }

    .input-wrap input,
    .input-wrap select {
        width: 100%;
        height: 46px;
        padding: 0 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        outline: none;
        color: #111827;
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
        transition: all 0.25s ease;
    }

    .input-wrap input:focus,
    .input-wrap select:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .input-wrap input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .input-wrap select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239ca3af%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 10px auto;
        padding-right: 40px;
    }

    /* Error Message */
    .error-message {
        color: #dc2626;
        font-size: 10px;
        font-weight: 600;
        display: none;
        margin-top: 6px;
    }

    .error-message.show {
        display: block;
    }

    /* ── FORM ACTIONS ── */
    .form-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .cancel-btn {
        height: 44px;
        padding: 0 22px;
        border-radius: 10px;
        background: #f3f4f6;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .cancel-btn:hover {
        background: #e5e7eb;
    }

    /* ── TOMBOL SIMPAN TEAL ── */
    .submit-btn {
        height: 44px;
        padding: 0 28px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.02em;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .submit-btn:active {
        transform: scale(0.97);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .welcome-action {
            border-left: none;
            padding-left: 0;
            min-width: unset;
            width: 100%;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .form-card {
            padding: 20px 16px;
        }

        .teacher-form {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .cancel-btn,
        .submit-btn {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }
    }
</style>

<script>
    // Ambil elemen
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const form = document.getElementById('teacherForm');

    const nameError = document.getElementById('nameError');
    const phoneError = document.getElementById('phoneError');
    const passwordError = document.getElementById('passwordError');

    // Validasi nama: hanya huruf dan spasi
    function validateName() {
        const name = nameInput.value;
        const regex = /^[A-Za-z\s]+$/;
        if (!regex.test(name) && name !== '') {
            nameError.classList.add('show');
            return false;
        } else {
            nameError.classList.remove('show');
            return true;
        }
    }

    // Validasi nomor telepon: hanya angka
    function validatePhone() {
        const phone = phoneInput.value;
        const regex = /^[0-9]+$/;
        if (!regex.test(phone) && phone !== '') {
            phoneError.classList.add('show');
            return false;
        } else {
            phoneError.classList.remove('show');
            return true;
        }
    }

    // Validasi password: minimal 1 huruf kapital dan 1 huruf kecil
    function validatePassword() {
        const password = passwordInput.value;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        if (password !== '' && (!hasUpper || !hasLower)) {
            passwordError.classList.add('show');
            return false;
        } else {
            passwordError.classList.remove('show');
            return true;
        }
    }

    // Event listener saat input berubah
    nameInput.addEventListener('input', validateName);
    phoneInput.addEventListener('input', validatePhone);
    passwordInput.addEventListener('input', validatePassword);

    // Validasi sebelum submit
    form.addEventListener('submit', function(e) {
        const isNameValid = validateName();
        const isPhoneValid = validatePhone();
        const isPasswordValid = validatePassword();

        if (!isNameValid || !isPhoneValid || !isPasswordValid) {
            e.preventDefault();
            alert('Harap periksa kembali data yang Anda masukkan:\n- Nama hanya boleh huruf dan spasi\n- Nomor telepon hanya angka\n- Password harus mengandung huruf kapital dan huruf kecil');
        }
    });
</script>
@endsection
