# Weisz Design System v3: Soft Cloud Clay

> **Project:** Weisz (Gen Z Mental Health Expert System + Conversational Companion)
> **Visual Style:** Soft Blue Claymorphism. Tactile matte clay surfaces, cushioned soft-blue depth, airy cloud canvas, and organic interactive cards that respond like real soft clay without feeling rigid or childish.
> **Dial:** `ENERGY 2 / RHYTHM 2 / MOTION 2`
> **Design Read:** Reading this as a mental-health reflection web app for Gen Z users, in a Soft Blue Claymorphism style with tactile interactive cards, dial ENERGY 2 / RHYTHM 2 / MOTION 2.

---

## 1. Identity, Dials & Decision Ledger

Weisz feels like holding a smooth, warm-matte soft-blue pebble: approachable, calm, cushioned, and safe to lean on. Clay depth is reserved for meaningful containers and controls so the screen never turns into visual mush.

### Liveliness Dials

- **ENERGY 2 (Soft & Tactile):** Friendly sculpted warmth that invites interaction without harsh contrast or neon overstimulation.
- **RHYTHM 2 (Balanced):** Flat matte cloud canvas anchors the eye, contrasted by puffed clay interactive cards, recessed input wells, and open conversational prose.
- **MOTION 2 (Tactile Micro-Feedback):** Spring-eased (`220ms cubic-bezier(0.22, 1, 0.36, 1)`) hover lift, press squish, and restrained desktop pointer tilt on interactive cards only. Fully degrades under `prefers-reduced-motion: reduce`.

### Core Levers

- **One focal point per screen:** One hero clay card or active conversation stream leads every view; secondary elements stay recessed or matte.
- **Identity motif (The Sculpted Cloud Cushion):** Matte soft-blue clay surfaces (`--surface-clay-blue`) combining a gentle outer blue-tinted shadow with a soft inner top highlight and soft inner bottom shade.
- **One deliberate accent:** Soft Blue (`--sky-primary` / `--sky-soft`) unifies primary actions, active selections, and supportive highlights.

### Decision Ledger (Why Every Choice Exists)

- **Claymorphism (Soft Blue + Cloud Matte):** Chosen per product direction to replace cold flat corporate boxes with soft, comforting tactile affordances suited for stressed Gen Z users.
- **Three-Tier Clay Discipline (Flat Canvas / Raised Clay / Recessed Well):** Prevents the common claymorphism flaw where everything floats equally. Inputs press *into* the page; interactive cards rise *above* it.
- **Typography (`Poppins Rounded` + `Plus Jakarta Sans`):** `Poppins Rounded` brings calm, approachable rounded warmth to hero and section headings; `Plus Jakarta Sans` keeps body copy, navbar, buttons, chatbot, and metadata crisp and effortless to scan.
- **Interactive Non-Rigid Cards:** Hover lift, press squish, inner icon nudge, and optional `±2.5deg` fine-pointer tilt communicate real responsiveness and playfulness while staying calm.
- **Static vs. Interactive Separation:** Non-clickable cards never lift or tilt on hover, preventing false affordance.
- **Restrained Iconography:** Minimal functional stroke icons nestled inside small soft-blue clay icon pills. No decorative sparkles, robots, or emojis.

---

## 2. Color System (Soft Blue Clay, WCAG AA Verified)

Palette cap: 1 Soft Blue scale + airy blue-tinted neutrals + restrained semantic tokens. Both Light Mode (Airy Cloud) and Dark Mode (Night Cloud) are first-class themes.

### 2.1 Soft Blue Scale

