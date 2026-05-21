﻿﻿﻿﻿﻿<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    cms_redirect('login.php');
}

$page_title = 'Add New Post';
$page_alert = '';

$title = '';
$slug = '';
$excerpt = '';
$content = '';
$featured_image = '';
$category = '';
$author = '';
$status = 'published';

$categories = [];
$authors = [];

try {
    $categories = get_categories();
    $authors = get_authors();
} catch (Exception $exception) {
    $page_alert = 'Unable to load category and author lists: ' . html_escape($exception->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $featured_image = trim($_POST['featured_image'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $status = trim($_POST['status'] ?? 'published');

    if ($title === '' || $excerpt === '' || $content === '' || $category === '' || $author === '') {
        $page_alert = 'Please fill in a title, excerpt, content, category, and author before saving.';
    } else {
        if ($slug === '') {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title)));
            $slug = trim($slug, '-');
        }

        try {
            $pdo = get_pdo();
            $stmt = $pdo->prepare('INSERT INTO posts (title, slug, excerpt, content, featured_image, category, author, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $slug, $excerpt, $content, $featured_image, $category, $author, $status]);

            $_SESSION['admin_message'] = 'Post successfully created.';
            cms_redirect('admin-dashboard.php');
        } catch (Exception $exception) {
            $page_alert = 'Unable to save the post: ' . html_escape($exception->getMessage());
        }
    }
}

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <form method="post" class="form-grid">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo html_escape($title); ?>" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="<?php echo html_escape($slug); ?>" placeholder="auto-generated from title">
        </div>
        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" required><?php echo html_escape($excerpt); ?></textarea>
        </div>
        <div class="form-group">
            <label for="content">Content</label>
            <div id="editor-container" style="height: 500px;"></div>
            <textarea id="content" name="content" style="display:none;"><?php echo html_escape($content); ?></textarea>
        </div>
        <div class="form-group">
            <label for="featured_image">Featured Image URL</label>
            <input type="url" id="featured_image" name="featured_image" value="<?php echo html_escape($featured_image); ?>" placeholder="https://example.com/image.jpg">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $categoryRow): ?>
                    <option value="<?php echo html_escape($categoryRow['name']); ?>" <?php echo $category === $categoryRow['name'] ? 'selected' : ''; ?>><?php echo html_escape($categoryRow['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="author">Author</label>
            <select id="author" name="author" required>
                <option value="">Select author</option>
                <?php foreach ($authors as $authorRow): ?>
                    <option value="<?php echo html_escape($authorRow['name']); ?>" <?php echo $author === $authorRow['name'] ? 'selected' : ''; ?>><?php echo html_escape($authorRow['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
            </select>
        </div>
        <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Save Post</button>
            <a href="admin-dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Initialize WYSIWYG Editor to support Markdown shortcuts and ChatGPT formatting paste -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<link rel="stylesheet" href="../assets/css/quill-custom.css?v=<?php echo time(); ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    var converter = new showdown.Converter({
        tables: true,
        strikethrough: true,
        tasklists: true,
        ghCodeBlocks: true
    });

    const quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ]
        }
    });
    
    // Override Quill's default font to match the CMS styling
    quill.root.style.fontFamily = "'Outfit', sans-serif";
    quill.root.style.fontSize = "16px";

    const contentTextarea = document.querySelector('#content');
    
    // Load initial content
    quill.clipboard.dangerouslyPasteHTML(contentTextarea.value);
    
    // Sync content on form submission or change
    quill.on('text-change', function() {
        contentTextarea.value = quill.getSemanticHTML();
    });
    
    // Force an immediate clean conversion on load 
    contentTextarea.value = quill.getSemanticHTML();
            
    // Intercept paste event to automatically convert ChatGPT Markdown text into structured HTML
    quill.root.addEventListener('paste', function(e) {
        let clipboardData = e.clipboardData || window.clipboardData || (e.originalEvent && e.originalEvent.clipboardData);
        if (!clipboardData) return;
        
        let textData = clipboardData.getData('text/plain');
        if (!textData) return;
        
        // Identify strong markdown patterns: Headers, bold blocks, tables, lists, quotes, code, or links
        let isMarkdown = /(^|\r?\n)(#{1,6}\s|[-*]\s|\d+\.\s|> |\|.*?\||```)/.test(textData) || textData.includes('**') || textData.includes('`') || /\[.*?\]\(.*?\)/.test(textData);
        
        if (isMarkdown) {
            e.preventDefault();
            e.stopPropagation();
            let htmlContent = converter.makeHtml(textData);
            let range = quill.getSelection(true);
            quill.clipboard.dangerouslyPasteHTML(range.index, htmlContent);
        }
    }, true);
</script>
<?php include __DIR__ . '/includes/admin-footer.php'; ?>
