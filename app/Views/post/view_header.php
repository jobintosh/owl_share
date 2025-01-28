<?php
// views/post/view_header.php
?>

<div class="bg-gradient-to-b from-white to-gray-50 border-b">
    <div class="container mx-auto px-4 py-8">
        <!-- Category Badge -->
        <a href="<?= site_url('category/' . $post['category_slug']) ?>" 
           class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                  <?php
                    // สีแตกต่างกันตามหมวดหมู่
                    switch($post['category_slug']) {
                        case 'technology':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'programming':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'design':
                            echo 'bg-purple-100 text-purple-800';
                            break;
                        default:
                            echo 'bg-gray-100 text-gray-800';
                    }
                  ?> 
                  hover:bg-opacity-80 transition mb-4">
            <?php if (!empty($post['category_icon'])): ?>
                <i class="<?= $post['category_icon'] ?> mr-1"></i>
            <?php endif; ?>
            <?= esc($post['category_name']) ?>
        </a>

        <!-- Post Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-4">
            <?= esc($post['title']) ?>
        </h1>

        <!-- Post Excerpt if exists -->
        <?php if (!empty($post['excerpt'])): ?>
        <p class="text-xl text-gray-600 mb-6 leading-relaxed">
            <?= esc($post['excerpt']) ?>
        </p>
        <?php endif; ?>

        <!-- Author and Meta Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <!-- Author Info -->
            <div class="flex items-center space-x-4">
                <a href="<?= site_url('profile/' . $post['author_username']) ?>" 
                   class="flex items-center group">
                    <img src="<?= esc($post['author_avatar']) ?: '/images/default-avatar.png' ?>" 
                         alt="<?= esc($post['author_name']) ?>"
                         class="w-12 h-12 rounded-full border-2 border-white shadow-sm
                                group-hover:border-blue-500 transition duration-300">
                    <div class="ml-3">
                        <div class="text-gray-900 font-medium group-hover:text-blue-600 transition">
                            <?= esc($post['author_name']) ?>
                        </div>
                        <div class="text-sm text-gray-500 flex items-center">
                            <span title="<?= $post['created_at'] ?>">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= date('d M Y', strtotime($post['created_at'])) ?>
                            </span>
                            <?php if ($post['created_at'] != $post['updated_at']): ?>
                            <span class="mx-2">•</span>
                            <span title="แก้ไขล่าสุด: <?= $post['updated_at'] ?>" class="text-gray-400">
                                <i class="far fa-edit mr-1"></i>
                                แก้ไขเมื่อ <?= time_ago($post['updated_at']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Post Stats -->
            <div class="flex items-center space-x-6 text-gray-500">
                <div class="flex items-center" title="จำนวนการดู">
                    <i class="far fa-eye mr-2 text-lg"></i>
                    <span class="text-lg"><?= number_format($post['view_count']) ?></span>
                </div>
                <div class="flex items-center" title="จำนวนความคิดเห็น">
                    <i class="far fa-comment mr-2 text-lg"></i>
                    <span class="text-lg"><?= number_format($post['comment_count']) ?></span>
                </div>
                <div class="flex items-center" title="จำนวนการถูกใจ">
                    <i class="far fa-heart mr-2 text-lg"></i>
                    <span class="text-lg"><?= number_format($post['like_count']) ?></span>
                </div>
            </div>
        </div>

        <!-- Tags -->
        <?php if (!empty($post['tags'])): ?>
        <div class="flex flex-wrap gap-2">
            <?php foreach ($post['tags'] as $tag): ?>
            <a href="<?= site_url('tag/' . urlencode($tag)) ?>" 
               class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-white 
                      text-gray-700 hover:bg-blue-50 hover:text-blue-600 
                      border border-gray-200 transition duration-200">
                <i class="fas fa-hashtag mr-1 opacity-50"></i>
                <?= esc($tag) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Reading Time & Share Buttons (Optional) -->
        <?php
            // คำนวณเวลาในการอ่านโดยประมาณ
            $words = str_word_count(strip_tags($post['content']));
            $minutes = ceil($words / 200); // สมมติว่าอ่าน 200 คำต่อนาที
        ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between 
                    mt-6 pt-6 border-t border-gray-100">
            <!-- Reading Time -->
            <div class="text-gray-500 mb-4 sm:mb-0">
                <i class="far fa-clock mr-2"></i>
                ใช้เวลาอ่านประมาณ <?= $minutes ?> นาที
            </div>

            <!-- Share Buttons -->
            <div class="flex items-center space-x-4">
                <span class="text-gray-500">แชร์บทความ:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= current_url() ?>" 
                   target="_blank"
                   class="text-blue-600 hover:bg-blue-50 p-2 rounded-full transition duration-200">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= current_url() ?>&text=<?= urlencode($post['title']) ?>" 
                   target="_blank"
                   class="text-blue-400 hover:bg-blue-50 p-2 rounded-full transition duration-200">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="https://line.me/R/msg/text/?<?= urlencode($post['title'] . ' ' . current_url()) ?>"
                   target="_blank"
                   class="text-green-600 hover:bg-green-50 p-2 rounded-full transition duration-200">
                    <i class="fab fa-line text-xl"></i>
                </a>
                <button onclick="copyToClipboard('<?= current_url() ?>')"
                        class="text-gray-500 hover:bg-gray-50 p-2 rounded-full transition duration-200">
                    <i class="fas fa-link text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php if ($post['image']): ?>
<!-- Hero Image -->
<div class="w-full bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <img src="<?= esc($post['image']) ?>" 
             alt="<?= esc($post['title']) ?>"
             class="w-full h-auto md:h-[400px] object-cover rounded-lg shadow-lg">
    </div>
</div>
<?php endif; ?>