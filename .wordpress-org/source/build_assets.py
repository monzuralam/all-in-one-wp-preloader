"""
Build the WordPress.org assets ( banner, icons, screenshots ) into .wordpress-org/.

Screenshots: one per preloader design, the loading state in a large browser window,
plus a "How it hides" strip ( Loading, Hiding, Page ready ) that follows each
loader's real CSS transition in assets/css/style.css.

SVGs are rendered to PNG with headless Chrome. Set the CHROME environment variable
if Chrome is not found automatically.

Usage: python build_assets.py
"""

import math
import os
import pathlib
import shutil
import subprocess
import tempfile

SOURCE = pathlib.Path(__file__).parent
OUT = SOURCE.parent
W, H = 1280, 800
FONT = "'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif"
ACCENT = '#3858e9'
LOADER_SCALE = 1.35

STYLES = [
    {'n': 1, 'name': 'Spinning Ring', 'bg': '#1e1b4b',
     'desc': 'Your logo inside a spinning ring. Slides away to the top right.'},
    {'n': 2, 'name': 'Slide Away', 'bg': '#ffffff',
     'desc': 'Your logo on a clean background. Slides away to the right.'},
    {'n': 3, 'name': 'Three-Panel Reveal', 'bg': '#ffffff',
     'desc': 'Your logo on three panels that pull back to reveal the page.'},
    {'n': 4, 'name': 'Bouncing Dots', 'bg': '#f5f3ff',
     'desc': 'Your logo with bouncing dots. Fades out smoothly.'},
    {'n': 5, 'name': 'Progress Bar', 'bg': '#ffffff',
     'desc': 'Your logo with a progress bar. Slides up out of view.'},
    {'n': 6, 'name': 'Split Curtain', 'bg': '#111827',
     'desc': 'Your logo with an orbiting dot. Curtains open from the center.'},
    {'n': 7, 'name': 'Circle Reveal', 'bg': '#eef2ff',
     'desc': 'Your logo with soft ripples. Shrinks into a closing circle.'},
]

_uid = [0]


def uid(prefix):
    _uid[0] += 1
    return '%s-%d' % (prefix, _uid[0])


def f(v):
    return ('%.2f' % v).rstrip('0').rstrip('.')


# --------------------------------------------------------------
# Shapes
# --------------------------------------------------------------
def logo(cx, cy, size, opacity=1):
    r = size * 0.26
    return (
        '<g opacity="%s">'
        '<rect x="%s" y="%s" width="%s" height="%s" rx="%s" fill="url(#logo-grad)"/>'
        '<circle cx="%s" cy="%s" r="%s" fill="#fff"/>'
        '</g>'
    ) % (f(opacity), f(cx - size / 2), f(cy - size / 2), f(size), f(size), f(r),
         f(cx), f(cy), f(size * 0.17))


def ring(cx, cy, r, width, color, base_opacity):
    circumference = 2 * math.pi * r
    return (
        '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="%s" stroke-opacity="%s" stroke-width="%s"/>'
        '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="%s" stroke-width="%s" stroke-linecap="round" '
        'stroke-dasharray="%s %s" transform="rotate(-90 %s %s)"/>'
    ) % (f(cx), f(cy), f(r), color, f(base_opacity), f(width),
         f(cx), f(cy), f(r), color, f(width), f(circumference * 0.25), f(circumference), f(cx), f(cy))


def rect(x, y, w, h, fill, extra=''):
    return '<rect x="%s" y="%s" width="%s" height="%s" fill="%s" %s/>' % (f(x), f(y), f(w), f(h), fill, extra)


def edge(x, y, w, h):
    # Faint leading edge so a light panel stays visible over a light page.
    return rect(x, y, w, h, '#1f2340', 'fill-opacity="0.12"')


