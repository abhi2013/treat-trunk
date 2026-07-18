#!/usr/bin/env python3
"""Rebuild the /our-snacks/ page _elementor_data: 13 repetitive rows -> intro +
3-col sticker-card grid + Ethical closer + existing CTA. Reuses original widget
IDs and image settings wherever possible; only layout containers are new."""
import json, sys

SRC = "/private/tmp/claude-501/-Users-abhisingh-Documents-treat-trunk-corporate/38b6bda7-64a0-48d1-beaf-972768a9533b/scratchpad/our-snacks-elementor.json"
OUT = "/private/tmp/claude-501/-Users-abhisingh-Documents-treat-trunk-corporate/38b6bda7-64a0-48d1-beaf-972768a9533b/scratchpad/our-snacks-new.json"

with open(SRC) as f:
    data = json.loads(f.read())

# index every element by id
by_id = {}
def index(node):
    if isinstance(node, dict):
        if node.get("id"):
            by_id[node["id"]] = node
        for v in node.get("elements") or []:
            index(v)
    elif isinstance(node, list):
        for v in node:
            index(v)
index(data)

# ---- copy ----
INTRO = (
    "<p>One of the best parts of running a healthy snack box business is "
    "discovering all the wonderful healthy snack brands out there. Snack boxes "
    "are a great way of supporting smaller brands, many of whom can't afford "
    "to pay supermarkets to stock them.</p>"
    "<p>There's plenty of debate about which foods are actually healthy, so at "
    "Treat Trunk we keep it simple: every snack we include has to tick lots of "
    "these boxes.</p>"
)

# (image_widget_id, heading_editor_id, body_editor_id, new_heading_html, new_body_html)
CARDS = [
    ("8f427b3", "8cfff92", "947c154", "<h3>Delicious</h3>",
     "<p>We taste test every snack we consider and only the winners make it into the boxes! Gone are the days of cardboard-tasting health food. There are some seriously delicious healthier treats out there now, and we keep munching through them to find the best.</p>"),
    ("15e0850", "3216556", "c21974c", "<h3>Natural</h3>",
     "<p>Made with real food ingredients, not chemicals created in a lab. Real foods bring beneficial nutrients, and with no harmful additives you get double the benefit compared to regular sweets.</p>"),
    ("b3fa065", "28b1ab9", "a684e9b", "<h3>Sugar Sensible</h3>",
     "<p>We prefer sugars that are lower GI and bring their own nutrients: coconut sugar, raw cane sugar, maple syrup or whole fruits. They don't spike your blood sugar the way refined white sugar does, so your mood stays stable and cravings stay down.</p>"),
    ("ca3a716", "fa3053a", "8bdf695", "<h3>Convenient</h3>",
     "<p>Quick and easy snacks that save you time. No preparation, no chilling needed. Perfect for grabbing on the go.</p>"),
    ("5811ec9", "b1c3bee", "4c5a52c", "<h3>Macro Balanced</h3>",
     "<p>Sweet treats with their carbs balanced by protein or healthy fat, which is thought to soften the blood sugar spike. That helps keep your mood and energy steady.</p>"),
    ("4936025", "f2203e8", "4f80128", "<h3>Gut Friendly</h3>",
     "<p>Foods that keep the good bacteria in your body happy and scare off the bad ones. Some snacks are even made with pre and probiotics. Did you know you're made up of more bacteria than human cells?! Scientists are now exploring the gut microbiome to help treat allergies, depression, obesity and bowel disorders.</p>"),
    ("2ba9a27", "c13b9f9", "0f9445d", "<h3>Fun!</h3>",
     "<p>We seek out the most exciting snacks, the ones that feel like such a treat you don't miss the unhealthier versions. It makes healthy eating much easier to stick to.</p>"),
    ("0411f8b", "3b42e32", "b772324", "<h3>1 of your 5 a day</h3>",
     "<p>Fresh fruit and vegetables are best, but hitting your 5 a day isn't always easy. These snacks give you a tasty head start.</p>"),
    ("ffae00b", "cf1985f", "f9a029f", "<h3>Contain antioxidants</h3>",
     "<p>Some snacks are rich in antioxidants, which help your body deal with toxins and are linked to a lower risk of serious illness.</p>"),
    ("fa4c4c9", "508b551", "a95933b", "<h3>Supports wellbeing</h3>",
     "<p>Snacks with nutrients chosen for a purpose, whether it's maca for balancing hormones, guarana for energy or herbs for relaxation.</p>"),
    ("37b66bb", "58540c7", "b991d36", "<h3>Varied</h3>",
     "<p>We hunt down an adventurous range and mix up every box, so you never get bored and keep discovering new favourites.</p>"),
    ("e4cde00", "26126ae", "39aafd5", "<h3>Suitable for lunchboxes</h3>",
     "<p>Every box includes nut-free snacks perfect for lunchboxes, in child-friendly packaging so nobody feels like they're missing out. Who knows, they might start a trend!</p>"),
]