| Token | Hex | Role |
|---|---|---|
| `--sky-50` | `#F3F7FB` | Matte cloud canvas (Light) |
| `--sky-100` | `#E6F2FC` | Soft blue clay card tint & supportive surface (Light) |
| `--sky-200` | `#CFE6FA` | User chat clay bubble & selected chip fill (Light) |
| `--sky-300` | `#A6D4F7` | Dark Mode primary clay button hover |
| `--sky-400` | `#8DC5F2` | Dark Mode primary clay button fill & accent text |
| `--sky-500` | `#86BCE8` | Signature soft blue clay accent, icons, focus tint |
| `--sky-600` | `#2B74AE` | Light Mode primary clay button fill (4.99:1 with `#FFFFFF`) |
| `--sky-700` | `#226194` | Light Mode primary button hover (6.55:1 with `#FFFFFF`) |
| `--sky-800` | `#1E5C8E` | Light Mode readable soft-blue accent text (7.05:1 on white) |
| `--sky-900` | `#182433` | Deep slate-navy ink (Light Mode primary text) |

### 2.2 Theme Surface & Text Tokens

| Token | Light Mode (Airy Cloud) | Dark Mode (Night Cloud) | Contrast / Usage |
|---|---|---|---|
| `--bg-page` | `#F3F7FB` | `#121821` | Flat matte ground canvas (never pure `#000000`) |
| `--bg-subtle` | `#E9F1F9` | `#18202C` | Sidebar & secondary bands |
| `--surface-clay` | `#FFFFFF` | `#1E2836` | Primary elevated matte clay card |
| `--surface-clay-blue` | `#E6F2FC` | `#1B2D40` | Soft blue clay card, reflection prompt, selected state |
| `--surface-clay-hover` | `#F7FBFF` | `#243142` | Interactive clay card hover tint |
| `--surface-well` | `#EDF4FA` | `#151C26` | Recessed clay well for text inputs & search bars |
| `--text-primary` | `#182433` | `#EFF5FB` | Primary text (15.68:1 / 16.23:1) |
| `--text-secondary` | `#475A6E` | `#BFD0E2` | Descriptions & subtitles (7.11:1 / 9.44:1) |
| `--text-muted` | `#5B7085` | `#8EA5BC` | Helper copy & timestamps (5.12:1 / 5.85:1) |
| `--text-accent` | `#1E5C8E` | `#8DC5F2` | Soft blue links & headings (7.05:1 / 8.07:1) |
| `--border-clay` | `#D6E6F5` | `#2B3B4E` | Soft hairline edge for clay definition |
| `--border-focus` | `#2B74AE` | `#8DC5F2` | Accessible `:focus-visible` outline (`>= 3:1`) |

### 2.3 Restrained Semantic Colors

Use strictly for real status feedback, never as page decoration.

| Role | Light Text / Surface / Border | Dark Text / Surface / Border |
|---|---|---|
| Success (Soft Mint-Sage) | `#1E6B4B` / `#EBF7F1` / `#BCE3D0` | `#7AD4AC` / `#162A24` / `#254A3E` |
| Care / Crisis (Warm Peach-Ochre) | `#8C4F16` / `#FDF5EB` / `#F2D2AD` | `#F2BE7A` / `#2B2217` / `#4D3A24` |
| Error (Soft Clay-Rose) | `#B2333B` / `#FDF1F2` / `#F2C4C7` | `#F5959B` / `#2E1B1F` / `#542C33` |

---

## 3. Claymorphism Depth, Radius & Elevation System

Claymorphism works when depth is **consistent, directional (top-left light source), and matte**. Never mix glassmorphism blur onto clay cards.

### 3.1 Four Clay Depth Tiers

