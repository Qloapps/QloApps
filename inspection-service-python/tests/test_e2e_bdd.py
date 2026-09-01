"""BDD and End-to-End Test Suite for Visual Inspection (QLO-FEAT-002)."""

import io
import os
import pytest
from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)
FIXTURES_DIR = os.path.join(os.path.dirname(__file__), "fixtures")


class TestVisualInspectionBDDScenarios:
    """
    Feature: Análise Objetiva de Qualidade de Imagens de Inspeção
    """

    def test_scenario_1_sharp_well_lit_image_is_approved(self):
        """
        Scenario 1: Imagem nítida e bem iluminada é aprovada com sucesso
          Given que o serviço Python está ativo
          When o supervisor envia a foto "room_sharp.jpg" para o quarto "room-101"
          Then o status HTTP da resposta deve ser 200
          And o campo "assessment" deve ser "EVIDENCE_VALID"
          And o campo "metrics.luminance_status" deve ser "OPTIMAL"
          And o campo "metrics.sharpness_status" deve ser "SHARP"
          And a lista de "warnings" deve estar vazia
        """
        sharp_path = os.path.join(FIXTURES_DIR, "room_sharp.jpg")
        with open(sharp_path, "rb") as f:
            response = client.post(
                "/v1/visual-inspections",
                files={"file": ("room_sharp.jpg", f, "image/jpeg")},
                data={"room_id": "room-101", "inspection_id": "INSP-01"},
                headers={"X-Correlation-ID": "test-e2e-sharp-01"},
            )

        assert response.status_code == 200
        payload = response.json()
        assert payload["assessment"] == "EVIDENCE_VALID"
        assert payload["metrics"]["luminance_status"] == "OPTIMAL"
        assert payload["metrics"]["sharpness_status"] == "SHARP"
        assert payload["warnings"] == []
        assert payload["metrics"]["width"] >= 800
        assert payload["metrics"]["height"] >= 600

    def test_scenario_2_dark_image_requires_retake(self):
        """
        Scenario 2: Imagem escura gera alerta de subexposição
          Given que o serviço Python está ativo
          When o supervisor envia a foto "room_dark.jpg"
          Then o status HTTP deve ser 200
          And o campo "metrics.luminance_status" deve ser "UNDEREXPOSED"
          And o array "warnings" deve conter "UNDEREXPOSED"
          And o campo "assessment" deve ser "EVIDENCE_REQUIRES_RETAKE"
        """
        dark_path = os.path.join(FIXTURES_DIR, "room_dark.jpg")
        with open(dark_path, "rb") as f:
            response = client.post(
                "/v1/visual-inspections",
                files={"file": ("room_dark.jpg", f, "image/jpeg")},
                data={"room_id": "room-102", "inspection_id": "INSP-02"},
            )

        assert response.status_code == 200
        payload = response.json()
        assert payload["metrics"]["luminance_status"] == "UNDEREXPOSED"
        assert "UNDEREXPOSED" in payload["warnings"]
        assert payload["assessment"] == "EVIDENCE_REQUIRES_RETAKE"

    def test_scenario_3_corrupt_upload_returns_rfc7807_400(self):
        """
        Scenario 3: Upload de arquivo não suportado
          Given que o serviço Python está ativo
          When um arquivo com conteúdo de texto simples é enviado como imagem
          Then o status HTTP retornado deve ser 400
          And o corpo da resposta deve conter cabeçalho Problem Details (RFC 7807)
        """
        fake_binary = b"Corrupted arbitrary data that is not a valid image file"
        response = client.post(
            "/v1/visual-inspections",
            files={"file": ("malformed.png", io.BytesIO(fake_binary), "image/png")},
            data={"room_id": "room-103", "inspection_id": "INSP-03"},
        )

        assert response.status_code == 400
        assert response.headers.get("content-type") == "application/problem+json"
        payload = response.json()
        assert payload["status"] == 400
        assert payload["title"] == "Arquivo de Imagem Inválido"
        assert payload["instance"] == "/v1/visual-inspections"

    def test_scenario_4_blurry_low_res_image_requires_retake(self):
        """
        Scenario 4: Imagem de baixa resolução e desfocada
          Given que o serviço Python está ativo
          When o supervisor envia a foto "room_blurry.jpg" (640x480)
          Then o status HTTP deve ser 200
          And "LOW_RESOLUTION" e "BLURRY_IMAGE" devem estar presentes em warnings
          And o campo "assessment" deve ser "EVIDENCE_REQUIRES_RETAKE"
        """
        blurry_path = os.path.join(FIXTURES_DIR, "room_blurry.jpg")
        with open(blurry_path, "rb") as f:
            response = client.post(
                "/v1/visual-inspections",
                files={"file": ("room_blurry.jpg", f, "image/jpeg")},
                data={"room_id": "room-104", "inspection_id": "INSP-04"},
            )

        assert response.status_code == 200
        payload = response.json()
        assert "LOW_RESOLUTION" in payload["warnings"]
        assert "BLURRY_IMAGE" in payload["warnings"]
        assert payload["metrics"]["sharpness_status"] == "BLURRY"
        assert payload["assessment"] == "EVIDENCE_REQUIRES_RETAKE"
