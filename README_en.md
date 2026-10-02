**English** | [简体中文](./README.md) 

---

# ObsidianCallout for Typecho

A lightweight and elegant Typecho plugin that seamlessly brings Obsidian-style Callouts (admonitions) to your blog. 

## Features

- **Full Syntax Compatibility**: Supports all 13 core Obsidian callout types and their 28 official aliases.
- **Lightweight & Fast**: Regex-based server-side parsing with low impact on frontend loading speed.

## Supported Callout Types

The plugin automatically maps the following syntax to their corresponding theme colors and SVG icons:

- `note`
- `abstract`, `summary`, `tldr`
- `info`, `todo`
- `tip`, `hint`, `important`
- `success`, `check`, `done`
- `question`, `help`, `faq`
- `warning`, `caution`, `attention`
- `failure`, `fail`, `missing`
- `danger`, `error`
- `bug`
- `example`
- `quote`, `cite`

## Installation

1. Download the latest release from the repository.
2. Rename the downloaded folder to `ObsidianCallout` (Case-sensitive).
3. Upload the folder to your Typecho plugin directory (usually `/typecho/data/plugins/`).
4. Log in to your Typecho Admin Panel, navigate to **Plugins**, and activate `ObsidianCallout`.

## Usage

Simply write in standard Obsidian Markdown syntax inside your Typecho editor:

> [!tip] Pro Tip
> This is a beautifully rendered callout box!

> [!danger] Watch Out!
> This operation cannot be undone.

The plugin will automatically parse the blockquotes and render them as beautifully styled warning and info blocks on your frontend.
