"""Fixture generator for visual inspection test scenarios."""

import os
from PIL import Image, ImageDraw, ImageFilter

FIXTURES_DIR = os.path.join(os.path.dirname(__file__), "fixtures")
os.makedirs(FIXTURES_DIR, exist_ok=True)

def generate_all_fixtures():
    # 1. Sharp & Well-lit image (1920x1080)
    sharp_img = Image.new("RGB", (1920, 1080), color=(128, 128, 128))
    draw = ImageDraw.Draw(sharp_img)
    for i in range(0, 1920, 40):
        draw.line([(i, 0), (i, 1080)], fill=(255, 255, 255), width=4)
        draw.line([(0, i % 1080), (1920, i % 1080)], fill=(0, 0, 0), width=2)
    sharp_path = os.path.join(FIXTURES_DIR, "room_sharp.jpg")
    sharp_img.save(sharp_path, "JPEG", quality=95)
    print(f"Generated: {sharp_path}")

    # 2. Dark / Underexposed image (1920x1080)
    dark_img = Image.new("RGB", (1920, 1080), color=(15, 15, 20))
    dark_path = os.path.join(FIXTURES_DIR, "room_dark.jpg")
    dark_img.save(dark_path, "JPEG", quality=90)
    print(f"Generated: {dark_path}")

    # 3. Blurry / Low-res image (640x480 with Gaussian Blur)
    # Start with a smooth low-detail gradient image and blur it heavily
    blurry_base = Image.new("RGB", (640, 480), color=(100, 110, 120))
    b_draw = ImageDraw.Draw(blurry_base)
    b_draw.rectangle([100, 100, 540, 380], fill=(130, 140, 150))
    blurry_img = blurry_base.filter(ImageFilter.GaussianBlur(radius=30))
    blurry_path = os.path.join(FIXTURES_DIR, "room_blurry.jpg")
    blurry_img.save(blurry_path, "JPEG", quality=60)
    print(f"Generated: {blurry_path}")

if __name__ == "__main__":
    generate_all_fixtures()