| Tier | Token | Light Mode Shadow Recipe | Dark Mode Shadow Recipe |
|---|---|---|---|
| **Well (Recessed)** | `--clay-well` | `inset 0 2px 5px rgba(24, 36, 51, 0.08), inset 0 -1px 2px rgba(255, 255, 255, 0.9)` | `inset 0 3px 6px rgba(0, 0, 0, 0.45), inset 0 -1px 2px rgba(255, 255, 255, 0.05)` |
| **Resting Clay** | `--clay-rest` | `0 10px 24px -6px rgba(43, 116, 174, 0.13), inset 0 2px 4px rgba(255, 255, 255, 0.95), inset 0 -4px 8px rgba(134, 188, 232, 0.20)` | `0 12px 26px -6px rgba(0, 0, 0, 0.45), inset 0 2px 3px rgba(255, 255, 255, 0.08), inset 0 -4px 8px rgba(0, 0, 0, 0.35)` |
| **Hover Lift** | `--clay-lift` | `0 18px 34px -8px rgba(43, 116, 174, 0.20), inset 0 2px 5px rgba(255, 255, 255, 1), inset 0 -5px 10px rgba(134, 188, 232, 0.26)` | `0 18px 36px -8px rgba(0, 0, 0, 0.58), inset 0 2px 4px rgba(141, 197, 242, 0.14), inset 0 -5px 10px rgba(0, 0, 0, 0.40)` |
| **Pressed Squish** | `--clay-press` | `0 4px 10px -2px rgba(43, 116, 174, 0.14), inset 0 3px 7px rgba(43, 116, 174, 0.16), inset 0 -1px 3px rgba(255, 255, 255, 0.8)` | `0 4px 10px -2px rgba(0, 0, 0, 0.40), inset 0 3px 7px rgba(0, 0, 0, 0.48), inset 0 -1px 2px rgba(255, 255, 255, 0.05)` |

### 3.2 Deliberate Soft Radius Hierarchy (Never Pill Everything)

| Token | Value | Components |
|---|---|---|
| `--radius-well` | `14px` | Recessed text inputs, search bars, inner tags, compact badges |
| `--radius-btn` | `18px` | Clay buttons, toasts, alerts, selectable list rows |
| `--radius-card` | `24px` | Standard interactive clay cards, chat composer, user clay bubbles |
| `--radius-hero` | `28px` | Hero feature card, modals, major reflection containers |
| `--radius-full` | `9999px` | Circular icon triggers (send, mic, stop) and quick-reply chips only |

### 3.3 Restraint Caps

- Backdrop blur capped at **at most 1 element** (sticky top header or modal backdrop). Clay cards themselves stay **100% opaque matte**.
- Zero ambient corner glow blobs, zero rainbow gradients, zero dot-grid backgrounds.

---

## 4. Interactive Clay Card System (Fluid & Non-Rigid)

Cards are Weisz's signature tactile element. They must feel soft, alive, and responsive without jittering or looking rigid.

### 4.1 Card Variants (Content-Driven Variation, Not Copy-Paste)

1. **Interactive Clay Card (`.clay-card-interactive`):** For selectable categories, topics, history sessions, and clickable actions. Uses `--clay-rest`, full hover lift, press squish, inner icon nudge, and optional subtle tilt.
2. **Soft Blue Cushion Card (`.clay-card-blue`):** For supportive reflections, expert-system reasoning, and mood check-in prompts (`--surface-clay-blue`).
3. **Static Matte Card (`.clay-card-static`):** For read-only summaries, auth containers, and disclaimers. Uses softer resting depth and **zero hover movement** so users never mistake it for a button.
4. **3D Book Clay Card (`.clay-book` / `.book`):** For curated featured collections such as landing category cards. Uses a tactile 3D perspective (`perspective: 2000px; transform-origin: 0% 50%`) where the cover swings open (`rotateY(-80deg)`) on hover to reveal available subcategories on the inside page, paired with a transition shadow and "Lihat Selengkapnya" navigation.

### 4.2 Interactive Micro-Physics

- **Hover (Desktop):** Lifts gently (`transform: translateY(-4px) scale(1.01)`), shifts to `--clay-lift` and `--surface-clay-hover`, with smooth spring easing (`220ms cubic-bezier(0.22, 1, 0.36, 1)`).
- **Nested Content Choreography:**
  - Icon pill inside card nudges gently (`transform: scale(1.06) rotate(-3deg)`).
  - Action cue text shifts slightly (`transform: translateX(3px)`).
