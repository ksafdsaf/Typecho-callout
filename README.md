[English](./README_en.md) | **简体中文**

---

# ObsidianCallout for Typecho

一款轻量、优雅的 Typecho 插件，为你的博客引入 Obsidian 风格的 Callout。

## 特性

- **完整语法兼容**：全面支持所有 13 种核心 Obsidian callout 类型及其 28 个官方别名。
- **轻量且极速**：基于服务端的正则解析，对前端页面加载速度影响低。

## 支持的 Callout 类型

插件会自动将以下语法映射至对应的主题颜色与 SVG 图标：

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

## 安装指南

1. 从代码仓库下载最新版本。
2. 将下载解压后的文件夹重命名为 `ObsidianCallout`（严格区分大小写）。
3. 将该文件夹上传至你的 Typecho 插件目录（通常为 `/typecho/data/plugins/`）。
4. 登录你的 Typecho 后台管理面板，导航至 **插件** 页面，并激活 `ObsidianCallout`。

## 使用方法

只需在你的 Typecho 编辑器中使用标准的 Obsidian Markdown 语法编写即可：

```markdown
> [!tip] 进阶提示
> 这是一个提示框！

> [!danger] 警告！
> 此操作无法撤销。
```
## 演示地址

https://typecho.1151111.xyz/index.php/default/4
