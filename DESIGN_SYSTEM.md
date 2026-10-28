# Weisz Design System

> **Project:** Weisz  
> **Product type:** Gen Z Mental Health Expert-System + AI Chatbot  
> **Design direction:** Simple, calm, modern, rounded, minimal, trustworthy  
> **Themes:** White Mode + Dark Mode  
> **Primary UI reference:** Conversational simplicity similar to ChatGPT, without copying its branding or exact interface.

---

## 1. Design Principles

Weisz must always follow these principles:

1. **Simple** — avoid unnecessary decoration and visual noise.
2. **Calm** — the interface must feel safe, comfortable, and non-judgmental.
3. **Modern** — clean layouts, generous spacing, subtle borders, and contemporary typography.
4. **Rounded** — buttons, cards, inputs, dialogs, chips, and containers use soft rounded corners.
5. **Content first** — the conversation and user's information are more important than decoration.
6. **Consistent** — use the design tokens in this document instead of inventing new values.
7. **Accessible** — maintain readable contrast, visible focus states, and sufficiently large interactive targets.
8. **Responsive** — every page must work on mobile, tablet, and desktop.

Do not introduce gradients, excessive shadows, glassmorphism, excessive illustrations, neon colors, or unnecessary animations unless explicitly requested.

---

# 2. Brand

## 2.1 Name

**Weisz**

The name is inspired by **John R. Weisz**, associated with research on child and adolescent mental health.

The UI must not visually depend on the historical reference. Weisz should feel like a modern digital mental-health product for Gen Z.

## 2.2 Brand personality

- Calm
- Friendly
- Intelligent
- Trustworthy
- Private
- Modern
- Human
- Non-judgmental

Avoid a clinical/hospital visual identity.

---

# 3. Color System

Weisz uses one accent color and neutral colors. White Mode and Dark Mode share the same accent.

## 3.1 Brand / Accent

| Token | Hex | Usage |
|---|---|---|
| `--color-primary-50` | `#EEF2FF` | Very light accent background |
| `--color-primary-100` | `#E0E7FF` | Selected background |
| `--color-primary-200` | `#C7D2FE` | Soft borders |
| `--color-primary-300` | `#A5B4FC` | Secondary accent |
| `--color-primary-400` | `#818CF8` | Hover / secondary |
| `--color-primary-500` | `#6366F1` | Main brand color |
| `--color-primary-600` | `#4F46E5` | Primary button |
| `--color-primary-700` | `#4338CA` | Button hover / active |
| `--color-primary-800` | `#3730A3` | Strong accent |
| `--color-primary-900` | `#312E81` | Deep accent |

**Primary default:** `#6366F1`

Do not use a different primary color on individual pages.

---

# 4. White Mode

## 4.1 Backgrounds

| Token | Hex | Usage |
|---|---|---|
| `--background` | `#FFFFFF` | Main page background |
| `--background-secondary` | `#F8F9FC` | Secondary sections |
| `--surface` | `#FFFFFF` | Cards / dialogs |
| `--surface-secondary` | `#F5F6FA` | Inputs / secondary containers |
| `--surface-hover` | `#F1F3F7` | Hover state |

## 4.2 Text

| Token | Hex | Usage |
|---|---|---|
| `--text-primary` | `#171717` | Main text |
| `--text-secondary` | `#525252` | Secondary text |
| `--text-tertiary` | `#737373` | Metadata / helper text |
| `--text-disabled` | `#A3A3A3` | Disabled text |

## 4.3 Borders

| Token | Hex | Usage |
|---|---|---|
| `--border` | `#E5E7EB` | Default border |
| `--border-secondary` | `#F0F1F3` | Subtle divider |
| `--border-focus` | `#6366F1` | Focused input |

---

# 5. Dark Mode

Dark mode must not use pure black for the main background.

## 5.1 Backgrounds

| Token | Hex | Usage |
|---|---|---|
| `--background` | `#171717` | Main page background |
| `--background-secondary` | `#1F1F1F` | Secondary sections |
| `--surface` | `#242424` | Cards / dialogs |
| `--surface-secondary` | `#2A2A2A` | Inputs / secondary containers |
| `--surface-hover` | `#303030` | Hover state |

