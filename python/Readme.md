# notq-ai

A FastAPI service with:

- Speech utilities (level measurement, word-level measurement, Azure Text-to-Speech)
- An AI Plan Generator using Google Gemini via LangChain
- A simple Streamlit UI to exercise the endpoints

Static files (generated TTS WAVs) are persisted under `public/` and served at `/public`.

## Contents

- Setup (Python + env vars)
- Running the API and Streamlit
- Endpoints and examples
- Docker usage
- Troubleshooting

---

## 1) Setup

### Prerequisites

- Python 3.12
- An Azure Cognitive Services Speech resource (for TTS)
- A Google API key (for Gemini model)

### Environment variables

Create a `.env` file in the project root (or set env vars directly):

```
AZURE_SPEECH_KEY_TTS=your-azure-speech-key
AZURE_SPEECH_REGION_TTS=your-azure-region   # e.g., eastus
GOOGLE_API_KEY=your-google-api-key          # for Gemini via LangChain
# Optional for Streamlit default
API_URL=http://localhost:8000
```

### Install dependencies

```powershell
python -m venv venv
venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

---

## 2) Run locally

### Start the API

```powershell
python main.py
```

The API listens on `http://localhost:8000` and serves generated audio at `http://localhost:8000/public/...`.

### Start the Streamlit UI

```powershell
streamlit run streamlit.py
```

- In the sidebar, set API base URL (or it reads `API_URL` from env).
- Use the pages to call each endpoint and view results.

---

## 3) Endpoints

Base URL: `http://localhost:8000`

### GET /health

- Purpose: quick service check.
- Response:

```json
{ "status": "API is running" }
```

### POST /level_measurement

- Content-Type: multipart/form-data
- Fields:
  - `audio_file`: audio to analyze (wav/mp3/…)
  - `reference_text`: the transcript or phrase to evaluate
  - `language`: locale string (default: `en-US`)
- Returns: JSON with the measurement results.

Example (PowerShell):

```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8000/level_measurement `
  -Form @{ audio_file=Get-Item .\sample.wav; reference_text="hello world"; language="en-US" }
```

### POST /word_level_measurement

- Content-Type: multipart/form-data
- Fields: same as `/level_measurement`
- Returns: JSON with word-level details.

Example (PowerShell):

```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8000/word_level_measurement `
  -Form @{ audio_file=Get-Item .\sample.wav; reference_text="hello world"; language="en-US" }
```

### POST /text_to_speach

- Content-Type: application/x-www-form-urlencoded (form fields)
- Fields:
  - `text` (required)
  - `voice_name` (required) e.g., `en-US-AndrewMultilingualNeural`, `en-US-JennyNeural`, `ar-EG-SalmaNeural`
  - `language` (default `en-US`)
- Behavior:
  - Saves a WAV under `public/tts_<uuid>.wav`
  - Returns a direct `download_url`

Example (PowerShell):

```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8000/text_to_speach `
  -Body @{ text="Hello"; voice_name="en-US-AndrewMultilingualNeural"; language="en-US" } `
  -ContentType "application/x-www-form-urlencoded"
```

Response:

```json
{
  "success": true,
  "message": "Speech synthesized successfully.",
  "filename": "tts_<uuid>.wav",
  "download_url": "http://localhost:8000/public/tts_<uuid>.wav"
}
```

Notes:

- For very short inputs (one word), the service uses SSML and extra pauses to avoid empty WAVs.
- Files remain in `public/`; `.dockerignore` is configured to skip committing them.

### POST /generate_plan

- Content-Type: application/x-www-form-urlencoded (form fields)
- Fields:
  - `system_prompt` (required): instructions for the planner
  - `context` (required): long-form background grounding text
  - `objective` (required): what to achieve
  - `constraints` (optional): comma-separated constraints (e.g., `budget <= $10k, 2 weeks`)
  - `steps_hint` (optional, int): suggested approximate step count
- Returns: JSON with a structured `plan` adhering to:
  - `objective: str`
  - `assumptions: list[str]`
  - `constraints: list[str]`
  - `milestones: list[str]`
  - `steps: list[ { id:int, title:str, description:str, owner?:str, duration?:str, dependencies:list[int] } ]`
  - `risks: list[str]`, `mitigations: list[str]`, `metrics: list[str]`, `timeline?: str`, `notes?: str`

Example (PowerShell):

```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8000/generate_plan `
  -Body @{
    system_prompt="You are a senior planning assistant. Output JSON per schema.";
    context="We’re launching a small web app with login and profile.";
    objective="Ship MVP in 2 weeks";
    steps_hint=8;
    constraints="budget <= $10k, use Python 3.12"
  } -ContentType "application/x-www-form-urlencoded"
```

---

## 4) Streamlit testing

Run the UI and use the sidebar to switch between pages:

- Health
- Level Measurement
- Word Level Measurement
- Text to Speech
- Generate Plan (full-width fields; constraints as comma-separated)

It posts to the API base URL shown in the sidebar (reads `API_URL` by default). TTS results show a download link and inline audio playback.

---

## 5) Docker

A minimal, production-friendly image is provided.

Build:

```powershell
docker build -t notq-ai .
```

Run:

```powershell
docker run --rm -p 8000:8000 `
  -e AZURE_SPEECH_KEY_TTS=$Env:AZURE_SPEECH_KEY_TTS `
  -e AZURE_SPEECH_REGION_TTS=$Env:AZURE_SPEECH_REGION_TTS `
  -e GOOGLE_API_KEY=$Env:GOOGLE_API_KEY `
  notq-ai
```

Then open `http://localhost:8000/health` or run Streamlit separately pointing to that API URL.

---

## 6) Troubleshooting

- Missing Azure/Google keys
  - Ensure `AZURE_SPEECH_KEY_TTS`, `AZURE_SPEECH_REGION_TTS`, and `GOOGLE_API_KEY` are set.
- Short TTS inputs produce empty audio
  - The service adds SSML breaks and retries. If still empty, try a longer phrase or a different voice.
- Permissions writing to `public/`
  - The API creates `public/` automatically. Check file permissions if running in restricted environments.
- CORS/Fetch errors in UI
  - The Streamlit app fetches audio via the `download_url`. If hosted separately, ensure the URL is reachable.

---

## 7) Project structure

```
notq-ai/
  main.py                 # FastAPI app and endpoints, /public static mount
  streamlit.py            # Streamlit UI to test endpoints
  requirements.txt        # Python dependencies
  nodes/
    level_measurement.py  # Audio/text analysis (reference level)
    text_to_speech.py     # Azure TTS with robust handling for short texts
    generate_plan.py      # LangChain + Gemini plan generator
  public/                 # Generated TTS WAVs (runtime)
```

---

## 8) Notes

- Uses `langchain-google-genai` with model `gemini-2.5-flash`.
- TTS formats output as `Riff16Khz16BitMonoPcm` WAV files.
- Streamlit voice dropdown includes common example voices; pass any supported Azure voice name.
