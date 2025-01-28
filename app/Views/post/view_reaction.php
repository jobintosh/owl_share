<div class="card-body">
                    <div class="row text-center">
                        <?php
                        $reactions = [
                            'like' => ['icon' => 'like-icon.png', 'label' => 'ถูกใจ'],
                            'love' => ['icon' => 'love-icon.png', 'label' => 'รักเลย'],
                            'haha' => ['icon' => 'haha-icon.png', 'label' => 'ฮาๆ'],
                            'wow' => ['icon' => 'wow-icon.png', 'label' => 'ว้าว'],
                            'sad' => ['icon' => 'sad-icon.png', 'label' => 'เสียใจ'],
                            'angry' => ['icon' => 'angry-icon.png', 'label' => 'โกรธ']
                        ];
                        foreach ($reactions as $type => $details): ?>
                            <div class="col-4 col-md-2 mb-3">
                                <button
                                    onclick="toggleReaction(<?= $post['id'] ?>, '<?= $type ?>'); return false;"
                                    class="reaction-button d-flex flex-column align-items-center">
                                    <!-- PNG Icon -->
                                    <img src="<?= base_url('images/icons/' . $details['icon']) ?>" alt="<?= $details['label'] ?>" class="reaction-icon mb-2" />
                                    <!-- Label and Count -->
                                    <div class="text-center">
                                        <small class="d-block text-secondary"><?= $details['label'] ?></small>
                                        <span id="<?= $type ?>Count" class="text-secondary">
                                            <?= number_format($post['reactions'][$type]) ?>
                                        </span>
                                    </div>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <script>
                const BASE_URL = '<?= site_url() ?>';
                const POST_ID = <?= $post['id'] ?>;

                // Function to fetch and update reaction counts
                function updateReactionCounts() {
                    fetch(`${BASE_URL}/post/getReactionCounts/${POST_ID}`)
                        .then(response => response.json())
                        .then(data => {
                            // Update each reaction count
                            for (const [type, count] of Object.entries(data)) {
                                const countElement = document.getElementById(`${type}Count`);
                                if (countElement) {
                                    countElement.textContent = count.toLocaleString();
                                }
                            }
                        })
                        .catch(error => console.error('Error fetching reaction counts:', error));
                }

                // Relay update count reaction
                setInterval(updateReactionCounts, 5000);
            </script>
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">