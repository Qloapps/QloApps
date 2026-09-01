"""Integration tests for FastAPI endpoints."""

import io
import json
import os
import pytest
from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)
FIXTURES_DIR = os.path.join(os.path.dirname(__file__), "fixtures")

def test_healthz_endpoint():
    response = client.get("/healthz")
    assert response.status_code == 200
    assert response.json() == {"status": "UP"}

def test_post_valid_sharp_image(caplog):
    img_path = os.path.join(FIXTURES_DIR, "room_sharp.jpg")
    with open(img_path, "rb") as f:
        files = {"file": ("room_sharp.jpg", f, "image/jpeg")}
        data = {
            "room_id": "room-101",
            "inspection_id": "INSP-2026-001",
        }
        headers = {"X-Correlation-ID": "test-corr-123"}
        response = client.post("/v1/visual-inspections", files=files, data=data, headers=headers)

    assert response.status_code == 200
    body = response.json()
    assert body["correlation_id"] == "test-corr-123"
    assert body["room_id"] == "room-101"
    assert body["inspection_id"] == "INSP-2026-001"
    assert body["assessment"] == "EVIDENCE_VALID"
    assert body["warnings"] == []
    assert body["metrics"]["width"] == 1920
    assert body["metrics"]["height"] == 1080
    assert body["metrics"]["luminance_status"] == "OPTIMAL"
    assert body["metrics"]["sharpness_status"] == "SHARP"

    # Check structured log record in logger
    matching_records = [r for r in caplog.records if r.name == "visual_inspection"]
    assert len(matching_records) > 0
    log_json = json.loads(matching_records[-1].message)
    assert log_json["event"] == "IMAGE_INSPECTED"
    assert log_json["correlation_id"] == "test-corr-123"
    assert log_json["assessment"] == "EVIDENCE_VALID"

def test_post_dark_image():
    img_path = os.path.join(FIXTURES_DIR, "room_dark.jpg")
    with open(img_path, "rb") as f:
        files = {"file": ("room_dark.jpg", f, "image/jpeg")}
        data = {
            "room_id": "room-102",
            "inspection_id": "INSP-2026-002",
        }
        response = client.post("/v1/visual-inspections", files=files, data=data)

    assert response.status_code == 200
    body = response.json()
    assert body["assessment"] == "EVIDENCE_REQUIRES_RETAKE"
    assert "UNDEREXPOSED" in body["warnings"]
    assert body["metrics"]["luminance_status"] == "UNDEREXPOSED"

def test_post_corrupt_text_file_as_image():
    corrupt_bytes = b"This is plain text and not a JPEG or PNG image binary."
    files = {"file": ("fake_image.jpg", io.BytesIO(corrupt_bytes), "image/jpeg")}
    data = {
        "room_id": "room-103",
        "inspection_id": "INSP-2026-003",
    }
    response = client.post("/v1/visual-inspections", files=files, data=data)

    assert response.status_code == 400
    assert response.headers.get("content-type") == "application/problem+json"
    body = response.json()
    assert body["status"] == 400
    assert body["title"] == "Arquivo de Imagem Inválido"
    assert body["instance"] == "/v1/visual-inspections"

def test_post_empty_file():
    files = {"file": ("empty.jpg", io.BytesIO(b""), "image/jpeg")}
    data = {
        "room_id": "room-104",
        "inspection_id": "INSP-2026-004",
    }
    response = client.post("/v1/visual-inspections", files=files, data=data)

    assert response.status_code == 400
    body = response.json()
    assert body["title"] == "Arquivo Vazio"