# --------------------------------------------------------------
# Website behind the preloader
# --------------------------------------------------------------
def page(x, y, w, h, s):
    p = [rect(x, y, w, h, '#ffffff')]
    # Nav.
    p.append(rect(x + 24 * s, y + 18 * s, 22 * s, 22 * s, 'url(#logo-grad)', 'rx="%s"' % f(6 * s)))
    p.append(rect(x + 54 * s, y + 25 * s, 78 * s, 9 * s, '#1f2340', 'rx="%s"' % f(4.5 * s)))
    for i, lw in enumerate((44, 52, 40)):
        p.append(rect(x + w - (24 + 150 - i * 52) * s, y + 26 * s, lw * s, 7 * s, '#c3c6db', 'rx="%s"' % f(3.5 * s)))
    # Hero text.
    p.append(rect(x + 24 * s, y + 80 * s, 290 * s, 22 * s, '#1f2340', 'rx="%s"' % f(6 * s)))
    p.append(rect(x + 24 * s, y + 110 * s, 210 * s, 22 * s, '#1f2340', 'rx="%s"' % f(6 * s)))
    for i, lw in enumerate((320, 290, 240)):
        p.append(rect(x + 24 * s, y + (152 + i * 18) * s, lw * s, 9 * s, '#d3d5e6', 'rx="%s"' % f(4.5 * s)))
    p.append(rect(x + 24 * s, y + 220 * s, 118 * s, 34 * s, ACCENT, 'rx="%s"' % f(17 * s)))
    # Hero image.
    ix, iy, iw, ih = x + w - 304 * s, y + 70 * s, 280 * s, 190 * s
    p.append(rect(ix, iy, iw, ih, 'url(#image-grad)', 'rx="%s"' % f(14 * s)))
    p.append('<circle cx="%s" cy="%s" r="%s" fill="#ffffff" fill-opacity="0.8"/>' % (f(ix + 210 * s), f(iy + 52 * s), f(20 * s)))
    p.append('<path d="M%s %s L%s %s L%s %s L%s %s L%s %s Z" fill="#a5b4fc" fill-opacity="0.75"/>' % (
        f(ix), f(iy + ih), f(ix + 90 * s), f(iy + 90 * s), f(ix + 150 * s), f(iy + 140 * s),
        f(ix + 200 * s), f(iy + 105 * s), f(ix + iw), f(iy + ih)))
    # Cards.
    cw = (w - 48 * s - 32 * s) / 3
    for i in range(3):
        cx = x + 24 * s + i * (cw + 16 * s)
        p.append(rect(cx, y + 292 * s, cw, 170 * s, '#f4f5fb', 'rx="%s"' % f(12 * s)))
        p.append(rect(cx + 16 * s, y + 310 * s, 34 * s, 34 * s, '#e0e7ff', 'rx="%s"' % f(9 * s)))
        p.append(rect(cx + 16 * s, y + 360 * s, cw * 0.6, 9 * s, '#1f2340', 'rx="%s"' % f(4.5 * s)))
        p.append(rect(cx + 16 * s, y + 380 * s, cw * 0.8, 7 * s, '#d3d5e6', 'rx="%s"' % f(3.5 * s)))
        p.append(rect(cx + 16 * s, y + 394 * s, cw * 0.7, 7 * s, '#d3d5e6', 'rx="%s"' % f(3.5 * s)))
    return ''.join(p)


# --------------------------------------------------------------
# Loaders ( state: loading | hiding | done )
# --------------------------------------------------------------
def loader(n, bg, x, y, w, h, s, state):
    if state == 'done':
        return ''
    cx, cy = x + w / 2, y + h / 2

    if n == 1:
        # Width shrinks to the right and moves up ( top: -100% ).
        if state == 'hiding':
            x, y, w, h = x + w * 0.5, y - h * 0.5, w * 0.5, h
            cx, cy = x + w / 2, y + h / 2
        return rect(x, y, w, h, bg) + ring(cx, cy, 50 * s, 4 * s, '#ffffff', 0.2) + logo(cx, cy, 52 * s)

    if n == 2:
        # Width shrinks to the right, image stays centered in the panel.
        line = ''
        if state == 'hiding':
            x, w = x + w * 0.55, w * 0.45
            cx = x + w / 2
            line = edge(x, y, 1.5, h)
        return rect(x, y, w, h, bg) + line + logo(cx, cy, 68 * s)

    if n == 3:
        # Three overlays shrink to the left, logo fades out.
        out = []
        for i in range(3):
            col = w / 3
            out.append(rect(x + i * col, y, col * (0.45 if state == 'hiding' else 1), h, bg))
            if state == 'hiding':
                out.append(edge(x + i * col + col * 0.45 - 1.5, y, 1.5, h))
        opacity = 0.35 if state == 'hiding' else 1
        out.append(logo(cx, cy, 60 * s, opacity))
        out.append('<text x="%s" y="%s" text-anchor="end" font-size="%s" font-weight="600" fill="#000" opacity="%s">Preloader Close</text>' % (
            f(x + w - 16 * s), f(y + h - 14 * s), f(12 * s), f(opacity)))
        return ''.join(out)

    if n == 4:
        # Fades out.
        opacity = 0.45 if state == 'hiding' else 1
        dots = ''.join(
            '<circle cx="%s" cy="%s" r="%s" fill="%s" fill-opacity="%s"/>' % (f(cx + dx * s), f(cy + 38 * s), f(r * s), ACCENT, f(op))
            for dx, r, op in ((-22, 5, 0.45), (0, 7, 1), (22, 5, 0.45)))
        return '<g opacity="%s">%s%s%s</g>' % (f(opacity), rect(x, y, w, h, bg), logo(cx, cy - 16 * s, 56 * s), dots)

    if n == 5:
        # Slides up ( translateY( -100% ) ).
        dy = -h * 0.5 if state == 'hiding' else 0
        bar = rect(x, y, w, 4 * s, '#000000', 'fill-opacity="0.06"') + rect(x + w * 0.3, y, w * 0.4, 4 * s, ACCENT)
        line = edge(x, y + h - 1.5, w, 1.5) if state == 'hiding' else ''
        return '<g transform="translate(0 %s)">%s%s%s%s</g>' % (f(dy), rect(x, y, w, h, bg), bar, line, logo(cx, cy, 56 * s))

    if n == 6:
        # Halves slide out to the sides, logo fades out.
        shift = w * 0.25 if state == 'hiding' else 0
        out = rect(x - shift, y, w / 2, h, bg) + rect(x + w / 2 + shift, y, w / 2, h, bg)
        opacity = 0.2 if state == 'hiding' else 1
        angle = math.radians(-50)
        orbit = (
            '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="#ffffff" stroke-opacity="0.15" stroke-width="%s" stroke-dasharray="%s %s"/>'
            '<circle cx="%s" cy="%s" r="%s" fill="%s"/>'
        ) % (f(cx), f(cy), f(46 * s), f(1.5 * s), f(4 * s), f(6 * s),
             f(cx + 46 * s * math.cos(angle)), f(cy + 46 * s * math.sin(angle)), f(6 * s), ACCENT)
        return out + '<g opacity="%s">%s%s</g>' % (f(opacity), orbit, logo(cx, cy, 52 * s))

    if n == 7:
        # Shrinks into a circle ( clip-path ).
        ripples = ''.join(
            '<circle cx="%s" cy="%s" r="%s" fill="none" stroke="%s" stroke-opacity="%s" stroke-width="%s"/>' % (
                f(cx), f(cy), f(r * s), ACCENT, f(op), f(2 * s))
            for r, op in ((48, 0.55), (72, 0.25)))
        body = rect(x, y, w, h, bg) + ripples + logo(cx, cy, 52 * s)
        if state == 'hiding':
            clip = uid('circle')
            return '<clipPath id="%s"><circle cx="%s" cy="%s" r="%s"/></clipPath><g clip-path="url(#%s)">%s</g>' % (
                clip, f(cx), f(cy), f(max(w, h) * 0.3), clip, body)
        return body

    return ''