- **Press / Tap (`:active`):** Soft clay squish (`transform: translateY(1px) scale(0.985)` + `--clay-press`), giving immediate physical confirmation on both desktop click and mobile tap.
- **Selected State:** Locks into `--surface-clay-blue` with `2px solid var(--sky-primary)` and a cushioned inner rim.
- **Keyboard Focus (`:focus-visible`):** Triggers the same visual lift as hover plus a clear `2px solid var(--border-focus)` ring (`outline-offset: 3px`).
- **Optional Subtle Pointer Tilt (Progressive Enhancement):** On fine-pointer devices (`(hover: hover) and (pointer: fine)`), cards carrying `data-clay-tilt` apply a gentle max `±2.5deg` 3D tilt (`perspective(800px) rotateX(...) rotateY(...)`) following cursor position, easing smoothly back to flat on `mouseleave`. Always disabled on touch devices and when `prefers-reduced-motion: reduce` is active.

### 4.3 Vanilla JS Helper for Subtle Card Tilt (Optional, ~15 Lines)

```js
if (window.matchMedia('(hover: hover) and (pointer: fine)').matches && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  document.querySelectorAll('[data-clay-tilt]').forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `perspective(800px) translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
}
```

---

## 5. Typography

- **Display / Heading (`--font-serif`):** `"Poppins Rounded", "Poppins", sans-serif` exclusively for hero headings and section/page headings.
- **UI & Conversation Sans (`--font-sans`):** `"Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif` for body text, navbar, buttons, chatbot, and metadata.

### Element Font Mapping

| Element | Font |
|---|---|
| Hero heading | Poppins Rounded |
| Section heading | Poppins Rounded |
| Body text | Plus Jakarta Sans |
| Navbar | Plus Jakarta Sans |
| Button | Plus Jakarta Sans |
| Chatbot | Plus Jakarta Sans |
| Metadata | Plus Jakarta Sans |

### Type Scale

| Level | Font | Size (Mobile / Desktop) | Weight | Line Height |
|---|---|---|---|---|
| Display (Hero) | Poppins Rounded | `30px` / `44px` | 500 | `1.16` |
| Page Title (`h1`) | Poppins Rounded | `24px` / `30px` | 500 | `1.22` |
| Section Heading (`h2`) | Poppins Rounded | `20px` / `24px` | 500 | `1.3` |
| Card Title (`h3`) | Plus Jakarta Sans | `15px` / `16px` | 600 | `1.4` |
| Chat & Body | Plus Jakarta Sans | `15px` / `16px` | 400 | `1.68` |
| Navbar / Button / Control | Plus Jakarta Sans | `14px` | 600 | `1.2` |
| Helper / Validation / Metadata | Plus Jakarta Sans | `13px` | 400 | `1.45` |

Rules: Zero wide-tracked uppercase boilerplate labels (`HOW IT WORKS`), zero monospace decorative headings, keep reading line length under `68ch`.

---

## 6. Core UI Components

### 6.1 Clay Buttons (Min `44px` Mobile Height, `42px` Desktop)

- **Primary Soft Blue Clay:** Light `--sky-600` (`#2B74AE`) + `#FFFFFF` text with soft clay bevel (`0 8px 18px -4px rgba(43, 116, 174, 0.35), inset 0 2px 3px rgba(255, 255, 255, 0.35), inset 0 -3px 6px rgba(18, 48, 74, 0.25)`); Dark `--sky-400` (`#8DC5F2`) + `#121821` ink. Radius `18px`. Hover lifts `-2px`; active squishes `scale(0.98)`.
- **Secondary Clay:** `--surface-clay`, `--clay-rest`, `--text-primary` text, `--border-clay` edge.
- **Destructive Action:** Used strictly inside confirmation dialogs (`--color-error` soft clay fill on final confirm only).
- Required states: default, hover, `:focus-visible`, active squish, disabled (`opacity: 0.45`, no lift), loading.