ETHICAL_BODY = (
    "<p>Did you know <a href=\"https://www.mentalhealth.org.uk/publications/"
    "doing-good-does-you-good\">doing good actually makes us healthier</a>? "
    "We look into the ethics of every company we work with, and love to "
    "include brands that are Fair Trade and use biodegradable or recyclable "
    "packaging. Smaller brands tend to care the most and need the most help "
    "getting noticed, and that's exactly where our support goes.</p>"
)

seq = 0
def nid():
    global seq
    seq += 1
    return "a5c%04x" % seq  # unique 7-char ids, distinct from existing

def image_widget(orig_id):
    w = by_id[orig_id]
    s = dict(w["settings"]) if isinstance(w["settings"], dict) else {}
    s["width"] = {"unit": "px", "size": 160, "sizes": []}
    s["align"] = "center"
    return {"id": w["id"], "elType": "widget", "settings": s,
            "elements": [], "widgetType": "image"}

def text_widget(orig_id, html, center=False):
    s = {"editor": html}
    if center:
        s["align"] = "center"
    return {"id": orig_id, "elType": "widget", "settings": s,
            "elements": [], "widgetType": "text-editor"}

def card_column(img_id, head_id, body_id, head_html, body_html):
    return {
        "id": nid(), "elType": "column",
        "settings": {
            "_column_size": 33, "_inline_size": None,
            "padding": {"unit": "px", "top": "10", "right": "24",
                        "bottom": "10", "left": "24", "isLinked": False},
        },
        "elements": [
            image_widget(img_id),
            text_widget(head_id, head_html, center=True),
            text_widget(body_id, body_html),
        ],
        "isInner": False,
    }

# --- intro section: reuse, tighten copy, readable measure ---
intro_sec = by_id["0ea2524"]
if not isinstance(intro_sec["settings"], dict):
    intro_sec["settings"] = {}
intro_sec["settings"]["content_width"] = {"unit": "px", "size": 860, "sizes": []}
by_id["1b5bdf9"]["settings"]["editor"] = INTRO

# --- grid sections: 4 rows x 3 cards ---
grid_sections = []
for row_start in range(0, 12, 3):
    cols = [card_column(*CARDS[i]) for i in range(row_start, row_start + 3)]
    grid_sections.append({
        "id": nid(), "elType": "section",
        "settings": {
            "structure": "30",
            "padding": {"unit": "px", "top": "16", "right": "0",
                        "bottom": "16", "left": "0", "isLinked": False},
        },
        "elements": cols, "isInner": False,
    })

# --- ethical closer: reuse existing 2-col section, update copy ---
ethical_sec = by_id["a9c31b3"]
by_id["1a277d7"]["settings"]["editor"] = ETHICAL_BODY

# --- cta section preserved as-is ---
cta_sec = by_id["1f750c8"]

new_data = [intro_sec] + grid_sections + [ethical_sec, cta_sec]

# sanity checks
blob = json.dumps(new_data, ensure_ascii=False)
assert "—" not in blob and "–" not in blob, "em/en-dash found"
assert blob.count('"widgetType": "image"') == 13  # 12 cards + ethical
assert "mentalhealth.org.uk" in blob
ids = []
def collect(node):
    if isinstance(node, dict):
        if node.get("id"): ids.append(node["id"])
        for v in node.get("elements") or []: collect(v)
    elif isinstance(node, list):
        for v in node: collect(v)
collect(new_data)
assert len(ids) == len(set(ids)), "duplicate element ids"

with open(OUT, "w") as f:
    f.write(json.dumps(new_data, ensure_ascii=False, separators=(",", ":")))
print("sections:", len(new_data), "| elements:", len(ids), "| bytes:", len(blob))
