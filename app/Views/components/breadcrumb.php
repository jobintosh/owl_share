<?php
/**
 * Breadcrumb Component
 * 
 * @param array $items รายการ breadcrumb แต่ละ item ประกอบด้วย:
 *   - title: ข้อความที่แสดง
 *   - url: ลิงก์ (null สำหรับ item สุดท้าย)
 *   - icon: ไอคอน (optional)
 */

// เช็คว่ามี background image หรือไม่
$hasBgImage = isset($bgImage) && $bgImage;
$showTitle = $show_title ?? false;
$titleSize = $title_size ?? 'default'; // small, default, large
?>

<!-- Breadcrumb Section -->
<div class="relative <?= $hasBgImage ? 'bg-cover bg-center bg-no-repeat' : 'bg-gradient-to-r from-gray-50 to-white' ?>"
     <?= $hasBgImage ? 'style="background-image: url(' . $bgImage . ');"' : '' ?>>
    
    <?php if ($hasBgImage): ?>
    <!-- Overlay สำหรับ background image -->
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <?php endif; ?>

    <!-- Container -->
    <div class="relative container mx-auto px-4 <?= $hasBgImage ? 'py-12' : 'py-4' ?>">
        <!-- Breadcrumb Navigation -->
        
            <!-- <?php foreach ($items as $index => $item): ?>
                <?php 
                $isLast = $index === count($items) - 1;
                $textColor = $hasBgImage 
                    ? ($isLast ? 'text-white' : 'text-gray-300 hover:text-white') 
                    : ($isLast ? 'text-gray-600' : 'text-gray-500 hover:text-blue-600');
                ?>
               
                
                    <?php if ($item['url'] && !$isLast): ?>
                        <a href="<?= esc($item['url']) ?>" 
                           class="<?= $textColor ?> transition duration-200 flex items-center">
                            <?php if (isset($item['icon'])): ?>
                                
                                <i class="<?= esc($item['icon']) ?> mr-2"></i>
                            <?php endif; ?>
                            <?= esc($item['title']) ?>
                        </a>
                    <?php else: ?>
                        <span class="<?= $textColor ?> flex items-center">
                            <?php if (isset($item['icon'])): ?>
                                <i class="<?= esc($item['icon']) ?> mr-2"></i>
                            <?php endif; ?>
                            <?= esc($item['title']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!$isLast): ?>
                        >
                    <?php endif; ?>
              
            <?php endforeach; ?> -->

         

            <?php if (isset($extra_info)): ?>
                <div class="ml-auto">
                    <span class="<?= $hasBgImage ? 'text-gray-300' : 'text-gray-500' ?>">
                        <?= $extra_info ?>
                       >
                    </span>
                </div>
            <?php endif; ?>
        

        <?php if ($showTitle && !empty($items)): ?>
            <?php 
            $lastItem = end($items);
            $titleClasses = 'font-bold mt-4 ' . ($hasBgImage ? 'text-white' : 'text-gray-900');
            switch ($titleSize) {
                case 'small':
                    $titleClasses .= ' text-xl md:text-2xl';
                    break;
                case 'large':
                    $titleClasses .= ' text-3xl md:text-4xl';
                    break;
                default:
                    $titleClasses .= ' text-2xl md:text-3xl';
                    break;
            }
            ?>
            <!-- Page Title -->
            <h1 class="<?= $titleClasses ?>">
                <?= esc($lastItem['title']) ?>
            </h1>

            <?php if (isset($subtitle)): ?>
                <p class="mt-2 <?= $hasBgImage ? 'text-gray-200' : 'text-gray-600' ?>">
                    <?= esc($subtitle) ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($hasBgImage): ?>
        <!-- Bottom Fade Effect -->
        <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-white opacity-10"></div>
    <?php endif; ?>
</div>

<?php if (!$hasBgImage): ?>
    <!-- Separator Line -->
    <div class="h-px bg-gradient-to-r from-gray-200 via-gray-200 to-transparent"></div>
<?php endif; ?>