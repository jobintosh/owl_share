<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_token() ?>">
    <meta name="X-CSRF-HASH" content="<?= csrf_hash() ?>">
    <title><?= esc($title) ?></title>

    <link rel="icon" href="<?= base_url('images/logo/owl.png') ?>" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script> -->

    <!-- Custom CSS -->
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet">
    <!-- <script src="<?= base_url('js/reactions.js') ?>"></script> -->

    <!-- <?php if (current_url() == base_url('post')): ?>
    <script src="<?= base_url('js/reactions.js') ?>"></script>
    <?php endif; ?> -->

    </script>


    <!-- Prompt Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">



    <!-- Share Page Specific -->
    <?php if (current_url() == base_url('share')): ?>
        <!-- TinyMCE -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>

        <!-- Share CSS -->
        <link href="<?= base_url('css/share.css') ?>" rel="stylesheet">

        <!-- Initialize TinyMCE -->
        <script>
            const baseUrl = '<?= base_url() ?>';
            const userId = '<?= session()->get('user_id') ?>';

            // TinyMCE Configuration
            document.addEventListener('DOMContentLoaded', function() {
                tinymce.init({
                    selector: '#editor',
                    height: 400,
                    width: '100%',
                    menubar: false,
                    branding: false,
                    statusbar: false,
                    promotion: false,
                    plugins: 'autolink link image lists table paste help wordcount',
                    toolbar: [
                        'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist',
                        'removeformat | help'
                    ].join(' | '),
                    content_style: `
                        body { 
                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
                            font-size: 16px;
                            line-height: 1.6;
                            padding: 1rem;
                        }
                    `,
                    formats: {
                        bold: {
                            inline: 'strong'
                        },
                        italic: {
                            inline: 'em'
                        },
                        underline: {
                            inline: 'u'
                        },
                        strikethrough: {
                            inline: 'del'
                        }
                    },
                    valid_elements: 'p,br,strong,em,u,del,a[href|target],ul,ol,li,h1,h2,h3,h4,h5,h6,table,tr,td,th,thead,tbody',
                    extended_valid_elements: 'img[src|alt|width|height]',
                    paste_as_text: true,
                    paste_enable_default_filters: false,
                    browser_spellcheck: true,
                    contextmenu: false,
                    language: 'th',
                    setup: function(editor) {
                        editor.on('Change', function() {
                            editor.save();
                        });
                    }
                });
            });
        </script>
    <?php endif; ?>
</head>

<body>

    <body>


        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.classList.remove('show');
                        alert.addEventListener('transitionend', () => alert.remove());
                    });
                }, 5000); // 5000 ms = 5 seconds หายใน 5 วิ
            });
        </script>