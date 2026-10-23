# Design Tokens

**Authoritative source:** `public/css/rtk-app-theme.css:45-195`. Values below are exact default source values; the theme drawer can change primary color, typography and rounding at runtime.

## Foundation

| Token group | Light | Dark |
|---|---|---|
| `--pri`, `--pri-soft`, `--pri-dark` | `#1b4fe0`, `#EEF2FF`, `#1338b0` | primary stays custom/default; soft `#1e2f60` |
| `--red`, `--grn` | `#D92B2B`, `#1B8A4E` | unchanged |
| page / side / top / card | `#F4F6FB`, `#fff`, `#fff`, `#fff` | `#0F1117`, `#161B27`, `#161B27`, `#1E2435` |
| input / sub / hover / horizontal nav | `#F4F6FB`, `#F8FAFF`, `#EEF2FF`, `#fff` | `#252D3F`, `#1A2030`, `#1F2A44`, `#161B27` |
| `--tx1`, `--tx2`, `--tx3` | `#111827`, `#6B7280`, `#9CA3AF` | `#F1F5F9`, `#94A3B8`, `#64748B` |
| `--bdr`, `--bdr2` | `#E5E7EB`, `#D1D5DB` | `#2A3347`, `#374151` |
| table bg/head/stripe/hover | `#fff`, `#F8FAFF`, `#FAFBFF`, `#EEF2FF` | `#1E2435`, `#1A2030`, `#192030`, `#1F2A44` |
| table text/border | `#111827`, `#6B7280`, `#E5E7EB` | `#F1F5F9`, `#94A3B8`, `#2A3347` |

## Semantic and icon colors

| Purpose | Background / foreground (light) | Background / foreground (dark) |
|---|---|---|
| success badge | `#D1FAE5` / `#065F46` | `#064e3b` / `#6EE7B7` |
| danger badge | `#FEE2E2` / `#991B1B` | `#4c0519` / `#FCA5A5` |
| warning badge | `#FEF9C3` / `#854D0E` | `#451a03` / `#FCD34D` |
| KPI blue | `#EFF6FF` / `#3B82F6` | `#1e3260` / `#60A5FA` |
| KPI green | `#ECFDF5` / `#10B981` | `#063d27` / `#34D399` |
| KPI yellow | `#FFFBEB` / `#F59E0B` | `#422006` / `#FCD34D` |
| KPI rose | `#FFF1F2` / `#F43F5E` | `#3b0f18` / `#FB7185` |
| KPI purple | `#F5F3FF` / `#8B5CF6` | `#2e1065` / `#A78BFA` |
| KPI orange | `#FFF7ED` / `#EA580C` | `#431407` / `#FB923C` |

## Geometry, typography, motion

| Token | Exact value / use |
|---|---|
| `--sw`, `--sw-col` | `250px`, `56px` sidebar expanded/collapsed |
| `--th`, `--hh` | `55px` topbar, `52px` horizontal nav |
| `--fs-base` | `14px`; body line height `1.5` |
| `--font`, `--font-kh` | `'Inter', sans-serif`; `'Noto Sans Khmer', sans-serif` |
| `--rad`, `--rad-s` | `12px` card/dialog; `4px` controls. Early layout script overwrites Bootstrap radii from `tc_rounding` (default input radius `4px`, derived large `5px`). |
| shadows | card `0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04)`; drop `0 8px 24px rgba(0,0,0,.12)`; pop `0 4px 20px rgba(0,0,0,.14)` |
| `--tr` | `.22s cubic-bezier(.4, 0, .2, 1)` |
| drawer | `--td-w:350px`; header / active wire blue `#2563EB` |

Observed fixed sizes include 44px topbar icon buttons, 36px generic icon buttons, 40px global search input, 44px custom switch (`24px` high), 20px module checkboxes, and 18/19px common Lucide icons. Common Bootstrap spacing utilities—not a separately declared spacing scale—supply most page spacing (`p-2/3/4`, `gap-2/3`, `mb-3/4`).

## User-selectable palette

Early boot maps named options to `blue #2563EB`, `sky #0EA5E9`, `purple #7C3AED`, `emerald #059669`, `royal #1D4ED8`, `orange #EA580C`, `cyan #06B6D4`, `mint #10B981`, and `coral #F43F5E` (`layouts/app.blade.php`). It derives `--pri-soft` as alpha `.1` and darkens RGB channels by 30. This coexists with the static default `#1b4fe0`; reproduce both behavior and precedence if matching exactly.

Breakpoints verified in the theme: `1199px`, `991px`, `767px`, `575px`, and `479px`, with reduced-motion queries. There is no verified container maximum token; Bootstrap `.container`/`.container-fluid` and page markup determine width.
