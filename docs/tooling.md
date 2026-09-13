# Tooling — Cursor & Boost

Back to [project context](CONTEXT.md)

## Purpose

How agents and developers work on this repo with AI assistance.

## Facts

- IDE: **Cursor** (not Claude Code / Antigravity).
- `boost.json` agents: `["cursor"]` — **local only** (gitignored from public GitHub).
- MCP: `.cursor/mcp.json` → Laravel Boost (`php artisan boost:mcp`).
- Guidelines: `AGENTS.md` (local; not pushed).
- Skills path: `.cursor/skills/` (Boost skills + project skills; not pushed).
- Project rules: `.ai/rules/` (local; not pushed).

## Decisions

- Public GitHub repo tracks **application code** only. `.gitignore` excludes `docs/`, `.cursor/`, `.ai/`, `AGENTS.md`, `boost.json` so hosting paths and agent tooling stay off the public remote.
- Project skill **`actualize`**: summarize dialogue into local `docs/` with deep links (still useful for agents; not in GitHub).

## How to actualize docs

1. Say `actualize` (or ask to актуализировать документацию).
2. Agent loads `.cursor/skills/actualize/SKILL.md`.
3. Updates [CONTEXT.md](CONTEXT.md) hub + relevant branch files + [changelog.md](changelog.md).

## Links

- Hub: [CONTEXT.md](CONTEXT.md)
- Setup: [setup.md](setup.md)
- Deploy: [DEPLOY.md](DEPLOY.md)
- Tech: [tech.md](tech.md)
