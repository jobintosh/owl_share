
<div class="container my-5">

<!-- เพิ่มในส่วนบนของ container -->

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Content... -->


    <div class="row">
        <!-- Share Form Section -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-share-alt me-2"></i>แบ่งปันข้อมูล</span>
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
                    <form id="shareForm">
                        <!-- Title Input -->
                        <div class="mb-3">
                            <label for="title" class="form-label">หัวข้อ</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                <input type="text" class="form-control" id="title" name="title" 
                                       required minlength="5" placeholder="ใส่หัวข้อที่นี่...">
                                       <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="contentTypeTabsContent">
                            <!-- Text Editor Tab -->
                            <div class="tab-pane fade show active" id="text" role="tabpanel">
                                <textarea id="editor" name="content"></textarea>
                            </div>

                            <!-- Whiteboard Tab -->
                            <div class="tab-pane fade" id="whiteboard" role="tabpanel">
                                <div class="whiteboard-container">
                                    <div class="whiteboard-tools">
                                        <div class="btn-group me-2">
                                            <button type="button" id="penTool" class="btn btn-outline-primary active">
                                                <i class="fas fa-pen"></i> ปากกา
                                            </button>
                                            <button type="button" id="eraserTool" class="btn btn-outline-primary">
                                                <i class="fas fa-eraser"></i> ยางลบ
                                            </button>
                                        </div>
                                        <div class="d-flex align-items-center me-2">
                                            <label class="me-2">สี:</label>
                                            <input type="color" id="colorPicker" class="form-control form-control-color" 
                                                   value="#000000">
                                        </div>
                                        <div class="d-flex align-items-center me-2">
                                            <label class="me-2">ขนาด:</label>
                                            <select id="brushSize" class="form-select" style="width: auto;">
                                                <option value="1">1px</option>
                                                <option value="3">3px</option>
                                                <option value="5" selected>5px</option>
                                                <option value="8">8px</option>
                                                <option value="10">10px</option>
                                            </select>
                                        </div>
                                        <button type="button" id="clearCanvas" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i> ล้างกระดาน
                                        </button>
                                    </div>
                                    <canvas id="whiteboardCanvas"></canvas>
                                </div>
                            </div>

                           

                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel">
                                <div id="imageDropzone" class="dropzone-container">
                                    <input type="file" id="imageUpload" multiple accept="image/*" style="display: none;">
                                    <div class="dropzone-message">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                        <p>ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                                        <p class="small text-muted">รองรับไฟล์ภาพ JPG, PNG, GIF</p>
                                    </div>
                                </div>
                                <div id="imagePreview" class="image-preview mt-3"></div>
                            </div>
                        </div>

                        <!-- Category and Tags -->
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">
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
                            <div class="col-md-6">
                                <label for="tagsInput" class="form-label">
                                    <i class="fas fa-tags"></i> แท็ก
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="tagsInput" 
                                           placeholder="พิมพ์แท็กแล้วกด Enter">
                                    <button class="btn btn-outline-secondary" type="button" id="addTagBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div id="tagsContainer" class="mt-2"></div>
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
        </div>

        <!-- Recent Posts Section -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-2"></i>โพสต์ล่าสุด</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="RecentPosts.refresh()">
                            <i class="fas fa-sync-alt"></i> รีเฟรช
                        </button>
                    </h5>

                    <!-- Posts Container -->
                    <div id="recentPosts">
                        <?php if (!empty($recent_posts)): ?>
                            <?php foreach ($recent_posts as $post): ?>
                                <? //view('share/partials/_post_card', ['post' => $post]) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>ยังไม่มีโพสต์</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php //exit; ?>

                    <!-- Load More -->
                    <?php if (count($recent_posts) >= 5): ?>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary" onclick="RecentPosts.loadMore()">
                                <i class="fas fa-plus"></i> โหลดเพิ่มเติม
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load required scripts -->
<?= view('share/partials/_scripts') ?>
<script src="<?= base_url('js/alert-handler.js') ?>"></script>