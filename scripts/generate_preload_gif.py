"""Generate a professional 3D spinning preload GIF from logo_horizon.svg."""

from __future__ import annotations

import math
import subprocess
import sys
from pathlib import Path

import numpy as np
from PIL import Image, ImageDraw, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
SVG_PATH = ROOT / "public" / "images" / "logo_horizon.svg"
RENDER_PATH = ROOT / "public" / "images" / "logo_horizon_render.png"
OUTPUT_PATH = ROOT / "public" / "images" / "logo_horizon_preload.gif"

CANVAS = 360
FRAMES = 72
DURATION_MS = 38
FOCAL = 920.0
CAMERA_Z = 2.8
PLANE_SCALE = 1.35


def render_svg() -> Image.Image:
    if not SVG_PATH.exists():
        raise FileNotFoundError(f"SVG not found: {SVG_PATH}")

    cmd = (
        f'npx --yes @resvg/resvg-js-cli --fit-width 900 '
        f'"{SVG_PATH}" "{RENDER_PATH}"'
    )
    subprocess.run(cmd, cwd=ROOT, shell=True, check=False)

    if not RENDER_PATH.exists():
        raise RuntimeError("Failed to render SVG with resvg-js")

    return Image.open(RENDER_PATH).convert("RGBA")


def crop_logo(image: Image.Image) -> Image.Image:
    alpha = np.array(image.split()[3])
    coords = np.argwhere(alpha > 12)
    if coords.size == 0:
        return image

    y0, x0 = coords.min(axis=0)
    y1, x1 = coords.max(axis=0) + 1
    return image.crop((int(x0), int(y0), int(x1), int(y1)))


def add_shadow(frame: Image.Image, angle: float) -> Image.Image:
    shadow_layer = Image.new("RGBA", frame.size, (0, 0, 0, 0))
    draw = ImageDraw.Draw(shadow_layer)

    depth = abs(math.cos(angle))
    offset_x = int(math.sin(angle) * 26)
    width = int(120 + depth * 70)
    height = int(16 + depth * 8)
    left = (CANVAS - width) // 2 + offset_x
    top = int(CANVAS * 0.72)

    draw.ellipse(
        (left, top, left + width, top + height),
        fill=(4, 120, 52, int(45 + depth * 55)),
    )
    shadow_layer = shadow_layer.filter(ImageFilter.GaussianBlur(8))
    return Image.alpha_composite(shadow_layer, frame)


def add_glow(frame: Image.Image) -> Image.Image:
    glow = frame.copy().filter(ImageFilter.GaussianBlur(12))
    glow_arr = np.array(glow, dtype=np.float32)
    glow_arr[:, :, 3] *= 0.28
    glow = Image.fromarray(glow_arr.astype(np.uint8), mode="RGBA")
    return Image.alpha_composite(glow, frame)


def project_frame(rgba: np.ndarray, width: int, height: int, angle: float) -> Image.Image:
    canvas = np.zeros((CANVAS, CANVAS, 4), dtype=np.float32)

    cos_a = math.cos(angle)
    sin_a = math.sin(angle)

    light = np.array([0.25, 0.45, 1.0], dtype=np.float32)
    light /= np.linalg.norm(light)
    normal = np.array([sin_a, 0.0, cos_a], dtype=np.float32)
    diffuse = max(0.15, float(np.dot(normal, light)))
    lighting = 0.42 + diffuse * 0.58
    rim = abs(sin_a) ** 1.6 * 0.22
    facing = max(0.0, cos_a)

    cx = CANVAS / 2.0
    cy = CANVAS / 2.0
    half_w = (width * PLANE_SCALE) / 2.0
    half_h = (height * PLANE_SCALE) / 2.0

    ys, xs = np.nonzero(rgba[:, :, 3] > 12)
    if xs.size == 0:
        return Image.new("RGBA", (CANVAS, CANVAS), (0, 0, 0, 0))

    u = (xs.astype(np.float32) / max(width - 1, 1)) * 2.0 - 1.0
    v = (ys.astype(np.float32) / max(height - 1, 1)) * 2.0 - 1.0

    x3d = u * half_w
    y3d = -v * half_h
    rx = x3d * cos_a
    ry = y3d
    rz = -x3d * sin_a

    depth = np.clip(CAMERA_Z - rz, 0.35, None)
    sx = np.rint((FOCAL * rx / depth) + cx).astype(np.int32)
    sy = np.rint((FOCAL * ry / depth) + cy).astype(np.int32)

    colors = rgba[ys, xs].copy()
    colors[:, :3] *= lighting
    colors[:, 0] += rim * 18 + facing * 12
    colors[:, 1] += rim * 70 + facing * 12
    colors[:, 2] += rim * 28 + facing * 12
    colors[:, 3] *= (0.72 + facing * 0.28)
    colors = np.clip(colors, 0, 255)

    valid = (sx >= 0) & (sx < CANVAS) & (sy >= 0) & (sy < CANVAS)
    sx = sx[valid]
    sy = sy[valid]
    colors = colors[valid]
    rz = rz[valid]

    order = np.argsort(rz)
    sx = sx[order]
    sy = sy[order]
    colors = colors[order]

    flat = sy * CANVAS + sx
    _, unique_idx = np.unique(flat[::-1], return_index=True)
    unique_idx = len(flat) - 1 - unique_idx

    sx = sx[unique_idx]
    sy = sy[unique_idx]
    colors = colors[unique_idx]

    alpha = colors[:, 3] / 255.0
    for channel in range(3):
        canvas[sy, sx, channel] = colors[:, channel]
    canvas[sy, sx, 3] = colors[:, 3]

    frame = Image.fromarray(canvas.astype(np.uint8), mode="RGBA")
    frame = add_shadow(frame, angle)
    return add_glow(frame)


def quantize_frames(frames: list[Image.Image]) -> list[Image.Image]:
    sample = Image.new("RGBA", (CANVAS, CANVAS * len(frames)))
    for index, frame in enumerate(frames):
        sample.paste(frame, (0, index * CANVAS))

    palette_img = sample.convert("RGB").quantize(colors=180, method=Image.Quantize.MEDIANCUT)

    quantized: list[Image.Image] = []
    for frame in frames:
        rgb = frame.convert("RGB").quantize(
            palette=palette_img,
            dither=Image.Dither.FLOYDSTEINBERG,
        )
        quantized.append(rgb)

    return quantized


def main() -> None:
    logo = crop_logo(render_svg())
    rgba = np.array(logo, dtype=np.float32)
    width, height = rgba.shape[1], rgba.shape[0]

    frames = [project_frame(rgba, width, height, (2 * math.pi * i) / FRAMES) for i in range(FRAMES)]
    frames = quantize_frames(frames)

    frames[0].save(
        OUTPUT_PATH,
        save_all=True,
        append_images=frames[1:],
        duration=DURATION_MS,
        loop=0,
        disposal=2,
        optimize=True,
    )

    print(f"Professional GIF created: {OUTPUT_PATH}")


if __name__ == "__main__":
    try:
        main()
    except Exception as error:
        print(f"Error: {error}", file=sys.stderr)
        raise