## 5.2 Text

| Token | Hex | Usage |
|---|---|---|
| `--text-primary` | `#F5F5F5` | Main text |
| `--text-secondary` | `#D4D4D4` | Secondary text |
| `--text-tertiary` | `#A3A3A3` | Metadata / helper text |
| `--text-disabled` | `#666666` | Disabled text |

## 5.3 Borders

| Token | Hex | Usage |
|---|---|---|
| `--border` | `#3A3A3A` | Default border |
| `--border-secondary` | `#303030` | Subtle divider |
| `--border-focus` | `#818CF8` | Focused input |

---

# 6. Semantic Colors

Semantic colors are used only when their meaning is necessary.

| Purpose | Light | Dark |
|---|---|---|
| Success | `#16A34A` | `#4ADE80` |
| Warning | `#D97706` | `#FBBF24` |
| Error | `#DC2626` | `#F87171` |
| Info | `#2563EB` | `#60A5FA` |

Do not use semantic colors as decoration.

For mental-health risk states, avoid dramatic red UI unless an immediate safety action is required.

---

# 7. Typography

## 7.1 Font

Use:

**Inter**

Fallback:

`Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`

Do not use multiple font families.

## 7.2 Type Scale

| Token | Size | Line Height | Weight | Usage |
|---|---:|---:|---:|---|
| `display` | 40px | 48px | 700 | Hero heading |
| `h1` | 32px | 40px | 700 | Main page heading |
| `h2` | 24px | 32px | 700 | Section heading |
| `h3` | 20px | 28px | 600 | Card heading |
| `h4` | 18px | 26px | 600 | Small heading |
| `body-lg` | 16px | 26px | 400 | Important body |
| `body` | 14px | 22px | 400 | Default UI text |
| `body-sm` | 13px | 20px | 400 | Secondary information |
| `caption` | 12px | 18px | 400 | Metadata |
| `button` | 14px | 20px | 600 | Button text |

### Typography rules

- Do not use font sizes smaller than `12px`.
- Main UI text should normally be `14px`.
- Body content should normally be `14px–16px`.
- Use weight `700` sparingly.
- Do not use all-caps for normal UI text.

---

# 8. Spacing System

Use a 4px base spacing scale.

| Token | Value |
|---|---:|
| `space-1` | 4px |
| `space-2` | 8px |
| `space-3` | 12px |
| `space-4` | 16px |
| `space-5` | 20px |
| `space-6` | 24px |
| `space-8` | 32px |
| `space-10` | 40px |
| `space-12` | 48px |
| `space-16` | 64px |
| `space-20` | 80px |
| `space-24` | 96px |

### Default rules

- Small gap: `8px`
- Component gap: `12–16px`
- Card internal padding: `20–24px`
- Section gap: `32–48px`
- Large section gap: `64–96px`

Avoid arbitrary spacing values such as `13px`, `17px`, `27px`, etc. unless technically necessary.

---

# 9. Border Radius

Everything should feel softly rounded.

| Token | Value | Usage |
|---|---:|---|
| `radius-sm` | 8px | Small controls |
| `radius-md` | 12px | Inputs / buttons |
| `radius-lg` | 16px | Cards |
| `radius-xl` | 20px | Large cards / containers |
| `radius-2xl` | 24px | Feature sections |
| `radius-full` | 9999px | Avatar / pill / circular controls |

### Default

- Button: `12px`
- Input: `12px`
- Card: `16px`
- Modal: `20px`
- Chat bubble: `16px`
- Avatar: `9999px`

Avoid sharp 0px corners unless required for a specific layout.

---

# 10. Buttons

## 10.1 Standard sizes

| Size | Height | Horizontal Padding | Font |
|---|---:|---:|---:|
| Small | 36px | 14px | 13px |
| Medium | 40px | 16px | 14px |
| Large | 48px | 20px | 14px |
| XL / Hero | 52px | 24px | 15px |

Default button size: **40px**

Minimum touch target: **44px** where practical on mobile.

## 10.2 Button styles

### Primary
- Background: `primary-600`
- Text: `#FFFFFF`
- Radius: `12px`
- Hover: `primary-700`
- Active: `primary-800`

