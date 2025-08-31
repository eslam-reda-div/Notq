# Notq Monorepo (Laravel + Python)

This adds a single, practical README that documents the available endpoints and how to run both the Laravel app and the Python FastAPI/Streamlit services on Windows.

## Contents

- Project layout
- How to run: Laravel
- How to run: Python API and Streamlit
- Endpoints: Laravel (API + Admin)
- Endpoints: Python FastAPI
- Makefile tasks (Python)

---

## Project layout

- `laravel/` — Laravel 11 backend with Filament Admin Panel, Sanctum, and Swagger.
- `python/` — FastAPI service (speech tools + plan generator) and Streamlit UI.

---

## How to run: Laravel

Prerequisites: PHP 8.2+, Composer, a database (MySQL/PostgreSQL/SQLite), Node.js (only if you plan to build assets).

1. Install dependencies and prepare env

```powershell
cd laravel
copy .env.example .env
composer install
php artisan key:generate
```

2. Configure `.env`

- Set database connection (DB\_\*) and `APP_URL` (e.g., http://localhost:8000).
- Configure mail if you want to test password reset emails.

3. Initialize the project (migrate fresh + seed + caches + swagger)

```powershell
php artisan project:init
```

Notes:

- This command resets the DB (migrate:fresh) and seeds default data.
- It also runs `l5-swagger:generate` so API docs are available if enabled.

4. Optional but recommended

```powershell
php artisan storage:link
```

5. Run the server

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

Admin Panel

- URL: http://localhost:8000/admin
- Default admin (from seeders):
  - Email: admin@admin.com
  - Password: password

API Docs (Swagger)

- If L5-Swagger is enabled, open: http://localhost:8000/api/documentation

---

## How to run: Python API and Streamlit

Prerequisites: Python 3.12.

1. Create venv and install

```powershell
cd python
python -m venv venv
venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

2. Environment variables

Set these in your shell or a `.env` in `python/`:

- `AZURE_SPEECH_KEY_TTS`, `AZURE_SPEECH_REGION_TTS` — for Text-to-Speech
- `AZURE_SPEECH_KEY_LEVEL_MEASUREMENT`, `AZURE_SPEECH_REGION_LEVEL_MEASUREMENT` — for level measurement endpoints
- `GOOGLE_API_KEY` — for the plan generator (Gemini)

3. Run the API (FastAPI via uvicorn in `main.py`)

```powershell
python main.py
```

- FastAPI base URL: http://localhost:8000
- Generated audio files are served under: http://localhost:8000/public/

4. Run the Streamlit UI (optional)

```powershell
streamlit run streamlit.py
```

You can point the UI to the API at http://localhost:8000.

---

## Endpoints: Laravel (API + Admin)

Routing entry: `routes/api.php` loads everything under `routes/api/**` with version prefixes. Available v1 customer auth endpoints (`routes/api/v1/customer/auth.php`):

- POST `/api/v1/customer/auth/register`

  - JSON body: `{ name: string, email: string, password: string }`
  - Returns Sanctum token and customer object.

- POST `/api/v1/customer/auth/login`

  - JSON body: `{ email: string, password: string }`
  - Returns Sanctum token and customer object.

- POST `/api/v1/customer/auth/forgot`

  - JSON body: `{ email: string }`
  - Sends password reset link (uses customers broker).

- POST `/api/v1/customer/auth/logout`
  - Requires `Authorization: Bearer <token>` (Sanctum).

Web routes (`routes/web.php`):

- `GET /` redirects to `/admin`.
- `GET /customer/auth/password/reset/{token}` renders the Livewire reset form.

Filament Admin Panel

- Provider: `App\Providers\Filament\AdminPanelProvider`
- Path: `/admin`
- Guard: `admin` (session)
- Login and password reset enabled for admins
- Seeded admin credentials: `admin@admin.com` / `password`

---

## Endpoints: Python FastAPI

Base URL: `http://localhost:8000`

- GET `/health`

  - Quick health check. Returns `{ "status": "API is running" }`.

- POST `/level_measurement`

  - multipart/form-data:
    - `audio_file`: file (wav/mp3/…)
    - `reference_text`: string
    - `language`: string (default `en-US`)
  - Returns pronunciation/fluency analytics JSON.

- POST `/word_level_measurement`

  - Same fields as `/level_measurement`.
  - Returns word-level metrics JSON.

- POST `/text_to_speach`

  - Form fields (application/x-www-form-urlencoded):
    - `text` (required)
    - `voice_name` (required) e.g., `en-US-JennyNeural`, `ar-EG-SalmaNeural`
    - `language` (default `en-US`)
  - Writes WAV under `python/public/` and returns `download_url`.

- POST `/generate_plan`
  - Form fields (application/x-www-form-urlencoded):
    - `system_prompt` (required)
    - `context` (required)
    - `objective` (required)
    - `constraints` (optional, comma-separated)
    - `steps_hint` (optional, integer)
  - Returns a structured plan JSON.

---

## Makefile tasks (Python)

The `python/Makefile` defines two helpers:

- `main` — runs the API

  - Equivalent to: `python main.py`

- `stl` — runs the Streamlit UI
  - Equivalent to: `streamlit run streamlit.py`

On Windows, `make` may not be installed by default. You can:

- Install make (e.g., via Chocolatey), then run from `python/`:
  - `make main` or `make stl`
- Or just run the equivalent PowerShell commands above.

---

## Notes & tips

- Laravel init: always configure `.env` first, then run `php artisan project:init`.
- The init command resets the DB; don’t use it on production data.
- If admin login fails, ensure seeders ran (command above) and that your DB connection is correct.
- FastAPI and Streamlit can run on different ports if needed; adjust URLs accordingly.
