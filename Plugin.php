<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 
 * 
 * @package ObsidianCallout
 * @author ajsn
 * @version 1.1.0
 * @link https://typecho.1151111.xyz
 */
class ObsidianCallout_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('ObsidianCallout_Plugin', 'parseCallout');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('ObsidianCallout_Plugin', 'parseCallout');
        Typecho_Plugin::factory('Widget_Archive')->header = array('ObsidianCallout_Plugin', 'headerCss');
        return _t('Obsidian Callout 插件已成功激活！');
    }

    public static function deactivate() {}

    public static function config(Typecho_Widget_Helper_Form $form) {}

    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    /**
     * 规范化 Callout 类型映射（将同义别名统一映射到标准分类）
     */
    private static function resolveType($type)
    {
        $map = [
            // note
            'note'      => 'note',
            // abstract, summary, tldr
            'abstract'  => 'abstract',
            'summary'   => 'abstract',
            'tldr'      => 'abstract',
            // info, todo
            'info'      => 'info',
            'todo'      => 'todo',
            // tip, hint, important
            'tip'       => 'tip',
            'hint'      => 'tip',
            'important' => 'tip',
            // success, check, done
            'success'   => 'success',
            'check'     => 'success',
            'done'      => 'success',
            // question, help, faq
            'question'  => 'question',
            'help'      => 'question',
            'faq'       => 'question',
            // warning, caution, attention
            'warning'   => 'warning',
            'caution'   => 'warning',
            'attention' => 'warning',
            // failure, fail, missing
            'failure'   => 'failure',
            'fail'      => 'failure',
            'missing'   => 'failure',
            // danger, error
            'danger'    => 'danger',
            'error'     => 'danger',
            // bug
            'bug'       => 'bug',
            // example
            'example'   => 'example',
            // quote, cite
            'quote'     => 'quote',
            'cite'      => 'quote',
        ];

        return isset($map[$type]) ? $map[$type] : 'note';
    }

    /**
     * 注入配套的 CSS 样式与配色变量
     */
    public static function headerCss()
    {
        echo <<<HTML
<style>
.callout {
    --callout-color: #086ddd;
    --callout-bg: rgba(8, 109, 221, 0.08);
    margin: 1.2em 0;
    padding: 0.85em 1.1em;
    border-radius: 6px;
    border-left: 4px solid var(--callout-color);
    background-color: var(--callout-bg);
    font-size: 0.95em;
    line-height: 1.6;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.callout-title {
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 0.4em;
    text-transform: capitalize;
    color: var(--callout-color);
}
.callout-title svg {
    flex-shrink: 0;
}
.callout-content {
    margin: 0;
}
.callout-content p:last-child {
    margin-bottom: 0;
}

/* 1. note: 默认蓝 */
.callout[data-callout="note"] { --callout-color: #086ddd; --callout-bg: rgba(8, 109, 221, 0.08); }

/* 2. abstract, summary, tldr: 青色 */
.callout[data-callout="abstract"] { --callout-color: #00b0ff; --callout-bg: rgba(0, 176, 255, 0.08); }

/* 3. info, todo: 浅蓝/靛青 */
.callout[data-callout="info"], .callout[data-callout="todo"] { --callout-color: #0091ea; --callout-bg: rgba(0, 145, 234, 0.08); }

/* 4. tip, hint, important: 水绿 */
.callout[data-callout="tip"] { --callout-color: #00bfa5; --callout-bg: rgba(0, 191, 165, 0.08); }

/* 5. success, check, done: 绿色 */
.callout[data-callout="success"] { --callout-color: #00c853; --callout-bg: rgba(0, 200, 83, 0.08); }

/* 6. question, help, faq: 橙黄色 */
.callout[data-callout="question"] { --callout-color: #e6a23c; --callout-bg: rgba(230, 162, 60, 0.08); }

/* 7. warning, caution, attention: 橙色 */
.callout[data-callout="warning"] { --callout-color: #ff9100; --callout-bg: rgba(255, 145, 0, 0.08); }

/* 8. failure, fail, missing: 砖红色/红褐色 */
.callout[data-callout="failure"] { --callout-color: #d32f2f; --callout-bg: rgba(211, 47, 47, 0.08); }

/* 9. danger, error: 亮红 */
.callout[data-callout="danger"] { --callout-color: #ff1744; --callout-bg: rgba(255, 23, 68, 0.08); }

/* 10. bug: 猩红 */
.callout[data-callout="bug"] { --callout-color: #e91e63; --callout-bg: rgba(233, 30, 99, 0.08); }

/* 11. example: 紫色 */
.callout[data-callout="example"] { --callout-color: #7c4dff; --callout-bg: rgba(124, 77, 255, 0.08); }

/* 12. quote, cite: 灰色 */
.callout[data-callout="quote"] { --callout-color: #78909c; --callout-bg: rgba(120, 144, 156, 0.08); }
</style>
HTML;
    }

    /**
     * 正则匹配并替换 blockquote
     */
    public static function parseCallout($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;

        // 兼容 [!type]、[!type|metadata]、折叠语法 [!type]+ / [!type]-
        $pattern = '/<blockquote>\s*<p>\s*\[!([a-zA-Z0-9_-]+)(?:\|[^\]\n]+)?\](?:\s*([+-]))?(?:\s*([^\n<]+))?(.*?)<\/blockquote>/is';

        $text = preg_replace_callback($pattern, function ($matches) {
            $rawType     = strtolower(trim($matches[1]));
            $collapse    = isset($matches[2]) ? trim($matches[2]) : '';
            $titleInput  = isset($matches[3]) ? trim($matches[3]) : '';
            $body        = isset($matches[4]) ? trim($matches[4]) : '';

            $resolvedType = self::resolveType($rawType);
            $customTitle  = !empty($titleInput) ? $titleInput : ucfirst($rawType);

            // 清理开头残留的换行
            $body = preg_replace('/^<br\s*\/?>\s*/i', '', $body);
            $icon = self::getIcon($resolvedType);

            return '<div class="callout" data-callout="' . htmlspecialchars($resolvedType) . '">'
                . '<div class="callout-title">' . $icon . '<span>' . htmlspecialchars($customTitle) . '</span></div>'
                . '<div class="callout-content">' . $body . '</div>'
                . '</div>';
        }, $text);

        return $text;
    }
    
    private static function getIcon($type)
    {
        $base = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="stroke-linecap: round; stroke-linejoin: round;">';
        
        switch ($type) {
            case 'note': // 铅笔
                return $base . '<path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/><path d="m15 5 4 4"/></svg>';
            case 'abstract': // 剪贴板
                return $base . '<rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><line x1="8" y1="11" x2="8.01" y2="11"/><line x1="8" y1="16" x2="8.01" y2="16"/></svg>';
            case 'info': // 圆圈 info
                return $base . '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
            case 'todo': // 圆圈勾
                return $base . '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>';
            case 'tip': // 火焰
                return $base . '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>';
            case 'success': // 单勾
                return $base . '<polyline points="20 6 9 17 4 12"/></svg>';
            case 'question': // 问号
                return $base . '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            case 'warning': // 警告三角
                return $base . '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            case 'failure': // 大叉号
                return $base . '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            case 'danger': // 闪电
                return $base . '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
            case 'bug': // 虫子
                return $base . '<rect width="8" height="14" x="8" y="6" rx="4"/><path d="m19 7-3 2"/><path d="m5 7 3 2"/><path d="m19 19-3-2"/><path d="m5 19 3 2"/><path d="M20 13h-4"/><path d="M4 13h4"/><path d="m10 4 1 2"/><path d="m14 4-1 2"/></svg>';
            case 'example': // 列表
                return $base . '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>';
            case 'quote': // 引号
                return $base . '<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/></svg>';
            default:
                return $base . '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        }
    }
}