### Secondary
- Background: transparent or `surface-secondary`
- Text: `text-primary`
- Border: `border`
- Hover: `surface-hover`

### Ghost
- Background: transparent
- No visible border
- Hover: `surface-hover`

### Danger
- Use only for destructive actions.
- Background: semantic error.
- Never use danger styling for ordinary mental-health states.

---

# 11. Inputs

Default input:

- Height: `44px`
- Padding: `0 14px`
- Radius: `12px`
- Border: `1px solid var(--border)`
- Font: `14px`
- Background: `var(--surface)`
- Text: `var(--text-primary)`

Focus:

- Border: `var(--border-focus)`
- Add a subtle focus ring.
- Do not use a large glowing effect.

Textarea:

- Minimum height: `100px`
- Padding: `12px 14px`
- Radius: `12px`

---

# 12. Cards

Default card:

- Background: `var(--surface)`
- Border: `1px solid var(--border)`
- Radius: `16px`
- Padding: `20px`
- Shadow: none or extremely subtle

Cards should primarily be separated by spacing and borders, not heavy shadows.

### Card sizes

- Compact: `16px` padding
- Default: `20px` padding
- Large: `24px` padding

---

# 13. Shadows

Weisz uses very subtle shadows.

```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
--shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
--shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.08);
```

Dark mode shadows should be reduced or replaced with borders.

Do not use large floating shadows as a primary visual style.

---

# 14. Layout & Content Width

## Desktop

- Maximum content width: `1200px`
- Comfortable reading width: `720px`
- Chat content maximum width: `768px`
- Page horizontal padding: `32px`

## Tablet

- Horizontal padding: `24px`

## Mobile

- Horizontal padding: `16px`

### Main content

Use centered content where appropriate.

```text
Desktop:
[ 32px ] [       Main Content       ] [ 32px ]

Mobile:
[ 16px ] [      Main Content      ] [ 16px ]
```

Do not stretch text-heavy content across the entire desktop screen.

---

# 15. Responsive Breakpoints

Use:

| Name | Width |
|---|---:|
| Mobile | `< 640px` |
| Tablet | `640px–1023px` |
| Desktop | `>= 1024px` |
| Large Desktop | `>= 1280px` |

Mobile is the priority for the chatbot experience.

---

# 16. Navbar

Desktop:

- Height: `64px`
- Horizontal padding: `24–32px`
- Logo on left
- Navigation/actions on right
- Border bottom: `1px solid var(--border)`

Mobile:

- Height: `56px`
- Horizontal padding: `16px`
- Keep navigation minimal.
- Use a menu/drawer when necessary.

Navbar must remain visually simple.

---

# 17. Sidebar

For authenticated dashboard/admin layouts:

- Width: `240px`
- Compact width when collapsed: `72px`
- Padding: `12px`
- Navigation item height: `40px`
- Radius: `10px`
- Gap between items: `4px`

Active navigation:

- Background: `primary-50` in White Mode
- Background: subtle primary-tinted surface in Dark Mode
- Text/icon: primary

Do not use highly saturated full-sidebar backgrounds.

---

# 18. Chatbot Design

The chatbot is the **core experience of Weisz**.

Its interaction model should feel familiar and simple, inspired by modern AI chat applications such as ChatGPT, while keeping Weisz's own visual identity.

## 18.1 Chat layout

Desktop:

```text
┌──────────────────────────────────────────────┐
│ Header                                       │
├───────────────┬──────────────────────────────┤
│ Conversation  │                              │
│ History       │       Chat Content           │
│               │                              │
│               │                              │
│               │                              │
│               │     Message Composer         │
└───────────────┴──────────────────────────────┘
```

Mobile:

```text
┌─────────────────────────────┐
│ Header                      │
├─────────────────────────────┤
│                             │
│       Chat Content          │
│                             │
│                             │
├─────────────────────────────┤
│ Message Composer            │
└─────────────────────────────┘
```

## 18.2 Message width

Assistant messages:

- Maximum width: `768px`
- Align: left

User messages:

- Maximum width: `70%` desktop
- Align: right

On mobile:

- Maximum width: `88%`

## 18.3 Message spacing

