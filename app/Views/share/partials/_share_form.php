<?php
/**
 * Share Form Partial
 */
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            <i class="fas fa-share-alt"></i> แบ่งปันข้อมูล
        </h5>

        <!-- Content Type Tabs -->
        <ul class="nav nav-tabs mb-3" id="contentTypeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#text" type="button">
                    <i class="fas fa-pen"></i> เขียนข้อความ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#whiteboard" type="button">
                    <i class="fas fa-paint-brush"></i> วาดภาพ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#gallery" type="button">
                    <i class="fas fa-images"></i> แกลเลอรี่
                </button>
            </li>
        </ul>

        <!-- Share Form -->
        <form id="shareForm" onsubmit="return ShareForm.handleSubmit(event)">
            <!-- Title Input -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-heading"></i>
                    </span>
                    <input type="text" class="form-control" id="title" name="title" 
                           placeholder="หัวข้อ" required minlength="5">
                </div>
            </div>

            <!-- Content Tabs -->
            <div class="tab-content" id="contentTypeTabsContent">
                <!-- Text Editor Tab -->
                <div class="tab-pane fade show active" id="text" role="tabpanel">
                    <?= view('share/partials/_text_editor') ?>
                </div>

                <!-- Whiteboard Tab -->
                <div class="tab-pane fade" id="whiteboard" role="tabpanel">
                    <?= view('share/partials/_whiteboard') ?>
                </div>

                <!-- Gallery Tab -->
                <div class="tab-pane fade" id="gallery" role="tabpanel">
                    <?= view('share/partials/_gallery') ?>
                </div>
            </div>

            <!-- Category and Tags -->
            <div class="row mb-3 mt-3">
                <!-- Category Select -->
                <div class="col-md-6">
                    <label for="category">
                        <i class="fas fa-folder"></i> หมวดหมู่
                    </label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tags Input -->
                <div class="col-md-6">
                    <label for="tagsInput">
                        <i class="fas fa-tags"></i> แท็ก
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="tagsInput" 
                               placeholder="พิมพ์แท็กแล้วกด Enter">
                        <button class="btn btn-outline-secondary" type="button" id="addTagBtn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <!-- Tags Container -->
                    <div id="tagsContainer" class="mt-2"></div>
                    
                    <!-- Trending Tags -->
                    <?php if (!empty($trending_tags)): ?>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-fire"></i> แท็กยอดนิยม:
                            </small>
                            <?php foreach ($trending_tags as $tag): ?>
                                <span class="tag-badge" onclick="ShareForm.addTag('<?= esc($tag['name']) ?>')">
                                    <?= esc($tag['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-share"></i> แบ่งปัน
                </button>
            </div>
        </form>
    </div>
</div>