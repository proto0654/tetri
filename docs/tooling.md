# Tooling — Cursor & Boost

Back to [project context](CONTEXT.md)

## Purpose

How agents and developers work on this repo with AI assistance.

## Facts

- IDE: **Cursor** (not Claude Code / Antigravity).
- `boost.json` agents: `["cursor"]`.
- MCP: `.cursor/mcp.json` → Laravel Boost (`php artisan boost:mcp`).
- Guidelines: `AGENTS.md`.
- Skills path: `.cursor/skills/` (Boost skills + project skills).

## Decisions

- Re-ran `php artisan boost:install --guidelines --skills --mcp --no-interaction` after correcting agent to Cursor.
- `.gitignore` keeps `.cursor/mcp.json` and `.cursor/skills/**` trackable; other `.cursor/*` stays ignored.
- Project skill **`actualize`**: summarize dialogue into `docs/` with deep links.

## How to actualize docs

1. Say `actualize` (or ask to актуализировать документацию).
2. Agent loads `.cursor/skills/actualize/SKILL.md`.
3. Updates [CONTEXT.md](CONTEXT.md) hub + relevant branch files + [changelog.md](changelog.md).

## Links

- Hub: [CONTEXT.md](CONTEXT.md)
- Setup: [setup.md](setup.md)
- Tech: [tech.md](tech.md)
