"""Image Analyzer Module for Visual Quality Inspection."""

from typing import Dict, List, Any, Tuple
from PIL import Image, ImageStat, ImageFilter

# Decompression bomb safety limitation
Image.MAX_IMAGE_PIXELS = 10_000_000

LUMINANCE_UNDEREXPOSED_THRESHOLD = 40.0
LUMINANCE_OVEREXPOSED_THRESHOLD = 220.0
SHARPNESS_MIN_THRESHOLD = 50.0

MIN_WIDTH = 800
MIN_HEIGHT = 600


def validate_dimensions(img: Image.Image) -> Tuple[bool, int, int]:
    """
    Validate whether image meets minimum resolution requirements.
    Supports landscape (>= 800x600) and portrait (>= 600x800).
    """
    width, height = img.size
    is_valid_landscape = width >= MIN_WIDTH and height >= MIN_HEIGHT
    is_valid_portrait = width >= MIN_HEIGHT and height >= MIN_WIDTH
    is_valid = is_valid_landscape or is_valid_portrait
    return is_valid, width, height


def calculate_luminance(img: Image.Image) -> float:
    """
    Convert image to grayscale ('L') and calculate global average luminance [0..255].
    """
    gray_img = img.convert("L")
    stat = ImageStat.Stat(gray_img)
    return float(stat.mean[0]) if stat.mean else 0.0


def classify_luminance(luminance: float) -> str:
    """
    Classify luminance into UNDEREXPOSED, OVEREXPOSED, or OPTIMAL.
    """
    if luminance < LUMINANCE_UNDEREXPOSED_THRESHOLD:
        return "UNDEREXPOSED"
    elif luminance > LUMINANCE_OVEREXPOSED_THRESHOLD:
        return "OVEREXPOSED"
    return "OPTIMAL"


def calculate_sharpness(img: Image.Image) -> float:
    """
    Calculate edge sharpness score using gradient / FIND_EDGES filter variance.
    Crops 1px border to eliminate convolution boundary padding artifacts.
    """
    gray_img = img.convert("L")
    edges = gray_img.filter(ImageFilter.FIND_EDGES)
    width, height = edges.size
    if width > 2 and height > 2:
        edges = edges.crop((1, 1, width - 1, height - 1))
    edge_stat = ImageStat.Stat(edges)
    return float(edge_stat.var[0]) if edge_stat.var else 0.0


def classify_sharpness(score: float) -> str:
    """
    Classify sharpness score into BLURRY or SHARP.
    """
    if score < SHARPNESS_MIN_THRESHOLD:
        return "BLURRY"
    return "SHARP"


def evaluate_image(img: Image.Image) -> Dict[str, Any]:
    """
    Consolidate metrics, warnings, and final quality assessment verdict.
    """
    is_dim_valid, width, height = validate_dimensions(img)
    luminance = calculate_luminance(img)
    lum_status = classify_luminance(luminance)
    sharpness = calculate_sharpness(img)
    sharp_status = classify_sharpness(sharpness)

    warnings: List[str] = []
    if not is_dim_valid:
        warnings.append("LOW_RESOLUTION")
    if lum_status == "UNDEREXPOSED":
        warnings.append("UNDEREXPOSED")
    elif lum_status == "OVEREXPOSED":
        warnings.append("OVEREXPOSED")
    if sharp_status == "BLURRY":
        warnings.append("BLURRY_IMAGE")

    assessment = "EVIDENCE_REQUIRES_RETAKE" if warnings else "EVIDENCE_VALID"

    return {
        "metrics": {
            "width": width,
            "height": height,
            "luminance": round(luminance, 2),
            "luminance_status": lum_status,
            "sharpness_score": round(sharpness, 2),
            "sharpness_status": sharp_status,
        },
        "warnings": warnings,
        "assessment": assessment,
    }
