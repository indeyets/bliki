<?php $this->set('title', $title) ?>
<?php $this->extend('layout') ?>

<h1>Blog Archive</h1>

<?php foreach ($articles as $article): ?>
    <h2><?php echo $article['title']; ?></h2>
    <?php echo $article['content'] ?>

    <br><br>
<?php endforeach; ?>