### 6.2 Recessed Clay Inputs & Inline Form Validation

- Surface: `--surface-well`, shadow `--clay-well`, radius `14px`, min-height `44px`, padding `10px 16px`.
- Focus: border `var(--border-focus)` + soft outer ring `0 0 0 3px rgba(43, 116, 174, 0.18)` (Light) / `rgba(141, 197, 242, 0.24)` (Dark).
- Invalid: border `var(--color-error)`, `aria-invalid="true"`, linked via `aria-describedby` to a `13px` helper line `6px` below.
- Validate on `blur` or submit; re-validate on `input` only after an error is shown. Write plain-language fix guidance.

### 6.3 Toasts, Alerts & Tables

- **Toasts:** Floating clay pills (`radius: 18px`, max width `380px`, bottom-right desktop / bottom-center mobile above safe area), max `3` stacked, auto-dismiss `4s` for info/success, persistent for error/safety, always with accessible close button and `Escape` support.
- **Admin Tables:** Wrapped inside a single `--surface-clay` container (`radius: 24px`) with clean `52px` rows and hairline dividers (never apply individual clay shadows to every table row).

---

## 7. Conversational Experience (Core Product)

### 7.1 Layout & Message Rhythm

- **Desktop:** `264px` solid `--bg-subtle` sidebar (collapsible to `72px`) + centered `740px` conversation stream on flat `--bg-page` cloud canvas.
- **Mobile:** Off-canvas drawer sidebar, top floating bar (`44px` circular clay controls for menu, crisis help, copy transcript), pinned keyboard-aware composer (`100dvh` + `env(safe-area-inset-bottom)`).
- **User message:** Right-aligned soft blue clay bubble (`--sky-200` Light / `--surface-clay-blue` Dark, `--text-primary`, radius `22px` with `6px` top-right corner, light resting clay bevel).
- **Assistant message:** Left-aligned open prose directly on `--bg-page` (`max-w-[68ch]`, `15.5px`, `leading-[1.68]`). Keeps long empathetic replies airy instead of trapped inside heavy puffy boxes.
- **Voice note entry:** Soft blue clay audio pill with circular `40px` play/pause button, progress bar, timer, and collapsible transcript.

### 7.2 Composer & Interaction States

- Container: Elevated `--surface-clay` container (`radius: 24px`, `--clay-rest`), containing an inner recessed textarea (`min-height: 44px`, auto-grow up to `120px`) and circular `44px` primary clay trigger (Voice Note when empty, Send when text exists, square Stop button during generation).
- Supported states: **Idle**, **Typing**, **Recording**, **Sending**, **Thinking/Streaming**, **Completed**, **Interrupted**, **Failed**, **Retrying**.
- Prevent duplicate submissions while generating; allow one-click abort via `AbortController` (`Respon dihentikan.`); preserve user text on network failure with an inline retry clay card.
- Quick suggestions: 2 to 4 tactile clay chips (`38px` height, pill radius, hover lift `-2px`).

---

## 8. Mental Health, Safety & Privacy Patterns