# --------------------------------------------------------------
# Browser window
# --------------------------------------------------------------
def browser(style, x, y, w, h, s, state, label=None):
    bar = 34 * s
    frame, content = uid('frame'), uid('content')
    cy = y + bar
    ch = h - bar
    out = [
        '<clipPath id="%s"><rect x="%s" y="%s" width="%s" height="%s" rx="%s"/></clipPath>' % (frame, f(x), f(y), f(w), f(h), f(12 * s)),
        '<clipPath id="%s"><rect x="%s" y="%s" width="%s" height="%s"/></clipPath>' % (content, f(x), f(cy), f(w), f(ch)),
        '<rect x="%s" y="%s" width="%s" height="%s" rx="%s" fill="#fff" filter="url(#shadow)"/>' % (f(x), f(y), f(w), f(h), f(12 * s)),
        '<g clip-path="url(#%s)">' % frame,
        rect(x, y, w, bar, '#f2f3f7'),
    ]
    for i, color in enumerate(('#ff5f57', '#febc2e', '#28c840')):
        out.append('<circle cx="%s" cy="%s" r="%s" fill="%s"/>' % (f(x + (18 + i * 16) * s), f(y + bar / 2), f(5 * s), color))
    out.append(rect(x + 72 * s, y + 8 * s, w - 90 * s, bar - 16 * s, '#ffffff', 'rx="%s"' % f((bar - 16 * s) / 2)))
    if s >= 1:
        out.append('<text x="%s" y="%s" font-size="12" fill="#757575">yoursite.com</text>' % (f(x + 86 * s), f(y + bar / 2 + 4)))
    # Loader elements drawn a bit larger than true scale so they read well in the illustration.
    out.append('<g clip-path="url(#%s)">%s%s</g>' % (content, page(x, cy, w, ch, s), loader(style['n'], style['bg'], x, cy, w, ch, s * LOADER_SCALE, state)))
    out.append('</g>')
    if label:
        lw = 22 + len(label) * 7.4
        out.append(rect(x + 12, y + h - 40, lw, 28, 'url(#logo-grad)', 'rx="14"'))
        out.append('<text x="%s" y="%s" font-size="13" font-weight="600" fill="#fff">%s</text>' % (f(x + 23), f(y + h - 21), label))
    return ''.join(out)


