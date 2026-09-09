---
name: actualize
description: >-
  Summarize the current dialogue into durable project documentation with deep
  links between meaning/function branches under docs/. Use when the user says
  "actualize", asks to актуализировать документацию, sync chat conclusions into
  docs, or refresh the project knowledge map.
disable-model-invocation: false
---

# Actualize

Capture durable conclusions from the current conversation into `docs/` using a hub + branch model with deep links.

## When to run

- User says `actualize` / `актуализируй` / asks to update project docs from chat
- End of a meaningful discovery, architecture, or setup session
- Before handoff to another agent or teammate

## Workflow

1. Read `docs/CONTEXT.md` first (hub). If missing, create it from the template below.
2. Extract only durable facts from the dialogue:
   - product intent and constraints
   - settled technical decisions
   - setup state (packages, agents, MCP, paths)
   - open questions / next steps
3. Update or create branch docs under `docs/` (one concern per file). Prefer editing existing branches over inventing new ones.
4. Keep every branch linked both ways:
   - Hub → branch section with relative link
   - Branch → backlink to `CONTEXT.md`
5. Append a short dated entry to `docs/changelog.md` summarizing what changed and why.
6. Do not invent product facts. Mark unknowns as open questions.
7. Do not commit unless the user asks.

## Hub template (`docs/CONTEXT.md`)

```markdown
# Project context

> Hub for Tetri documentation. Branches below are deep-linked meaning/function maps.

## Snapshot
- One short paragraph: what the project is

## Deep links
| Branch | Path | Status |
| --- | --- | --- |
| Product | [product.md](product.md) | draft/active |
| ... | ... | ... |

## Current decisions
- Bullet list of settled choices

## Open questions
- Bullet list

## Last actualized
- YYYY-MM-DD — brief note
```

## Branch rules

- One primary concern per file (`product`, `tech`, `tooling`, `setup`, `content`, `design`, …)
- Start with `# Title`, then `Back to [project context](CONTEXT.md)`
- Use relative Markdown links only
- Prefer short sections: Purpose, Facts, Decisions, Links
- Russian or English is fine; match existing docs language

## Quality bar

- Summarize conclusions, not chat transcript
- Prefer “decision + reason” over narrative
- Keep hub under ~120 lines; push detail into branches
- After writing, verify every new deep link resolves to an existing file
