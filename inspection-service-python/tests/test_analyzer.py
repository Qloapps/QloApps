"""Unit tests for analyzer module."""

import os
from PIL import Image
from app.analyzer import (
    validate_dimensions,
    calculate_luminance,
    classify_luminance,
    calculate_sharpness,
    classify_sharpness,
    evaluate_image,
)

FIXTURES_DIR = os.path.join(os.path.dirname(__file__), "fixtures")

def test_sharp_image_evaluation():
    img_path = os.path.join(FIXTURES_DIR, "room_sharp.jpg")
    img = Image.open(img_path)
    result = evaluate_image(img)

    assert result["assessment"] == "EVIDENCE_VALID"
    assert result["warnings"] == []
    assert result["metrics"]["width"] == 1920
    assert result["metrics"]["height"] == 1080
    assert result["metrics"]["luminance_status"] == "OPTIMAL"
    assert result["metrics"]["sharpness_status"] == "SHARP"
    assert result["metrics"]["sharpness_score"] >= 50.0
    assert 40.0 <= result["metrics"]["luminance"] <= 220.0

def test_dark_image_evaluation():
    img_path = os.path.join(FIXTURES_DIR, "room_dark.jpg")
    img = Image.open(img_path)
    result = evaluate_image(img)

    assert result["assessment"] == "EVIDENCE_REQUIRES_RETAKE"
    assert "UNDEREXPOSED" in result["warnings"]
    assert result["metrics"]["luminance_status"] == "UNDEREXPOSED"
    assert result["metrics"]["luminance"] < 40.0

def test_blurry_low_resolution_image_evaluation():
    img_path = os.path.join(FIXTURES_DIR, "room_blurry.jpg")
    img = Image.open(img_path)
    result = evaluate_image(img)

    assert result["assessment"] == "EVIDENCE_REQUIRES_RETAKE"
    assert "LOW_RESOLUTION" in result["warnings"]
    assert "BLURRY_IMAGE" in result["warnings"]
    assert result["metrics"]["sharpness_status"] == "BLURRY"
    assert result["metrics"]["sharpness_score"] < 50.0

def test_portrait_dimension_support():
    portrait_img = Image.new("RGB", (650, 900), color=(100, 100, 100))
    is_valid, w, h = validate_dimensions(portrait_img)
    assert is_valid is True
    assert w == 650
    assert h == 900

def test_classify_luminance_thresholds():
    assert classify_luminance(39.9) == "UNDEREXPOSED"
    assert classify_luminance(40.0) == "OPTIMAL"
    assert classify_luminance(120.0) == "OPTIMAL"
    assert classify_luminance(220.0) == "OPTIMAL"
    assert classify_luminance(220.1) == "OVEREXPOSED"

def test_classify_sharpness_thresholds():
    assert classify_sharpness(49.9) == "BLURRY"
    assert classify_sharpness(50.0) == "SHARP"
    assert classify_sharpness(150.0) == "SHARP"
