"""FastAPI Server for Visual Inspection Quality Evaluation."""

import io
import json
import logging
import sys
import time
import uuid
from datetime import datetime, timezone
from typing import Optional

from fastapi import FastAPI, UploadFile, File, Form, Header, Request
from fastapi.responses import JSONResponse
from PIL import Image, UnidentifiedImageError
import uvicorn

from app.analyzer import evaluate_image

# Configure standard logger to output structured JSON
logger = logging.getLogger("visual_inspection")
logger.setLevel(logging.INFO)
handler = logging.StreamHandler(sys.stdout)
handler.setFormatter(logging.Formatter("%(message)s"))
logger.handlers = [handler]

app = FastAPI(title="Visual Inspection Quality Evaluator", version="1.0.0")


def rfc7807_error_response(status_code: int, title: str, detail: str, instance: str = "/v1/visual-inspections") -> JSONResponse:
    """Format an RFC 7807 compliant Problem Details error response."""
    return JSONResponse(
        status_code=status_code,
        content={
            "type": "https://hotel.local/errors/invalid-image",
            "title": title,
            "status": status_code,
            "detail": detail,
            "instance": instance,
        },
        headers={"Content-Type": "application/problem+json"},
    )


@app.get("/healthz")
async def health_check():
    """Health check endpoint."""
    return {"status": "UP"}


@app.post("/v1/visual-inspections")
async def create_visual_inspection(
    file: UploadFile = File(...),
    room_id: str = Form(...),
    inspection_id: str = Form(...),
    x_correlation_id: Optional[str] = Header(default=None),
):
    """
    Evaluate room inspection photo quality.
    Validates image binary, computes luminance, sharpness, dimensions,
    and returns assessment verdict with structured logging.
    """
    start_time = time.perf_counter()
    correlation_id = x_correlation_id or str(uuid.uuid4())

    try:
        contents = await file.read()
        if not contents:
            return rfc7807_error_response(
                status_code=400,
                title="Arquivo Vazio",
                detail="O arquivo enviado está vazio.",
            )

        # Attempt to open and verify image integrity
        try:
            img = Image.open(io.BytesIO(contents))
            img.load()  # Force decode full image to catch corrupted data
        except (UnidentifiedImageError, OSError, ValueError) as exc:
            return rfc7807_error_response(
                status_code=400,
                title="Arquivo de Imagem Inválido",
                detail="O arquivo enviado não pôde ser decodificado como uma imagem válida.",
            )

        # Evaluate quality metrics
        analysis = evaluate_image(img)

        duration_ms = round((time.perf_counter() - start_time) * 1000, 2)

        # Structured JSON log
        log_record = {
            "timestamp": datetime.now(timezone.utc).isoformat(),
            "level": "INFO",
            "correlation_id": correlation_id,
            "event": "IMAGE_INSPECTED",
            "room_id": room_id,
            "inspection_id": inspection_id,
            "width": analysis["metrics"]["width"],
            "height": analysis["metrics"]["height"],
            "luminance": analysis["metrics"]["luminance"],
            "sharpness": analysis["metrics"]["sharpness_score"],
            "assessment": analysis["assessment"],
            "duration_ms": duration_ms,
        }
        logger.info(json.dumps(log_record))

        return {
            "correlation_id": correlation_id,
            "inspection_id": inspection_id,
            "room_id": room_id,
            "metrics": analysis["metrics"],
            "warnings": analysis["warnings"],
            "assessment": analysis["assessment"],
        }

    except Exception as e:
        return rfc7807_error_response(
            status_code=500,
            title="Erro Interno no Processamento",
            detail=f"Ocorreu um erro interno ao analisar a imagem: {str(e)}",
        )


if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8102)
