<?php
// views/post/view.php
?>



<?= $this->section('meta') ?>
<!-- SEO Meta Tags -->
<title><?= esc($seo['title']) ?></title>
<meta name="description" content="<?= esc($seo['description']) ?>">
<meta name="keywords" content="<?= esc($seo['keywords']) ?>">
<meta name="author" content="<?= esc($seo['author']) ?>">

<!-- Open Graph Tags -->
<meta property="og:title" content="<?= esc($seo['title']) ?>">
<meta property="og:description" content="<?= esc($seo['description']) ?>">
<meta property="og:image" content="<?= esc($seo['image']) ?>">
<meta property="og:url" content="<?= current_url() ?>">
<meta property="og:type" content="article">
<meta property="article:published_time" content="<?= $seo['published_time'] ?>">
<meta property="article:modified_time" content="<?= $seo['modified_time'] ?>">
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<!-- Header Section -->
<?= view('post/view_header', ['post' => $post]) ?>
<!-- Main Content -->
<?= view('post/view_content', [
    'post' => $post,
    'related_posts' => $related_posts,
    'categories' => $categories,
    'trending_tags' => $trending_tags
]) ?>

<!-- Comments Section -->
<?= view('post/view_comments', ['post' => $post, 'comments' => $comments]) ?>

<!-- Scripts -->
<!-- <?= view('post/view_scripts') ?> -->