- Message group gap: `16px`
- Individual message gap: `8px`
- Paragraph line-height: `1.6`

Avoid excessive speech-bubble styling.

The chatbot should feel like a clean conversation rather than a traditional messaging app.

## 18.4 User message

- Background: `primary-600`
- Text: white
- Radius: `16px`
- Padding: `10px 14px`

## 18.5 Assistant message

White Mode:

- Background: transparent or `surface`
- Text: `text-primary`

Dark Mode:

- Background: transparent or `surface`
- Text: `text-primary`

Assistant messages should not look visually heavier than user messages.

---

# 19. Chat Composer

The message composer is a major component.

Desktop:

- Maximum width: `768px`
- Minimum height: `52px`
- Radius: `16px`
- Border: `1px solid var(--border)`
- Background: `var(--surface)`

Mobile:

- Minimum height: `48px`
- Horizontal margin: `12–16px`

Structure:

```text
┌──────────────────────────────────────────┐
│  Message...                         [↑]  │
└──────────────────────────────────────────┘
```

Send button:

- Size: `36px`
- Radius: `9999px`
- Primary color
- Disabled when there is no message

Do not create a giant input box.

---

# 20. Chatbot Quick Suggestions

Suggestion chips may be displayed when the user starts a conversation or becomes stuck.

Chip:

- Height: `36px`
- Padding: `8px 14px`
- Radius: `9999px`
- Border: `1px solid var(--border)`
- Background: `var(--surface)`
- Font: `13px`

Hover:

- Background: `surface-hover`

Examples:

- "I'm feeling overwhelmed"
- "Help me understand this"
- "What should I do next?"
- "I don't know how to explain it"

Do not present too many options at once. Prefer 2–4 suggestions.

---

# 21. Context Confirmation

Before entering the chatbot, show the selected context clearly.

Example:

```text
Your selected topic

Academic Stress

We'll focus this conversation on academic stress.
You can change the topic anytime.

[Continue] [Change Topic]
```

Use a simple card with:

- Radius: `16px`
- Padding: `24px`
- Border
- No heavy illustration

---

# 22. Problem Selection UI

For Problem Explorer, Subcategory, and Topic Selection:

Use cards or selectable list items.

Selectable item:

- Minimum height: `64px`
- Padding: `16px`
- Radius: `14px`
- Border: `1px solid var(--border)`
- Gap: `12px`

Selected:

- Border: primary
- Background: subtle primary background
- Optional check icon

Do not use huge colorful category cards.

---

# 23. Conversation Summary

The summary should look like a calm information card.

Recommended structure:

```text
Conversation Summary

Main topic
Academic Stress

What you shared
...

Possible factors
...

Suggested next steps
...

Professional support
Recommended when appropriate
```

Use clear sections and whitespace.

Do not describe the summary as a medical diagnosis.

---

# 24. Professional Recommendation

Professional recommendations are informational.

Each professional card should contain:

- Name
- Profession: Psychologist / Psychiatrist
- Location
- Contact information
- Optional short description
- Optional available service information if stored by admin

Card:

- Radius: `16px`
- Padding: `20px`
- Border: `1px solid var(--border)`

The AI must never invent professional data.

Professional information must come from the admin-managed database.

---

# 25. Admin UI

Admin pages use the same design system.

Admin dashboard:

- Maximum content width: `1200px`
- Metric cards: `16px radius`
- Table radius: `16px`
- Table row height: `52–56px`
- Button height: `40px`
- Page heading: `24–32px`

Admin UI should be functional and clean, not visually different from the user product.

---

# 26. Tables

Default:

- Border: `1px solid var(--border)`
- Radius: `16px`
- Overflow: hidden
- Row height: `52–56px`
- Cell padding: `12px 16px`

Header:

- Font weight: `600`
- Background: `surface-secondary`

Hover:

- Background: `surface-hover`

On mobile, convert wide tables into cards or horizontally scrollable containers.

---

# 27. Badges / Status

Default badge:

- Height: `28px`
- Padding: `4px 10px`
- Radius: `9999px`
- Font: `12px`
- Font weight: `600`

Use semantic colors only when the status requires meaning.

Examples:

- Active
- Inactive
- Pending
- Completed
- Draft

---