- **Onboarding & Consent:** Brief, skippable steps covering Weisz's purpose, non-diagnostic boundary, data privacy explanation, and optional initial mood check-in.
- **Mood Check-in (Non-Gamified):** Five balanced emotional anchors (`Very Difficult`, `Difficult`, `Okay`, `Good`, `Very Good`) presented as interactive soft clay tiles (`min-height: 44px`, radius `18px`) with tactile press squish and cushioned `--surface-clay-blue` selected lock. Zero streak guilt or cartoon emoji clutter.
- **Expert System Reasoning Card:** Uses `.clay-card-blue` (`--surface-clay-blue`) to explain supportive suggestions in calm observation phrasing ("Based on what you shared..."), never raw rule IDs or diagnostic labels.
- **Conversation Summary & Professionals:** Clean typographic summary card (no decorative emojis, clear non-diagnostic disclaimer) + interactive clay practitioner cards displaying verified factual details only.
- **Crisis Support & Safety Dialog:** Always one tap away from chat top bar and standard footer using Warm Peach-Ochre tokens (calm, never flashing). Lists verified 24/7 hotlines (**Hotline Kesehatan Jiwa Kemenkes 119 ext. 8**, **Halo Kemenkes 1500-567**), traps keyboard focus, and closes with `Escape`.
- **Privacy & Recovery States:** One-click transcript export, confirmation modal before destructive deletion, private conversations hidden from aggregate admin monitoring, plus explicit **Empty**, **Loading**, and **Error** states on every data view.

### 8.1 Crisis Path Availability

Crisis access is **not limited to the chat view**. It must be available on every authenticated page:

- Implemented as a global crisis button in `app.blade.php` navbar (authenticated users only).
- Uses `--color-warning-bg` / `--color-warning-border` / `--color-warning` tokens (Warm Peach-Ochre), minimum `40x40px`, with an accessible `aria-label`.
- The crisis modal traps keyboard focus (Tab cycling within the modal), closes with `Escape`, and returns focus to the trigger button on close. Backdrop click also closes it.
- Never use a flashing animation, red alert color, or alarming language. Calm, grounded phrasing always.

### 8.2 Two Entry Paths

The exploration flow has two distinct entry points:

1. **Explore path:** `/ > /explore > /{category} > /{subcategory} > /{topic} > /confirm > /chat` -- for users who want to browse and choose.
2. **Quick path:** `/curhat` -- bypasses explore, starts chat directly without topic selection.

Prioritize the Quick path CTA in the hero for users who arrive already distressed. Five navigation steps before reaching support is too many for someone in acute distress.

### 8.3 Mood Check-in Storage Contract

Mood values are stored directly in `chat_sessions`, not only in `chat_messages.metadata`:

| Column | Type | Values |
|---|---|---|
| `mood_score` | `TINYINT UNSIGNED NULL` | `1` = `overwhelmed`, `2` = `anxious`, `3` = `sad`, `4` = `numb`, `5` = `calm` |
| `mood_label` | `VARCHAR(20) NULL` | `overwhelmed`, `anxious`, `sad`, `numb`, `calm` |

Session creation (`chat.start`) and in-chat mood updates (`chat/{session}/update-mood`) persist both columns alongside conversation context history. This enables mood trend analysis and admin reporting without JSON parsing.

### 8.4 Loading State for Assistant Thinking

When the assistant is in Thinking/Streaming state, show a single calm text line:

```
Weisz sedang merespons...
```

Apply a gentle fade-pulse animation. Under `prefers-reduced-motion: reduce`, show static text only. No spinner, no skeleton screen. A single calm line is least alarming for anxious users.

### 8.5 Empty State: History

When a user has no prior sessions, show:

> "Belum ada refleksi. Mulai dari topik yang terasa berat sekarang."

Followed by a single CTA to `/explore`. No illustration, no empty-state graphic. The sentence carries the message.

---

## 9. Mobile, Accessibility & Copywriting Guardrails