# --------------------------------------------------------------
# Screenshot
# --------------------------------------------------------------
def screenshot(style):
    steps = (('loading', '1  Loading'), ('hiding', '2  Hiding'), ('done', '3  Page ready'))
    minis = []
    for i, (state, label) in enumerate(steps):
        y = 200 + i * 184
        minis.append(browser(style, 840, y, 360, 152, 0.5, state, label))
        if i < 2:
            minis.append('<path d="M1012 %s l8 8 l8 -8" fill="none" stroke="#fff" stroke-opacity="0.75" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>' % f(y + 164))

    return '''<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}" font-family="{FONT}">
	<defs>
		<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
			<stop offset="0" stop-color="#3858e9"/>
			<stop offset="0.55" stop-color="#6c3fd1"/>
			<stop offset="1" stop-color="#9b51e0"/>
		</linearGradient>
		<linearGradient id="logo-grad" x1="0" y1="0" x2="1" y2="1">
			<stop offset="0" stop-color="#3858e9"/>
			<stop offset="1" stop-color="#9b51e0"/>
		</linearGradient>
		<linearGradient id="image-grad" x1="0" y1="0" x2="1" y2="1">
			<stop offset="0" stop-color="#e0e7ff"/>
			<stop offset="1" stop-color="#f3e8ff"/>
		</linearGradient>
		<filter id="shadow" x="-10%" y="-10%" width="120%" height="130%">
			<feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#1b1464" flood-opacity="0.35"/>
		</filter>
	</defs>
	<rect width="{W}" height="{H}" fill="url(#bg)"/>
	<circle cx="1180" cy="-40" r="220" fill="#fff" fill-opacity="0.06"/>
	<circle cx="60" cy="820" r="180" fill="#fff" fill-opacity="0.05"/>

	<rect x="80" y="52" width="104" height="30" rx="15" fill="#fff" fill-opacity="0.18"/>
	<text x="132" y="72" text-anchor="middle" font-size="13" font-weight="700" letter-spacing="1.5" fill="#fff">LOADER {n}</text>
	<text x="80" y="134" font-size="42" font-weight="700" letter-spacing="-0.5" fill="#fff">{name}</text>
	<text x="82" y="172" font-size="18" fill="#fff" fill-opacity="0.88">{desc}</text>
	<text x="840" y="182" font-size="13" font-weight="700" letter-spacing="1.5" fill="#fff" fill-opacity="0.8">HOW IT HIDES</text>

	{main}
	{minis}
</svg>
'''.format(W=W, H=H, FONT=FONT, n=style['n'], name=style['name'], desc=style['desc'],
           main=browser(style, 80, 200, 720, 520, 1, 'loading'), minis=''.join(minis))


# --------------------------------------------------------------
# Render
# --------------------------------------------------------------
def find_chrome():
    candidates = [
        os.environ.get('CHROME'),
        r'C:\Program Files\Google\Chrome\Application\chrome.exe',
        r'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        shutil.which('google-chrome'),
        shutil.which('chromium'),
    ]
    for path in candidates:
        if path and os.path.exists(path):
            return path
    raise SystemExit('Chrome not found. Set the CHROME environment variable.')


def render(chrome, svg_path, png_path, width, height, scale):
    html = (
        '<!doctype html><html><head><meta charset="utf-8"><style>'
        'html,body{margin:0;overflow:hidden;background:transparent}'
        'svg{display:block;width:%dpx;height:%dpx}'
        '</style></head><body>%s</body></html>'
    ) % (width, height, svg_path.read_text(encoding='utf-8'))
    with tempfile.TemporaryDirectory() as tmp:
        page = pathlib.Path(tmp) / 'render.html'
        page.write_text(html, encoding='utf-8')
        subprocess.run([
            chrome, '--headless=new', '--disable-gpu', '--hide-scrollbars',
            '--default-background-color=00000000',
            '--force-device-scale-factor=%s' % scale,
            '--window-size=%d,%d' % (width, height),
            '--screenshot=%s' % png_path,
            page.as_uri(),
        ], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    print('rendered', png_path.name)


if __name__ == '__main__':
    for style in STYLES:
        path = SOURCE / ('screenshot-%d.svg' % style['n'])
        path.write_text(screenshot(style), encoding='utf-8')

    chrome = find_chrome()
    render(chrome, SOURCE / 'banner.svg', OUT / 'banner-772x250.png', 772, 250, 1)
    render(chrome, SOURCE / 'banner.svg', OUT / 'banner-1544x500.png', 772, 250, 2)
    render(chrome, OUT / 'icon.svg', OUT / 'icon-128x128.png', 128, 128, 1)
    render(chrome, OUT / 'icon.svg', OUT / 'icon-256x256.png', 128, 128, 2)
    for style in STYLES:
        n = style['n']
        render(chrome, SOURCE / ('screenshot-%d.svg' % n), OUT / ('screenshot-%d.png' % n), W, H, 1)