# 28. Modal / Dialog

- Maximum width: `480px` default
- Large modal: `640px`
- Radius: `20px`
- Padding: `24px`
- Background: `var(--surface)`
- Border: `1px solid var(--border)`

Overlay:

- Use a subtle dark translucent overlay.
- Do not heavily blur the entire interface.

---

# 29. Avatar

Sizes:

| Size | Diameter |
|---|---:|
| Small | 32px |
| Medium | 40px |
| Large | 48px |
| XL | 64px |

Radius: `9999px`

---

# 30. Icons

Use **Lucide Icons** or another consistent outline icon library.

Rules:

- Default size: `20px`
- Small: `16px`
- Large: `24px`
- Stroke width: approximately `2`
- Do not mix multiple icon styles.

Icons should support the text, not replace important labels.

---

# 31. Animation

Animations must be subtle.

Recommended:

- Hover: `120–180ms`
- Standard transition: `180ms`
- Modal: `200ms`
- Page transition: `200–250ms`

Use:

```css
transition: all 180ms ease;
```

Avoid:

- Bouncing UI
- Excessive scaling
- Large parallax
- Constant motion
- Distracting background animations

For chatbot typing/loading, use a subtle three-dot indicator.

---

# 32. Accessibility

Always:

- Provide visible focus states.
- Use semantic HTML.
- Maintain sufficient text/background contrast.
- Do not communicate information by color alone.
- Make interactive controls keyboard accessible.
- Provide labels for form inputs.
- Use at least `44px` touch targets where practical on mobile.
- Respect `prefers-reduced-motion`.

---

# 33. Mental Health UX Rules

This is a mental-health-related product, so the UI must feel safe.

### Always

- Use calm language.
- Avoid judgmental wording.
- Explain uncertainty.
- Make recommendations feel optional and supportive.
- Keep professional recommendations factual.
- Provide clear safety actions when an immediate-risk situation is detected.

### Never

- Claim that the AI has diagnosed the user.
- Present an expert-system result as a medical diagnosis.
- Use frightening visual effects for risk states.
- Shame the user.
- Invent psychologist/psychiatrist information.
- Make the chatbot appear to replace a mental-health professional.

---

# 34. Design Token Priority

When implementing a new page, follow this order:

1. Layout
2. Spacing
3. Typography
4. Colors
5. Components
6. Interaction states
7. Responsive behavior
8. Accessibility

Do not create a new design token unless an existing token cannot reasonably solve the requirement.

---

# 35. Forbidden Visual Patterns

Do NOT introduce these patterns unless explicitly requested:

- Neon cyberpunk colors
- Excessive gradients
- Glassmorphism
- Excessive blur
- Excessive shadows
- Sharp rectangular cards
- Excessive illustrations
- Random decorative shapes
- Multiple unrelated accent colors
- Excessive animations
- Huge text everywhere
- Dense dashboard layouts
- Hospital/medical UI aesthetics
- Generic AI robot imagery
- Chatbot UI filled with speech bubbles

---

# 36. Page-Specific Design Direction

## User Pages

### Landing Page
Minimal hero, short explanation, clear CTA, calm visual hierarchy.

### Login
Centered authentication card, minimal distractions.

### Register
Same visual language as Login.

### Dashboard
Simple overview with "Start Conversation" as the main CTA.

### Problem Explorer
Simple problem categories with selectable cards.

### Subcategory
Progressive selection with clear hierarchy.

### Topic Selection
Specific topic choices with short descriptions.

### Context Confirmation
Confirm the selected context before starting the chatbot.

### AI Chatbot
Primary product experience. Maximum simplicity.

### Chatbot Suggestion
Small suggestion chips/cards that help users continue.

### Conversation Summary
Structured summary with clear sections.

### Professional Recommendation
Clean factual professional cards.

### Conversation History
Simple chronological conversation list.

### Profile & Settings
Grouped settings with clean forms.

## Admin Pages

### Admin Dashboard
Metrics, recent activity, and system overview.

### Category Management
CRUD interface for problem categories.

### Knowledge Base
Manage expert knowledge used by the system.

### Expert Rule Management
Manage conditions, facts, rules, and actions.

### Professional Management
CRUD interface for psychologist/psychiatrist data.