- **Accessibility (WCAG AA):** Minimum `4.5:1` normal text contrast, `3:1` focus/UI contrast, `44x44px` touch targets, `.skip-to-content` link to `#mainContent`, visible `2px` `:focus-visible` outlines, `role="log" aria-live="polite"` on chat feed, explicit `aria-label` on icon controls, and full `prefers-reduced-motion: reduce` support.
- **Semantic Hierarchy (Avoid `<span>`):** Avoid generic `<span>` tags across UI templates. Express hierarchy using semantic HTML paired with clay depth tiers: `.clay-pill-blue` / `.clay-pill-well` / `.clay-pill-{success|warning|error}` on `<p>` or `<div>` for badges and metadata, `.clay-badge-num` for step/priority indices, `<time>` for timestamps, `<strong>`/`<em>` for inline prose emphasis, and direct text inside buttons and links.
- **Voice & Specific CTAs:** Calm, grounded, Gen-Z respectful copy. Concrete CTAs (`Mulai Refleksi Sekarang`, `Lihat Topik Refleksi`, `Bantuan Krisis`, `Salin Transkrip`) instead of generic `Get Started` or `Explore`.
- **Forbidden Patterns:** Zero em dashes (`U+2014`) in UI copy, zero AI buzzwords (`AI Powered`, `Seamless`, `Revolutionary`), zero toxic positivity, zero medical diagnosis claims, zero fake stats/testimonials, zero glowing corner blobs or blueprint dot-grids.

---

## 10. CSS Tokens & Interactive Clay Classes

Copy-ready foundation for `resources/css/app.css`:

```css
:root {
  --font-serif: "Poppins Rounded", "Poppins", sans-serif;
  --font-sans: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, sans-serif;

  /* Soft Blue Clay Scale */
  --sky-50: #f3f7fb;
  --sky-100: #e6f2fc;
  --sky-200: #cfe6fa;
  --sky-300: #a6d4f7;
  --sky-400: #8dc5f2;
  --sky-500: #86bce8;
  --sky-600: #2b74ae;
  --sky-700: #226194;
  --sky-800: #1e5c8e;
  --sky-900: #182433;

  /* Light Mode: Airy Cloud */
  --bg-page: #f3f7fb;
  --bg-subtle: #e9f1f9;
  --surface-clay: #ffffff;
  --surface-clay-blue: #e6f2fc;
  --surface-clay-hover: #f7fbff;
  --surface-well: #edf4fa;

  --text-primary: #182433;
  --text-secondary: #475a6e;
  --text-muted: #5b7085;
  --text-accent: #1e5c8e;

  --border-clay: #d6e6f5;
  --border-focus: #2b74ae;

  /* Clay Depth Recipes */
  --clay-well: inset 0 2px 5px rgba(24, 36, 51, 0.08), inset 0 -1px 2px rgba(255, 255, 255, 0.9);
  --clay-rest: 0 10px 24px -6px rgba(43, 116, 174, 0.13), inset 0 2px 4px rgba(255, 255, 255, 0.95), inset 0 -4px 8px rgba(134, 188, 232, 0.2);
  --clay-lift: 0 18px 34px -8px rgba(43, 116, 174, 0.2), inset 0 2px 5px #ffffff, inset 0 -5px 10px rgba(134, 188, 232, 0.26);
  --clay-press: 0 4px 10px -2px rgba(43, 116, 174, 0.14), inset 0 3px 7px rgba(43, 116, 174, 0.16), inset 0 -1px 3px rgba(255, 255, 255, 0.8);
  --clay-btn-primary: 0 8px 18px -4px rgba(43, 116, 174, 0.34), inset 0 2px 3px rgba(255, 255, 255, 0.32), inset 0 -3px 6px rgba(18, 48, 74, 0.24);

  /* Semantics */
  --color-success: #1e6b4b;
  --color-success-bg: #ebf7f1;
  --color-success-border: #bce3d0;
  --color-warning: #8c4f16;
  --color-warning-bg: #fdf5eb;
  --color-warning-border: #f2d2ad;
  --color-error: #b2333b;
  --color-error-bg: #fdf1f2;
  --color-error-border: #f2c4c7;

  /* Radii & Motion */
  --radius-well: 14px;
  --radius-btn: 18px;
  --radius-card: 24px;
  --radius-hero: 28px;
  --radius-full: 9999px;
  --ease-clay: cubic-bezier(0.22, 1, 0.36, 1);
}

html.dark {
  --bg-page: #121821;
  --bg-subtle: #18202c;
  --surface-clay: #1e2836;
  --surface-clay-blue: #1b2d40;
  --surface-clay-hover: #243142;
  --surface-well: #151c26;

  --text-primary: #eff5fb;
  --text-secondary: #bfd0e2;
  --text-muted: #8ea5bc;
  --text-accent: #8dc5f2;

  --border-clay: #2b3b4e;
  --border-focus: #8dc5f2;

  --clay-well: inset 0 3px 6px rgba(0, 0, 0, 0.45), inset 0 -1px 2px rgba(255, 255, 255, 0.05);
  --clay-rest: 0 12px 26px -6px rgba(0, 0, 0, 0.45), inset 0 2px 3px rgba(255, 255, 255, 0.08), inset 0 -4px 8px rgba(0, 0, 0, 0.35);
  --clay-lift: 0 18px 36px -8px rgba(0, 0, 0, 0.58), inset 0 2px 4px rgba(141, 197, 242, 0.14), inset 0 -5px 10px rgba(0, 0, 0, 0.4);
  --clay-press: 0 4px 10px -2px rgba(0, 0, 0, 0.4), inset 0 3px 7px rgba(0, 0, 0, 0.48), inset 0 -1px 2px rgba(255, 255, 255, 0.05);
  --clay-btn-primary: 0 8px 20px -4px rgba(0, 0, 0, 0.45), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 6px rgba(34, 97, 148, 0.3);

  --color-success: #7ad4ac;
  --color-success-bg: #162a24;
  --color-success-border: #254a3e;
  --color-warning: #f2be7a;
  --color-warning-bg: #2b2217;
  --color-warning-border: #4d3a24;
  --color-error: #f5959b;
  --color-error-bg: #2e1b1f;
  --color-error-border: #542c33;
}

.clay-card-static {
  background-color: var(--surface-clay);
  border: 1px solid var(--border-clay);
  border-radius: var(--radius-card);
  box-shadow: var(--clay-rest);
}

.clay-card-blue {
  background-color: var(--surface-clay-blue);
  border: 1px solid var(--border-clay);
  border-radius: var(--radius-card);
  box-shadow: var(--clay-rest);
}

.clay-card-interactive {
  background-color: var(--surface-clay);
  border: 1px solid var(--border-clay);
  border-radius: var(--radius-card);
  box-shadow: var(--clay-rest);
  cursor: pointer;
  will-change: transform;
  transition: transform 220ms var(--ease-clay), box-shadow 220ms ease, background-color 180ms ease, border-color 180ms ease;
}

.clay-card-interactive:hover,
.clay-card-interactive:focus-visible {
  background-color: var(--surface-clay-hover);
  border-color: var(--sky-500);
  box-shadow: var(--clay-lift);
  transform: translateY(-4px) scale(1.01);
}

.clay-card-interactive:active {
  box-shadow: var(--clay-press);
  transform: translateY(1px) scale(0.985);
}

.clay-card-interactive:hover .clay-icon-pill,
.clay-card-interactive:focus-visible .clay-icon-pill {
  transform: scale(1.06) rotate(-3deg);
}

.clay-card-interactive:hover .clay-arrow-cue,
.clay-card-interactive:focus-visible .clay-arrow-cue {
  transform: translateX(3px);
}

.clay-well-input {
  background-color: var(--surface-well);
  border: 1px solid var(--border-clay);
  border-radius: var(--radius-well);
  box-shadow: var(--clay-well);
  color: var(--text-primary);
  min-height: 44px;
  padding: 10px 16px;
}

@media (prefers-reduced-motion: reduce) {
  .clay-card-interactive,
  .clay-card-interactive:hover,
  .clay-card-interactive:focus-visible,
  .clay-card-interactive:active,
  .clay-icon-pill,
  .clay-arrow-cue {
    transition: none !important;
    transform: none !important;
  }
}
```