### Conversation Monitoring
System-level conversation/activity statistics.

---

# 37. Default Component Specifications

| Component | Default |
|---|---|
| Button | 40px height / 12px radius |
| Input | 44px height / 12px radius |
| Card | 16px radius / 20px padding |
| Modal | 20px radius / 24px padding |
| Chat Bubble | 16px radius |
| Badge | 28px height / full radius |
| Avatar | 40px |
| Navbar | 64px desktop / 56px mobile |
| Sidebar | 240px |
| Table Row | 52–56px |
| Chat Composer | 52px minimum |
| Page max width | 1200px |
| Chat max width | 768px |
| Mobile page padding | 16px |
| Desktop page padding | 32px |

---

# 38. AI Implementation Rules

When generating UI/UX or frontend code for Weisz:

1. **Always use this design system.**
2. Do not invent new colors when an existing token is available.
3. Do not invent arbitrary spacing values.
4. Do not change the primary color per page.
5. Use `Inter`.
6. Use rounded components.
7. Support both White Mode and Dark Mode.
8. Use CSS variables/design tokens for colors.
9. Keep the interface minimal.
10. Keep chatbot UI simple and conversational.
11. Make mobile responsive by default.
12. Use consistent iconography.
13. Prefer borders and spacing over heavy shadows.
14. Do not add unnecessary decorative elements.
15. Do not redesign the brand unless explicitly instructed.
16. Preserve existing components and tokens when extending the system.
17. If a new component is required, derive its dimensions, spacing, radius, typography, and colors from existing tokens.
18. Never create a one-off visual style for a single page.
19. The user experience must remain calm and non-clinical.
20. Never represent chatbot output as a medical diagnosis.

---

# 39. Recommended CSS Variables

```css
:root {
  --font-sans: "Inter", ui-sans-serif, system-ui, -apple-system,
    BlinkMacSystemFont, "Segoe UI", sans-serif;

  --color-primary-50: #EEF2FF;
  --color-primary-100: #E0E7FF;
  --color-primary-200: #C7D2FE;
  --color-primary-300: #A5B4FC;
  --color-primary-400: #818CF8;
  --color-primary-500: #6366F1;
  --color-primary-600: #4F46E5;
  --color-primary-700: #4338CA;
  --color-primary-800: #3730A3;
  --color-primary-900: #312E81;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
  --radius-2xl: 24px;
  --radius-full: 9999px;

  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
  --space-10: 40px;
  --space-12: 48px;
  --space-16: 64px;
  --space-20: 80px;
  --space-24: 96px;
}

[data-theme="light"] {
  --background: #FFFFFF;
  --background-secondary: #F8F9FC;
  --surface: #FFFFFF;
  --surface-secondary: #F5F6FA;
  --surface-hover: #F1F3F7;

  --text-primary: #171717;
  --text-secondary: #525252;
  --text-tertiary: #737373;
  --text-disabled: #A3A3A3;

  --border: #E5E7EB;
  --border-secondary: #F0F1F3;
  --border-focus: #6366F1;
}

[data-theme="dark"] {
  --background: #171717;
  --background-secondary: #1F1F1F;
  --surface: #242424;
  --surface-secondary: #2A2A2A;
  --surface-hover: #303030;

  --text-primary: #F5F5F5;
  --text-secondary: #D4D4D4;
  --text-tertiary: #A3A3A3;
  --text-disabled: #666666;

  --border: #3A3A3A;
  --border-secondary: #303030;
  --border-focus: #818CF8;
}
```

---

# 40. Final Rule

**Weisz should look like one coherent product, not 20 individually designed pages.**

Every new screen, component, feature, and frontend implementation must reuse this design system.

When there is a design decision that is not explicitly defined here:

1. Prefer simplicity.
2. Prefer existing tokens.
3. Prefer consistency with existing components.
4. Prefer whitespace over decoration.
5. Prefer accessibility.
6. Prefer calm and trustworthy visual communication.
7. Do not introduce a new visual pattern without a strong functional reason.

**Design keywords:**

`Minimal · Calm · Modern · Rounded · Clean · Friendly · Trustworthy · Gen-Z · Conversational · Accessible`
